<?php
//ACTION
if( isset($_POST["action"])){        
    $opt= $_POST["action"];
    unset($_POST['action']);    
    // Classes
    require_once("Conexion.php");
    // 
    // Instance
    $sala = new Sala();
    switch($opt){
        case "ReadSalabyDC":
            echo json_encode($sala->ReadSalabyDC());
            break;
    }
}

class Sala{
    public $id="";
    public $idDataCenter="";            

    function __construct(){

        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }
        if(isset($_POST["idDataCenter"])){
            $this->idDataCenter= $_POST["idDataCenter"];
        }
        
        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
            
            require_once("UUID.php");
            $this->id= $obj["id"] ?? UUID::v4();
        }
    }

    function ReadSalabyDC(){
        try {
            $sql='SELECT id, nombre FROM sala
                WHERE idDataCenter = :idDataCenter;';   
            $param= array(':idDataCenter'=>$this->idDataCenter);  
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