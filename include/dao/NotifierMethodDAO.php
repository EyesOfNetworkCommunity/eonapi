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
class NotifierMethodDAO {
    private $connexion;
    private $create_request_pattern                 = "INSERT INTO methods VALUES('',:name,:type,:line)";
    private $update_method_by_id_request            = "UPDATE methods SET name = :name, type = :type, line = :line WHERE id = :id";
    private $delete_method_by_id_request            = "DELETE FROM methods WHERE id = :id";
    private $select_all_request                     = "SELECT * FROM methods";
    private $select_one_by_name_and_type_request    = "SELECT * FROM methods WHERE name = :name AND type = :type";
    private $select_one_by_id_request               = "SELECT * FROM methods WHERE id = :id";

    function __construct(){
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
     * This Function include a new method in the database.
     * 
     * @param string $name
     * @param string $type
     * @param string $line
     * @return false or the id (int) if insert success
     * 
     */
    public function createMethod($name,$type,$line){
        try{
            $request = $this->connexion->prepare($this->create_request_pattern);
            $request->execute(array(
                'name' => $name,
                'type' => $type,
                'line' => $line
            ));
        }
        catch (PDOException $e){
            echo $e;
            return false;
        }
        return $this->connexion->lastInsertId();
    }

    /**
     * This Function update an existing method in the database.
     * 
     * @param int $id
     * @param string $newline
     * @param string $newName
     * @param string $newType
     * @return boolean
     * 
     */
    public function updateMethod($id,$newName,$newType,$newLine){
        try{
            $request = $this->connexion->prepare($this->update_method_by_id_request);
            $request->execute(array(
                'id'    => $id,
                'name'  => $newName,
                'type'  => $newType,
                'line'  => $newLine
            ));
        }
        catch (PDOException $e){
            echo $e;
            return false;
        }
        return true;
    }

    /**
     * This Function delete a method in the database.
     * 
     * @param int $id
     * @return boolean
     * 
     */
    public function deleteMethod($id){
        try{
            $request = $this->connexion->prepare($this->delete_method_by_id_request);
            $request->execute(array(
                'id' => $id
            ));

            if($request->rowCount()>0){
                return true;
            }else return false;
        }
        catch (Exception $e){
            echo $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * This Function return all the methods saved in the database
     * 
     * @return array result
     * 
     */
    public function selectAllMethods(){
        $result = array();
        try{
            $request = $this->connexion->query($this->select_all_request);
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
     * This Function return a method in the database.
     * 
     * @param string $name
     * @param string $type
     * @return row
     * 
     */
    public function selectOneMethodByNameAndType($name,$type){
        
        try{
            $request = $this->connexion->prepare($this->select_one_by_name_and_type_request);
            $request->execute(array(
                'name' => $name,
                'type' => $type
            ));

            $result = $request->fetch();
            if(isset($result["id"])) return $result;
            return false ;
            
        }catch (PDOException $e){
            echo $e;
            return false;
        }
        
    }

    /**
     * This Function return a method in the database.
     * 
     * @param string $id
     * @return row
     * 
     */
    public function selectOneMethodById($id){
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
            echo $e;
            return false;
        }
    }
}
?>