<?php

require_once(__DIR__ . "/../dao/NotifierMethodDAO.php");

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
class NotifierMethod {
    private $methodDAO;
    private $id;
    private $name;
    private $type;
    private $line;

    /**
     * This is a constructeur.
     * 
     */
    function __construct(){
        $this->methodDAO = new NotifierMethodDAO();
    }

    /**
     * Update the current state of this method in the database
     * 
     * @return boolean 
     */
    public function save(){
        if(isset($this->id)){
            return $this->methodDAO->updateMethod($this->id,$this->name,$this->type,$this->line);
        }else{
            $this->id=(int)$this->methodDAO->createMethod($this->name,$this->type,$this->line);
            if(isset($this->id)){
                return true;
            }else return false;
        }
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

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}

?>