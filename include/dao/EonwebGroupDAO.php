<?php


/**
 *  Classe Data Access Object dedicated in group of eonweb 
 *  database treatment. 
 *  Provide a set of function which modify the object.
 * 
 *  @author Jérémy Hoarau <jeremy.hoarau@axians.com>
 *  @package eonapi for eyesofnetwork project
 *  @copyright (C) 2019 EyesOfNetwork Team
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
 */

class EonwebGroupDAO {
    protected $connexion;
    
    //function use for the creation of a group
    protected $request_create_grp           = "INSERT INTO groups (group_name,group_descr,group_type,group_dn) VALUES(:group_name, :group_descr, :group_type, :group_dn)";
    protected $request_select_dn_ldap_group = "SELECT dn FROM ldap_groups_extended WHERE group_name = :group_name";
    protected $request_create_grp_right     = "INSERT INTO groupright VALUES(:group_id, :tab_1, :tab_2, :tab_3, :tab_4, :tab_5, :tab_6, :tab_7)";
    
    //function usefull for update a group and the right set for it
    protected $request_update_grp           = "UPDATE groups SET group_name = :group_name, group_descr = :group_descr, group_type = :group_type, group_dn = :group_dn WHERE group_id = :group_id";
    protected $request_update_grp_right     = "UPDATE groupright SET tab_1 = :tab_1, tab_2 = :tab_2, tab_3 = :tab_3, tab_4 = :tab_4, tab_5 = :tab_5, tab_6 = :tab_6, tab_7 = :tab_7  WHERE group_id = :group_id";

    //function use for delete a group
    protected $request_delete_group_right   = "DELETE FROM groupright WHERE group_id=:group_id";
    protected $request_delete_group         = "DELETE FROM groups WHERE group_id=:group_id";

    //function usefull to verify if a group already exist or to get it
    protected $request_select_one_grp       = "SELECT groups.group_id as group_id, group_name, group_descr, group_dn, group_type, tab_1, tab_2, tab_3, tab_4, tab_5, tab_6, tab_7 FROM groups,groupright WHERE groups.group_id = groupright.group_id AND group_name = :group_name";

    public function __construct(){
        require(__DIR__."/../config.php");
        try
        {
            $this->connexion = new PDO('mysql:host='.$database_host.';dbname='.$database_eonweb.';charset=utf8', $database_username, $database_password);
        }
        catch(Exception $e)
        {
            die('Erreur : '.$e->getMessage());
        }
    }

    /**
     * Insert into eonweb databases a new group 
     * 
     * @return int group_id 
     */
    public function createEonwebGroup($group_name,$group_descr,$group_type,$group_dn,$group_right){
        try{
            // $ldaprequest = $this->connexion->prepare($this->request_select_dn_ldap_group);
            // $ldaprequest->execute(array(
            //     'group_name'    =>$group_name
            // ));
            // $group_dn = ($ldaprequest->fetch())["dn"];

            $request = $this->connexion->prepare($this->request_create_grp);
            $request->execute(array(
                'group_name'        => $group_name,
                'group_descr'       => $group_descr,
                'group_type'        => $group_type,
                'group_dn'          => $group_dn
            ));
            $group_id = $this->connexion->lastInsertId();
            //manage right data 
            $request = NULL;
            $request = $this->connexion->prepare($this->request_create_grp_right);
            $request->execute(array(
                'group_id'  => $group_id,
                'tab_1'     => $group_right["dashboard"],
                'tab_2'     => $group_right["disponibility"],
                'tab_3'     => $group_right["capacity"],
                'tab_4'     => $group_right["production"],
                'tab_5'     => $group_right["reports"],
                'tab_6'     => $group_right["administration"],
                'tab_7'     => $group_right["help"]
            ));
        }
        catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
        return $group_id;
    }

    /**
     * Delete a eongroup by id
     */
    public function deleteEonwebGroup($group_id){
        try{
            $request = $this->connexion->prepare($this->request_delete_group_right);
            $request->execute(array(
                'group_id' => $group_id
            ));

            $request= NULL;

            $request = $this->connexion->prepare($this->request_delete_group);
            $request->execute(array(
                'group_id' => $group_id
            ));

            if($request->rowCount()>0){
                return true;
            }else return false;
        }catch (Exception $e){
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Modify a group by his id
     */
    public function updateEonwebGroup($group_id, $group_name, $group_descr, $group_type, $group_dn, $group_right){
        try{
            $request = $this->connexion->prepare($this->request_update_grp);
            $request->execute(array(
                'group_id'          => $group_id,
                'group_name'        => $group_name,
                'group_descr'       => $group_descr,
                'group_type'        => $group_type,
                'group_dn'          => $group_dn
            ));
            $modification =$request->rowCount();
            $request = NULL;
            $request = $this->connexion->prepare($this->request_update_grp_right);
            $request->execute(array(
                'group_id'  => $group_id,
                'tab_1'     => $group_right["dashboard"],
                'tab_2'     => $group_right["disponibility"],
                'tab_3'     => $group_right["capacity"],
                'tab_4'     => $group_right["production"],
                'tab_5'     => $group_right["reports"],
                'tab_6'     => $group_right["administration"],
                'tab_7'     => $group_right["help"]
            ));

            $modification= $request->rowCount()+$modification;
            if($modification>1){
                return true;
            }else return false;

        }catch (PDOException $e){
            echo $e;
            return false;
        }
        return true;
    }

    /**
     * Return Elements of the given group name false otherwise.
     * 
     * @return dict
     */
    public function selectOneEonwebGroup($grp_name){
        try{
            $request = $this->connexion->prepare($this->request_select_one_grp);
            $request->execute(array(
                'group_name' => $grp_name
            ));

            $result = $request->fetch();
            if(isset($result["group_id"])) return $result;
            return false;
            
        }catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Return Elements of the given group name false otherwise.
     * 
     * @return dict
     */
    public function selectDnFromLdapGroupExtended($grp_name){
        try{
            $request = $this->connexion->prepare($this->request_select_dn_ldap_group);
            $request->execute(array(
                'group_name' => $grp_name
            ));

            $result = $request->fetch();
            if(isset($result["dn"])) return $result;
            return false;
            
        }catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }
}

?>