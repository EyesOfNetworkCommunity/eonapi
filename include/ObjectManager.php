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

        return rtrim($logs," | ");
    }

	/* LILAC - addEventBroker */
	function addEventBroker( $broker, $exportConfiguration = FALSE ){
		
		global $lilac;
		$error = "";
		$success = "";

		try {
			# Check if exist
			$module_list = NagiosBrokerModulePeer::doSelect(new Criteria());
			foreach($module_list as $module) {
				if($module->getLine()==$broker)
					$brokerExists = true;
			}
			
			# Add broker
			if(!isset($brokerExists)) {
				$module = new NagiosBrokerModule();
				$module->setLine($broker);
				$module->save();
				$success .= "EventBroker added\n";
			} else {
				$success .= "EventBroker already exists\n";
			}
		}
		catch(Exception $e) {
			$error .= $e->getMessage()."\n";
		}

		$logs = $this->getLogs($error, $success);
		return $logs;

	}

	/* LILAC - delEventBroker */
	function delEventBroker( $broker, $exportConfiguration = FALSE ){
		
		global $lilac;
		$error = "";
		$success = "";

		try {
			// Check if exist
			$module_list = NagiosBrokerModulePeer::doSelect(new Criteria());
			foreach($module_list as $module) {
				if($module->getLine()==$broker)
					$brokerExists = $module;
			}
			
			// Add broker
			if(isset($brokerExists)) {
				$brokerExists->delete();
				$success .= "EventBroker deleted\n";
			} else {
				$success .= "EventBroker not exists\n";
			}
		}
		catch(Exception $e) {
			$error .= $e->getMessage()."\n";
		}

		$logs = $this->getLogs($error, $success);
		return $logs;

	}
	
	/* LILAC - Exporter */
    function exportConfigurationToNagios(&$error = "", &$success = "", $jobName = "nagios"){
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
            
            $success .= $jobName." : Nagios configuration exported\n";
        }
        else{
            $error .= "ERROR during nagios configuration export\n";
        }
            
    }

	/* LILAC - Export Nagios Configuration */
	public function exportConfiguration($jobName = "nagios"){
        $error = "";
        $success = "";
		
		$this->exportConfigurationToNagios($error, $success, $jobName);
		
		$logs = $this->getLogs($error, $success);
        
        return $logs;
	}

	/* LILAC - List Hosts */
	public function listHosts( $hostName = false, $hostTemplate = false ){
		
		return true;
		
	}

	/* LILAC - Get Host */
	public function getHost( $hostName){
        $nhp = new NagiosHostPeer;
		// Find host
		$host = $nhp->getByName($hostName);
		if($host) {
			return $host->toArray();
		}else{
			return "Host named ".$hostName." doesn't exist."; 
		}
	}

	/* LILAC - Get Hosts by template name */
	public function getHostsBytemplate( $templateHostName){
        $nhtp = new NagiosHostTemplatePeer;
		// Find host template
		$template_host = $nhtp->getByName($templateHostName);
		if(!$template_host) {
			return "Host Template $templateHostName not found\n";
		}else{
			$hostList=$template_host->getAffectedHosts();
			if(!$hostList){
				return "No Host found for the template : $templateHostName \n";
			}else{
				$result = array();
				foreach($hostList as $host){
					array_push($result,$host->toArray());
				}
				return $result;
			}
		}
	}

	/* LILAC - Get Host by  HostGroup */
	public function getHostsByHostGroup( $hostGroupName){
		$nhgp = new NagiosHostgroupPeer;
		//Find HostGroup
		$hostGroup = $nhgp->getByName( $hostGroupName );
		if(!$hostGroup) {
			return "HostGroup named $hostGroupName not found\n";
		}else{
			$hostList=$hostGroup->getMembers();
			if(!$hostList){
				return "No Host found for the HostGroup: $hostGroupName \n";
			}else{
				$result = array();
				foreach($hostList as $host){
					array_push($result,$host->toArray());
				}
				return $result;
			}
		}
	}

	/* LILAC - Get Contact */	
	public function getContact($contactName=FALSE){
		if(!$contactName){
			$c = new Criteria();
			$c->addAscendingOrderByColumn(NagiosContactPeer::NAME);
			$result=array();
			$contact_list = NagiosContactPeer::doSelect($c);
			foreach($contact_list as $contact){
				array_push($result,$contact->toArray());
			}
			return $result;
		}else {
			$ncp = new NagiosContactPeer;
			// Find host contact
			$contact = $ncp->getByName( $contactName );
			if(!$contact) {
				return "Contact $contactName doesn't exist\n";
			}else{
				return $contact->toArray();
			}
		}
		
	}

	/* LILAC - Get ContactGroup */
	public function getContactGroups($contactGroupName=FALSE){
		if(!$contactGroupName){
			$c = new Criteria();
			$c->addAscendingOrderByColumn(NagiosContactGroupPeer::NAME);
			$result=array();
			$contactGroup_list = NagiosContactGroupPeer::doSelect($c);
			foreach($contactGroup_list as $contactGroup){
				array_push($result,$contactGroup->toArray());
			}
			return $result;
		}else {
			$ncgp = new NagiosContactGroupPeer;
			// Find host contact
			$contactGroup = $ncgp->getByName( $contactGroupName );
			if(!$contactGroup) {
				return "ContactGroup named $contactGroupName doesn't exist\n";
			}else{
				return $contactGroup->toArray();
			}
		}
	}

	/* LILAC - get command */
	function getCommand($commandName){
		$ncp = new NagiosCommandPeer;
		$targetCommand = $ncp->getByName($commandName);
        if(!$targetCommand) {
            return  "The command '".$commandName."' does not exist\n";
        }else{
			return $targetCommand->toArray();
		}
	}

	/* LILAC - Get Service by Host */
	public function getServicesByHost($hostName){
		$nhp = new NagiosHostPeer();
		$host = $nhp->getByName($hostName);
		if(!$host){
			return "No host named $hostName.\n";
		}else{
			$c = new Criteria();
			$c->add(NagiosServicePeer::HOST, $host->getId());
			$c->addAscendingOrderByColumn(NagiosServicePeer::ID);
			$services=NagiosServicePeer::doSelect($c);
			$result= array();
			foreach($services as $service) {
				array_push($result,$service->toArray());
			} 
			return $result;
		}
	}

	/* LILAC - Get Service by HostTemplate */	
	public function getServicesByHostTemplate($templateHostName){	
		$nhtp = new NagiosHostTemplatePeer();
		$templateHost = $nhtp->getByName($templateHostName);
		if(!$templateHost) {
			return "No hostTemplate named $templateHostName.\n";
		}else {
			$c = new Criteria();
			$c->add(NagiosServicePeer::HOST_TEMPLATE, $templateHost->getId());
			$c->addAscendingOrderByColumn(NagiosServicePeer::DESCRIPTION );
			$services=NagiosServicePeer::doSelect($c);
			$result= array();
			foreach($services as $service) {
				array_push($result,$service->toArray());
			} 
			return $result;
		}
	}

	/* LILAC - Create Host Template */
    function createHostTemplate( $templateHostName,$templateHostDescription="", $exportConfiguration = FALSE ){
        global $lilac;
        $error = "";
		$success = "";
		$code =0;
        
        
        // Check for pre-existing host template with same name        
        $nhtp = new NagiosHostTemplatePeer;
		$template_host = $nhtp->getByName($templateHostName);
		if($template_host) {
			$code=1;
			$error .= "A host template with that name already exists!\n";
		}
           
        if( $templateHostName == NULL || $templateHostName == "" ){
			$code=1;
            $error .= "A host template name must be defined\n";   
        }
           
           
        if( empty($error) ) {			
            /*---Create template---*/
            $template = new NagiosHostTemplate();
            $template->setName( $templateHostName );
            $template->setDescription( $templateHostDescription );
            $template->save();
            
            $success .= "Host template ".$templateHostName." created\n";
                        
            /*---Add host template inheritance ("GENERIC_HOST")---*/
            $targetTemplate = $nhtp->getByName("GENERIC_HOST");
            if(!$targetTemplate) {
				$code=1;
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
					$code=1;
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
        
        return array("code"=>$code,"description"=>$logs);
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

	/* LILAC - Create Service */
    public function createService( $hostName, $services, $host = NULL, $checkCommand=NULL, $exportConfiguration = FALSE ){
        
        $error = "";
		$success = "";
		$code=0;
        	    
        $nsp = new NagiosHostPeer;
        
        if( $host == NULL ){
            $host = $nsp->getByName($hostName);
            
            if(!$host) {
				$code=1;
                $error .= "Host $hostName doesn't exist\n";
            }
        }
        
        $nstp = new NagiosServiceTemplatePeer;
		
        //Test if the parent templates exist
        foreach($services as $key => $service) {
            $templateName = $service[0];
            $template = $nstp->getByName($templateName);
            if(!$template) {
				$code=1;
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
					if($checkCommand != NULL){
						$cmd = NagiosCommandPeer::getByName($checkCommand);
						if($tempService->setCheckCommand($cmd->getId())){
							$success .= "The command '".$checkCommand."' add to service ".$templateName."\n";
						}else{
							$code=1;
							$error .= "The command '".$checkCommand."' doesn't exist.\n";
						}
					}
					
		
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
				$code=1;
				$error .= $e->getMessage()."\n";
			}
		}
                
        $logs = $this->getLogs($error, $success);
        
        return array("code"=>$code,"description"=>$logs);
        
	}

	/* LILAC - Create Host and Services */
	public function createHost( $templateHostName, $hostName, $hostIp, $hostAlias = "", $contactName = NULL, $contactGroupName = NULL, $exportConfiguration = FALSE ){
        $error = "";
        $success = "";
		$code=0;
		
        $nhp = new NagiosHostPeer;
		// Find host
		$host = $nhp->getByName($hostName);
		if($host) {
			$code=1;
			$error .= "Host $hostName already exists\n";
		}

        $nhtp = new NagiosHostTemplatePeer;
		// Find host template
		$template_host = $nhtp->getByName($templateHostName);
		if(!$template_host) {
			$code=1;
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
				$code=1;
				$error .= $e->getMessage()."\n";
			}
		}
        
        
        $logs = $this->getLogs($error, $success);
        
        return array("code"=>$code,"description"=>$logs);        
	}
    
	/* LILAC - create service template */
	public function createServiceTemplate($templateName, $templateDescription="", $servicesGroup=array(), $contacts=array(), $contactsGroup=array(), $checkCommand, $checkCommandParameters=array(), $templatesToInherit=array(), $exportConfiguration = FALSE){
		$error = "";
		$success = "";
		$code=0;
		
		try{
			$t=NagiosServiceTemplatePeer::getByName($templateName);
			if(!$t){
				$nst=new NagiosServiceTemplate;
				$nst->setName($templateName);
				$nst->setDescription($templateDescription);
				
				if($templatesToInherit=array()){
					if($nst->addTemplateInheritance("GENERIC_SERVICE")){
						$success .= "The Template 'GENERIC_SERVICE' add to service Template ".$templateName."\n";
					}else {
						$code=1;
						$error .= "The template 'GENERIC_SERVICE' doesn't exist.\n";
					}
				}else{
					foreach($templatesToInherit as $template){
						if($nst->addTemplateInheritance($template)){
							$success .= "The Template '".$template."' add to service Template ".$templateName."\n";
						}else {
							$code=1;
							$error .= "The template '".$template."' doesn't exist.\n";
						}
					}
				}
	
				foreach($servicesGroup as $serviceGroup){
					if($nst->addServicegroupByName($serviceGroup)){
						$success .= "The Service group '".$serviceGroup."' add to service Template ".$templateName."\n";
					}else {
						$code=1;
						$error .= "The Service group '".$serviceGroup."' doesn't exist.\n";
					}
				}
				
				foreach($contactsGroup as $contactGroup){
					if($nst->addServicegroupByName($contactGroup)){
						$success .= "The Contact group '".$contactGroup."' add to service Template ".$templateName."\n";
					}else {
						$code=1;
						$error .= "The Contact group '".$contactGroup."' doesn't exist.\n";
					}
				}
				
				foreach($contacts as $contact){
					if($nst->addServicegroupByName($contact)){
						$success .= "The Contact '".$contact."' add to service Template ".$templateName."\n";
					}else {
						$code=1;
						$error .= "The Contact '".$contact."' doesn't exist.\n";
					}
				}
				
	
				if($nst->setCheckCommandByName($checkCommand)){
					$success .= "The command '".$checkCommand."' add to service Template ".$templateName."\n";
				}else{
					$code=1;
					$error .= "The command '".$checkCommand."' doesn't exist.\n";
				}
	
				foreach($checkCommandParameters as $arg){
					if($nst->addCheckCommandParameter($arg)){
						$success .= "The parameter'".$arg."' add to service Template ".$templateName."\n";
					}else {
						$code=1;
						$error .= "The Contact '".$arg."' doesn't exist.\n";
					}
				}
	
				// Export
				if( $exportConfiguration == TRUE )
					$this->exportConfigurationToNagios($error, $success);
	
			}else{
				$code=1;
				$error .= "The Service template '".$templateName."' already exist.\n";
			}
		}catch(Exception $e) {
			$code=1;
			$error .= $e->getMessage()."\n";
		}
		

		$logs = $this->getLogs($error, $success);
        
		return array("code"=>$code,"description"=>$logs);
	}

	/* LILAC - add Command */
    function addCommand($commandName,$commandLine,$commandDescription=""){
		$error = "";
		$success = "";
		$code=0;
		try{
			$ncp = new NagiosCommandPeer;
			$targetCommand = $ncp->getByName($commandName);
			
			//If command doesn't exist we create it
			if(!$targetCommand) {
				$command = new NagiosCommand;
				$command->setName($commandName);
				$command->setLine($commandLine);
				$command->setDescription($commandDescription);
				$result=$command->save();
				if(!$result){
					$code=1;
					$error .= "The command '".$command->getName()."' can't be created\n";
				}
				else{
					$success .= "The command '".$command->getName()."' has been created.\n";
				}
			}
			//if command already exist we modify it
			else{
				$code=1;
				$error .= "The command '".$targetCommand->getName()."' already exist, if you want to modify it see the function 'modifyCommand'.\n";
			}
		}catch(Exception $e) {
			$code=1;
			$error .= $e->getMessage()."\n";
		}
        
		$logs = $this->getLogs($error, $success);
		
		$result=array("code"=>$code,"description"=>$logs);
        return $result;
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
           
    /* LILAC - Add Template to Host */
    public function addHostTemplateToHost( $templateHostName, $hostName, $exportConfiguration = FALSE ){
        $error = "";
		$success = "";
		$code = 0;
        
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
				$code=1;
                $error .= $e->getMessage();
            }		
        }else $code=1;
        
        $logs = $this->getLogs($error, $success);
        
		return array("code"=>$code,"description"=>$logs);
	}
    
    /* LILAC - Add Contact to Host Template */
	public function addContactToHostTemplate( $contactName, $templateHostName, $exportConfiguration = FALSE ){
        $error = "";
		$success = "";
		$code=0;

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
				$code=1;
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
        }else $code=1;
        
        $logs = $this->getLogs($error, $success);
        
        return array("code"=>$code,"description"=>$logs);
    }
    
	/* LILAC - Add Contact Group to Host Template */
    public function addContactGroupToHostTemplate( $contactGroupName, $templateHostName, $exportConfiguration = FALSE ){
        $error = "";
		$success = "";
		$code=0;

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
				$code=1;
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
        }else $code=1;
             
        $logs = $this->getLogs($error, $success);
        
        return array("code"=>$code,"description"=>$logs);
    }
    
    /* LILAC - Add Contact to Host */
	public function addContactToExistingHost( $hostName, $contactName, $exportConfiguration = FALSE ){
        $error = "";
		$success = "";
		$code=0;
        
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
        }else $code=1;
        
        $logs = $this->getLogs($error, $success);
        
        return array("code"=>$code,"description"=>$logs);
    }
    
	/* LILAC - Add Contact Group to Host */
    public function addContactGroupToExistingHost( $hostName, $contactGroupName, $exportConfiguration = FALSE ){
        $error = "";
		$success = "";
		$code =0;
        
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
        }else $code=1;
        
        $logs = $this->getLogs($error, $success);
        
        return array("code"=>$code,"description"=>$logs);
	}

	/* LILAC - Modify Service */
	public function modifyService($serviceName,$hostName, $columns=array(), $exportConfiguration = FALSE ){
		$error = "";
        $success = "";
        $code=0;
        $nsp = new NagiosHostPeer;
		
		$host = $nsp->getByName($hostName);    
		if(!$host) {
			$code++;
			$error .= "Host $hostName doesn't exist\n";
		}else{
			$c = new Criteria();
			$c->add(NagiosServicePeer::DESCRIPTION, $serviceName);
			$services=$host->getNagiosServices($c);
			if(count($services)==1){
				foreach($columns as $column => $value){
					
					if($column == "CheckCommand"){
						$command=NagiosCommandPeer::getByName($value);
						if(!$command) $error .= "The command  : $value doesn't exist.\n";
						else $services[0]->setCheckCommand($command->getId());
						
					}else{
						if($column!="Host" && $column != "HostTemplate"){
							$services[0]->setByName($column,$value);
							$success.="$column updated.\n";
						}
					} 
					

				}
				$success .= "$serviceName in host $hostName has been updated.";
				$services[0]->save();

			}else{
				$error .= "Service didn't exist or to much services had been returned";
				$code++;
			}  
		}
		
		$logs = $this->getLogs($error, $success);
        
        return array("code"=>$code,"description"=>$logs);
	}

	/* LILAC - Modify Command */
    public function modifyCommand($commandName, $newCommandName=NULL, $commandLine, $commandDescription=NULL){
        /*---Modify check command ==> 'dummy_ok'---*/
		//TODO ==> Change command to 'dummy_ok' for template GENERIC_HOST (inheritance)
		$error = "";
		$success = "";
		$code=0;
		$changes=0;
		$ncp = new NagiosCommandPeer;
		$targetCommand = $ncp->getByName($commandName);
        if(!$targetCommand) {
			$code=1;
            $error .= $error .= "The command '".$commandName."' does not exist\n";
        }
        else{
			if(isset($newCommandName) && $newCommandName!=$commandName){
				$targetCommand->setName($newCommandName);
				$changes++;
			} 
			if(isset($commandDescription) && $commandDescription!=$targetCommand->getDescription()){
				$targetCommand->setDescription($commandDescription);
				$changes++;
			} 
			if($commandLine != $targetCommand->getLine()){
				$targetCommand->setLine($commandLine);
				$changes++;
			}
			
			$state=$targetCommand->save();   

			//if no changes state = false 
			if(!$state){
				$code=1;
				$error .= "The command '".$targetCommand->getName()."' failed to update\n";
			}else{
				$success .= "The command '".$targetCommand->getName()."' success to update.\n";
			} 
		}
		
		$logs = $this->getLogs($error, $success);
        
        $result=array("code"=>$code,"description"=>$logs,"changes"=>$changes);
        return $result;
	}

	/* LILAC - modify nagiosResources */
	public function modifyNagiosRessources($ressources){
		$error = "";
		$success = "";
		$code=0;
		try{
			$resourceCfg = NagiosResourcePeer::doSelectOne(new Criteria());
			if(!$resourceCfg) {
				$resourceCfg = new NagiosResource();
				$resourceCfg->save();
			}
			
			foreach($ressources as $key => $value){
				$resourceCfg->setByName($key,$value);
			}
			$row=$resourceCfg->save();
			
			if($row == 0 ) $code++;
			else $success .= "Ressources updated.";

		}catch (Exception $e){
			$code++;
			$error .= "An exception occured : $e";
		}

		$logs = $this->getLogs($error, $success);

		return array("code"=>$code,"description"=>$logs);
	}

	/* LILAC - Delete contact Group */
	public function deleteContactGroup($contactGroupName){
		$error = "";
		$success = "";
		$code = 0;
		try {
			$ncp = new NagiosContactGroupPeer;
			// Find host contact
			$contactGroup = $ncp->getByName( $contactGroupName );
			if(!$contactGroup) {
				$code=1;
				$error .= "Contact Group $contactGroupName doesn't exist\n";
			}else{
				$contactGroup->delete();
				$success .="$contactGroupName as been deleted\n";
			}
		}catch(Exception $e) {
			$code=1;
			$error .= $e->getMessage()."\n";
		}

		$logs = $this->getLogs($error, $success);
        
        return array("code"=>$code,"description"=>$logs);
	}

	/* LILAC - Delete contact */
	public function deleteContact($contactName){
		$error = "";
		$success = "";
		$code = 0;
		try {
			$ncp = new NagiosContactPeer;
			// Find host contact
			$contact = $ncp->getByName( $contactName );
			if(!$contact) {
				$code=1;
				$error .= "Contact $contactName doesn't exist\n";
			}else{
				$contact->delete();
				$success .="$contactName as been deleted\n";
			}
		}catch(Exception $e) {
			$code=1;
			$error .= $e->getMessage()."\n";
		}

		$logs = $this->getLogs($error, $success);
        
        return array("code"=>$code,"description"=>$logs);
	}

	/* LILAC - delete service template */
    public function deleteServiceTemplate($templateName){
		$error = "";
		$success = "";
		$code=0;
		
		try{
			$targetTemplate = NagiosServiceTemplatePeer::getByName($templateName);

			if(!$targetTemplate) {
				$code=1;
				$error .= "The command '".$templateName."'does not exist\n";
			}
			else{
				$targetTemplate->delete();
				$success .= "The command '".$templateName."' deleted.\n";
			}
		}catch(Exception $e) {
			$code=1;
			$error .= $e->getMessage()."\n";
		}
		$logs = $this->getLogs($error, $success);
        
        return $logs;
	}

	/* LILAC - Delete Host */
	public function deleteHost( $hostName, $exportConfiguration = FALSE ){
		$error = "";
		$success = "";
		$code=0;

		try {
			$nhp = new NagiosHostPeer;
			$host = $nhp->getByName($hostName);

			if($host) {
				$host->delete();
				$success .= "Host ".$hostName." deleted\n";
			} else {
				$code=1;
				$error .= "Host ".$hostName." not found\n";
			}

			// Export
			if( $exportConfiguration == TRUE )
				$this->exportConfigurationToNagios();
		}
		catch(Exception $e) {
			$code=1;
			$error .= $e->getMessage()."\n";
		}

		$logs = $this->getLogs($error, $success);
        return array("code"=>$code,"description"=>$logs);
	}
	
	/* LILAC - delete Command */
    function deleteCommand($commandName){
		$error = "";
		$success = "";
		$code=0;
		
		try{
			$ncp = new NagiosCommandPeer;

			$targetCommand = $ncp->getByName($commandName);
			if(!$targetCommand) {
				$code=1;
				$error .= "The command '".$commandName."'does not exist\n";
			}
			else{
				$targetCommand->delete();
				$success .= "The command '".$commandName."' deleted.\n";
			}
		}catch(Exception $e) {
			$code=1;
			$error .= $e->getMessage()."\n";
		}
		$logs = $this->getLogs($error, $success);
        
        return array("code"=>$code,"description"=>$logs);
	}
	
	/* LILAC - Add Host template to Service */

	/* LILAC - Delete Host Template to Service */

	/* LILAC - Delete Service */
	public function deleteService($serviceName,$hostName, $exportConfiguration = FALSE ){
		$error = "";
        $success = "";
        $code=0;
        $nsp = new NagiosHostPeer;
		
		$host = $nsp->getByName($hostName);    
		if(!$host) {
			$code++;
			$error .= "Host $hostName doesn't exist\n";
		}else{
			$c = new Criteria();
			$c->add(NagiosServicePeer::DESCRIPTION, $serviceName);
			$services=$host->getNagiosServices($c);
			if(count($services)==1){
				$services[0]->delete();
				$success .= "$serviceName in host $hostName had been deleted";
			}else{
				$error .= "Service didn't exist or to much services had been returned";
				$code++;
			}  
		}
		
		$logs = $this->getLogs($error, $success);
        
        return array("code"=>$code,"description"=>$logs);
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
	
		$backends = getEonConfig("sockets","array");
		for($i=0;$i<count($backends);$i++) {
			$backends_json[$i]["id"]=$i;
			$backends_json[$i]["backend"]=$backends[$i];
		}
		return $backends_json;
		
	}
		
	/* LIVESTATUS - List nagios objects */
	public function listNagiosObjects( $object, $backendid = NULL, $columns = FALSE, $filters = FALSE ) {
	
		// loop on each socket
		$sockets = getEonConfig("sockets","array");

		if($backendid != NULL) {
			$sockets = array_slice($sockets,$backendid,1);
		}

		foreach($sockets as $socket){
			$socket_parts = explode(":", $socket);
			$socket_type = $socket_parts[0];
			$socket_address = $socket_parts[1];
			$socket_port = $socket_parts[2];
			$socket_path = $socket_parts[3];
			$socket_name = $socket;

			// check if socket disabled
			if(isset($socket_parts[4]) && $backendid == NULL) {
				continue;
			}

			// check if socket is up
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
				$result[$socket_name] = $result[$socket_name]->executeAssoc();
			}
		}

		// response for the Ajax call
		return $result;

	}
		
	/* LIVESTATUS - List nagios states */
	public function listNagiosStates( $backendid = NULL, $filters = FALSE ) {
	
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

		if($backendid != NULL) {
			$sockets = array_slice($sockets,$backendid,1);
		}

		foreach($sockets as $socket){
			$socket_parts = explode(":", $socket);
			$socket_type = $socket_parts[0];
			$socket_address = $socket_parts[1];
			$socket_port = $socket_parts[2];
			$socket_path = $socket_parts[3];

			// check if socket disabled
			if(isset($socket_parts[4]) && $backendid == NULL) {
				continue;
			}

			// check if socket is up
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
