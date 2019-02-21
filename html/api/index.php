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
addRoute('get', '/getDowntimes', 'getDowntimes');
addRoute('get', '/getHostsDown', 'getHostsDown');
addRoute('get', '/getResources', 'getResources');
addRoute('get', '/getServicesDown', 'getServicesDown');


addRoute('post', '/getHost', 'getHost');
addRoute('post', '/getContact', 'getContact');
addRoute('post', '/getCommand', 'getCommand');
addRoute('post', '/getHostGroup', 'getHostGroup');
addRoute('post', '/getServiceGroup', 'getServiceGroup');
addRoute('post', '/getHostTemplate', 'getHostTemplate');
addRoute('post', '/getContactGroups', 'getContactGroups');
addRoute('post', '/getServicesByHost', 'getServicesByHost');
addRoute('post', '/getServiceTemplate', 'getServiceTemplate');
addRoute('post', '/getHostsBytemplate', 'getHostsBytemplate');
addRoute('post', '/getHostsByHostGroup', 'getHostsByHostGroup');
addRoute('post', '/getServicesByHostTemplate', 'getServicesByHostTemplate');

addRoute('post', '/createUser', 'createUser');
addRoute('post', '/createHost', 'createHost');
addRoute('post', '/createCommand', 'createCommand');
addRoute('post', '/createContact', 'createContact');
addRoute('post', '/createHostGroup', 'createHostGroup');
addRoute('post', '/createHostTemplate', 'createHostTemplate');
addRoute('post', '/createHostDowntimes', 'createHostDowntimes');
addRoute('post', '/createMultipleObjects', 'createMultipleObjects');
addRoute('post', '/createServiceTemplate', 'createServiceTemplate');
addRoute('post', '/createServiceDowntimes', 'createServiceDowntimes');

addRoute('post', '/addEventBroker', 'addEventBroker');
addRoute('post', '/addContactToHost', 'addContactToHost');
addRoute('post', '/addServiceToHost', 'addServiceToHost');
addRoute('post', '/addHostGroupToHost', 'addHostGroupToHost');
addRoute('post', '/addServiceToTemplate', 'addServiceToTemplate');
addRoute('post', '/addContactGroupToHost', 'addContactGroupToHost');
addRoute('post', '/addHostTemplateToHost', 'addHostTemplateToHost');
addRoute('post', '/addContactToHostTemplate', 'addContactToHostTemplate');
addRoute('post', '/addCustomArgumentsToHost', 'addCustomArgumentsToHost');
addRoute('post', '/addContactToServiceInHost', 'addContactToServiceInHost');
addRoute('post', '/addHostGroupToHostTemplate', 'addHostGroupToHostTemplate');
addRoute('post', '/addCustomArgumentsToService', 'addCustomArgumentsToService');
addRoute('post', '/addContactToServiceTemplate', 'addContactToServiceTemplate');
addRoute('post', '/addContactGroupToHostTemplate', 'addContactGroupToHostTemplate');
addRoute('post', '/addContactGroupToServiceInHost', 'addContactGroupToServiceInHost');
addRoute('post', '/addContactGroupToServiceTemplate', 'addContactGroupToServiceTemplate');
addRoute('post', '/addCustomArgumentsToHostTemplate', 'addCustomArgumentsToHostTemplate');
addRoute('post', '/addServiceGroupToServiceTemplate', 'addServiceGroupToServiceTemplate');
addRoute('post', '/addServiceTemplateToServiceInHost', 'addServiceTemplateToServiceInHost');
addRoute('post', '/addCustomArgumentsToServiceTemplate', 'addCustomArgumentsToServiceTemplate');
addRoute('post', '/addInheritanceTemplateToHostTemplate', 'addInheritanceTemplateToHostTemplate');
addRoute('post', '/addCheckCommandParameterToHostTemplate', 'addCheckCommandParameterToHostTemplate');
addRoute('post', '/addCheckCommandParameterToServiceInHost', 'addCheckCommandParameterToServiceInHost');
addRoute('post', '/addCheckCommandParameterToServiceTemplate', 'addCheckCommandParameterToServiceTemplate');
addRoute('post', '/addInheritServiceTemplateToServiceTemplate', 'addInheritServiceTemplateToServiceTemplate');

addRoute('post', '/modifyHost', 'modifyHost');
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
addRoute('post', '/deleteContactGroup', 'deleteContactGroup');
addRoute('post', '/deleteHostTemplate', 'deleteHostTemplate');
addRoute('post', '/deleteHostDowntimes', 'deleteHostDowntimes');
addRoute('post', '/deleteContactToHost', 'deleteContactToHost');
addRoute('post', '/deleteServiceTemplate', 'deleteServiceTemplate');
addRoute('post', '/deleteHostGroupToHost', 'deleteHostGroupToHost');
addRoute('post', '/deleteServiceDowntimes', 'deleteServiceDowntimes');
addRoute('post', '/deleteContactGroupToHost', 'deleteContactGroupToHost');
addRoute('post', '/deleteHostTemplateToHosts', 'deleteHostTemplateToHosts');
addRoute('post', '/deleteContactToHostTemplate', 'deleteContactToHostTemplate');
addRoute('post', '/deleteCustomArgumentsToHost', 'deleteCustomArgumentsToHost');
addRoute('post', '/deleteContactToServiceInHost', 'deleteContactToServiceInHost');
addRoute('post', '/deleteHostGroupToHostTemplate', 'deleteHostGroupToHostTemplate');
addRoute('post', '/deleteContactToServiceTemplate', 'deleteContactToServiceTemplate');
addRoute('post', '/deleteCustomArgumentsToService', 'deleteCustomArgumentsToService');
addRoute('post', '/deleteContactGroupToHostTemplate', 'deleteContactGroupToHostTemplate');
addRoute('post', '/deleteContactGroupToServiceInHost', 'deleteContactGroupToServiceInHost');
addRoute('post', '/deleteContactGroupToServiceTemplate', 'deleteContactGroupToServiceTemplate');
addRoute('post', '/deleteServiceGroupToServiceTemplate', 'deleteServiceGroupToServiceTemplate');
addRoute('post', '/deleteCustomArgumentsToHostTemplate', 'deleteCustomArgumentsToHostTemplate');
addRoute('post', '/deleteServiceTemplateToServiceInHost', 'deleteServiceTemplateToServiceInHost');
addRoute('post', '/deleteCustomArgumentsToServiceTemplate', 'deleteCustomArgumentsToServiceTemplate');
addRoute('post', '/deleteInheritanceTemplateToHostTemplate', 'deleteInheritanceTemplateToHostTemplate');
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