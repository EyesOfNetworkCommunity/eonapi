<?php
/*
#
# EONAPI - Create objects
#
# Copyright (c) 2017 AXIANS Cloud Builder
# Author: Jean-Philippe Levy <jean-philippe.levy@axians.com>
#
# Copyright (c) 2017 AXIANS C&S
# Author: Adrien van den Haak <adrien.vandenhaak@axians.com>
#
*/

include("/srv/eyesofnetwork/eonweb/include/config.php");
include("/srv/eyesofnetwork/eonweb/include/function.php");
include("/srv/eyesofnetwork/eonweb/module/monitoring_ged/ged_functions.php");
include("/srv/eyesofnetwork/lilac/includes/config.inc");


class CreateObjects {
    
    function __construct(){
        
    }
    
    
    function getLogs($error, $success){
        $logs = $error.$success;
        $countLogs = substr_count($logs, "\n");
        
        if( $countLogs > 1 )
            $logs = str_replace("\n", " | ", $logs );
        else
            $logs = str_replace("\n", "", $logs);

        return $logs;
    }
    
    function exportConfigurationToNagios(){
        $jobName = "nagios";
        $c = new Criteria();
        //$c->add(ExportJobPeer::END_TIME, null);
        $exportJobs = ExportJobPeer::doSelect($c);
        
        $nagiosJobId = NULL;
        foreach($exportJobs as $job){
            if( $job->getName() == $jobName ){
                $nagiosJobId = $job->getId();
                break;
            }
        }
        
        if( $nagiosJobId != NULL ){
            $exportJob = ExportJobPeer::retrieveByPK( $nagiosJobId );
            $exportJob->setStatusCode(ExportJob::STATUS_STARTING);
            $exportJob->setStartTime(time());
            $exportJob->setStatus("Starting...");
            $exportJob->save();
            exec("php /srv/eyesofnetwork/lilac/exporter/export.php " . $exportJob->getId() . " > /dev/null 2>&1", $tempOutput, $retVal);   
        }
            
    }
    


    
	/* LILAC -  Hosts and services creation */
	public function createHost( $templateHostName, $hostName, $hostIp, $hostAlias = "", $contactName = NULL, $contactGroupName = NULL ){
        $error = "";
        $success = "";
        
        $nhp = new NagiosHostPeer;
		// Find host
		$host = $nhp->getByName($hostName);
		if($host) {
			$error .= "Host $hostName already exists\n";
		}

        $nhtp = new NagiosHostTemplatePeer;
		// Find host template
		$template_host = $nhtp->getByName($templateHostName);
		if(!$template_host) {
			$error .= "Host Template $templateHostName not found\n";
		}
        
		
		// Lauch actions if no errors
		if(empty($error)) {	
			try {
				// host
				$tempHost = new NagiosHost();
				$tempHost->setName($hostName);
				$tempHost->setAlias($hostAlias);
				$tempHost->setAddress($hostIp);
				$tempHost->save();
				$success .= "Host $hostName added\n";
                

				// host-template
				$newInheritance = new NagiosHostTemplateInheritance();
				$newInheritance->setNagiosHost($tempHost);
				$newInheritance->setNagiosHostTemplateRelatedByTargetTemplate($template_host);
				$newInheritance->save();
				$success .= "Host Template ".$templateHostName." added to host ".$hostName."\n";
                
                
                if( $contactName != NULL ){
                    //Add a contact to a host
                    $this->addContactToHost( $tempHost, $contactName, $error, $success );    
                }
                
                if( $contactGroupName != NULL ){
                    //Add a contact group to a host
                    $this->addContactGroupToHost( $tempHost, $contactGroupName, $error, $success );    
                }
                                
                
				
				// Export
				$this->exportConfigurationToNagios();
			}
			catch(Exception $e) {
				$error .= $e->getMessage()."\n";
			}
		}
        
        
        $logs = $this->getLogs($error, $success);
        
        return $logs;
        
	}
    
    
    function createHostTemplate( $templateHostName ){
        global $lilac;
        $error = "";
        $success = "";
        $description = "host template";
        
        // Check for pre-existing host template with same name        
        $nhtp = new NagiosHostTemplatePeer;
		$template_host = $nhtp->getByName($templateHostName);
		if($template_host) {
			$error .= "A host template with that name already exists!\n";
		}
           
        if( $templateHostName == NULL || $templateHostName == "" ){
            $error .= "A host template name must be defined\n";   
        }
           
           
        if( empty($error) ) {			
            /*---Create template---*/
            $template = new NagiosHostTemplate();
            $template->setName( $templateHostName );
            $template->setDescription( $description );
            $template->save();
            
            $success .= "Host template ".$templateHostName." created\n";
            
            
            /*---Add host template inheritance ("GENERIC_HOST")---*/
            $targetTemplate = $nhtp->getByName("GENERIC_HOST");
            if(!$targetTemplate) {
                $error .= "The target template 'GENERIC_HOST' does not exit\n";
            }
            else{
                $newInheritance = new NagiosHostTemplateInheritance();
                $newInheritance->setNagiosHostTemplateRelatedBySourceTemplate($template);
                $newInheritance->setNagiosHostTemplateRelatedByTargetTemplate($targetTemplate);
                try {
                    $newInheritance->save();
                    $success .= "Template 'GENERIC_HOST' added to inheritance chain\n";				
                }
                catch(Exception $e) {
                    $error .= $e->getMessage();
                }   
            }
            
            /*---Create Host Group with Host Template name if not exists---*/
            if($lilac->hostgroup_exists( $templateHostName )) {
                $nhgp = new NagiosHostgroupPeer;
                $hostGroup = $nhgp->getByName( $templateHostName );
            }
            else{
                $hostGroup = $this->createHostGroup( $templateHostName, $error, $success );   
            }
            
            /*---Add Group Membership to Host template---*/
            if( $hostGroup != NULL ){
                $lilac->add_hostgroup_template_member( $hostGroup->getId(), $template->getId() );
                
                $success .= "Host group membership added to ".$templateHostName."\n";
            }

        }
        
        
        // Export
        $this->exportConfigurationToNagios();
        
        
        $logs = $this->getLogs($error, $success);
        
        return $logs;
    }
    
    
    function createHostGroup( $hostGroupName, &$error = "", &$success = "" ){
        global $lilac;
        $hostGroup = NULL;
        
        // Check for pre-existing contact with same name
		if($lilac->hostgroup_exists( $hostGroupName )) {
			$error .= "A host group with that name already exists!\n";
		}
		else {
			// Field Error Checking
			if( $hostGroupName == "" ) {
				$error .= "Host group name is required\n";
			}
			else {
				// All is well for error checking, add the hostgroup into the db.
				$hostGroup = new NagiosHostgroup();
				$hostGroup->setAlias( "host group" );
				$hostGroup->setName( $hostGroupName );				
				$hostGroup->save();				
				
                $success .= "Host group ".$hostGroupName." created\n";
			}
		}
        
        
        return $hostGroup;
    }
    
