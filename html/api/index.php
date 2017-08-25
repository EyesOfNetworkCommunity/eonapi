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

require "/srv/eyesofnetwork/eonapi/include/Api.php";
require "/srv/eyesofnetwork/eonapi/include/CreateObjects.php";


\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

/* API routes are defined here (association route / function) */
//GET
$app->get('/getApiKeyAuthorization','getApiKeyAuthorization');
$app->get('/getApiKey','getApiKey');
$app->get('/getAuthenticationStatus','getAuthenticationStatus');

//POST (parameters in body)
$app->post('/createHost','createHostRest');
$app->post('/createService','createServiceRest');
$app->post('/createUser','createUserRest');
$app->post('/addContactToHost','addContactToHostRest');
$app->post('/addContactGroupToHost','addContactGroupToHostRest');


function createHostRest(){
    $request = \Slim\Slim::getInstance()->request();
    $response = \Slim\Slim::getInstance()->response();
    $body = json_decode($request->getBody());
    
    //Parameters in request
    $paramUsername = $request->get('username');
    $paramApiKey = $request->get('apiKey');
    //Do not set $serverApiKey to NULL (bypass risk)
    $serverApiKey = "r@dom@cceSSM€mori€s269";
    
    //Parameters body
    $templateHostName = $body->templateHostName;
    $hostName = $body->hostName;
    $hostIp = $body->hostIp;
    $hostAlias = $body->hostAlias;
    $contactName = $body->contactName;
    $contactGroupName = $body->contactGroupName;
    
    /*Test parameters*/
    if( !(!empty($templateHostName) && !empty($hostName) && !empty($hostIp) && !empty($hostAlias)) ){    
        $array = array("message" => "invalid parameters");
        $result = getJsonResponse($response, "417", $array);
        echo $result;
        
        return;
    }
    
    $usersql = getUserByUsername( $paramUsername );
    $user_limitation = mysqli_result($usersql, 0, "user_limitation");
    $user_type = mysqli_result($usersql, 0, "user_type");
    
    //IF LOCAL USER AND ADMIN USER (No limitation)
    if( ($user_type != "1") && $user_limitation == "0"){
        //ID of the authenticated user
        $user_id = mysqli_result($usersql, 0, "user_id");
        $serverApiKey = apiKey( $user_id );    
    }
    
    
    //Only if API keys match
    if($paramApiKey == $serverApiKey){
        $co = new CreateObjects;
        $logs = $co->createHost($templateHostName, $hostName, $hostIp, $hostAlias, $contactName, $contactGroupName);
        
        try {
            $array = array("logs" => $logs);
            $result = getJsonResponse($response, "200", $array);
            echo $result;
        }
        catch(PDOException $e) {
            //error_log($e->getMessage(), 3, '/var/tmp/php.log');
            $array = array("error" => $e->getMessage());
            $result = getJsonResponse($response, "400", $array);
            echo $result;
        }
    }
    else{
        $array = array("status" => "unauthorized");
        $result = getJsonResponse($response, "401", $array);
        echo $result;
    }
    
}

function createServiceRest(){
    $request = \Slim\Slim::getInstance()->request();
    $response = \Slim\Slim::getInstance()->response();
    $body = json_decode($request->getBody());
    
    //Parameters in request
    $paramUsername = $request->get('username');
    $paramApiKey = $request->get('apiKey');
    //Do not set $serverApiKey to NULL (bypass risk)
    $serverApiKey = "r@dom@cceSSM€mori€s269";
    
    //Parameters body
    $hostName = $body->hostName;
    $services = $body->services;
 
    /* Test parameters compulsory */
    if( !(!empty($hostName) && !empty($services)) ){
        $array = array("message" => "invalid parameters");
        $result = getJsonResponse($response, "417", $array);
        echo $result; 
        
        return;
    }
    
    $usersql = getUserByUsername( $paramUsername );
    $user_limitation = mysqli_result($usersql, 0, "user_limitation");
    $user_type = mysqli_result($usersql, 0, "user_type");
    
    //IF LOCAL USER AND ADMIN USER (No limitation)
    if( ($user_type != "1") && $user_limitation == "0"){
        //ID of the authenticated user
        $user_id = mysqli_result($usersql, 0, "user_id");
        $serverApiKey = apiKey( $user_id );    
    }
    
    //Only if API keys match
    if($paramApiKey == $serverApiKey){
        $co = new CreateObjects;
        $logs = $co->createService($hostName, $services, $host = NULL);
        
        try {
            $array = array("logs" => $logs);
            $result = getJsonResponse($response, "200", $array);
            echo $result;
        }
        catch(PDOException $e) {
            //error_log($e->getMessage(), 3, '/var/tmp/php.log');
            $array = array("error" => $e->getMessage());
            $result = getJsonResponse($response, "400", $array);
            echo $result;
        }
    }
    else{
        $array = array("status" => "unauthorized");
        $result = getJsonResponse($response, "401", $array);
        echo $result;
    }
      
}

