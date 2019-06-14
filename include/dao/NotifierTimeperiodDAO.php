<?php

include("../config.php");

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
    
    private $connexion;
    private $create_request_pattern               = "INSERT INTO timeperiods(name, daysofweek, timeperiod) VALUES(:name, :daysofweek, :timeperiod)";
    private $update_timeperiod_by_id_request      = "UPDATE timeperiods SET name = :name, daysofweek =:daysofweek, timeperiod = :timeperiod WHERE id = :id";
    private $delete_timeperiod_by_id_request      = "DELETE FROM timeperiods WHERE id = :id ";
    private $select_all_request                   = "SELECT id, name, daysofweek, timeperiod FROM timeperiods";
    private $select_one_by_name_request           = "SELECT id, name, daysofweek, timeperiod FROM timeperiods WHERE name = :name";
    private $select_one_by_id_request             = "SELECT id, name, daysofweek, timeperiod FROM timeperiods WHERE id = :id";


    public __construct(){
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
     * This Function insert a new timeperiods in the database.
     * 
     * @param string $name
     * @param string $daysofweek
     * @param string $timeperiod
     * 
     * @return false or the id (int) if insert success
     * 
     */
    protected createTimeperiod($name,$daysofweek="*",$timeperiod="*"){
        try{
            $request = $connexion->prepare($create_request_pattern);
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
        return $connexion->lastInsertId();
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
    protected updateTimeperiod($id,$name,$daysofweek,$timeperiod){
        try{
            $request = $connexion->prepare($update_timeperiod_by_id_request);
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
    protected deleteTimeperiod($id){
        try{
            $request = $connexion->prepare($delete_timeperiod_by_id_request);
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
     * This Function return all the timeperiod saved in the database
     * 
     * @return array result
     * 
     */
    public selectAllTimeperiod(){
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
     * This Function return a timeperiod in the database.
     * 
     * @param string $name
     * @return row
     * 
     */
    public selectOneTimeperiodByName($name){
        $result = false;
        try{
            $request = $connexion->prepare($select_one_by_name_request);
            $request->execute(array(
                'name' => $name
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
     * This Function return a timeperiod in the database.
     * 
     * @param int $id
     * @return row
     * 
     */
    public selectOneTimeperiodByName($id){
        $result = false;
        try{
            $request = $connexion->prepare($select_one_by_id_request);
            $request->execute(array(
                'id' => $id
            ));

            $result = $request->fetch();
        }
        catch (PDOException $e){
            echo $e;
            return $result;
        }
        return $result;
    }

}
?>