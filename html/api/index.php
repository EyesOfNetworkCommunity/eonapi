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

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require '/srv/eyesofnetwork/eonapi/include/vendor/autoload.php';
require "/srv/eyesofnetwork/eonapi/include/api_functions.php";
require "/srv/eyesofnetwork/eonapi/include/ObjectManager.php";

$app = AppFactory::create();
$app->setBasePath('/eonapi');

/* API routes are defined here (http method / association route / function) */
//GET
$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Welcome on the EON api");
    return $response;
});
$app->get('/getApiKey', 'getApiKey');
$app->get('/getAuthenticationStatus', 'getAuthenticationStatus');
//POST (parameters in body)
addRoute('get', '/getDowntimes', 'getDowntimes', 'operator');
addRoute('get', '/getHostsDown', 'getHostsDown', 'operator');
addRoute('get', '/getResources', 'getResources', 'operator');
addRoute('get', '/getServicesDown', 'getServicesDown', 'operator');
addRoute('get', '/getHostChecks', 'getHostChecks', 'operator');
addRoute('get', '/getServiceChecks', 'getServiceChecks', 'operator');

addRoute('get', '/getServiceComments', 'getServiceComments', 'operator');
addRoute('get', '/getHostAcknowledges', 'getHostAcknowledges', 'operator');
addRoute('get', '/getHostComments', 'getHostComments', 'operator');

addRoute('get', '/getHostEventHandler', 'getHostEventHandler', 'operator');
addRoute('get', '/getServiceAcknowledges', 'getServiceAcknowledges', 'operator');
addRoute('get', '/getServiceEventHandler', 'getServiceEventHandler', 'operator');
addRoute('get', '/getServiceNotifications', 'getServiceNotifications', 'operator');
addRoute('get', '/getHostNotifications', 'getHostNotifications', 'operator');
addRoute('get', '/getServicesStatus', 'getServicesStatus', 'operator');

addRoute('post', '/enableHostCheck', 'enableHostCheck');
addRoute('post', '/disableHostCheck', 'disableHostCheck');
addRoute('post', '/disableServiceCheck', 'disableServiceCheck');
addRoute('post', '/enableServiceCheck', 'enableServiceCheck');
addRoute('post', '/submitServicePassiveCheckResult', 'submitPassiveCheckResult');
addRoute('post', '/submitHostPassiveCheckResult', 'submitHostPassiveCheckResult');

addRoute('post', '/disableServiceNotification', 'disableServiceNotification');
addRoute('post', '/disableServiceEventHandler', 'disableServiceEventHandler');
addRoute('post', '/enableHostNotification', 'enableHostNotification');
addRoute('post', '/disableHostNotification', 'disableHostNotification');
addRoute('post', '/disableHostEventHandler', 'disableHostEventHandler');
addRoute('post', '/enableHostEventHandler', 'enableHostEventHandler');
addRoute('post', '/enableServiceEventHandler', 'enableServiceEventHandler');
addRoute('post', '/enableServiceNotification', 'enableServiceNotification');
addRoute('post', '/scheduleHostForcedCheck', 'scheduleHostForcedCheck');
addRoute('post', '/scheduleServiceForcedCheck', 'scheduleServiceForcedCheck');

addRoute('post', '/createHostAcknowledge', 'createHostAcknowledge');
addRoute('post', '/createServiceAcknowledge', 'createServiceAcknowledge');
addRoute('post', '/deleteHostAcknowledge', 'deleteHostAcknowledge');
addRoute('post', '/deleteServiceAcknowledge', 'deleteServiceAcknowledge');

addRoute('post', '/createServiceComment', 'createServiceComment');
addRoute('post', '/createHostComment', 'createHostComment');
addRoute('post', '/deleteHostComment', 'deleteHostComment');
addRoute('post', '/deleteAllHostComments', 'deleteAllHostComments');

addRoute('get','/healthCheck', 'healthCheck', 'operator');

addRoute('post', '/getDetailsEvent', 'getDetailsEvent', 'operator');
addRoute('post', '/modifyEvent', 'modifyEvent');
addRoute('post', '/deleteEvent', 'deleteEvent');
addRoute('post', '/acknowledgeEvent', 'acknowledgeEvent');
addRoute('post', '/ownDisownEvent', 'ownDisownEvent');

addRoute('post', '/getPIDProcess', 'getPIDProcess');
addRoute('get', '/getNameProcess', 'getNameProcess');
addRoute('post', '/actionProcess', 'actionProcess');


addRoute('post', '/getHost', 'getHost', 'operator');
addRoute('post', '/getContact', 'getContact', 'operator');
addRoute('post', '/getCommand', 'getCommand', 'operator');
addRoute('post', '/getEonUser', 'getEonUser', 'operator');
addRoute('post', '/getEonGroup', 'getEonGroup', 'operator');
addRoute('post', '/getHostGroup', 'getHostGroup', 'operator');

