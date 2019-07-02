<?php

require(__DIR__ . "/../dao/NotifierMethodDAO.php");
require_once(__DIR__."/NotifierMethod.php");

/**
 *  Classe Data Transfer Object dedicated in method data treatment. 
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
 */
class NotifierMethodDTO {
    private $methodDAO;
    
    public function __construct(){
        $this->methodDAO = new NotifierMethodDAO();
    }

    /**
     * @return instance NotifierMethod or false if does not exist
     */
    public function getNotifierMethodByNameAndType($name, $type){
        $result = $this->methodDAO->selectOneMethodByNameAndType($name,$type);
        if($result){
            $method = new NotifierMethod();

            $method->setId($result["id"]);
            $method->setName($result["name"]);
            $method->setType($result["type"]);
            $method->setLine($result["line"]);

            return $method;
        } else return false;
        
    }

    /**
     * @return instance NotifierMethod or false if does not exist
     */
    public function getNotifierMethodById($id){
        $result = $this->methodDAO->selectOneMethodById($id);
        if($result){
            $method = new NotifierMethod();

            $method->setId($result["id"]);
            $method->setName($result["name"]);
            $method->setType($result["type"]);
            $method->setLine($result["line"]);

            return $method;
        } else return false;
        
    }
}

?>