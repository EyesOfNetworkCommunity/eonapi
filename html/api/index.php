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
$app->get('/getApiKey','getApiKey');
$app->get('/getAuthenticationStatus','getAuthenticationStatus');

//POST (parameters in body)
addRoute('post', '/getHost', 'getHost');
addRoute('post', '/getHostsBytemplate', 'getHostsBytemplate');
addRoute('post', '/getHostTemplate', 'getHostTemplate');
addRoute('post', '/getHostsByHostGroup', 'getHostsByHostGroup');
addRoute('post', '/getContact', 'getContact');
addRoute('post', '/getContactGroups', 'getContactGroups');
addRoute('post', '/getCommand', 'getCommand');
addRoute('post', '/getServiceTemplate', 'getServiceTemplate');
addRoute('post', '/getServiceGroup', 'getServiceGroup');
addRoute('post', '/getServicesByHost', 'getServicesByHost');
addRoute('post', '/getServicesByHostTemplate', 'getServicesByHostTemplate');
addRoute('get', '/getDowntimes', 'getDowntimes');
addRoute('get', '/getHostsDown', 'getHostsDown');
addRoute('get', '/getServicesDown', 'getServicesDown');
// addRoute('get', '/getAllHosts', 'getAllHosts');
// addRoute('get', '/getService', 'getService');

addRoute('post', '/createHost', 'createHost');
addRoute('post', '/createHostTemplate', 'createHostTemplate');
addRoute('post', '/createHostGroup', 'createHostGroup');
// addRoute('post', '/createContact', 'createContact');
// addRoute('post', '/createContactGroups', 'createContactGroups');
addRoute('post', '/createServiceTemplate', 'createServiceTemplate');
addRoute('post', '/createCommand', 'createCommand');
addRoute('post', '/createUser','createUser');
addRoute('post', '/createMultipleObjects','createMultipleObjects');


addRoute('post', '/addHostGroupToHostTemplate','addHostGroupToHostTemplate');
addRoute('post', '/addContactToHost', 'addContactToHost');
addRoute('post', '/addContactGroupToHost', 'addContactGroupToHost');
addRoute('post', '/addHostTemplateToHost', 'addHostTemplateToHost');
addRoute('post', '/addContactToHostTemplate', 'addContactToHostTemplate');
addRoute('post', '/addContactGroupToHostTemplate', 'addContactGroupToHostTemplate');
addRoute('post', '/addServiceToTemplate', 'addServiceToTemplate');
addRoute('post', '/addServicesToHost', 'addServicesToHost');
addRoute('post', '/addEventBroker','addEventBroker');

// addRoute('post', '/modifyContact', 'modifyContact');
// addRoute('post', '/modifyContactGroup', 'modifyContactGroup');
// addRoute('post', '/modifyServiceTemplate', 'modifyServiceTemplate');
// addRoute('post', '/modifyHostTemplate', 'modifyHostTemplate');
addRoute('post', '/modifyCommand', 'modifyCommand');
addRoute('post', '/modifyServicefromHost', 'modifyServicefromHost');
addRoute('post', '/modifyNagiosRessources', 'modifyNagiosRessources');
addRoute('post', '/modifyHost', 'modifyHost');
// addRoute('post', '/modifyUser','modifyUser');
// addRoute('post', '/modifyContactGroup','modifyContactGroup');


addRoute('post', '/deleteContact', 'deleteContact');
addRoute('post', '/deleteContactGroup', 'deleteContactGroup');
addRoute('post', '/deleteService', 'deleteService');
addRoute('post', '/deleteServiceTemplate', 'deleteServiceTemplate');
addRoute('post', '/deleteCommand', 'deleteCommand');
addRoute('post', '/deleteHost', 'deleteHost');
addRoute('post', '/deleteHostTemplate', 'deleteHostTemplate');
addRoute('post', '/deleteHostTemplateToHosts', 'deleteHostTemplateToHosts');
addRoute('post', '/deleteContactToHost', 'deleteContactToHost');
addRoute('post', '/deleteContactGroupToHost', 'deleteContactGroupToHost');
addRoute('post', '/delEventBroker','delEventBroker');
addRoute('post', '/deleteHostGroupToHostTemplate','deleteHostGroupToHostTemplate');

// addRoute('post', '/deleteUser','deleteUser');

addRoute('post', '/duplicateService', 'duplicateService');
// addRoute('post', '/duplicateHost', 'duplicateHost');
// addRoute('post', '/duplicateTemplate', 'duplicateTemplate');

addRoute('post', '/exportConfiguration', 'exportConfiguration');
addRoute('post', '/listNagiosBackends', 'listNagiosBackends', 'readonly');
addRoute('post', '/listNagiosObjects', 'listNagiosObjects', 'readonly');
addRoute('post', '/listNagiosStates', 'listNagiosStates', 'readonly');
addRoute('post', '/test', 'test');
 
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