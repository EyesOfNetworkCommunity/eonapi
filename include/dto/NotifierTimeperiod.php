<?php

require_once(__DIR__."/../dao/NotifierTimeperiodDAO.php");

/**
 *  Classe Data Transfer Object dedicated in timeperiod data treatment. 
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
class NotifierTimeperiod {
    //private $DAYS_FR = ["LUNDI","MARDI","MERCREDI","JEUDI","VENDREDI","SAMEDI","DIMANCHE"];
    //private $DAYS_EN = ["MONDAY","TUESDAY","WEDNESDAY","THURSDAY","FRIDAY","SATURSDAY","SUNDAY"];
    private $timeperiodDAO ;
    private $id;
    private $name;
    private $daysOfWeek; // Mon,Tue...
    private $timeperiod;

    /**
     * This is a multiple constructeur.
     * Tree use case are available :
     *      First Create a new timeperiod, you must provide 3 parameters
     *      @param args[1] name
     *      @param args[2] daysofweek * is available
     *      @param args[3] timeperiod * is available
     * 
     *      Second recovery an existing timeperiod, here you must provide his name as argument
     *      @param args[1] name
     *      
     *      third recovery an existing timeperiod with his id
     *      @param args[1] id
     * 
     *      After that if you want you can provide new value for others attributes 
     *      with getter and setter and save() de configuration afterwards.
     */
    public function __construct(){
        $this->timeperiodDAO = new NotifierTimeperiodDAO();
    }

    /**
     * Return a dictionnary of the object
     */
    function toArray(){
        $array = [];
        $array["id"] = $this->id;
        $array["name"] = $this->name;
        $array["daysOfWeek"] = $this->daysOfWeek;
        $array["timeperiod"] = $this->timeperiod;
        
        return $array;
    }

     /**
     * Update the current state of this Timeperiod in the database
     * 
     * @return boolean 
     */
    public function save(){
        if(isset($this->id)){
            return $this->timeperiodDAO->updateTimeperiod($this->id,$this->name,$this->daysOfWeek,$this->timeperiod);
        }else{
            return $this->timeperiodDAO->createTimeperiod($this->name,$this->daysOfWeek,$this->timeperiod);
        }
    }

    /**
     * Delete this Timeperiod from the database
     * 
     * @return boolean
     *
     */
    public function deleteTimeperiod(){
        return $this->timeperiodDAO->deleteTimeperiod($this->id);
    }


    //================================= AUTOGENERATE GET / SET =====================================


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
     * Get the value of daysOfWeek
     */ 
    public function getDaysOfWeek()
    {
        return $this->daysOfWeek;
    }

    /**
     * Set the value of daysOfWeek
     * Format mon,tue,wed,thu,fri,sat,sun | * 
     * @return  self
     */ 
    public function setDaysOfWeek($daysOfWeek)
    {
        $this->daysOfWeek = $daysOfWeek;

        return $this;
    }

    /**
     * Get the value of timeperiod
     */ 
    public function getTimeperiod()
    {
        return $this->timeperiod;
    }

    /**
     * set value in timeperiod
     * format 0000-0000,0010-2000,.... | * 
     * @return  self
     */ 
    public function setTimeperiod($timeperiod)
    {
        $this->timeperiod = $timeperiod;
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