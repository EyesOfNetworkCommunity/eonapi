<?php
/*
#
# EONAPI
#
# Copyright (c) 2017 AXIANS C&S
# Author: Adrien van den Haak <adrien.vandenhaak@axians.com>
#
*/

define("EONAPI_KEY", "€On@piK3Y");


/* API key encryption */
function apiKey( $user_id )
{
    $key = md5(EONAPI_KEY.$user_id);

    return hash('sha256', $key.$_SERVER['SERVER_ADDR']);
}


function getUserByUsername( $username ){
    global $database_eonweb;
    
    $usersql = sqlrequest($database_eonweb,"select U.user_id as user_id, U.group_id as group_id ,U.user_name as user_name, U.user_passwd as user_passwd, U.user_descr as user_descr, U.user_type as user_type, L.dn as user_location, U.user_limitation as user_limitation  from users as U left join ldap_users_extended as L on U.user_name = L.login  where U.user_name = '".$username."'");
    
    return $usersql;
}

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


?>