    function modifyCommand(){
        /*---Modify check command ==> 'dummy_ok'---*/
        //TODO ==> Change command to 'dummy_ok' for template GENERIC_HOST (inheritance)
        $ncp = new NagiosCommandPeer;
        $targetCommand = $ncp->getByName("dummy_ok");
        if(!$targetCommand) {
            $error .= "The target command 'dummy_ok' does not exist\n";
        }
        else{
            $template->setCheckCommand(NagiosCommandPeer::retrieveByPK($targetCommand->getId()));
            $template->save();   

            $success .= "Check command modified to 'dummy_ok'\n";
        }
    }
           
    
    public function addHostTemplateToHost( $templateHostName, $hostName ){
        $error = "";
        $success = "";
        
        $nhp = new NagiosHostPeer;
        $host = $nhp->getByName($hostName);
        
		if(!$host) {
			$error .= "Host $hostName not found\n";
		}
        
        $nhtp = new NagiosHostTemplatePeer;
		// Find host template
		$template_host = $nhtp->getByName($templateHostName);
		if(!$template_host) {
			$error .= "Host Template $templateHostName not found\n";
		}
        
        	
        // We need to get the count of templates already inherited
        $templateList = $host->getNagiosHostTemplateInheritances();
        foreach($templateList as $tempTemplate) {
            if($tempTemplate->getId() == $template_host->getId()) {
                $error .= "That template already exists in the inheritance chain\n";
            }
        }
        if(empty($error)) {
            $newInheritance = new NagiosHostTemplateInheritance();
            $newInheritance->setNagiosHost($host);
            $newInheritance->setNagiosHostTemplateRelatedByTargetTemplate($template_host);
            $newInheritance->setOrder(count($templateList));
            try {
                $newInheritance->save();
                $success .= "Host template ".$templateHostName." added to ".$hostName."\n";
                
                // Export
                $this->exportConfigurationToNagios();
            }
            catch(Exception $e) {
                $error .= $e->getMessage();
            }		
        }
		
        
        
        $logs = $this->getLogs($error, $success);
        
        return $logs;
    }
    