addRoute('post', '/getNotifierRule', 'getNotifierRule', 'operator');
addRoute('post', '/getServiceGroup', 'getServiceGroup', 'operator');
addRoute('post', '/getHostTemplate', 'getHostTemplate', 'operator');
addRoute('post', '/getContactGroups', 'getContactGroups', 'operator');
addRoute('post', '/getNotifierMethod', 'getNotifierMethod', 'operator');
addRoute('post', '/getServicesByHost', 'getServicesByHost', 'operator');
addRoute('post', '/getServiceTemplate', 'getServiceTemplate', 'operator');
addRoute('post', '/getHostsBytemplate', 'getHostsBytemplate', 'operator');
addRoute('post', '/getHostsByHostGroup', 'getHostsByHostGroup', 'operator');
addRoute('post', '/getNotifierTimeperiod', 'getNotifierTimeperiod', 'operator');
addRoute('post', '/getServicesByHostTemplate', 'getServicesByHostTemplate', 'operator');

addRoute('post', '/createUser', 'createUser');
addRoute('post', '/createHost', 'createHost');
addRoute('post', '/createCommand', 'createCommand');
addRoute('post', '/createContact', 'createContact');
addRoute('post', '/createHostGroup', 'createHostGroup');
addRoute('post', '/createContactGroup', 'createContactGroup');
addRoute('post', '/createServiceGroup', 'createServiceGroup');
addRoute('post', '/createHostTemplate', 'createHostTemplate');
addRoute('post', '/createHostDowntime', 'createHostDowntime');
addRoute('post', '/createServiceToHost', 'createServiceToHost');
addRoute('post', '/createMultipleObjects', 'createMultipleObjects');
addRoute('post', '/createServiceTemplate', 'createServiceTemplate');
addRoute('post', '/createServiceDowntime', 'createServiceDowntime');
addRoute('post', '/createServiceToHostTemplate', 'createServiceToHostTemplate');

addRoute('post', '/addEventBroker', 'addEventBroker');
addRoute('post', '/addParentToHost', 'addParentToHost');
addRoute('post', '/addContactToHost', 'addContactToHost');
addRoute('post', '/addHostGroupToHost', 'addHostGroupToHost');
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
addRoute('post', '/addContactToServiceInHostTemplate', 'addContactToServiceInHostTemplate');
addRoute('post', '/addServiceTemplateToServiceInHost', 'addServiceTemplateToServiceInHost');
addRoute('post', '/addCustomArgumentsToServiceTemplate', 'addCustomArgumentsToServiceTemplate');
addRoute('post', '/addInheritanceTemplateToHostTemplate', 'addInheritanceTemplateToHostTemplate');
addRoute('post', '/addContactNotificationCommandToContact', 'addContactNotificationCommandToContact');
addRoute('post', '/addCheckCommandParameterToHostTemplate', 'addCheckCommandParameterToHostTemplate');
addRoute('post', '/addContactGroupToServiceInHostTemplate', 'addContactGroupToServiceInHostTemplate');
addRoute('post', '/addServiceGroupToServiceInHostTemplate', 'addServiceGroupToServiceInHostTemplate');
addRoute('post', '/addCheckCommandParameterToServiceInHost', 'addCheckCommandParameterToServiceInHost');
addRoute('post', '/addServiceTemplateToServiceInHostTemplate', 'addServiceTemplateToServiceInHostTemplate');
addRoute('post', '/addCheckCommandParameterToServiceTemplate', 'addCheckCommandParameterToServiceTemplate');
addRoute('post', '/addInheritServiceTemplateToServiceTemplate', 'addInheritServiceTemplateToServiceTemplate');
addRoute('post', '/addCheckCommandParameterToServiceInHostTemplate', 'addCheckCommandParameterToServiceInHostTemplate');

addRoute('post', '/modifyHost', 'modifyHost');
addRoute('post', '/modifyContact', 'modifyContact');
addRoute('post', '/modifyCommand', 'modifyCommand');
addRoute('post', '/modifyHostTemplate', 'modifyHostTemplate');
addRoute('post', '/modifyContactGroup', 'modifyContactGroup');
addRoute('post', '/modifyServicefromHost', 'modifyServicefromHost');
addRoute('post', '/modifyNagiosResources', 'modifyNagiosResources');
addRoute('post', '/modifyNagiosMainConfiguration', 'modifyNagiosMainConfiguration');
addRoute('post', '/modifyServicefromHostTemplate', 'modifyServicefromHostTemplate');
addRoute('post', '/modifyCheckCommandToHostTemplate', 'modifyCheckCommandToHostTemplate');
addRoute('post', '/modifyCheckCommandToServiceTemplate', 'modifyCheckCommandToServiceTemplate');

