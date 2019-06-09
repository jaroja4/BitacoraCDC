<?php
//ACTION
if( isset($_POST["action"])){        
    $opt= $_POST["action"];
    unset($_POST['action']);    
    // Classes
    require_once("Conexion.php");
    // 
    // Instance
    $bitacora = new Bitacora();
    switch($opt){
        case "Create":
            echo json_encode($bitacora->Create());
            break;
        case "ReadbyRange":
            echo json_encode($bitacora->ReadbyRange());
            break;
    }
}

class Bitacora{
    public $id="";   
    public $read_fechaInicial="";   
    public $read_fechaFinal="";   
    public $ReadAllbyRange="";   
    public $entrada="";   
    public $salida="";  


    function __construct(){

        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }
        
        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
            $this->id=$obj["id"] ?? NULL;   
            $this->nombre=$obj["nombre"] ?? NULL;   
            $this->read_fechaInicial=$obj["read_fechaInicial"] ?? NULL;   
            $this->read_fechaFinal=$obj["read_fechaFinal"] ?? NULL;
            
            require_once("UUID.php");
            $this->id= $obj["id"] ?? UUID::v4();
        }
    }

    function ReadbyRange(){
        try {
            $sql="SELECT id, idFormulario, idVisitante, idTarjeta, entrada, salida FROM bitacora b
                WHERE (entrada BETWEEN :read_fechaInicial AND :read_fechaFinal);";
            $param= array(':read_fechaInicial'=>$this->read_fechaInicial, ':read_fechaFinal'=>$this->read_fechaFinal);            
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

    function Create(){
        try {
            $sql='INSERT INTO bitacora (id, idFormulario, idVisitante,idTarjeta, entrada, salida) 
                VALUES (UUID(), :idFormulario, :idVisitante, :idTarjeta, NOW(), NULL);'; 
            $param= array(':idFormulario'=>$this->idFormulario, 
                ':idVisitante'=>$this->idVisitante, ':idTarjeta'=>$this->idTarjeta);            
            $data= DATA::Ejecutar($sql, $param);   
            
            return true;
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