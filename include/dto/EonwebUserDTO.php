<?php

require_once(__DIR__ . "/../dao/EonwebUserDAO.php");
require_once(__DIR__."/EonwebUser.php");

/**
 *  Classe Data Transfer Object dedicated in user of eonweb 
 *  database treatment. 
 *  Provide a set of function which modify the object.
 * 
 * 
 *  @author Jérémy Hoarau <jeremy.hoarau@axians.com>
 *  @package eonapi for eyesofnetwork project
 *  @copyright (C) 2019 EyesOfNetwork Team
 * 
 *
 *  LICENCE :                                                     
 *  This program is free software; you can redistribute it and/or  
 *  modify it under the terms of the GNU General Public License    
 *  as published by the Free Software Foundation; either version 2 
 *  of the License, or (at your option) any later version.         
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of 
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the   
 *  GNU General Public License for more details.                   
 *                                                                  
 *  
 */
class EonwebUserDTO {
    private $eonwebUserDAO;
    
    public function __construct(){
        $this->eonwebUserDAO = new EonwebUserDAO();
    }
    /**
     * @return id or false
     */
    public function getNagvisGroupIdByName($role_name){
        return $this->eonwebUserDAO->selectNagvisRoleIdByName($role_name);
    }

    /**
     * @return instance EonwebUser or false if does not exist
     */
    public function getEonwebUserByName($name){
        $result = $this->eonwebUserDAO->selectOneEonwebUser($name);
        if(!$result){
            return $result;
        }else{
            $eonUser = new EonwebUser();
            $eonUser->setUser_id($result["user_id"]);
            $eonUser->setGroup_id($result["group_id"]);
            $eonUser->setUser_name($result["user_name"]);
            $eonUser->setUser_description($result["user_descr"]);
            $eonUser->setUser_password($result["user_passwd"]);
            $eonUser->setUser_type($result["user_type"]);
            $eonUser->setUser_location($result["user_location"]);
            $eonUser->setUser_limitation($result["user_limitation"]);
            $eonUser->setUser_language($result["user_language"]);
            $nagvis_id = $this->eonwebUserDAO->selectNagvisId($result["user_name"]);
            if($nagvis_id){
                $eonUser->setIn_nagvis(true);
                $role = $this->eonwebUserDAO->selectNagvisRoleOfOne($nagvis_id);
                $eonUser->setNagvis_group($role["name"]);
            }
            $cacti_id = $this->eonwebUserDAO->selectCactiId($result["user_name"]);
            if($cacti_id){
                $eonUser->setIn_cacti(true);
            }
            return $eonUser;
        }
    }
}

?>