addRoute('post', '/deleteHost', 'deleteHost');
addRoute('post', '/deleteContact', 'deleteContact');
addRoute('post', '/deleteService', 'deleteService');
addRoute('post', '/deleteCommand', 'deleteCommand');
addRoute('post', '/delEventBroker', 'delEventBroker');
addRoute('post', '/deleteHostGroup', 'deleteHostGroup');
addRoute('post', '/deleteContactGroup', 'deleteContactGroup');
addRoute('post', '/deleteServiceGroup', 'deleteServiceGroup');
addRoute('post', '/deleteHostTemplate', 'deleteHostTemplate');
addRoute('post', '/deleteHostDowntime', 'deleteHostDowntime');
addRoute('post', '/deleteParentToHost', 'deleteParentToHost');
addRoute('post', '/deleteContactToHost', 'deleteContactToHost');
addRoute('post', '/deleteServiceComment', 'deleteServiceComment');
addRoute('post', '/deleteAllServiceComments', 'deleteAllServiceComments');
addRoute('post', '/deleteServiceTemplate', 'deleteServiceTemplate');
addRoute('post', '/deleteHostGroupToHost', 'deleteHostGroupToHost');
addRoute('post', '/deleteServiceDowntime', 'deleteServiceDowntime');
addRoute('post', '/deleteContactGroupToHost', 'deleteContactGroupToHost');
addRoute('post', '/deleteHostTemplateToHosts', 'deleteHostTemplateToHosts');
addRoute('post', '/deleteServiceByHostTemplate', 'deleteServiceByHostTemplate');
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
addRoute('post', '/deleteContactToServiceInHostTemplate', 'deleteContactToServiceInHostTemplate');
addRoute('post', '/deleteCustomArgumentsToServiceTemplate', 'deleteCustomArgumentsToServiceTemplate');
addRoute('post', '/deleteInheritanceTemplateToHostTemplate', 'deleteInheritanceTemplateToHostTemplate');
addRoute('post', '/deleteContactGroupToServiceInHostTemplate', 'deleteContactGroupToServiceInHostTemplate');
addRoute('post', '/deleteServiceGroupToServiceInHostTemplate', 'deleteServiceGroupToServiceInHostTemplate');
addRoute('post', '/deleteContactNotificationCommandToContact', 'deleteContactNotificationCommandToContact');
addRoute('post', '/deleteCheckCommandParameterToHostTemplate', 'deleteCheckCommandParameterToHostTemplate');
addRoute('post', '/deleteCheckCommandParameterToServiceInHost', 'deleteCheckCommandParameterToServiceInHost');
addRoute('post', '/deleteCheckCommandParameterToServiceTemplate', 'deleteCheckCommandParameterToServiceTemplate');
addRoute('post', '/deleteServiceTemplateToServiceInHostTemplate', 'deleteServiceTemplateToServiceInHostTemplate');
addRoute('post', '/deleteInheritServiceTemplateToServiceTemplate', 'deleteInheritServiceTemplateToServiceTemplate');
addRoute('post', '/deleteCheckCommandParameterToServiceInHostTemplate', 'deleteCheckCommandParameterToServiceInHostTemplate');

addRoute('post', '/duplicateService', 'duplicateService');
addRoute('post', '/exportConfiguration', 'exportConfiguration');
addRoute('post', '/listNagiosStates', 'listNagiosStates', 'readonly');
addRoute('post', '/listNagiosObjects', 'listNagiosObjects', 'readonly');
addRoute('post', '/listNagiosBackends', 'listNagiosBackends', 'readonly');

//==============================================================================
//eonweb
addRoute('post', '/createEonGroup', 'createEonGroup');
addRoute('post', '/createEonUser', 'createEonUser');

addRoute('post', '/modifyEonGroup', 'modifyEonGroup'); 
addRoute('post', '/modifyEonUser', 'modifyEonUser');

addRoute('post', '/deleteEonGroup', 'deleteEonGroup');
addRoute('post', '/deleteEonUser', 'deleteEonUser'); 


//notifier
addRoute('post', '/addNotifierRule', 'addNotifierRule');
addRoute('post', '/addNotifierMethod', 'addNotifierMethod');
addRoute('post', '/addNotifierTimeperiod', 'addNotifierTimeperiod');

addRoute('post', '/deleteNotifierRule', 'deleteNotifierRule');
addRoute('post', '/deleteNotifierMethod', 'deleteNotifierMethod');
addRoute('post', '/deleteNotifierTimeperiod', 'deleteNotifierTimeperiod');

addRoute('post', '/modifyNotifierRule', 'modifyNotifierRule');
addRoute('post', '/modifyNotifierMethod', 'modifyNotifierMethod');
addRoute('post', '/modifyNotifierTimeperiod', 'modifyNotifierTimeperiod');

addRoute('post','/exporterNotifierConfig', 'exporterNotifierConfig');
//==============================================================================

/* Kind of framework to add routes very easily */
function addRoute($httpMethod, $routeName, $methodName, $right="admin"){
	
    global $app;

    $app->$httpMethod($routeName, function(Request $request, Response $response, $args) use ($methodName,$right){
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

            return $response;
        }

        $authenticationValid = verifyAuthenticationByApiKey( $request, $right );
        if( $authenticationValid == true ){
            $co = new ObjectManager($request, $response);
            $logs = call_user_func_array(array($co, $methodName), $paramsValue);
        }

        constructResponse( $response, $logs, $authenticationValid );
        return $response;
    });
}

$app->run();

?>
