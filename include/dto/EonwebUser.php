<?php

require_once(__DIR__ . "/../dao/EonwebUserDAO.php");

/**
 *  Classe Object dedicated in user of eonweb database treatment. 
 *  Provide a set of function which modify the object.
 * 
 *  @author Jérémy Hoarau <jeremy.hoarau@axians.com>
 *  @package eonapi for eyesofnetwork project
 *  @copyright (C) 2019 EyesOfNetwork Team
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
class EonwebUser {
    private $eonwebUserDAO;

    private $user_id;
    private $group_id;
    private $user_name;
    private $user_password;
    private $user_description;
    private $user_type;
    private $user_location="";
    private $user_limitation=0;
    private $user_language=0;
 
    //other databases components
    private $in_nagvis=false;
    private $nagvis_group=false;
    private $in_cacti=false;

    /**
     * This is a constructeur.
     */
    function __construct(){
        $this->eonwebUserDAO = new EonwebUserDAO();
    }

    /**
     * Create or update in databases the object
     */
    function save(){
        if(isset($this->user_id)){
            return $this->eonwebUserDAO->updateEonwebUser( $this->user_id,$this->user_name,$this->user_description,$this->group_id, $this->user_password, $this->user_type, $this->user_location, $this->user_limitation, $this->in_nagvis, $this->in_cacti, $this->nagvis_group, $this->user_language);
        }else{
            $this->user_id=(int)$this->eonwebUserDAO->createEonwebUser( $this->user_name,$this->user_description,$this->group_id, $this->user_password, $this->user_type, $this->user_location, $this->user_limitation, $this->in_nagvis, $this->in_cacti, $this->nagvis_group, $this->user_language);
            if(isset($this->user_id)){
                return true;
            }else return false;
        }
    }

    /**
     * Delete in databases the object
     */
    function delete(){
        return $this->eonwebUserDAO->deleteEonwebUser($this->user_id, $this->user_name);
    }

    /**
     * Return a dictionnary of the object
     */
    function toArray(){
        $array = [];
        $array["user_id"] = $this->user_id;
        $array["group_id"] = $this->group_id;
        $array["user_name"] = $this->user_name;
        $array["user_password"] = $this->user_password;
        $array["user_description"] = $this->user_description;
        $array["user_type"] = $this->user_type;
        $array["user_location"] = $this->user_location;
        $array["user_limitation"] = $this->user_limitation;
        $array["user_language"] = $this->user_language;
    
        //other databases components
        $array["in_nagvis"] = $this->in_nagvis;
        $array["nagvis_group"] = $this->nagvis_group;
        $array["in_cacti"] = $this->in_cacti;

        return $array;
    }

    /*============= GETTER AND SETTER =============*/

    /**
     * Get the value of user_id
     */ 
    public function getUser_id()
    {
        return $this->user_id;
    }

    /**
     * Set the value of user_id
     *
     * @return  self
     */ 
    public function setUser_id($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

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
     * Get the value of user_name
     */ 
    public function getUser_name()
    {
        return $this->user_name;
    }

    /**
     * Set the value of user_name
     *
     * @return  self
     */ 
    public function setUser_name($user_name)
    {
        $this->user_name = $user_name;

        return $this;
    }

    /**
     * Get the value of user_password
     */ 
    public function getUser_password()
    {
        return $this->user_password;
    }

    /**
     * Set the value of user_password
     *
     * @return  self
     */ 
    public function setUser_password($user_password)
    {
        $this->user_password = $user_password;

        return $this;
    }

    /**
     * Get the value of user_description
     */ 
    public function getUser_description()
    {
        return $this->user_description;
    }

    /**
     * Set the value of user_description
     *
     * @return  self
     */ 
    public function setUser_description($user_description)
    {
        $this->user_description = $user_description;

        return $this;
    }

    /**
     * Get the value of user_type
     */ 
    public function getUser_type()
    {
        return $this->user_type;
    }

    /**
     * Set the value of user_type
     *
     * @return  self
     */ 
    public function setUser_type($user_type)
    {
        $this->user_type = $user_type;

        return $this;
    }

    /**
     * Get the value of user_location
     */ 
    public function getUser_location()
    {
        return $this->user_location;
    }

    /**
     * Set the value of user_location
     *
     * @return  self
     */ 
    public function setUser_location($user_location)
    {
        $this->user_location = $user_location;

        return $this;
    }

    /**
     * Get the value of user_limitation
     */ 
    public function getUser_limitation()
    {
        return $this->user_limitation;
    }

    /**
     * Set the value of user_limitation
     *
     * @return  self
     */ 
    public function setUser_limitation($user_limitation)
    {
        $this->user_limitation = $user_limitation;

        return $this;
    }

    /**
     * Get the value of user_language
     */ 
    public function getUser_language()
    {
        if($this->user_language = 0){
            return "navigator language";
        }else{
            return $this->user_language;
        }
        
    }

    /**
     * Set the value of user_language
     *
     * @return  self
     */ 
    public function setUser_language($user_language)
    {
        $available_lang = array(
            "anglais"   => "en",
            "english"   => "en",
            "french"    => "fr",
            "francais"  => "fr",
            "navigator_language" => 0 
        );
        if(array_key_exists($user_language,$available_lang)){
            $this->user_language = $available_lang[$user_language];
        }else{
            $this->user_language = 0;
        }
        return $this;
    }

    /**
     * Get the value of in_nagvis
     */ 
    public function getIn_nagvis()
    {
        return $this->in_nagvis;
    }

    /**
     * Set the value of in_nagvis
     *
     * @return  self
     */ 
    public function setIn_nagvis($in_nagvis)
    {
        $this->in_nagvis = $in_nagvis;

        return $this;
    }

    /**
     * Get the value of nagvis_group
     */ 
    public function getNagvis_group()
    {
        return $this->nagvis_group;
    }

    /**
     * Set the value of nagvis_group
     *
     * @return  self
     */ 
    public function setNagvis_group($nagvis_group)
    {
        $this->nagvis_group = $nagvis_group;

        return $this;
    }

    /**
     * Get the value of in_cacti
     */ 
    public function getIn_cacti()
    {
        return $this->in_cacti;
    }

    /**
     * Set the value of in_cacti
     *
     * @return  self
     */ 
    public function setIn_cacti($in_cacti)
    {
        $this->in_cacti = $in_cacti;

        return $this;
    }
}

?>