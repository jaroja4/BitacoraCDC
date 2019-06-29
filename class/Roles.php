<?php
//ACTION
if( isset($_POST["action"])){        
    $opt= $_POST["action"];
    unset($_POST['action']);    
    // Classes
    require_once("Conexion.php");
    // 
    // Instance
    $roles = new Roles();
    switch($opt){
        case "ReadAll":
            echo json_encode($roles->ReadAll());
            break;
    }
}

class Roles{
    //Componente
    public $id="";
    public $nombre=""; 
    
           

    function __construct(){

        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }
        
        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
            
            require_once("UUID.php");
            $this->id= $obj["id"] ?? UUID::v4();
            $this->nombre= $obj["nombre"] ?? UUID::v4();
        }
    }

    function ReadAll(){
        try {
            $sql='SELECT id, nombre text
                FROM rol;';
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
}

?>