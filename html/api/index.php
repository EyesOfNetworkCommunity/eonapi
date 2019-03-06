<?php
/*
#
# EONAPI 
# Route calls
#
# Copyright (c) 2017 AXIANS C&S
# Author: Adrien van den Haak <adrien.vandenhaak@axians.com>
#
*/

require "/srv/eyesofnetwork/eonapi/include/Slim/Slim.php";
require "/srv/eyesofnetwork/eonapi/include/api_functions.php";
require "/srv/eyesofnetwork/eonapi/include/ObjectManager.php";

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

/* API routes are defined here (http method / association route / function) */
//GET
$app->get('/getApiKey', 'getApiKey');
$app->get('/getAuthenticationStatus', 'getAuthenticationStatus');

//POST (parameters in body)
addRoute('get', '/getDowntimes', 'getDowntimes', 'operator');
addRoute('get', '/getHostsDown', 'getHostsDown', 'operator');
addRoute('get', '/getResources', 'getResources', 'operator');
addRoute('get', '/getServicesDown', 'getServicesDown', 'operator');


addRoute('post', '/getHost', 'getHost', 'operator');
addRoute('post', '/getContact', 'getContact', 'operator');
addRoute('post', '/getCommand', 'getCommand', 'operator');
addRoute('post', '/getHostGroup', 'getHostGroup', 'operator');
addRoute('post', '/getServiceGroup', 'getServiceGroup', 'operator');
addRoute('post', '/getHostTemplate', 'getHostTemplate', 'operator');
addRoute('post', '/getContactGroups', 'getContactGroups', 'operator');
addRoute('post', '/getServicesByHost', 'getServicesByHost', 'operator');
addRoute('post', '/getServiceTemplate', 'getServiceTemplate', 'operator');
addRoute('post', '/getHostsBytemplate', 'getHostsBytemplate', 'operator');
addRoute('post', '/getHostsByHostGroup', 'getHostsByHostGroup', 'operator');
addRoute('post', '/getServicesByHostTemplate', 'getServicesByHostTemplate', 'operator');

addRoute('post', '/createUser', 'createUser');
addRoute('post', '/createHost', 'createHost');
addRoute('post', '/createCommand', 'createCommand');
addRoute('post', '/createContact', 'createContact');
addRoute('post', '/createHostGroup', 'createHostGroup');
addRoute('post', '/createHostTemplate', 'createHostTemplate');
addRoute('post', '/createHostDowntime', 'createHostDowntime');
addRoute('post', '/createMultipleObjects', 'createMultipleObjects');
addRoute('post', '/createServiceTemplate', 'createServiceTemplate');
addRoute('post', '/createServiceDowntime', 'createServiceDowntime');

