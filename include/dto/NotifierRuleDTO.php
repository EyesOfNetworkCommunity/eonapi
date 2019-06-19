<?php


include("../dao/NotifierRuleDAO.php");
include("./NotifierMethodDTO.php");
include("./NotifierTimeperiodDTO.php");
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
class NotifierRuleDTO {
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
     * This is a multiple constructeur.
     * Two use case are available :
     *      First Create a new rule, you must provide 3 parameters
     *      @param args[1] name
     *      @param args[2] type
     *      @param args[3] timeperiod_id or his name 
     * 
     *      Second recovery an existing method, here you must provide 2 arguments
     *      @param args[1] name
     *      @param args[2] type
     * 
     *      After that if you want you can provide new value for others attributes 
     *      with getter and setter and save() de configuration afterwards.
     * 
     */
    function __construct(){
        $this->ruleDAO = new NotifierRuleDAO();
        $ctp = func_num_args();
        $args = func_get_args();

        switch($ctp){
            case 2 : 
                $result             = $ruleDAO->selectOneRule($args[0],$args[1]);
                $id                 = $result["id"];
                $name               = $result["name"];
                $type               = $result["type"];
                $debug              = $result["debug"];
                $contact            = $result["contact"];
                $host               = $result["host"];
                $service            = $result["service"];
                $state              = $result["state"];
                $notificationnumber = $result["notificationnumber"];
                $tracking           = $result["tracking"];
                $sort_key           = $result["sort_key"];
                $timeperiod_id      = $result["timeperiod_id"];

                foreach($result["methods"] as $m_id){
                    array_push($methods, new NotifierMethodDTO($m_id));
                }
                break;
            case 3 :
                if(is_int($args[3])){
                    $timeperiod_id  = $args[3];
                }else{
                    $timeperiod_id = (new NotifierTimeperiodDTO($args[2]))->getId();
                }

                $result = $ruleDAO->createRule($args[0],$args[1],$timeperiod_id);
                if($result != false){
                    $id             = $result;
                    $name           = $args[0];
                    $type           = $args[1];
                }
                break;
            default:
                break;
        }
    }

    /**
     * 
     * @param $methodName
     */
    public function addMethod($methodName){
        array_push($this->methods, new NotifierMethodDTO($methodName,$this->type));
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
        $str_methods_id = '';
        foreach($this->methods as $m){
            $str_methods_id .= $m->getId();
        }
        return $this->ruleDAO->updateRule($this->id,$this->name,$this->type,$this->debug,$this->contact,$this->host,$this->service,$this->state,$this->notificationnumber,$this->timeperiod_id,$this->tracking,$this->sort_key,$str_methods_id);
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
}

?>