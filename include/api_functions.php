<?php
/*
#
# EONAPI
#
# Copyright (c) 2017 AXIANS C&S
# Author: Adrien van den Haak <adrien.vandenhaak@axians.com>
#
*/

// => Modify this key with your own secret at initialization
define("EONAPI_KEY", "€On@piK3Y");


/* API key encryption */
function apiKey( $user_id )
{
    $key = md5(EONAPI_KEY.$user_id);

    return hash('sha256', $key.$_SERVER['SERVER_ADDR']);
}


/*---General functions--*/
function getParametersNameFunction( $className, $functionName ){
    $reflector = new ReflectionMethod($className, $functionName);
    $params = array();
    $params[0]=NULL;   
    $params[1]=NULL;   
 
    foreach ($reflector->getParameters() as $param) {
        $params[0][] = $param->name;
        if( $param->isDefaultValueAvailable() ){
		$params[1][$param->name] = $param->getDefaultValue(); 
	}
    }
    
    return $params;
}

function getParametersNameCompulsoryFunction( $className, $functionName ){
    $reflector = new ReflectionMethod($className, $functionName);
    $params = array();
    
    foreach ($reflector->getParameters() as $param) {
        if( $param->isDefaultValueAvailable() == false ){
            $params[] = $param->name;
        }
    }
    
    return $params;
}

function has_empty($array) {
    foreach ($array as $value) {
        if ($value == null)
            return true;
    }
    return false;
}

function getUserByUsername( $username ){
    global $database_eonweb;
    
    $usersql = sqlrequest($database_eonweb,"select U.user_id as user_id, U.group_id as group_id ,U.user_name as user_name, U.user_passwd as user_passwd, U.user_descr as user_descr, U.user_type as user_type, L.dn as user_location, U.user_limitation as user_limitation  from users as U left join ldap_users_extended as L on U.user_name = L.login  where U.user_name = '".$username."'");
    
    return $usersql;
}

/*---HTTP Response---*/
function getJsonResponse( $response, $code, $array = null ){
    $header = "HTTP/1.1 ".$response->getMessageForCode($code)."\r\nContent-Type: application/json\r\n";

    $eonapi = \Slim\Slim::VERSION;
    $codeMessage = $response->getMessageForCode( $code );
    $arrayHeader = array("api_version" => $eonapi, "http_code" => $codeMessage);
    $arrayMerge = array_merge( $arrayHeader, $array );

    $jsonResponse = json_encode($arrayMerge, JSON_PRETTY_PRINT);
    $jsonResponseWithHeader = $header.$jsonResponse;
    header('Content-Type: application/json');
    
    return $jsonResponseWithHeader;
}

function constructResponse( $response, $logs, $authenticationValid = false ){
    //Only if API keys match
    if($authenticationValid == true){
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

/*---Authorization checks--*/
function verifyAuthenticationByApiKey( $request ){
    $authenticationValid = false;
    
    //Parameters in request
    $paramUsername = $request->get('username');
    $paramApiKey = $request->get('apiKey');
    
    //Do not set $serverApiKey to NULL (bypass risk)
    $serverApiKey = EONAPI_KEY;
    
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
        $authenticationValid = true;
    }

    
    return $authenticationValid;
}

function verifyAuthenticationByPassword( $request ){
    $authenticationValid = false;
    
    //Parameters in request
    $paramUsername = $request->get('username');
    $paramPassword = $request->get('password');
    
    $usersql = getUserByUsername( $paramUsername );
    $user_limitation = mysqli_result($usersql, 0, "user_limitation");
    $user_type = mysqli_result($usersql, 0, "user_type");
    
    //IF LOCAL USER AND ADMIN USER (No limitation)
    if( ($user_type != "1") && $user_limitation == "0"){
        $userpasswd = mysqli_result($usersql, 0, "user_passwd");
        $password = md5($paramPassword);
        
        //IF match the hashed password
        if($userpasswd == $password)
            $authenticationValid = true;
    }
    
    return $authenticationValid;
}



/*---Custom calls---*/
function getApiKey(){
    $request = \Slim\Slim::getInstance()->request();
    $response = \Slim\Slim::getInstance()->response();
    
    $authenticationValid = verifyAuthenticationByPassword( $request );
    if( $authenticationValid == TRUE ){
        //ID of the authenticated user
        $paramUsername = $request->get('username');
        $usersql = getUserByUsername( $paramUsername );
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

function getAuthenticationStatus(){
    $request = \Slim\Slim::getInstance()->request();
    $response = \Slim\Slim::getInstance()->response();
    
    $authenticationValid = verifyAuthenticationByApiKey( $request );    
    if( $authenticationValid == TRUE ){
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




?>