addRoute('post', '/addEventBroker', 'addEventBroker');
addRoute('post', '/addContactToHost', 'addContactToHost');
addRoute('post', '/addServiceToHost', 'addServiceToHost');
addRoute('post', '/addHostGroupToHost', 'addHostGroupToHost');
addRoute('post', '/addServiceToTemplate', 'addServiceToTemplate');
addRoute('post', '/addContactGroupToHost', 'addContactGroupToHost');
addRoute('post', '/addHostTemplateToHost', 'addHostTemplateToHost');
addRoute('post', '/addContactGroupToContact', 'addContactGroupToContact');
addRoute('post', '/addContactToHostTemplate', 'addContactToHostTemplate');
addRoute('post', '/addCustomArgumentsToHost', 'addCustomArgumentsToHost');
addRoute('post', '/addContactToServiceInHost', 'addContactToServiceInHost');
addRoute('post', '/addHostGroupToHostTemplate', 'addHostGroupToHostTemplate');
addRoute('post', '/addCustomArgumentsToService', 'addCustomArgumentsToService');
addRoute('post', '/addContactToServiceTemplate', 'addContactToServiceTemplate');
addRoute('post', '/addContactGroupToHostTemplate', 'addContactGroupToHostTemplate');
addRoute('post', '/addContactGroupToServiceInHost', 'addContactGroupToServiceInHost');
addRoute('post', '/addServiceGroupToServiceInHost', 'addServiceGroupToServiceInHost');
addRoute('post', '/addContactGroupToServiceTemplate', 'addContactGroupToServiceTemplate');
addRoute('post', '/addCustomArgumentsToHostTemplate', 'addCustomArgumentsToHostTemplate');
addRoute('post', '/addServiceGroupToServiceTemplate', 'addServiceGroupToServiceTemplate');
addRoute('post', '/addServiceTemplateToServiceInHost', 'addServiceTemplateToServiceInHost');
addRoute('post', '/addCustomArgumentsToServiceTemplate', 'addCustomArgumentsToServiceTemplate');
addRoute('post', '/addInheritanceTemplateToHostTemplate', 'addInheritanceTemplateToHostTemplate');
addRoute('post', '/addContactNotificationCommandToContact', 'addContactNotificationCommandToContact');
addRoute('post', '/addCheckCommandParameterToHostTemplate', 'addCheckCommandParameterToHostTemplate');
addRoute('post', '/addCheckCommandParameterToServiceInHost', 'addCheckCommandParameterToServiceInHost');
addRoute('post', '/addCheckCommandParameterToServiceTemplate', 'addCheckCommandParameterToServiceTemplate');
addRoute('post', '/addInheritServiceTemplateToServiceTemplate', 'addInheritServiceTemplateToServiceTemplate');

addRoute('post', '/modifyHost', 'modifyHost');
addRoute('post', '/modifyContact', 'modifyContact');
addRoute('post', '/modifyCommand', 'modifyCommand');
addRoute('post', '/modifyServicefromHost', 'modifyServicefromHost');
addRoute('post', '/modifyNagiosResources', 'modifyNagiosResources');
addRoute('post', '/modifyCheckCommandToHostTemplate', 'modifyCheckCommandToHostTemplate');
addRoute('post', '/modifyCheckCommandToServiceTemplate', 'modifyCheckCommandToServiceTemplate');