    public function addContactToHostTemplate( $contactName, $templateHostName ){
        $error = "";
        $success = "";

        $ncp = new NagiosContactPeer;
        // Find host contact
        $tempContact = $ncp->getByName( $contactName );
        if(!$tempContact) {
            $error .= "Contact $contactName not found\n";	
        }
        
        $nhtp = new NagiosHostTemplatePeer;
		// Find host template
		$template_host = $nhtp->getByName($templateHostName);
		if(!$template_host) {
			$error .= "Host Template $templateHostName not found\n";
		}

        
        if( empty($error) ) {
            $c = new Criteria();
            $c->add(NagiosHostContactMemberPeer::TEMPLATE, $template_host->getId());
            $c->add(NagiosHostContactMemberPeer::CONTACT, $tempContact->getId());
            $membership = NagiosHostContactMemberPeer::doSelectOne($c);
            if($membership) {
                $error .= "That contact already exists in that list!\n";
            }
            else {
                $membership = new NagiosHostContactMember();
                $membership->setTemplate( $template_host->getId() );
                $membership->setNagiosContact($tempContact);
                $membership->save();
                $success .= "Contact ".$contactName." added to host template ".$templateHostName."\n";
                
                // Export
                $this->exportConfigurationToNagios();
            }
        } 
        
        
        
        $logs = $this->getLogs($error, $success);
        
        return $logs;
    }
    
    public function addContactGroupToHostTemplate( $contactGroupName, $templateHostName ){
        $error = "";
        $success = "";

        $ncgp = new NagiosContactGroupPeer;
        $tempContactGroup = $ncgp->getByName( $contactGroupName );
        if(!$tempContactGroup) {
            $error .= "Contact group $contactGroupName not found\n";	
        }
        
        $nhtp = new NagiosHostTemplatePeer;
		// Find host template
		$template_host = $nhtp->getByName($templateHostName);
		if(!$template_host) {
			$error .= "Host Template $templateHostName not found\n";
		}

        
        if( empty($error) ) {
            $c = new Criteria();
            $c->add(NagiosHostContactgroupPeer::HOST_TEMPLATE, $template_host->getId());
            $c->add(NagiosHostContactgroupPeer::CONTACTGROUP, $tempContactGroup->getId());
            $membership = NagiosHostContactgroupPeer::doSelectOne($c);
            if($membership) {
                $error .= "That contact group already exists in that list!\n";
            }
            else {
                $membership = new NagiosHostContactgroup();
                $membership->setHostTemplate( $template_host->getId() );
                $membership->setNagiosContactgroup($tempContactGroup);
                $membership->save();
                $success .= "Contact group ".$contactGroupName." added to host template ".$templateHostName."\n";
                
                // Export
                $this->exportConfigurationToNagios();
            }
        } 
        
        
        
        $logs = $this->getLogs($error, $success);
        
        return $logs;
    }
    
