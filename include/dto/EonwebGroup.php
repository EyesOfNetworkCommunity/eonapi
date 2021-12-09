<?php

require_once(__DIR__ . "/../dao/EonwebGroupDAO.php");

/**
 *  Classe Object dedicated in group of eonweb database treatment. 
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
class EonwebGroup {
    private $eonwebGroupDAO;
    private $group_id;
    private $group_name;
    private $group_type;
    private $group_description;
    private $group_dn;
    private $group_right=array("dashboard"=>1, "disponibility"=>0,"capacity"=>0,"production"=>0,"reports"=>0,"administration"=>0,"help"=>0);

    /**
     * This is a constructeur.
     */
    function __construct(){
        $this->eonwebGroupDAO = new EonwebGroupDAO();
    }

    /**
     * Create or update in databases the object
     */
    function save(){
        if(isset($this->group_id)){
            return $this->eonwebGroupDAO->updateEonwebGroup($this->group_id, $this->group_name, $this->group_description, $this->group_type, $this->group_dn, $this->group_right);
        }else{
            $this->group_id=(int)$this->eonwebGroupDAO->createEonwebGroup( $this->group_name, $this->group_description, $this->group_type, $this->group_dn, $this->group_right);
            if(isset($this->group_id)){
                return true;
            }else return false;
        }
    }

    /**
     * Return a dictionnary of the object
     */
    function toArray(){
        $array = [];
        $array["group_id"] = $this->group_id;
        $array["group_name"] = $this->group_name;
        $array["group_type"] = $this->group_type;
        $array["group_description"] = $this->group_description;
        $array["group_dn"] = $this->group_dn;
        $array["group_right"] = $this->group_right;
        return $array;
    }

    /**
     * Delete in databases the object
     */
    function delete(){
        return $this->eonwebGroupDAO->deleteEonwebGroup($this->group_id);
    }


    /*============= GETTER AND SETTER =============*/

    /**
     * Get the value of group_id
     */ 
    public function getGroup_id()
    {
        return $this->group_id;
    }

    /**
     * Set the value of group_id
     *
     * @return  self
     */ 
    public function setGroup_id($group_id)
    {
        $this->group_id = $group_id;

        return $this;
    }

    /**
     * Get the value of group_name
     */ 
    public function getGroup_name()
    {
        return $this->group_name;
    }

    /**
     * Set the value of group_name
     *
     * @return  self
     */ 
    public function setGroup_name($group_name)
    {
        $this->group_name = $group_name;

        return $this;
    }

    /**
     * Get the value of group_type
     */ 
    public function getGroup_type()
    {
        return $this->group_type;
    }

    /**
     * Set the value of group_type
     *
     * @return  self
     */ 
    public function setGroup_type($group_type)
    {
        $this->group_type = $group_type;

        return $this;
    }

    /**
     * Get the value of group_description
     */ 
    public function getGroup_description()
    {
        return $this->group_description;
    }

    /**
     * Set the value of group_description
     *
     * @return  self
     */ 
    public function setGroup_description($group_description)
    {
        $this->group_description = $group_description;

        return $this;
    }

    /**
     * Get the value of group_right
     */ 
    public function getGroup_right()
    {
        return $this->group_right;
    }

    /**
     * Set the value of group_right
     *
     * @return  self
     */ 
    public function setGroup_right($group_right)
    {
        $this->group_right = $group_right;

        return $this;
    }

    /**
     * Get the value of group_dn
     */ 
    public function getGroup_dn()
    {
        return $this->group_dn;
    }

    /**
     * Set the value of group_dn
     *
     * @return  self
     */ 
    public function setGroup_dn($group_dn)
    {
        $this->group_dn = $group_dn;

        return $this;
    }
}

?>