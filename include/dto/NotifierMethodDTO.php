<?php

include("../config.php");
include("../dao/NotifierMethodDAO.php");

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
 *  
 */
class NotifierMethodDTO {
    private $methodDAO = new NotifierMethodDAO();
    private $id;
    private $name;
    private $type;
    private $line;

    /**
     * This is a multiple constructeur.
     * Tree use case are available :
     *      First Create a new methods, you must provide 3 parameters
     *      @param args[1] name
     *      @param args[2] type
     *      @param args[3] line
     * 
     *      Second recovery an existing method, here you must provide 2 arguments
     *      @param args[1] name
     *      @param args[2] type
     *      
     *      third recovery an existing method with his id
     *      @param args[1] id
     * 
     *      After that if you want you can provide new value for others attributes 
     *      with getter and setter and save() de configuration afterwards.
     */
    function __construct(){
        $ctp = func_num_args();
        $args = func_get_args();

        switch($ctp){
            case 1 :
                $result = $methodDAO->selectOneMethodById($args[1]);
            case 2 : 
                $result = $methodDAO->selectOneMethodByNameAndType($args[1],$args[2]);
                $id     = $result["id"];
                $name   = $result["name"];
                $type   = $result["type"];
                $line   = $result["line"];
                break;
            case 3 :
                $result = $methodDAO->createMethod($args[1],$args[2],$args[3]);
                if($result != false){
                    $id     = $result;
                    $name   = $args[1];
                    $type   = $args[2];
                    $line   = $args[3];
                }
                break;
            default:
                break;
        }
    }

    /**
     * Update the current state of this method in the database
     * 
     * @return boolean 
     */
    public function save(){
        return $this->methodDAO->updateMethod($this->id,$this->name,$this->type,$this->line);
    }

    /**
     * Delete this method from the database
     * 
     * @return boolean
     *
     */
    public function deleteMethod(){
        return $this->methodDAO->deleteMethod($this->id);
    }


    //================================= AUTOGENERATE GET / SET =====================================


    /**
     * Get the value of line
     */ 
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Set the value of line
     *
     * @return  self
     */ 
    public function setLine($line)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * Get the value of type
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */ 
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }
}

?>