addRoute('post', '/deleteHost', 'deleteHost');
addRoute('post', '/deleteContact', 'deleteContact');
addRoute('post', '/deleteService', 'deleteService');
addRoute('post', '/deleteCommand', 'deleteCommand');
addRoute('post', '/delEventBroker', 'delEventBroker');
addRoute('post', '/deleteHostGroup', 'deleteHostGroup');
addRoute('post', '/deleteContactGroup', 'deleteContactGroup');
addRoute('post', '/deleteHostTemplate', 'deleteHostTemplate');
addRoute('post', '/deleteHostDowntime', 'deleteHostDowntime');
addRoute('post', '/deleteContactToHost', 'deleteContactToHost');
addRoute('post', '/deleteServiceTemplate', 'deleteServiceTemplate');
addRoute('post', '/deleteHostGroupToHost', 'deleteHostGroupToHost');
addRoute('post', '/deleteServiceDowntime', 'deleteServiceDowntime');
addRoute('post', '/deleteContactGroupToHost', 'deleteContactGroupToHost');
addRoute('post', '/deleteHostTemplateToHosts', 'deleteHostTemplateToHosts');
addRoute('post', '/deleteContactGroupToContact', 'deleteContactGroupToContact');
addRoute('post', '/deleteContactToHostTemplate', 'deleteContactToHostTemplate');
addRoute('post', '/deleteCustomArgumentsToHost', 'deleteCustomArgumentsToHost');
addRoute('post', '/deleteContactToServiceInHost', 'deleteContactToServiceInHost');
addRoute('post', '/deleteHostGroupToHostTemplate', 'deleteHostGroupToHostTemplate');
addRoute('post', '/deleteContactToServiceTemplate', 'deleteContactToServiceTemplate');
addRoute('post', '/deleteCustomArgumentsToService', 'deleteCustomArgumentsToService');
addRoute('post', '/deleteContactGroupToHostTemplate', 'deleteContactGroupToHostTemplate');
addRoute('post', '/deleteContactGroupToServiceInHost', 'deleteContactGroupToServiceInHost');
addRoute('post', '/deleteServiceGroupToServiceInHost', 'deleteServiceGroupToServiceInHost');
addRoute('post', '/deleteContactGroupToServiceTemplate', 'deleteContactGroupToServiceTemplate');
addRoute('post', '/deleteServiceGroupToServiceTemplate', 'deleteServiceGroupToServiceTemplate');
addRoute('post', '/deleteCustomArgumentsToHostTemplate', 'deleteCustomArgumentsToHostTemplate');
addRoute('post', '/deleteServiceTemplateToServiceInHost', 'deleteServiceTemplateToServiceInHost');
addRoute('post', '/deleteCustomArgumentsToServiceTemplate', 'deleteCustomArgumentsToServiceTemplate');
addRoute('post', '/deleteInheritanceTemplateToHostTemplate', 'deleteInheritanceTemplateToHostTemplate');
addRoute('post', '/deleteContactNotificationCommandToContact', 'deleteContactNotificationCommandToContact');
addRoute('post', '/deleteCheckCommandParameterToHostTemplate', 'deleteCheckCommandParameterToHostTemplate');
addRoute('post', '/deleteCheckCommandParameterToServiceInHost', 'deleteCheckCommandParameterToServiceInHost');
addRoute('post', '/deleteCheckCommandParameterToServiceTemplate', 'deleteCheckCommandParameterToServiceTemplate');
addRoute('post', '/deleteInheritServiceTemplateToServiceTemplate', 'deleteInheritServiceTemplateToServiceTemplate');



addRoute('post', '/duplicateService', 'duplicateService');
addRoute('post', '/exportConfiguration', 'exportConfiguration');
addRoute('post', '/listNagiosStates', 'listNagiosStates', 'readonly');
addRoute('post', '/listNagiosObjects', 'listNagiosObjects', 'readonly');
addRoute('post', '/listNagiosBackends', 'listNagiosBackends', 'readonly');

 
/* Kind of framework to add routes very easily */
function addRoute($httpMethod, $routeName, $methodName, $right="admin"){
	
    global $app;
    
    $app->$httpMethod($routeName, function() use ($methodName,$right){
		
        $request = \Slim\Slim::getInstance()->request();
        $response = \Slim\Slim::getInstance()->response();
        $body = json_decode($request->getBody());
        $logs = "";

        $className = 'ObjectManager';

        /*Parameters body (POST)*/
        $params = getParametersNameFunction( $className, $methodName );
        $paramsValue = array();
        $i = 0;
        foreach( $params[0] as $param ){
			$var[] = $param;
			if(!isset($body->$param)) {
				$body->$param = $params[1][$param];
			}
			${$var[$i]} = $body->$param;
			$paramsValue[] = $body->$param;		
			$i++;
        }

        /*Test parameters*/
        $paramsCompulsoryName = getParametersNameCompulsoryFunction( $className, $methodName );
        $paramsCompulsory = array();
        foreach( $paramsCompulsoryName as $p ){
            $paramsCompulsory[] = ${$p};
        }

        if( has_empty( $paramsCompulsory ) == true ){
            $array = array("message" => "invalid parameters");
            $result = getJsonResponse($response, "417", $array);
            echo $result;

            return;
        }

        $authenticationValid = verifyAuthenticationByApiKey( $request, $right );
        if( $authenticationValid == true ){
            $co = new $className;
            $logs = call_user_func_array(array($co, $methodName), $paramsValue);
        }

        constructResponse( $response, $logs, $authenticationValid );
    });
}

$app->run();

?>