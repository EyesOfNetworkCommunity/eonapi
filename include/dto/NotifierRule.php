<?php

require_once(__DIR__."/NotifierMethodDTO.php");
/**
 *  Classe Data Transfer Object dedicated in rule data treatment. 
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

class NotifierRule {
    private $ruleDAO;
    private $id;
    private $name;
    private $type;
    private $debug;
    private $contact;
    private $host;
    private $service;
    private $state;
    private $notificationnumber;
    private $timeperiod_id; 
  //TODO external object timeperiod : 
  //private NotifierTimePeriodDTO $timeperiod = array();  
    private $tracking;
    private $sort_key;
    private $methods = array();

    /**
     * This is a constructeur.
     */
    function __construct(){
        $this->ruleDAO = new NotifierRuleDAO();
    }

    /**
     * 
     * @param $method (id or name)
     */
    public function addMethod($method){
        if(is_int($method)){
            $m =(new NotifierMethodDTO())->getNotifierMethodById($method);
            if($m){
                array_push($this->methods, $m );
            }
        }else {
            $m = (new NotifierMethodDTO())->getNotifierMethodByNameAndType($method,$this->type);
            if($m){
                array_push($this->methods,$m );
            }
        }
    }

    /**
     * 
     * @param $methodName
     */
    public function deleteMethod($methodName){
        for($i; $i<sizeof($this->methods);$i++ ){
          if($this->methods[$i]->getName() == $methodName){
              unset($this->methods[$i]);
          } 
        }
    }


    /**
     * Update the current state of this rule in the database
     * 
     * @return boolean 
     */
    public function save(){
        $str_methods_id = array();
        foreach($this->methods as $m){
            array_push($str_methods_id,$m->getId());
        }
        if(isset($this->id)){
            return $this->ruleDAO->updateRule($this->id,$this->name,$this->type,$this->debug,$this->contact,$this->host,$this->service,$this->state,$this->notificationnumber,$this->timeperiod_id,$this->tracking,$this->sort_key,implode(",",$str_methods_id));
        }else{
            return $this->ruleDAO->createRule($this->name,$this->type,$this->timeperiod_id,$this->debug,$this->contact,$this->host,$this->service,$this->state,$this->notificationnumber,$this->tracking,implode(",",$str_methods_id));
        }
    }

    /**
     * Delete this rule from the database
     * 
     * @return boolean
     *
     */
    public function deleteRule(){
        return $this->ruleDAO->deleteRule($this->id);
    }

    //==================================== GET / SET ========================================

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
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
     * Get the value of debug
     */ 
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * Set the value of debug
     *
     * @return  self
     */ 
    public function setDebug($debug)
    {
        $this->debug = $debug;

        return $this;
    }

    /**
     * Get the value of contact
     */ 
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set the value of contact
     *
     * @return  self
     */ 
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get the value of host
     */ 
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set the value of host
     *
     * @return  self
     */ 
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get the value of service
     */ 
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set the value of service
     *
     * @return  self
     */ 
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get the value of state
     */ 
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set the value of state
     *
     * @return  self
     */ 
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get the value of notificationnumber
     */ 
    public function getNotificationnumber()
    {
        return $this->notificationnumber;
    }

    /**
     * Set the value of notificationnumber
     *
     * @return  self
     */ 
    public function setNotificationnumber($notificationnumber)
    {
        $this->notificationnumber = $notificationnumber;

        return $this;
    }

    /**
     * Get the value of sort_key
     */ 
    public function getSort_key()
    {
        return $this->sort_key;
    }

    /**
     * Set the value of sort_key
     *
     * @return  self
     */ 
    public function setSort_key($sort_key)
    {
        $this->sort_key = $sort_key;

        return $this;
    }

    /**
     * Get the value of tracking
     */ 
    public function getTracking()
    {
        return $this->tracking;
    }

    /**
     * Set the value of tracking
     *
     * @return  self
     */ 
    public function setTracking($tracking)
    {
        $this->tracking = $tracking;

        return $this;
    }

    /**
     * Get the value of methods
     */ 
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Get the value of timeperiod_id
     */ 
    public function getTimeperiod_id()
    {
        return $this->timeperiod_id;
    }

    /**
     * Set the value of timeperiod_id
     *
     * @return  self
     */ 
    public function setTimeperiod_id($timeperiod_id)
    {
        $this->timeperiod_id = $timeperiod_id;

        return $this;
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