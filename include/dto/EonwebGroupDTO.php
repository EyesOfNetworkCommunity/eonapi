<?php

require_once(__DIR__ . "/../dao/EonwebGroupDAO.php");
require_once(__DIR__."/EonwebGroup.php");

/**
 *  Classe Data Transfer Object dedicated in group of eonweb 
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
class EonwebGroupDTO {
    private $eonwebGroupDAO;
    
    public function __construct(){
        $this->eonwebGroupDAO = new EonwebGroupDAO();
    }

    /**
     * @return instance EonwebGroup or false if does not exist
     */
    public function getEonwebGroupByName($name){
        $result = $this->eonwebGroupDAO->selectOneEonwebGroup($name);
        if(!$result){
            return $result;
        }else{
            $eonGroup = new EonwebGroup();
            $eonGroup->setGroup_id($result["group_id"]);
            $eonGroup->setGroup_name($result["group_name"]);
            $eonGroup->setGroup_description($result["group_descr"]);
            $eonGroup->setGroup_dn($result["group_dn"]);
            $eonGroup->setGroup_type($result["group_type"]);
            $tab_right = array("dashboard"=>$result["tab_1"], "disponibility"=>$result["tab_2"],"capacity"=>$result["tab_3"],"production"=>$result["tab_4"],"reports"=>$result["tab_5"],"administration"=>$result["tab_6"],"help"=>$result["tab_7"]);
            $eonGroup->setGroup_right($tab_right);
            return $eonGroup;
        }
    }

    public function getDN($group_name){
        $result = $this->eonwebGroupDAO->selectDnFromLdapGroupExtended($group_name);
        require_once("/srv/eyesofnetwork/eonweb/include/function.php");
        return ldap_escape($result);
    }

}

?>