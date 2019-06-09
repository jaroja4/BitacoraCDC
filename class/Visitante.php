<?php
//ACTION
if( isset($_POST["action"])){        
    $opt= $_POST["action"];
    unset($_POST['action']);    
    // Classes
    require_once("Conexion.php");
    // 
    // Instance
    $visitante = new Visitante();
    switch($opt){
        case "Search":        
            $visitante->search_value= $_POST["search_value"] ?? "";
            echo json_encode($visitante->Search());
            break;
    }
}

class Visitante{
    //Componente
    public $id="";
    public $search_value="";
    public $idEstado="";
    public $idSala="";
    public $idTramitante="";
    public $idAutorizador="";
    public $idResponsable="";
    public $consecutivo="";
    public $fechaSolicitud="";
    public $fechaIngreso="";
    public $fechaSalida="";
    public $placa="";
    public $activos="";
    public $motivoVisita="";
    public $RFC=""; 
    public $read_fechaInicial = "";
    public $read_fechaFinal = "";    
    
           

    function __construct(){

        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }
        
        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
            
            require_once("UUID.php");
            $this->id= $obj["id"] ?? UUID::v4();
            $this->search_value= $obj["search_value"] ?? UUID::v4();
            // $this->read_fechaInicial= $obj["read_fechaInicial"] ?? null;
            // $this->read_fechaFinal= $obj["read_fechaFinal"] ?? null;
        }
    }

    function Search(){
        try {
            $sql='SELECT u.id, u.cedula, u.nombre text, u.empresa, u.fechaCreacion FROM usuario_n u
                    INNER JOIN usuario_rol ur
                    ON ur.idUsuario = u.id
                    WHERE 
                        ur.idRol = "c4d35117-d57e-4cad-b3bc-b589f002814d"
                        AND (
                        cedula LIKE "%' . $this->search_value . '%" OR 
                        nombre LIKE "%' . $this->search_value . '%" OR 
                        empresa LIKE "%' . $this->search_value . '%")
                    ORDER BY u.nombre DESC
                    LIMIT 10;';            
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

    function ReadAllbyRange(){
        try {
            $sql="SELECT f.id,
                        f.consecutivo,
                        e.nombre Estado,
                        f.fechaSolicitud,
                        f.fechaIngreso,
                        f.motivoVisita,
                        f.RFC
                    FROM
                        formulario f
                            INNER JOIN
                        estado e ON f.idEstado = e.id
                    WHERE (fechaSolicitud BETWEEN :read_fechaInicial AND :read_fechaFinal);";
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
}

?>