function createUserRest(){
    $request = \Slim\Slim::getInstance()->request();
    $response = \Slim\Slim::getInstance()->response();
    $body = json_decode($request->getBody());
    
    //Parameters in request
    $paramUsername = $request->get('username');
    $paramApiKey = $request->get('apiKey');
    //Do not set $serverApiKey to NULL (bypass risk)
    $serverApiKey = "r@dom@cceSSM€mori€s269";
    
    //Parameters body
    $user_name = $body->userName;
    $user_mail = $body->userMail;
    $admin = $body->admin;
    $filterName = $body->filterName;
    $filterValue = $body->filterValue;
    
    /* Test parameters compulsory */
    if( !(!empty($user_name) && !empty($user_mail)) ){
        $array = array("message" => "invalid parameters");
        $result = getJsonResponse($response, "417", $array);
        echo $result; 
        
        return;
    }
    
    
    $usersql = getUserByUsername( $paramUsername );
    $user_limitation = mysqli_result($usersql, 0, "user_limitation");
    $user_type = mysqli_result($usersql, 0, "user_type");
    
    //IF LOCAL USER AND ADMIN USER (No limitation)
    if( ($user_type != "1") && $user_limitation == "0"){
        //ID of the authenticated user
        $user_id = mysqli_result($usersql, 0, "user_id");
        $serverApiKey = apiKey( $user_id );    
    }
    
    
    //Only if API keys match
    if($paramApiKey == $serverApiKey){
        $co = new CreateObjects;
        $logs = $co->createUser($user_name, $user_mail, $admin, $filterName, $filterValue);
        
        try {
            $array = array("logs" => $logs);
            $result = getJsonResponse($response, "200", $array);
            echo $result;
        }
        catch(PDOException $e) {
            //error_log($e->getMessage(), 3, '/var/tmp/php.log');
            $array = array("error" => $e->getMessage());
            $result = getJsonResponse($response, "400", $array);
            echo $result;
        }
    }
    else{
        $array = array("status" => "unauthorized");
        $result = getJsonResponse($response, "401", $array);
        echo $result;
    }
    
    
}


function addContactToHostRest(){
    $request = \Slim\Slim::getInstance()->request();
    $response = \Slim\Slim::getInstance()->response();
    $body = json_decode($request->getBody());
    
    //Parameters in request
    $paramUsername = $request->get('username');
    $paramApiKey = $request->get('apiKey');
    //Do not set $serverApiKey to NULL (bypass risk)
    $serverApiKey = "r@dom@cceSSM€mori€s269";
    
    //Parameters body
    $contactName = $body->contactName;
    $hostName = $body->hostName;
    
    /* Test parameters compulsory */
    if( !(!empty($contactName) && !empty($hostName)) ){
        $array = array("message" => "invalid parameters");
        $result = getJsonResponse($response, "417", $array);
        echo $result; 
        
        return;
    }
    
    
    $usersql = getUserByUsername( $paramUsername );
    $user_limitation = mysqli_result($usersql, 0, "user_limitation");
    $user_type = mysqli_result($usersql, 0, "user_type");
    
    //IF LOCAL USER AND ADMIN USER (No limitation)
    if( ($user_type != "1") && $user_limitation == "0"){
        //ID of the authenticated user
        $user_id = mysqli_result($usersql, 0, "user_id");
        $serverApiKey = apiKey( $user_id );    
    }
    
    
    //Only if API keys match
    if($paramApiKey == $serverApiKey){
        $co = new CreateObjects;
        $logs = $co->addContactToExistingHost( $hostName, $contactName );
        
        try {
            $array = array("logs" => $logs);
            $result = getJsonResponse($response, "200", $array);
            echo $result;
        }
        catch(PDOException $e) {
            //error_log($e->getMessage(), 3, '/var/tmp/php.log');
            $array = array("error" => $e->getMessage());
            $result = getJsonResponse($response, "400", $array);
            echo $result;
        }
    }
    else{
        $array = array("status" => "unauthorized");
        $result = getJsonResponse($response, "401", $array);
        echo $result;
    }
    
    
}


