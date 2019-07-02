<?php

require(__DIR__."/../dao/NotifierTimeperiodDAO.php");
require_once(__DIR__."/NotifierTimeperiod.php");


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
class NotifierTimeperiodDTO {
    //private $DAYS_FR = ["LUNDI","MARDI","MERCREDI","JEUDI","VENDREDI","SAMEDI","DIMANCHE"];
    //private $DAYS_EN = ["MONDAY","TUESDAY","WEDNESDAY","THURSDAY","FRIDAY","SATURSDAY","SUNDAY"];
    private $timeperiodDAO ;
    
    public function __construct(){
        $this->timeperiodDAO = new NotifierTimeperiodDAO();
    }

    public function getNotifierTimeperiodById($id){
        $result = $this->timeperiodDAO->selectOneTimeperiodById($id);
        if($result){
            $timeperiod = new NotifierTimeperiod(); 
            $timeperiod->setId($result["id"]);
            $timeperiod->setName($result["name"]);
            $timeperiod->setDaysOfWeek($result["daysofweek"]);
            $timeperiod->setTimeperiod($result["timeperiod"]);

            return $timeperiod;
        }else return false;
    }

    public function getNotifierTimeperiodByName($name){
        $result = $this->timeperiodDAO->selectOneTimeperiodByName($name);
        if($result){
            $timeperiod = new NotifierTimeperiod(); 
            $timeperiod->setId($result["id"]);
            $timeperiod->setName($result["name"]);
            $timeperiod->setDaysOfWeek($result["daysofweek"]);
            $timeperiod->setTimeperiod($result["timeperiod"]);

            return $timeperiod;
        }else return false;
    }

}

?>