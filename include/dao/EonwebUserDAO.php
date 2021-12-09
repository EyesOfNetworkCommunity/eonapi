<?php

/**
 *  Classe Data Access Object dedicated in user of eonweb 
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

class EonwebUserDAO {
    protected $connexion;
    protected $connexion_cacti;
    protected $connexion_nagvis;
    
    //function use for the creation of a user
    protected $request_create_usr                   = "INSERT INTO users (user_name, user_descr, group_id, user_passwd, user_type, user_location, user_limitation, user_language) VALUES(:user_name, :user_descr, :user_group, :user_password, :user_type, :user_location, :user_limitation, :user_language)";
    protected $request_create_usr_cacti             = "INSERT INTO user_auth (username,password,realm,full_name,show_tree,show_list,show_preview,graph_settings,login_opts,policy_graphs,policy_trees,policy_hosts,policy_graph_templates,enabled) VALUES (:user_name,'',2,:user_descr,'on','on','on','on',3,2,2,2,2,'on')";
    protected $request_create_usr_nagvis            = "INSERT INTO users (name, password) VALUES (:user_name, :sha_password)"; 
    protected $request_create_usr_nagvis_right      = "INSERT INTO users2roles (userId, roleId) VALUES (:nagvis_id, :nagvis_group)";//Guest by default
    
    protected $request_update_usr                   = "UPDATE users SET user_name = :user_name, user_descr = :user_descr,group_id = :user_group,user_passwd = :passwd_temp,user_type = :user_type,user_location = :user_location,user_limitation = :user_limitation,user_language = :user_language WHERE user_id = :user_id";
    protected $request_update_usr_cacti             = "UPDATE user_auth SET username = :user_name WHERE id = :cacti_id";
    protected $request_update_usr_nagvis            = "UPDATE users SET name = :user_name, password = :password WHERE userId = :nagvis_id";
    protected $request_update_usr_nagvis_right      = "UPDATE users2roles SET roleId = :nagvis_role_id WHERE userId = :nagvis_id";

    protected $request_delete_usr                   = "DELETE FROM users WHERE user_id = :user_id";
    protected $request_delete_usr_cacti             = "DELETE FROM user_auth WHERE id = :cacti_id";
    protected $request_delete_usr_nagvis            = "DELETE FROM users WHERE userId = :nagvis_id";
    protected $request_delete_usr_nagvis_right      = "DELETE FROM users2roles WHERE userId = :nagvis_id";

    protected $request_select_one_usr_by_id         = "SELECT user_id, user_name, user_descr, group_id, user_passwd, user_type, user_location, user_limitation, user_language FROM users WHERE user_id = :user_id";
    protected $request_select_one_usr               = "SELECT user_id, user_name, user_descr, group_id, user_passwd, user_type, user_location, user_limitation, user_language FROM users WHERE user_name = :user_name";
    protected $request_select_passwd                = "SELECT user_passwd FROM users WHERE user_id = :user_id";
    protected $request_select_cacti_id              = "SELECT id FROM user_auth WHERE username = :user_name";
    protected $request_select_nagvis_id             = "SELECT userId FROM users WHERE name = :user_name";
    protected $request_select_nagvis_all_roles      = "SELECT * FROM roles";
    protected $request_select_nagvis_user2role      = "SELECT name, roles.roleId as roleId FROM users2roles, roles WHERE userId = :user_id AND roles.roleId = users2roles.roleId";
    protected $request_select_nagvis_role_by_name   = "SELECT roleId FROM roles WHERE name = :role_name";
    
    public function __construct(){
        require(__DIR__."/../config.php");
        try
        {
            $this->connexion        = new PDO('mysql:host='.$database_host.';dbname='.$database_eonweb.';charset=utf8', $database_username, $database_password);
            $this->connexion_cacti  = new PDO('mysql:host='.$database_host.';dbname='.$database_cacti.';charset=utf8', $database_username, $database_password);
            $this->connexion_nagvis = new PDO('sqlite:/srv/eyesofnetwork/nagvis/etc/auth.db');
        }
        catch(Exception $e)
        {
            die('Erreur : '.$e->getMessage());
        }
    }

    /**
     * Insert into databases a new user
     * 
     * @return int user_id 
     */
    public function createEonwebUser($user_name,$user_descr,$user_group, $user_password, $user_type, $user_location, $user_limitation, $in_nagvis = false, $in_cacti = false, $nagvis_group = false, $user_language = false){
        try{
            // EON 6.0.1 - Upgrade password hash
            $passwd_temp = password_hash(md5($user_password), PASSWORD_DEFAULT);
            $request = $this->connexion->prepare($this->request_create_usr);
            $request->execute(array(
                 'user_name'        => $user_name,
                 'user_descr'       => $user_descr,
                 'user_group'       => $user_group,
                 'user_password'    => $passwd_temp,
                 'user_type'        => $user_type,
                 'user_location'    => $user_location,
                 'user_limitation'  => $user_limitation,
                 'user_language'    => $user_language
                
            ));
            $user_id = $this->connexion->lastInsertId();

            if($in_cacti){
                $cacti_id = $this->createCactiUser($user_name, $user_descr);
            }

            if($in_nagvis){
                $nagvis_id = $this->createNagvisUser($user_name, $user_password, $nagvis_group);
            }
        }
        catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
        return $user_id;
    }

    /**
     * Insert into cacti the parallel eon user
     * 
     * @param user_name 
     * @param user_descr
     * @return
     * 
     */
    public function createCactiUser($user_name, $user_descr="cacti user"){
        try{
            $request = $this->connexion_cacti->prepare($this->request_create_usr_cacti);
            $request->execute(array(
                 'user_name'        => $user_name,
                 'user_descr'       => $user_descr
                
            ));
            $user_id = $this->connexion_cacti->lastInsertId();
            
        }
        catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
        return $user_id;
    }

    /**
     * Insert into nagvis the parallel eon user.
     * 
     * @param user_name string 
     * @param password the password in clear
     * @param nagvis_group id of the right's group chosen
     * 
     * @return
     */
    public function createNagvisUser($user_name, $password, $nagvis_group){
        try{
            $nagvis_salt = '29d58ead6a65f5c00342ae03cdc6d26565e20954';
            // EON 6.0.1 - Upgrade password hash
            $sha_password = sha1($nagvis_salt.password_hash($password, PASSWORD_DEFAULT));
            
            $request = $this->connexion_nagvis->prepare($this->request_create_usr_nagvis);
            $request->execute(array(
                 'user_name'        => $user_name,
                 'sha_password'     => $sha_password
            ));
            $user_id = $this->connexion_nagvis->lastInsertId();
            
            $request = NULL;
            $nagvis_group_id =$this->selectNagvisRoleIdByName($nagvis_group);
            $request = $this->connexion_nagvis->prepare($this->request_create_usr_nagvis_right);
            $request->execute(array(
                 'nagvis_id'        => $user_id,
                 'nagvis_group'     => $nagvis_group_id
            ));
        }
        catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
        return $user_id;
    }

    /**
     * Insert into nagvis the parallel eon user.
     * 
     * @param nagvis_id id
     * @param user_name string 
     * @param password the password in clear
     * @param nagvis_group id of the right's group chosen
     * 
     * @return
     */
    public function updateNagvisUser($nagvis_id, $user_name, $password, $nagvis_group){
        try{
            $nagvis_salt = '29d58ead6a65f5c00342ae03cdc6d26565e20954';
            // EON 6.0.1 - Upgrade password hash
            $sha_password = sha1($nagvis_salt.password_hash($password, PASSWORD_DEFAULT));

            $request = $this->connexion_nagvis->prepare($this->request_update_usr_nagvis);
            $request->execute(array(
                 'nagvis_id'        => $nagvis_id,
                 'user_name'        => $user_name,
                 'password'         => $sha_password
            ));
            $modification = $request->rowCount();
            $request = NULL;
            $nagvis_group_id =$this->selectNagvisRoleIdByName($nagvis_group);
            $request = $this->connexion_nagvis->prepare($this->request_update_usr_nagvis_right);
            $request->execute(array(
                 'nagvis_id'        => $nagvis_id,
                 'nagvis_role_id'   => $nagvis_group_id
            ));
            $modification = $modification + $request->rowCount();
            if($modification>1){
                return true;
            }else return false;
        }
        catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
        
    }

    /**
     * Insert into cacti the parallel eon user
     * 
     * @param user_name 
     * @param user_descr
     * @return
     * 
     */
    public function updateCactiUser($cacti_id, $user_name){
        try{
            $request = $this->connexion_cacti->prepare($this->request_update_usr_cacti);
            $request->execute(array(
                 'cacti_id'         => $cacti_id,
                 'user_name'        => $user_name
            ));

            if($request->rowCount()>0){
                return true;
            }else return false;
        }
        catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }

    

    /**
     * Delete a eonuser by id
     * 
     * @param user_id
     * @return boolean
     */
    public function deleteEonwebUser($user_id, $user_name){
        try{
            $request = $this->connexion->prepare($this->request_delete_usr);
            $request->execute(array(
                'user_id' => $user_id
            ));
            $cacti_id = $this->selectCactiId($user_name);
            $nagvis_id = $this->selectNagvisId($user_name);
            if($cacti_id){
                $this->deleteCactiUser($cacti_id);
            }

            if($nagvis_id){
                $this->deleteNagvisUser($nagvis_id);
            }

            if($request->rowCount()>0){
                return true;
            }else return false;

        }catch (Exception $e){
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Delete a cacti by id
     * 
     * @param cacti_id
     * @return boolean
     */
    public function deleteCactiUser($cacti_id){
        try{
            $request = $this->connexion_cacti->prepare($this->request_delete_usr_cacti);
            $request->execute(array(
                'cacti_id' => $cacti_id
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
     * Delete a nagvis by id
     * 
     * @param nagvis_id
     * @return boolean
     */
    public function deleteNagvisUser($nagvis_id){
        try{
            $request = $this->connexion_nagvis->prepare($this->request_delete_usr_nagvis);
            $request->execute(array(
                'nagvis_id' => $nagvis_id
            ));

            $modification = $request->rowCount();
            $request = NULL;

            $request = $this->connexion_nagvis->prepare($this->request_delete_usr_nagvis_right);
            $request->execute(array(
                'nagvis_id' => $nagvis_id
            ));
            
            $modification = $modification + $request->rowCount();

            if($modification>1){
                return true;
            }else return false;

        }catch (Exception $e){
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * update a user
     * 
     * @return 
     */
    public function updateEonwebUser($user_id,$user_name,$user_descr,$user_group, $user_password, $user_type, $user_location, $user_limitation, $in_nagvis, $in_cacti, $nagvis_group, $user_language){
        try{
            $old_user = $this->selectOneEonwebUserById($user_id);
            $old_pwd = $this->getUserPasswd($user_id);
            //Verify if password is changed
            // EON 6.0.1 - Upgrade password hash
            // if($old_pwd != false && md5($user_password) != $old_pwd){ 
            if($old_pwd != false && (!password_verify(md5($user_password),$old_pwd))){ 
                
                $user_password = password_hash(md5($user_password), PASSWORD_DEFAULT);
            }
             
            $request = $this->connexion->prepare($this->request_update_usr);
            $request->execute(array(
                 'user_id'          => $user_id,
                 'user_name'        => $user_name,
                 'user_descr'       => $user_descr,
                 'user_group'       => $user_group,
                 'passwd_temp'      => $user_password,
                 'user_type'        => $user_type,
                 'user_location'    => $user_location,
                 'user_limitation'  => $user_limitation,
                 'user_language'    => $user_language
            ));

            if($in_cacti){
                $cacti_id = $this->selectCactiId($old_user["user_name"]);
                if(!$cacti_id){
                    $result_cacti = $this->createCactiUser($user_name, $user_descr);
                }else{
                    $result_cacti = $this->updateCactiUser((int)$cacti_id, $user_name);
                }
                
            }else{
                $cacti_id = $this->selectCactiId($old_user["user_name"]);
                if($cacti_id){
                    $result_cacti = $this->deleteCactiUser((int)$cacti_id);
                }
            }

            if($in_nagvis){
                $nagvis_id = $this->selectNagvisId($old_user["user_name"]);
                if(!$nagvis_id){
                    $result_nagvis = $this->createNagvisUser($user_name, $user_password, $nagvis_group);

                }else{
                    $result_nagvis= $this->updateNagvisUser((int)$nagvis_id, $user_name, $user_password, $nagvis_group);
                }
            }else{
                $nagvis_id = $this->selectNagvisId($old_user["user_name"]);
                if($nagvis_id){
                    $result_nagvis = $this->deleteNagvisUser((int)$nagvis_id);
                }
            }
            if($request->rowCount()>0) return true;
            else return false;
        }
        catch (PDOException $e){
            var_dump($e);
            return false;
        }
    }

    public function selectCactiId($user_name){
        try{
            $request = $this->connexion_cacti->prepare($this->request_select_cacti_id);
            $request->execute(array(
                'user_name' => $user_name
            ));

            $result = $request->fetch();
            if(isset($result["id"])) return $result["id"];
            else return false;
            
        }catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }

    public function selectNagvisId($user_name){
        try{
            $request = $this->connexion_nagvis->prepare($this->request_select_nagvis_id);
            $request->execute(array(
                'user_name' => $user_name
            ));

            $result = $request->fetch();
            if(isset($result["userId"])) return (int)$result["userId"];
            else return false;
            
        }catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }

    public function selectNagvisAllRoles(){
        try{
            $request = $this->connexion_nagvis->prepare($this->request_select_nagvis_all_roles);
            $request->execute();
            $result = array();
            while($row = $request->fetch()){
                array_push($result, $row);
            } 
            $request->closeCursor();

            if(!empty($result)) return $result;
            else return false;
            
        }catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }

    public function selectNagvisRoleOfOne($nagvis_id){
        try{
            $request = $this->connexion_nagvis->prepare($this->request_select_nagvis_user2role);
            $request->execute(array(
                "user_id" => $nagvis_id
            ));
            
            $result = $request->fetch();
            if(isset($result)) return $result;
            else return false;
            
        }catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }

    public function selectNagvisRoleIdByName($role_name){
        try{
            $request = $this->connexion_nagvis->prepare($this->request_select_nagvis_role_by_name);
            $request->execute(array(
                "role_name" => $role_name
            ));
            
            $result = $request->fetch();
            if(isset($result)) return $result["roleId"];
            else return false;
            
        }catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }
    

    /**
     * Return the password of an existing user
     * 
     */
    public function getUserPasswd($user_id){
        try{
            $request = $this->connexion->prepare($this->request_select_passwd);
            $request->execute(array(
                'user_id' => $user_id
            ));

            $result = $request->fetch();
            if(isset($result["user_passwd"])) return $result["user_passwd"];
            else return false;
            
        }catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Return Elements of the given user name false otherwise.
     * 
     * @return dict
     */
    public function selectOneEonwebUser($user_name){
        try{
            $request = $this->connexion->prepare($this->request_select_one_usr);
            $request->execute(array(
                'user_name' => $user_name
            ));

            $result = $request->fetch();
            if(isset($result["user_id"])) return $result;
            else return false;
        }catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Return Elements of the given user name false otherwise.
     * 
     * @return dict
     */
    public function selectOneEonwebUserById($id){
        try{
            $request = $this->connexion->prepare($this->request_select_one_usr_by_id);
            $request->execute(array(
                'user_id' => $id
            ));

            $result = $request->fetch();
            if(isset($result["user_id"])) return $result;
            else return false;
        }catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }
}

?>
