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
addRoute('post', '/getCommand', 'getCommand');
addRoute('post', '/getServicesByHostTemplate', 'getServicesByHostTemplate');
addRoute('post', '/getServicesByHost', 'getServicesByHost');
addRoute('post', '/getContact', 'getContact');
addRoute('post', '/getContactGroups', 'getContactGroups');
addRoute('get', '/getAllHosts', 'getAllHosts');
addRoute('post', '/listNagiosBackends', 'listNagiosBackends', 'readonly');
addRoute('post', '/listNagiosObjects', 'listNagiosObjects', 'readonly');
addRoute('post', '/listNagiosStates', 'listNagiosStates', 'readonly');
addRoute('post', '/createServiceTemplate', 'createServiceTemplate');
addRoute('post', '/createHost', 'createHost');
addRoute('post', '/createHostTemplate', 'createHostTemplate');
addRoute('post', '/createService', 'createService');
addRoute('post', '/createUser','createUser');
addRoute('post', '/addHostTemplateToHost', 'addHostTemplateToHost');
addRoute('post', '/addContactToHostTemplate', 'addContactToHostTemplate');
addRoute('post', '/addContactGroupToHostTemplate', 'addContactGroupToHostTemplate');
addRoute('post', '/addContactToHost', 'addContactToExistingHost');
addRoute('post', '/addContactGroupToHost', 'addContactGroupToExistingHost');
addRoute('post', '/addEventBroker','addEventBroker');
addRoute('post', '/addCommand', 'addCommand');
addRoute('post', '/modifyCommand', 'modifyCommand');
addRoute('post', '/modifyService', 'modifyService');
addRoute('post', '/modifyNagiosRessources', 'modifyNagiosRessources');
addRoute('post', '/deleteContact', 'deleteContact');
addRoute('post', '/deleteContactGroup', 'deleteContactGroup');
addRoute('post', '/deleteService', 'deleteService');
addRoute('post', '/deleteHost', 'deleteHost');
addRoute('post', '/deleteServiceTemplate', 'deleteServiceTemplate');
addRoute('post', '/delEventBroker','delEventBroker');
addRoute('post', '/deleteCommand', 'deleteCommand');
addRoute('post', '/deleteHostTemplate', 'deleteHostTemplate');
addRoute('post', '/deleteHostTemplateToHosts', 'deleteHostTemplateToHosts');
addRoute('post', '/deleteContactToHost', 'deleteContactToHost');
addRoute('post', '/deleteContactGroupToHost', 'deleteContactGroupToHost');
addRoute('post', '/exportConfiguration', 'exportConfiguration');
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