function addContactGroupToHostRest(){
    $request = \Slim\Slim::getInstance()->request();
    $response = \Slim\Slim::getInstance()->response();
    $body = json_decode($request->getBody());
    
    //Parameters in request
    $paramUsername = $request->get('username');
    $paramApiKey = $request->get('apiKey');
    //Do not set $serverApiKey to NULL (bypass risk)
    $serverApiKey = "r@dom@cceSSM€mori€s269";
    
    //Parameters body
    $contactGroupName = $body->contactGroupName;
    $hostName = $body->hostName;
    
    /* Test parameters compulsory */
    if( !(!empty($contactGroupName) && !empty($hostName)) ){
        $array = array("message" => "invalid parameters");
        $result = getJsonResponse($response, "417", $array);
        echo $result; 
        
        return;
    }
    
    
    $usersql = getUserByUsername( $paramUsername );
    $user_limitation = mysqli_result($usersql, 0, "user_limitation");
    $user_type = mysqli_result($usersql, 0, "user_type");
    
    //IF LOCAL USER AND ADMIN USER (No limitation)
    if( ($user_type != "1") && $user_limitation == "0"){
        //ID of the authenticated user
        $user_id = mysqli_result($usersql, 0, "user_id");
        $serverApiKey = apiKey( $user_id );    
    }
    
    
    //Only if API keys match
    if($paramApiKey == $serverApiKey){
        $co = new CreateObjects;
        $logs = $co->addContactGroupToExistingHost( $hostName, $contactGroupName );
        
        try {
            $array = array("logs" => $logs);
            $result = getJsonResponse($response, "200", $array);
            echo $result;
        }
        catch(PDOException $e) {
            //error_log($e->getMessage(), 3, '/var/tmp/php.log');
            $array = array("error" => $e->getMessage());
            $result = getJsonResponse($response, "400", $array);
            echo $result;
        }
    }
    else{
        $array = array("status" => "unauthorized");
        $result = getJsonResponse($response, "401", $array);
        echo $result;
    }
    
    
}



function getAuthenticationStatus(){
    $request = \Slim\Slim::getInstance()->request();
    $response = \Slim\Slim::getInstance()->response();
    
    $paramUsername = $request->get('username');
    $paramApiKey = $request->get('apiKey');
    //Do not set $serverApiKey to NULL (bypass risk)
    $serverApiKey = "r@dom@cceSSM€mori€s269";
    
    $usersql = getUserByUsername( $paramUsername );
    $user_limitation = mysqli_result($usersql, 0, "user_limitation");
    $user_type = mysqli_result($usersql, 0, "user_type");
    
    //IF LOCAL USER AND ADMIN USER (No limitation)
    if( ($user_type != "1") && $user_limitation == "0"){
        //ID of the authenticated user
        $user_id = mysqli_result($usersql, 0, "user_id");
        $serverApiKey = apiKey( $user_id );    
    }
    
    
    if( $paramApiKey == $serverApiKey ){
        $array = array("status" => "authorized");
        $result = getJsonResponse($response, "200", $array);
        echo $result;
    }
    else{
        $array = array("status" => "unauthorized");
        $result = getJsonResponse($response, "401", $array);
        echo $result;
    }
}

function getApiKeyAuthorization(){
    global $database_eonweb;
    $authorization = false;
    
    $request = \Slim\Slim::getInstance()->request();
    $response = \Slim\Slim::getInstance()->response();
    $paramUsername = $request->get('username');
    $paramPassword = $request->get('password');
    
    $usersql = getUserByUsername( $paramUsername );
    $user_limitation = mysqli_result($usersql, 0, "user_limitation");
    $user_type = mysqli_result($usersql, 0, "user_type");
    
    //IF LOCAL USER AND ADMIN USER (No limitation)
    if( $user_type != "1" && $user_limitation == "0" ){
        $userpasswd = mysqli_result($usersql, 0, "user_passwd");
        $password = md5($paramPassword);
        
        if($userpasswd == $password)
            $authorization = true;
    }
    
    $array = array("authorization" => $authorization);
    $result = getJsonResponse($response, "200", $array);
    echo $result;
}

function getApiKey(){
    $request = \Slim\Slim::getInstance()->request();
    $response = \Slim\Slim::getInstance()->response();
    $paramUsername = $request->get('username');
    $paramPassword = $request->get('password');
    
    $usersql = getUserByUsername( $paramUsername );
    $user_limitation = mysqli_result($usersql, 0, "user_limitation");
    $user_type = mysqli_result($usersql, 0, "user_type");
    
    $authorization = false;
    //IF LOCAL USER AND ADMIN USER (No limitation)
    if( ($user_type != "1") && $user_limitation == "0"){
        $userpasswd = mysqli_result($usersql, 0, "user_passwd");
        $password = md5($paramPassword);
        
        //IF match the hashed password
        if($userpasswd == $password)
            $authorization = true;
    }
        
    if($authorization == true){
        //ID of the authenticated user
        $user_id = mysqli_result($usersql, 0, "user_id");
        $serverApiKey = apiKey( $user_id );
        
        $array = array("EONAPI_KEY" => $serverApiKey);
        $result = getJsonResponse($response, "200", $array);
        echo $result;
    }
    else{
        $array = array("message" => "The username-password credentials of your authentication can not be accepted or the user doesn't have admin privileges");
        $result = getJsonResponse($response, "401", $array);
        echo $result;
    }  
}





$app->run();

?>