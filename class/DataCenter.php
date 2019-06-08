<?php
//ACTION
if( isset($_POST["action"])){        
    $opt= $_POST["action"];
    unset($_POST['action']);    
    // Classes
    require_once("Conexion.php");
    // 
    // Instance
    $dataCenter = new DataCenter();
    switch($opt){
        case "ReadAll":
            echo json_encode($dataCenter->ReadAll());
            break;
        case "ReadByIP":
            echo json_encode($dataCenter->ReadByIP());
            break;
    }
}

class DataCenter{
    public $id=""; 
    public $direccionesIP=[];            

    function __construct(){

        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }
        
        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
            
            require_once("UUID.php");
            $this->id= $obj["id"] ?? UUID::v4();
        }
    }

    function ReadAll(){
        try {
            $sql='SELECT id, nombre FROM dataCenter;';      
            $data= DATA::Ejecutar($sql);            
            if($data){
                return $data;
            }
            else {
                return false;
            }

        }     
        catch(Exception $e) {
            error_log("[ERROR]  (".$e->getCode()."): ". $e->getMessage());
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar la lista'))
            );
        }
    }
    
    function ReadByIP(){
        try {
            $ip = explode('/',$_SERVER['REQUEST_URI'])[2];
            $ip = $_SERVER['REMOTE_ADDR']?:($_SERVER['HTTP_X_FORWARDED_FOR']?:$_SERVER['HTTP_CLIENT_IP']);
            $ip == "::1"?$ip="10.3.204.68":$ip;

            $sql='SELECT id, nombre, direccionesIP FROM dataCenter;';      
            $data= DATA::Ejecutar($sql);            
            if($data){
                foreach ($data as $value) {
                    foreach (json_decode($value["direccionesIP"])->ip as $direccionIP) {
                        if($ip == $direccionIP){
                            return $value;
                        }
                    }
                }
            }
            else {
                return false;
            }

        }     
        catch(Exception $e) {
            error_log("[ERROR]  (".$e->getCode()."): ". $e->getMessage());
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar la lista'))
            );
        }
    }
}

?>