    public function addContactToExistingHost( $hostName, $contactName ){
        $error = "";
        $success = "";
        
        $nhp = new NagiosHostPeer;
        $host = $nhp->getByName($hostName);
        
		if(!$host) {
			$error .= "Host $hostName not found\n";
		}
        
        // Lauch actions if no errors
		if(empty($error)) {	
            if( $contactName != NULL ){
                //Add a contact to a host
                $this->addContactToHost( $host, $contactName, $error, $success );
                
                // Export
                $this->exportConfigurationToNagios();
            }
        }
        
        $logs = $this->getLogs($error, $success);
        
        return $logs;
    }
    
    public function addContactGroupToExistingHost( $hostName, $contactGroupName ){
        $error = "";
        $success = "";
        
        $nhp = new NagiosHostPeer;
        $host = $nhp->getByName($hostName);
        
		if(!$host) {
			$error .= "Host $hostName not found\n";
		}
        
        // Lauch actions if no errors
		if(empty($error)) {	
            if( $contactGroupName != NULL ){
                //Add a contact group to a host
                $this->addContactGroupToHost( $host, $contactGroupName, $error, $success );
                
                // Export
                $this->exportConfigurationToNagios();
            }
        }
        
        $logs = $this->getLogs($error, $success);
        
        return $logs;
    }
    
    public function addContactToHost( $tempHost, $contactName, &$error, &$success ){
        $ncp = new NagiosContactPeer;
        
        // Find host contact
        $tempContact = $ncp->getByName( $contactName );
        if(!$tempContact) {
            $error .= "Contact $contactName not found\n";	
        }

        
        //If contact exists
        if($tempContact) {
            $c = new Criteria();
            $c->add(NagiosHostContactMemberPeer::HOST, $tempHost->getId());
            $c->add(NagiosHostContactMemberPeer::CONTACT, $tempContact->getId());
            $membership = NagiosHostContactMemberPeer::doSelectOne($c);
            
            //Test if contact doesn't already exist
            if($membership) {
                $error .= "That contact already exists in that list!\n";
            }
            else{
                // host-contact
                $membership = new NagiosHostContactMember();
                $membership->setHost( $tempHost->getId() );
                $membership->setNagiosContact( $tempContact );
                $membership->save();
                $hostName = $tempHost->getName();
                $success .= "Contact $contactName added to host $hostname\n";
            }
        }    
    }
    
    public function addContactGroupToHost( $tempHost, $contactGroupName, &$error, &$success ){
        $ncgp = new NagiosContactGroupPeer;

        // Find host group contact
        $tempContactGroup = $ncgp->getByName( $contactGroupName );
        if(!$tempContactGroup) {
            $error .= "Contact group $contactGroupName not found\n";	
        }


        if($tempContactGroup) {
            $c = new Criteria();
            $c->add(NagiosHostContactgroupPeer::HOST, $tempHost->getId());
            $c->add(NagiosHostContactgroupPeer::CONTACTGROUP, $tempContactGroup->getId());
            $membership = NagiosHostContactgroupPeer::doSelectOne($c);
            
            //Test if contact group doesn't already exist
            if($membership) {
                $error .= "That contact group already exists in that list!\n";
            }
            else{
                $membership = new NagiosHostContactgroup();
                $membership->setHost( $tempHost->getId() );
                $membership->setNagiosContactGroup( $tempContactGroup );
                $membership->save();
                $hostName = $tempHost->getName();
                $success .= "Contact group $contactGroupName added to host $hostName\n";   
            }	
        }
    }
    
    
    

