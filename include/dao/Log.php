<?php
class log{
    private $connexion;
    protected $request_inset_log = "INSERT INTO logs VALUES('',:time,:user,:module,:cmd,:remote_adr)";


    public function __construct(){
        $this->connexion = new PDO('mysql:host='.$database_host.';dbname='.$database_eonweb.';charset=utf8', $database_username, $database_password);
    }

    function logging($module,$command,$user=false){
        require(__DIR__."/../config.php");
        global $dateformat;
        if($user){
            try{
                $request = $this->connexion->prepare($this->request_inset_log);
                $request->execute(array(
                    'time'       => time(),
                    'user'       => $user,
                    'module'     => $module,
                    'cmd'        => $command,
                    'remote_adr' => $_SERVER["REMOTE_ADDR"]
                ));
            }catch (PDOException $e){
                echo $e;
            }
        }elseif(isset($_COOKIE['user_name'])){
            try{
                $request = $this->connexion->prepare($this->request_inset_log);
                $request->execute(array(
                    'time'      => time(),
                    'user'      => $_COOKIE['user_name'],
                    'module'    => $module,
                    'cmd'       => $command,
                    'remote_adr'=> $_SERVER["REMOTE_ADDR"]
                ));
            }catch (PDOException $e){
                echo $e;
            }
            
        }
    }
}


?>