<?php

/**
 *  Classe Data Access Object dedicated in timeperiod data recovery 
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
class NotifierTimeperiodDAO {
    
    protected $connexion;
    protected $create_request_pattern               = "INSERT INTO timeperiods(name, daysofweek, timeperiod) VALUES(:name, :daysofweek, :timeperiod)";
    protected $update_timeperiod_by_id_request      = "UPDATE timeperiods SET name = :name, daysofweek =:daysofweek, timeperiod = :timeperiod WHERE id = :id";
    protected $delete_timeperiod_by_id_request      = "DELETE FROM timeperiods WHERE id = :id ";
    protected $select_all_request                   = "SELECT id, name, daysofweek, timeperiod FROM timeperiods";
    protected $select_one_by_name_request           = "SELECT id, name, daysofweek, timeperiod FROM timeperiods WHERE name = :name";
    protected $select_one_by_id_request             = "SELECT id, name, daysofweek, timeperiod FROM timeperiods WHERE id = :id";


    public function __construct(){
        require(__DIR__."/../config.php");
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
     * This Function insert a new timeperiods in the database.
     * 
     * @param $name
     * @param $daysofweek
     * @param $timeperiod
     * 
     * @return false or the id (int) if insert success
     * 
     */
    public function createTimeperiod($name,$daysofweek="*",$timeperiod="*"){
        try{
            $request = $this->connexion->prepare($this->create_request_pattern);
            $request->execute(array(
                'name'        => $name,
                'daysofweek'  => $daysofweek,
                'timeperiod'  => $timeperiod
            ));
        }
        catch (PDOException $e){
            echo $e;
            return false;
        }
        return $this->connexion->lastInsertId();
    }

    /**
     * This Function update an existing timeperiod in the database.
     * 
     * @param int    $id
     * @param string $name
     * @param string $daysofweek
     * @param string $timeperiod
     *
     * @return boolean
     * 
     */
    public function updateTimeperiod($id,$name,$daysofweek,$timeperiod){
        try{
            $request = $this->connexion->prepare($this->update_timeperiod_by_id_request);
            $request->execute(array(
                'id'                    => $id,
                'name'                  => $name,
                'daysofweek'            => $daysofweek,
                'timeperiod'            => $timeperiod
            ));
        }
        catch (PDOException $e){
            echo $e;
            return false;
        }
        return true;
    }

    /**
     * This Function delete a timeperiod in the database.
     * 
     * @param int $id
     * @return boolean
     * 
     */
    public function deleteTimeperiod($id){
        try{
            $request = $this->connexion->prepare($this->delete_timeperiod_by_id_request);
            $request->bindParam('id', $id);
            $request->execute();
            if($request->rowCount()>0){
                return true;
            }else return false;
        }
        catch (PDOException $e){
            echo $e;
            return false;
        }
        return true;
    }

    /**
     * This Function return all the timeperiod saved in the database
     * 
     * @return array result
     * 
     */
    public function selectAllTimeperiod(){
        $result = array();
        try{
            $request = $this->connexion->query($select_all_request);
            while($row = $request->fetch()){
                array_push($result, $row);
            } 
            $request->closeCursor();
        }
        catch (PDOException $e){
            echo $e;
            return false;
        }
        return $result;
    }

    /**
     * This Function return a timeperiod in the database.
     * 
     * @param string $name
     * @return row
     * 
     */
    public function selectOneTimeperiodByName($name){
        try{
            $request = $this->connexion->prepare($this->select_one_by_name_request);
            $request->execute(array(
                'name' => $name
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
     * This Function return a timeperiod in the database.
     * 
     * @param int $id
     * @return row
     * 
     */
    public function selectOneTimeperiodById($id){
        $result = false;
        try{
            $request = $this->connexion->prepare($this->select_one_by_id_request);
            $request->bindParam('id', $id, PDO::PARAM_INT);
            $request->execute();

            $result =$request->fetch();
            if(isset($result["id"])) return $result;
            return false ;
        }
        catch (PDOException $e){
            echo $e;
            return false;
        }
    }

}
?>
