<?php
//ACTION
if( isset($_POST["action"])){        
    $opt= $_POST["action"];
    unset($_POST['action']);    
    // Classes
    require_once("Conexion.php");
    // 
    // Instance
    $responsable = new Responsable();
    switch($opt){
        case "ReadAll":
            $responsable->search_value= $_POST["search_value"] ?? "";
            echo json_encode($responsable->ReadAll());
            break;
        case "ReadbyID":
            $responsable->id= $_POST["id"] ?? "";
            echo json_encode($responsable->ReadbyID());
            break;
    }
}

class Responsable{
    public $id="";
    public $search_value="";

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
            $sql='SELECT un.id, un.nombre text FROM usuario_n un
                INNER JOIN usuario_rol ur
                ON ur.idUsuario = un.id
                WHERE ur.idRol = "bcec0ea4-4a0b-4e69-b9f5-744530a765ed"
                AND (un.nombre LIKE "%' . $this->search_value . '%"
                    OR cedula LIKE "%' . $this->search_value . '%")
                    ORDER BY un.nombre ASC
                    LIMIT 11;';
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

    function ReadbyID(){
        try {
            $sql='SELECT un.id, un.nombre text FROM usuario_n un
                INNER JOIN usuario_rol ur
                ON ur.idUsuario = un.id
                WHERE ur.idRol = "bcec0ea4-4a0b-4e69-b9f5-744530a765ed"
                AND un.id = :id';
            $param= array(':id'=>$this->id);            
            $data= DATA::Ejecutar($sql, $param);               
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