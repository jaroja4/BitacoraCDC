<?php
//ACTION
if( isset($_POST["action"])){        
    $opt= $_POST["action"];
    unset($_POST['action']);    
    // Classes
    require_once("Conexion.php");
    // 
    // Instance
    $tarjeta = new Tarjeta();
    switch($opt){
        case "ReadAll":
            echo json_encode($tarjeta->ReadAll());
            break;
    }
}

class Tarjeta{
    public $id="";
    public $idDataCenter="";            

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
            $sql='SELECT t.id, t.idSala, s.nombre nombreSala, t.estado, t.consecutivo 
            FROM tarjeta t
            INNER JOIN sala s
            ON s.id = t.idSala
            ORDER BY t.consecutivo ASC;';  
            $data = DATA::Ejecutar($sql);       
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