	public function createService( $hostName, $services, $host = NULL ){
        
        $error = "";
        $success = "";
        
	    
        $nsp = new NagiosHostPeer;
        
        if( $host == NULL ){
            $host = $nsp->getByName($hostName);
            
            if(!$host) {
                $error .= "Host $hostName doesn't exist\n";
            }
        }
        
        $nstp = new NagiosServiceTemplatePeer;
		

        //Test if the parent templates exist
        foreach($services as $key => $service) {
            $templateName = $service[0];
            $template = $nstp->getByName($templateName);
            if(!$template) {
                $error .= "Service Template $templateName not found\n";	
            }       
        }
		
		if(empty($error)) {	
			try {
				// service interface
				foreach($services as $key => $service) {
					$tempService = new NagiosService();
					$tempService->setDescription($key);
					$tempService->setHost($host->getId());
					$tempService->save();
					$success .= "Service $key added\n";
					
					$newInheritance = new NagiosServiceTemplateInheritance();
					$newInheritance->setNagiosService($tempService);
					$template = NagiosServiceTemplatePeer::getByName($service[0]);
					$newInheritance->setNagiosServiceTemplateRelatedByTargetTemplate($template);
					$newInheritance->save();
					$success .= "Service Template ".$service[0]." added to service $key\n";

					for($i=1 ; $i < count($service) ; $i++) {
						$param = new NagiosServiceCheckCommandParameter();
						$param->setService($tempService->getId());
						$param->setParameter($service[$i]);
						$param->save();
						$success .= "Command Parameter ".$service[$i]." added to $key\n";
					}
				}
				
				// Export
				$this->exportConfigurationToNagios();
			}
			catch(Exception $e) {
				$error .= $e->getMessage()."\n";
			}
		}
        
        
        $logs = $this->getLogs($error, $success);
        
        return $logs;
        
	}
    
    
    public function createUser($user_name, $user_mail, $admin = false, $filterName = "", $filterValue = ""){
        //Lower case
        $user_name = strtolower($user_name);
        
        $success = "";
        $error = "";
        $user_group = 0;
        //Local user
        $user_type = 0;
        $user_password1 = $user_name;
        $user_password2 = $user_name;
        $message = false;
        
        //Admin
        if( $admin == true ){
            //admins group
            $user_group = 1;
            
            $user_descr = "admin user";
        }
        else{
            $user_descr = "limited user";
        }
        
        $created_user_limitation = !($admin);
        // EONWEB - User creation 
        $user = insert_user($user_name, $user_descr, $user_group, $user_password1, $user_password2, $user_type, "", $user_mail, $created_user_limitation, $message);

        if($user) {
            $success .= "User $user_name created\n";
        } else {
            $error .= "Unable to create user $user_name\n";	
        }


        // EONWEB - XML Filter creation
        $xml_file = "/srv/eyesofnetwork/eonweb/cache/".$user_name."-ged.xml";
        $dom = openXml();
        $root = $dom->createElement("ged");
        $root = $dom->appendChild($root);
        
        $default = $dom->createElement("default");
        $default = $root->appendChild($default);
        
        //GED filters for non admin users
        if($admin == false){
            $default = $root->getElementsByTagName("default")->item(0);
            $default->appendChild($dom->createTextNode($user_name));

            $filters = $dom->createElement("filters");
            $filters = $root->appendChild($filters);
            $filters->setAttribute("name",$user_name);
            $filter = $dom->createElement("filter");
            $filter = $filters->appendChild($filter);
            $filter->setAttribute("name", $filterName);
            $filter->appendChild($dom->createTextNode( $filterValue ));    
        }
        
        
        
        $dom->save($file);
        $xml=$dom->saveXML();

        $fp=@fopen($xml_file,"w+");
        
        if(fwrite($fp,$xml)) {
            $success .= "Events filters file $xml_file is created\n";
        } else {
            $error .= "Unable to create xml file\n";
        }
        
        fclose($fp);

        chown($xml_file,"apache");
        chgrp($xml_file,"apache");
        
        
        // Export
        $this->exportConfigurationToNagios();


        $logs = $this->getLogs($error, $success);
        
        return $logs;
        
    }
    
	
}



?>
