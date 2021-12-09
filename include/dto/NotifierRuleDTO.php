<?php


require(__DIR__."/../dao/NotifierRuleDAO.php");
require_once(__DIR__."/NotifierRule.php");

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

    public function __construct(){
        $this->ruleDAO = new NotifierRuleDAO();
    }

    public function getAllNotifierRule(){
        $result = $this->ruleDAO->selectAllRules();
        return $result;
    }

    /**
     * 
     */
    public function getNotifierRuleByNameAndType($name,$type){
        $result   = $this->ruleDAO->selectOneRuleByNameAndType($name,strtolower($type));
        if($result){
            $rule = new NotifierRule();
            $rule->setId                 ($result["id"]);
            $rule->setName               ($result["name"]);
            $rule->setType               ($result["type"]);
            $rule->setDebug              ($result["debug"]);
            $rule->setContact            ($result["contact"]);
            $rule->setHost               ($result["host"]);
            $rule->setService            ($result["service"]);
            $rule->setState              ($result["state"]);
            $rule->setNotificationnumber ($result["notificationnumber"]);
            $rule->setTracking           ($result["tracking"]);
            $rule->setSort_key           ($result["sort_key"]);
            $rule->setTimeperiod_id      ($result["timeperiod_id"]);
            
            foreach(explode(",", $result["methods"]) as $m_id){
                $rule->addMethod($m_id);
            }
            return $rule;
        }else  return false;
    }

    public function getNotifierRuleById($id){
        $result   = $this->ruleDAO->selectOneRuleById($id);
        if($result){
            $rule = new NotifierRule();
            $rule->setId                 ($result["id"]);
            $rule->setName               ($result["name"]);
            $rule->setType               ($result["type"]);
            $rule->setDebug              ($result["debug"]);
            $rule->setContact            ($result["contact"]);
            $rule->setHost               ($result["host"]);
            $rule->setService            ($result["service"]);
            $rule->setState              ($result["state"]);
            $rule->setNotificationnumber ($result["notificationnumber"]);
            $rule->setTracking           ($result["tracking"]);
            $rule->setSort_key           ($result["sort_key"]);
            $rule->setTimeperiod_id      ($result["timeperiod_id"]);
            
            foreach(split(",", $result["methods"]) as $m_id){
                $rule->addMethod((int)$m_id);
            }
            return $rule;
        }else  return false;
    }

}

?>