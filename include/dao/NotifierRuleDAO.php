<?php

include("../config.php");

/**
 *  Classe Data Access Object dedicated in method data recovery 
 *  locate in notifier database. 
 *  Provide a set of function which interrogate the database.
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
class NotifierRuleDAO {
    
    private $connexion;
    private $create_request_pattern               = "INSERT INTO rules(name, type, debug, contact, host, service ,state, notificationnumber, timeperiod_id, tracking, sort_key) VALUES(:name, :type, :debug, :contact, :host, :service, :state, :notificationnumber, :timeperiod_id, :tracking, :sort_key)";
    private $update_rule_by_id_request            = "UPDATE rules SET name = :name, type = :type, debug = :debug, contact = :contact, host = :host, service = :service, state = :state, notificationnumber = :notificationnumber, timeperiod_id = :timeperiod_id, tracking = :tracking, sort_key = :sort_key WHERE id = :id";
    private $add_rule_method_request              = "INSERT INTO rule_method VALUES(:rule_id, :method_id)";
    private $delete_rule_method_request           = "DELETE FROM rule_method WHERE rule_id= :rule_id AND method_id= :method_id";
    private $delete_rule_by_id_request            = "DELETE rules, rule_method FROM rules INNER JOIN rule_method ON rules.id = rule_method.rule_id WHERE id = :id ";
    private $select_all_request                   = "SELECT id, name, debug, contact, host, type, service, state, notificationnumber, timeperiod_id, tracking, GROUP_CONCAT(method_id) as methods FROM rules,rule_method";
    private $select_one_by_name_and_type_request  = "SELECT id, name, debug, contact, host, type, service, state, notificationnumber, timeperiod_id, tracking, GROUP_CONCAT(method_id) as methods FROM rules,rule_method WHERE rules.id = rule_method.rule_id AND name = :name AND type =:type";
    private $select_linked_methods_id             = "SELECT method_id FROM rule_method WHERE rule_id = :id";
    

    function __construct(){
        try
        {
            $connexion = new PDO('mysql:host='.$database_host.';dbname='.$database_notifier.';charset=utf8', $database_username, $database_password);
        }
        catch(Exception $e)
        {
            die('Erreur : '.$e->getMessage());
        }
    }

    /**
     * This Function include a new rule in the database.
     * 
     * @param string $name
     * @param string $type
     * @param string $debug
     * @param string $contact
     * @param string $host
     * @param string $service
     * @param string $state
     * @param string $notificationnumber
     * @param string $timeperiod_id
     * @param string $tracking
     * @param string $sort_key
     * 
     * @return false or the id (int) if insert success
     * 
     */
    protected function createRule($name,$type,$debug=0,$contact="*",$host="*",$service="*",$state="*",$notificationnumber="*",$timeperiod_id,$tracking=0,$sort_key=0){
        try{
            $request = $connexion->prepare($create_request_pattern);
            $request->execute(array(
                'name'                  => $name,
                'type'                  => $type,
                'line'                  => $debug,
                'contact'               => $contact,
                'host'                  => $host,
                'service'               => $service,
                'state'                 => $state,
                'notificationnumber'    => $notificationnumber,
                'timeperiod_id'         => $timeperiod_id,
                'tracking'              => $tracking,
                'sort_key'              => $sort_key
            ));
        }
        catch (PDOException $e){
            echo $e;
            return false;
        }
        return $connexion->lastInsertId();
    }

    /**
     * This Function update an existing rule in the database.
     * 
     * @param int    $id
     * @param string $name
     * @param string $type
     * @param string $debug
     * @param string $contact ie: toto,admin,titi,...
     * @param string $host ie: host1,host2,...
     * @param string $service ie: s1,s2,s3,...
     * @param string $state ie: OK,UP,*
     * @param string $notificationnumber
     * @param string $timeperiod_id ie: 0,1,2,3,...
     * @param string $tracking
     * @param string $sort_key
     * @param string $methods_id_str ie: 0,1,2,...
     * @return boolean
     * 
     */
    protected function updateRule($id,$name,$type,$debug,$contact,$host,$service,$state,$notificationnumber,$timeperiod_id,$tracking,$sort_key,$methods_id_str){
        try{
            $request = $connexion->prepare($update_rule_by_id_request);
            $request->execute(array(
                'id'                    => $id,
                'name'                  => $name,
                'type'                  => $type,
                'line'                  => $debug,
                'contact'               => $contact,
                'host'                  => $host,
                'service'               => $service,
                'state'                 => $state,
                'notificationnumber'    => $notificationnumber,
                'timeperiod_id'         => $timeperiod_id,
                'tracking'              => $tracking,
                'sort_key'              => $sort_key
            ));
            
            $tab = $this->selectLinkedMethodsId($id);
            
            //Delete the method that have benn unlink from rule
            foreach($tab as $method_id){
                if(!in_array($method_id,split(",",$methods_id_str)){
                    $request = $connexion->prepare($delete_rule_method_request);
                    $request->execute(array(
                        'rule_id'       => $id,
                        'method_id'     => $method_id
                    ));
                }
            }

            //Add the method that have been linked to rule
            foreach(split(",",$methods_id_str) as $method_id){
                if(!in_array($method_id,tab){
                    $request = $connexion->prepare($add_rule_method_request);
                    $request->execute(array(
                        'rule_id'       => $id,
                        'method_id'     => $method_id
                    ));
                }
            }

        }
        catch (PDOException $e){
            echo $e;
            return false;
        }
        return true;
    }

    /**
     * This Function delete a rule in the database.
     * 
     * @param int $id
     * @return boolean
     * 
     */
    protected function deleteRule($id){
        try{
            //DELETE rule and rule_method where rule_id = id
            $request = $connexion->prepare($delete_rule_by_id_request);
            $request->execute(array(
                'id' => $id
            ));
        }
        catch (PDOException $e){
            echo $e;
            return false;
        }
        return true;
    }

    /**
     * This Function return all the rules saved in the database
     * 
     * @return array result
     * 
     */
    public function selectAllRules(){
        $result = array();
        try{
            $request = $connexion->query($select_all_request);
            while($row = $request->fetch()){
                array_push($result, $row);
            } 
            $request->closeCursor();
        }
        catch (PDOException $e){
            echo $e;
        }
        return $result;
    }

    /**
     * This Function return a rule in the database.
     * 
     * @param string $name
     * @param string $type
     * @return row
     * 
     */
    public function selectOneRule($name,$type){
        $result = false;
        try{
            $request = $connexion->prepare($select_one_by_name_and_type_request);
            $request->execute(array(
                'name' => $name,
                'type' => $type
            ));

            $result = $request->fetch();
        }
        catch (PDOException $e){
            echo $e;
            return $result;
        }
        return $result;
    }

    /**
     * This Function return methods id linked to a specified rules in the database.
     * 
     * @param in $id
     * @return array result of the request
     * 
     */
    public function selectLinkedMethodsId($id){
        $result = array();
        try{
            $request = $connexion->prepare($select_linked_methods_id);
            $request->execute(array(
                'id' => $id
            ));

            while($row = $request->fetch()){
                array_push($result, $row["method_id"]);
            } 
            $request->closeCursor();
        }
        catch (PDOException $e){
            echo $e;
            return $result;
        }
        return $result;
    }
}
?>