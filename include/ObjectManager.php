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

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);

include("/srv/eyesofnetwork/eonweb/include/config.php");
include("/srv/eyesofnetwork/eonweb/include/arrays.php");
include("/srv/eyesofnetwork/eonweb/include/function.php");
include("/srv/eyesofnetwork/eonweb/include/livestatus/Client.php");
include("/srv/eyesofnetwork/eonweb/module/monitoring_ged/ged_functions.php");
include("/srv/eyesofnetwork/lilac/includes/config.inc");

use Nagios\Livestatus\Client;

# Class with all api functions
class ObjectManager {
    
	private $authUser;
		
    function __construct(){
		# Get api userName
		$request = \Slim\Slim::getInstance()->request();
		$this->authUser = $request->get('username');  
    }
     
	/* EONAPI - Display results */
    function getLogs($error, $success){
        $logs = $error.$success;
        $countLogs = substr_count($logs, "\n");
        
        if( $countLogs > 1 )
            $logs = str_replace("\n", " | ", $logs );
        else
            $logs = str_replace("\n", "", $logs);

        return retrim($logs," | ");
    }
   
	/* LILAC - Exporter */
    function exportConfigurationToNagios( &$error = "", &$success = "" ){
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
            
            $success .= "Nagios configuration exported\n";
        }
        else{
            $error .= "ERROR during nagios configuration export\n";
        }
            
    }
        
	/* LILAC - Create Host and Services */
	public function createHost( $templateHostName, $hostName, $hostIp, $hostAlias = "", $contactName = NULL, $contactGroupName = NULL, $exportConfiguration = FALSE ){
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
                if( $exportConfiguration == TRUE )
				    $this->exportConfigurationToNagios($error, $success);
			}
			catch(Exception $e) {
				$error .= $e->getMessage()."\n";
			}
		}
        
        
        $logs = $this->getLogs($error, $success);
        
        return $logs;
        
	}
    
	/* LILAC - Delete Host */
	public function deleteHost( $hostName, $exportConfiguration = FALSE ){
		$error = "";
		$success = "";

		try {
			$nhp = new NagiosHostPeer;
			$host = $nhp->getByName($hostName);

			if($host) {
				$host->delete();
				$success .= "Host ".$hostName." deleted\n";
			} else {
				$error .= "Host ".$hostName." not found\n";
			}

			// Export
			if( $exportConfiguration == TRUE )
				$this->exportConfigurationToNagios();
		}
		catch(Exception $e) {
			$error .= $e->getMessage()."\n";
		}

		$logs = $this->getLogs($error, $success);
		return $logs;
	}
    	
	/* LILAC - Create Host Template */
    function createHostTemplate( $templateHostName, $exportConfiguration = FALSE ){
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
        if( $exportConfiguration == TRUE )
            $this->exportConfigurationToNagios($error, $success);
                
        $logs = $this->getLogs($error, $success);
        
        return $logs;
    }
    
    /* LILAC - Create Host Group */
    function createHostGroup( $hostGroupName, &$error = "", &$success = "", $exportConfiguration = FALSE ){
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
    
	/* LILAC - Modify Command */
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
           
    /* LILAC - Add Template to Host */
    public function addHostTemplateToHost( $templateHostName, $hostName, $exportConfiguration = FALSE ){
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
        if( $host ){
            $templateList = $host->getNagiosHostTemplateInheritances();
            foreach($templateList as $tempTemplate) {
                if($tempTemplate->getId() == $template_host->getId()) {
                    $error .= "That template already exists in the inheritance chain\n";
                }
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
                if( $exportConfiguration == TRUE )
                    $this->exportConfigurationToNagios($error, $success);
            }
            catch(Exception $e) {
                $error .= $e->getMessage();
            }		
        }
		
        
        
        $logs = $this->getLogs($error, $success);
        
        return $logs;
    }
    
    /* LILAC - Add Contact to Host Template */
	public function addContactToHostTemplate( $contactName, $templateHostName, $exportConfiguration = FALSE ){
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
                if( $exportConfiguration == TRUE )
                    $this->exportConfigurationToNagios($error, $success);
            }
        } 
        
        
        
        $logs = $this->getLogs($error, $success);
        
        return $logs;
    }
    
	/* LILAC - Add Contact Group to Host Template */
    public function addContactGroupToHostTemplate( $contactGroupName, $templateHostName, $exportConfiguration = FALSE ){
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
                if( $exportConfiguration == TRUE )
                    $this->exportConfigurationToNagios($error, $success);
            }
        } 
             
        $logs = $this->getLogs($error, $success);
        
        return $logs;
    }
    
    /* LILAC - Add Contact to Host */
	public function addContactToExistingHost( $hostName, $contactName, $exportConfiguration = FALSE ){
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
                if( $exportConfiguration == TRUE )
                    $this->exportConfigurationToNagios($error, $success);
            }
        }
        
        $logs = $this->getLogs($error, $success);
        
        return $logs;
    }
    
	/* LILAC - Add Contact Group to Host */
    public function addContactGroupToExistingHost( $hostName, $contactGroupName, $exportConfiguration = FALSE ){
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
                if( $exportConfiguration == TRUE )
                    $this->exportConfigurationToNagios($error, $success);
            }
        }
        
        $logs = $this->getLogs($error, $success);
        
        return $logs;
    }
    
	/* LILAC - Add Contact */
    public function addContactToHost( $tempHost, $contactName, &$error, &$success, $exportConfiguration = FALSE ){
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
    
	/* LILAC - Add Contact Group to Host */
    public function addContactGroupToHost( $tempHost, $contactGroupName, &$error, &$success, $exportConfiguration = FALSE ){
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
 
	/* LILAC - Create Service */
    public function createService( $hostName, $services, $host = NULL, $exportConfiguration = FALSE ){
        
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
                if( $exportConfiguration == TRUE )
				    $this->exportConfigurationToNagios($error, $success);
			}
			catch(Exception $e) {
				$error .= $e->getMessage()."\n";
			}
		}
                
        $logs = $this->getLogs($error, $success);
        
        return $logs;
        
	}
    
    /* EONWEB - Create User */
    public function createUser($userName, $userMail, $admin = false, $filterName = "", $filterValue = "", $exportConfiguration = FALSE){
        //Lower case
        $userName = strtolower($userName);
        
        $success = "";
        $error = "";
        $userGroup = 0;
        //Local user
        $userType = 0;
        $userPassword1 = $userName;
        $userPassword2 = $userName;
        $message = false;
        
        //Admin
        if( $admin == true ){
            //admins group
            $userGroup = 1;
            
            $userDescr = "admin user";
        }
        else{
            $userDescr = "limited user";
        }
        
        $createdUserLimitation = !($admin);
        // EONWEB - User creation 
        $user = insert_user($userName, $userDescr, $userGroup, $userPassword1, $userPassword2, $userType, "", $userMail, $createdUserLimitation, $message);

        if($user) {
            $success .= "User $userName created\n";
        } else {
            $error .= "Unable to create user $userName\n";	
        }


        // EONWEB - XML Filter creation
        $xml_file = "/srv/eyesofnetwork/eonweb/cache/".$userName."-ged.xml";
        $dom = openXml();
        $root = $dom->createElement("ged");
        $root = $dom->appendChild($root);
        
        $default = $dom->createElement("default");
        $default = $root->appendChild($default);
        
        //GED filters for non admin users
        if($admin == false){
            $default = $root->getElementsByTagName("default")->item(0);
            $default->appendChild($dom->createTextNode($userName));

            $filters = $dom->createElement("filters");
            $filters = $root->appendChild($filters);
            $filters->setAttribute("name",$userName);
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
        if( $exportConfiguration == TRUE )
            $this->exportConfigurationToNagios($error, $success);


        $logs = $this->getLogs($error, $success);
        
        return $logs;
        
    }
    	
	/* LIVESTATUS - checkHost */
	private function checkHost($type, $address, $port, $path){
		$host = false;
		if($type == "unix"){
			$socket_path_connexion = "unix://".$path;
			$host = fsockopen($socket_path_connexion, $port, $errno, $errstr, 5);
		}
		else{
			$host = fsockopen($address, $port, $errno, $errstr, 5);
		}
		return $host;
	}
		
	/* LIVESTATUS - List backends */
	public function listNagiosBackends() {
	
		return getEonConfig("sockets","array");
	
	}
		
	/* LIVESTATUS - List nagios objects */
	public function listNagiosObjects( $object, $backend = NULL, $columns = FALSE, $filters = FALSE ) {
	
		// loop on each socket
		$sockets = getEonConfig("sockets","array");

		if($backend != NULL) {
			$sockets = array_slice($sockets,$backend,1);
		}

		foreach($sockets as $socket){
			$socket_parts = explode(":", $socket);
			$socket_type = $socket_parts[0];
			$socket_address = $socket_parts[1];
			$socket_port = $socket_parts[2];
			$socket_path = $socket_parts[3];
			$socket_name = $socket;

			if( $this->checkHost($socket_type,$socket_address,$socket_port,$socket_path) ){
				if($socket_port == -1){
					$socket_port = "";
					$socket_address = "";
					$socket_name = "default";
				}
				$options = array(
					'socketType' => $socket_type,
					'socketAddress' => $socket_address,
					'socketPort' => $socket_port,
					'socketPath' => $socket_path
				);

				// construct mklivestatus request, and get the response
				$client = new Client($options);

				// get objects
				$result[$socket_name] = $client->get($object);
				
				// get columns
				if($columns) {
					$result[$socket_name] = $result[$socket_name]->columns($columns);
				}		

				// get filters
				if($filters) {
					foreach($filters as $filter) {
						$result[$socket_name] = $result[$socket_name]->filter($filter);
					}
				}

				// set user
				$result[$socket_name] = $result[$socket_name]->authUser($this->authUser);
				
				// execute
				$result[$socket_name]=$result[$socket_name]->executeAssoc();
			}
		}

		// response for the Ajax call
		return $result;

	}
		
	/* LIVESTATUS - List nagios states */
	public function listNagiosStates( $backend = NULL, $filters = FALSE ) {
	
		// loop on each socket
		$sockets = getEonConfig("sockets","array");

		$result = array();
		$result["hosts"]["pending"] = 0;
		$result["hosts"]["up"] = 0;
		$result["hosts"]["down"] = 0;
		$result["hosts"]["unreachable"] = 0;
		$result["hosts"]["unknown"] = 0;
		$result["services"]["pending"] = 0;
		$result["services"]["ok"] = 0;
		$result["services"]["warning"] = 0;
		$result["services"]["critical"] = 0;
		$result["services"]["unknown"] = 0;

		if($backend != NULL) {
			$sockets = array_slice($sockets,$backend,1);
		}

		foreach($sockets as $socket){
			$socket_parts = explode(":", $socket);
			$socket_type = $socket_parts[0];
			$socket_address = $socket_parts[1];
			$socket_port = $socket_parts[2];
			$socket_path = $socket_parts[3];

			if( $this->checkHost($socket_type,$socket_address,$socket_port,$socket_path) ){
				if($socket_port == -1){
					$socket_port = "";
					$socket_address = "";
				}
				$options = array(
					'socketType' => $socket_type,
					'socketAddress' => $socket_address,
					'socketPort' => $socket_port,
					'socketPath' => $socket_path
				);

				// construct mklivestatus request, and get the response
				$client = new Client($options);

				// get all host PENDING
				$test = $client
					->get('hosts')
					->filter('has_been_checked = 0');			

				// get filters
				if($filters) {
					foreach($filters as $filter) {
						$test = $test->filter($filter);
					}
				}

				// set user
				$test = $test->authUser($this->authUser);
				
				// execute
				$test = $test->execute();
				$result["hosts"]["pending"] += count($test) - 1;

				// construct mklivestatus request, and get the response
				$response = $client
					->get('hosts')
					->stat('state = 0')
					->stat('state = 1')
					->stat('state = 2')
					->stat('state = 3')
					->filter('has_been_checked = 1');

				// get filters
				if($filters) {
					foreach($filters as $filter) {
						$response = $response->filter($filter);
					}
				}

				// set user
				$response = $response->authUser($this->authUser);
				
				// execute
				$response = $response->execute();

				$result["hosts"]["up"] += $response[0][0];
				$result["hosts"]["down"] += $response[0][1];
				$result["hosts"]["unreachable"] += $response[0][2];
				$result["hosts"]["unknown"] += $response[0][3];

				// get all service PENDING
				$test = $client
					->get('services')
					->filter('has_been_checked = 0');

				// get filters
				if($filters) {
					foreach($filters as $filter) {
						$test = $test->filter($filter);
					}
				}

				// set user
				$test = $test->authUser($this->authUser);
				
				// execute
				$test = $test->execute();
				$result["services"]["pending"] += count($test) - 1;

				// construct mklivestatus request, and get the response
				$response = $client
					->get('services')
					->stat('state = 0')
					->stat('state = 1')
					->stat('state = 2')
					->stat('state = 3')
					->filter('has_been_checked = 1');

				// get filters
				if($filters) {
					foreach($filters as $filter) {
						$response = $response->filter($filter);
					}
				}

				// set user
				$response = $response->authUser($this->authUser);
				
				// execute	
				$response = $response->execute();

				$result["services"]["ok"] += $response[0][0];
				$result["services"]["warning"] += $response[0][1];
				$result["services"]["critical"] += $response[0][2];
				$result["services"]["unknown"] += $response[0][3];
			}
		}

		// response for the Ajax call
		return $result;

	}
	
}

?>
