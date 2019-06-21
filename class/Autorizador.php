<?php
//ACTION
if( isset($_POST["action"])){        
    $opt= $_POST["action"];
    unset($_POST['action']);    
    // Classes
    require_once("Conexion.php");
    // 
    // Instance
    $autorizador = new Autorizador();
    switch($opt){
        case "ReadAll_list":
            echo json_encode($autorizador->ReadAll_list());
            break;
    }
}

class Autorizador{
    public $id="";
    public $idDataCenter="";            

    function __construct(){

        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }
    }

    function ReadAll_list(){
        try {
            $sql='SELECT u.id, u.usuario, u.passwd, u.cedula, u.nombre, u.correo, u.empresa, u.fechaCreacion 
                    FROM usuario_n u
                    INNER JOIN usuario_rol ur
                    ON ur.idUsuario = u.id
                    INNER JOIN rol r
                    ON r.id = ur.idRol
                    WHERE r.nombre = :rolNombre;';   
            $param= array(':rolNombre'=>"Autorizador");  
            $data = DATA::Ejecutar($sql,$param);       
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