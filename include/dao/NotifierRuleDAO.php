<?php

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
    
    protected $connexion;
    protected $create_request_pattern               = "INSERT INTO rules(name, type, debug, contact, host, service, state, notificationnumber, timeperiod_id, tracking, sort_key) VALUES(:name, :type, :debug, :contact, :host, :service, :state, :notificationnumber, :timeperiod_id, :tracking, :sort_key)";
    protected $update_rule_by_id_request            = "UPDATE rules SET name = :name, type = :type, debug = :debug, contact = :contact, host = :host, service = :service, state = :state, notificationnumber = :notificationnumber, timeperiod_id = :timeperiod_id, tracking = :tracking, sort_key = :sort_key WHERE id = :id";
    protected $add_rule_method_request              = "INSERT INTO rule_method VALUES(:rule_id, :method_id)";
    protected $delete_rule_method_request           = "DELETE FROM rule_method WHERE rule_id = :rule_id AND method_id = :method_id";
    protected $delete_rule_by_id_request2           = "DELETE FROM rules WHERE id = :id ";
    protected $delete_rule_by_id_request1           = "DELETE FROM rule_method WHERE rule_id = :id ";
    protected $select_all_request                   = "SELECT id, name, debug, contact, host, type, service, state, notificationnumber, timeperiod_id, tracking, sort_key, GROUP_CONCAT(method_id) as methods FROM rules, rule_method";
    protected $select_one_by_name_and_type_request  = "SELECT id, name, debug, contact, host, type, service, state, notificationnumber, timeperiod_id, tracking, sort_key, GROUP_CONCAT(method_id) as methods FROM rules, rule_method WHERE rules.id = rule_method.rule_id AND name = :name AND type = :type";
    protected $select_one_by_id_request             = "SELECT id, name, debug, contact, host, type, service, state, notificationnumber, timeperiod_id, tracking, sort_key, GROUP_CONCAT(method_id) as methods FROM rules, rule_method WHERE rules.id = rule_method.rule_id AND rules.id = :id";
    protected $select_linked_methods_id             = "SELECT method_id FROM rule_method WHERE rule_id = :id";
    

    function __construct(){
        require(__DIR__."/../config.php");
        global $database_host;
        global $database_notifier;
        global $database_password;
        global $database_username;
        try
        {
            $this->connexion = new PDO('mysql:host='.$database_host.';dbname='.$database_notifier.';charset=utf8', $database_username, $database_password);
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
     * 
     * @return false or the id (int) if insert success
     * 
     */
    public function createRule($name,$type,$timeperiod_id,$debug=0,$contact="*",$host="*",$service="*",$state="*",$notificationnumber="*",$tracking=0,$methods_id_str){
        try{
            $sort_key =0 ;
            $request = $this->connexion->prepare($this->create_request_pattern);
            $request->bindParam('name'              , $name);
            $request->bindParam('type'              , $type);
            $request->bindParam('host'              , $host);
            $request->bindParam('state'             , $state);
            $request->bindParam('debug'             , $debug);
            $request->bindParam('contact'           , $contact);
            $request->bindParam('service'           , $service);
            $request->bindParam('sort_key'          , $sort_key);
            $request->bindParam('tracking'          , $tracking);
            $request->bindParam('timeperiod_id'     , $timeperiod_id);
            $request->bindParam('notificationnumber', $notificationnumber);
            $request->execute();
            //return $request->errorInfo();
            $id = $this->connexion->lastInsertId();
            $request = null;
            //Add the method that have been linked to rule
            foreach(explode(",",$methods_id_str) as $method_id){
                $request = $this->connexion->prepare($this->add_rule_method_request);
                $request->execute(array(
                    'rule_id'       => $id,
                    'method_id'     => $method_id
                ));
            }

            return $id;
        }
        catch (PDOException $e){
            echo $e->getMessage() . " | ERROR_CODE PDO STATEMENT". $request->errorCode();
            return false;
        }
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
    public function updateRule($id,$name,$type,$debug,$contact,$host,$service,$state,$notificationnumber,$timeperiod_id,$tracking,$sort_key,$methods_id_str){
        try{
            $request = $this->connexion->prepare($this->update_rule_by_id_request);
            $request->execute(array(
                'id'                    => $id,
                'name'                  => $name,
                'type'                  => $type,
                'host'                  => $host,
                'state'                 => $state,
                'debug'                 => $debug,
                'contact'               => $contact,
                'service'               => $service,
                'sort_key'              => $sort_key,
                'tracking'              => $tracking,
                'timeperiod_id'         => $timeperiod_id,
                'notificationnumber'    => $notificationnumber
            ));
            
            $tab = $this->selectLinkedMethodsId($id);
            
            //Delete the method that have benn unlink from rule
            foreach($tab as $method_id){
                if(!in_array($method_id,explode(",",$methods_id_str))){
                    $request = $this->connexion->prepare($this->delete_rule_method_request);
                    $request->execute(array(
                        'rule_id'       => $id,
                        'method_id'     => $method_id
                    ));
                }
            }

            //Add the method that have been linked to rule
            foreach(explode(",",$methods_id_str) as $method_id){
                if(!in_array($method_id,$tab)){
                    $request = $this->connexion->prepare($this->add_rule_method_request);
                    $request->execute(array(
                        'rule_id'       => $id,
                        'method_id'     => $method_id
                    ));
                }
            }

            if($request->rowCount()>0){
                return true;
            }else return false;

        }
        catch (PDOException $e){
            echo $e->getMessage();
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
    public function deleteRule($id){
        try{
            //DELETE rule and rule_method where rule_id = id
            $request = $this->connexion->prepare($this->delete_rule_by_id_request1);
            $request->execute(array(
                'id' => $id
            ));
            
            $request = $this->connexion->prepare($this->delete_rule_by_id_request2);
            $request->execute(array(
                'id' => $id
            ));
            if($request->rowCount()>0){
                return true;
            }else return false;
            

        }
        catch (PDOException $e){
            echo $e->getMessage();
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
            $request = $this->connexion->query($this->select_all_request);
            while($row = $request->fetch()){
                array_push($result, $row);
            } 
            $request->closeCursor();
        }
        catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
        if(!empty($result))
            return $result;
        else return false;
    }

    /**
     * This Function return a rule in the database.
     * 
     * @param string $name
     * @param string $type
     * @return row
     * 
     */
    public function selectOneRuleByNameAndType($name,$type){
        
        try{
            $request = $this->connexion->prepare($this->select_one_by_name_and_type_request);
            $request->execute(array(
                'name' => $name,
                'type' => $type
            ));

            $result =$request->fetch();
            if(isset($result["id"])) return $result;
            return false ;
            
        }
        catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * This Function return a rule in the database.
     * 
     * @param int $id
     * @return row
     * 
     */
    public function selectOneRuleById($id){
        
        try{
            $request = $this->connexion->prepare($this->select_one_by_id_request);
            $request->execute(array(
                'id' => $id
            ));
            
            $result =$request->fetch();
            if(isset($result["id"])) return $result;
            return false ;
            
        }
        catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
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
            $request = $this->connexion->prepare($this->select_linked_methods_id);
            $request->execute(array(
                'id' => $id
            ));

            while($row = $request->fetch()){
                array_push($result, $row["method_id"]);
            } 
            $request->closeCursor();
        }
        catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
        return $result;
    }
}
?>