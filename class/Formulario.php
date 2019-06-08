<?php
//ACTION
if( isset($_POST["action"])){        
    $opt= $_POST["action"];
    unset($_POST['action']);    
    // Classes
    require_once("Conexion.php");
    // 
    // Instance
    $formulario = new Formulario();
    switch($opt){
        case "ReadAllbyRange":
            echo json_encode($formulario->ReadAllbyRange());
            break;
        case "Create":
            echo json_encode($formulario->Create());
            break;
        case "ReadbyID":
            echo json_encode($formulario->ReadbyID());
            break;
        case "Update":
            echo json_encode($formulario->Update());
            break;
        case "Buscar":
            echo json_encode($formulario->Buscar($_POST['value'], $_POST['idDataCenter']));
            break;
    }
}

class Formulario{
    //Componente
    public $id="";
    public $idEstado="";
    public $idDataCenter;
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
    public $arrayVisitantes = [];    
    public $otrosDetalles =[];

    function __construct(){

        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }
        
        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
            
            require_once("UUID.php");
                        
            $this->id= $obj["id"] ?? UUID::v4();
            $this->idDataCenter = $obj["idDataCenter"] ?? "";
            $this->idEstado = $obj["idEstado"] ?? "";
            $this->idSala = $obj["idSala"] ?? "";
            $this->idTramitante = $obj["idTramitante"] ?? "";
            $this->idAutorizador = $obj["idAutorizador"] ?? "";
            $this->idResponsable = $obj["idResponsable"] ?? "";
            $this->consecutivo = $obj["consecutivo"] ?? "";
            $this->fechaSolicitud = $obj["fechaSolicitud"] ?? "";
            $this->fechaIngreso = $obj["fechaIngreso"] ?? "";
            $this->fechaSalida = $obj["fechaSalida"] ?? "";
            $this->motivoVisita = $obj["motivoVisita"] ?? "";
            $this->otrosDetalles = $obj["otrosDetalles"] ?? "";
            $this->arrayVisitantes= $obj["arrayVisitantes"] ?? [];
            $this->read_fechaInicial= $obj["read_fechaInicial"] ?? null;
            $this->read_fechaFinal= $obj["read_fechaFinal"] ?? null;
        }
    }

    
    function Buscar($cedula, $idDataCenter){        
        try { 
            $sql = 'SELECT f.id, f.idEstado, f.consecutivo, f.fechaSolicitud, 
                (SELECT nombre FROM usuario_n where id = f.idAutorizador) autorizador, 
                f.fechaIngreso, f.fechaSalida, dc.nombre dataCenter, s.id idSala, 
                s.nombre sala, u.cedula, u.nombre, u.empresa
                    FROM formulario f
                    INNER JOIN visitante_formulario vf
                    ON vf.idFormulario = f.id
                    INNER JOIN usuario_n u
                    ON u.id = idVisitante
                    INNER JOIN sala s
                    ON s.id = f.idSala
                    INNER JOIN dataCenter dc
                    ON s.idDataCenter = dc.id
                    WHERE u.cedula = :cedula
                    AND DATE_ADD(now(), INTERVAL 1 HOUR) > f.fechaIngreso 
                    AND NOW() < f.fechaSalida 
                    AND f.idEstado = 1
                    AND dc.id = :idDataCenter
                    LIMIT 1;';
            $param= array(':cedula'=>$cedula,       
                        ':idDataCenter'=>$idDataCenter);            
            $data= DATA::Ejecutar($sql, $param);
            if ($data){
                $this->id = $data[0]["id"];
                $this->idEstado = $data[0]["idEstado"];
                $this->consecutivo = $data[0]["consecutivo"];
                $this->fechaSolicitud = $data[0]["fechaSolicitud"];
                $this->fechaIngreso = $data[0]["fechaIngreso"];
                $this->fechaSalida = $data[0]["fechaSalida"];
                $this->dataCenter = $data[0]["dataCenter"];
                $this->idSala = $data[0]["idSala"];
                $this->sala = $data[0]["sala"];


                $sql = 'SELECT id, consecutivo, estado
                    FROM tarjeta
                    WHERE idSala = :idSala
                    AND estado = 0
                    ORDER BY consecutivo ASC
                    LIMIT 1;';
                $param= array(':idSala'=>$data[0]["idSala"]);            
                $data= DATA::Ejecutar($sql, $param);
                if ($data){
                    $this->consecutivoTarjeta = $data[0]["consecutivo"];
                    $sql= 'UPDATE tarjeta
                        SET estado = 1
                        WHERE id = :id;';
                    $param= array(':id'=>$data[0]["id"]);            
                    $data= DATA::Ejecutar($sql, $param, false);
                    return $this;
                }
                return "notarjeta";
            }
            return "noformulario";
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

    function Update(){
        try {
            $sql="UPDATE formulario 
                SET
                idEstado = :idEstado,
                idSala = :idSala,
                idTramitante = :idTramitante,
                idAutorizador = :idAutorizador,
                idResponsable = :idResponsable,
                fechaSolicitud = :fechaSolicitud,
                fechaIngreso = :fechaIngreso,
                fechaSalida = :fechaSalida,
                motivoVisita = :motivoVisita,
                otrosDetalles = :otrosDetalles
                WHERE id= :id;";
            $param= array(':id'=>$this->id, 
                            ':idEstado'=>$this->idEstado, 
                            ':idSala'=>$this->idSala, 
                            ':idTramitante'=>$this->idTramitante, 
                            ':idAutorizador'=>$this->idAutorizador, 
                            ':idResponsable'=>$this->idResponsable, 
                            ':fechaSolicitud'=>$this->fechaSolicitud, 
                            ':fechaIngreso'=>$this->fechaIngreso, 
                            ':fechaSalida'=>$this->fechaSalida, 
                            ':motivoVisita'=>$this->motivoVisita, 
                            ':otrosDetalles'=>$this->otrosDetalles);            
            $data= DATA::Ejecutar($sql, $param, false);
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
            $this->id = UUID::v4();
            $sql="INSERT INTO formulario 
                (id, idEstado, idSala, idTramitante, idAutorizador, 
                idResponsable, fechaSolicitud, fechaIngreso,
                fechaSalida, motivoVisita, otrosDetalles)
                VALUES (:id, :idEstado, :idSala, :idTramitante, :idAutorizador, 
                    :idResponsable, :fechaSolicitud, :fechaIngreso, 
                    :fechaSalida, :motivoVisita, :otrosDetalles)";
            $param= array(':id'=>$this->id, 
                            ':idEstado'=>$this->idEstado, 
                            ':idSala'=>$this->idSala, 
                            ':idTramitante'=>$this->idTramitante, 
                            ':idAutorizador'=>$this->idAutorizador, 
                            ':idResponsable'=>$this->idResponsable, 
                            ':fechaSolicitud'=>$this->fechaSolicitud, 
                            ':fechaIngreso'=>$this->fechaIngreso, 
                            ':fechaSalida'=>$this->fechaSalida, 
                            ':motivoVisita'=>$this->motivoVisita, 
                            ':otrosDetalles'=>$this->otrosDetalles);            
            $data= DATA::Ejecutar($sql, $param, false);

            foreach ($this->arrayVisitantes as $visitante) {
                $sql="INSERT INTO visitante_formulario 
                (idVisitante, idFormulario)
                VALUES (:idVisitante, :idFormulario)";
                $param= array(':idVisitante'=>$visitante, ':idFormulario'=>$this->id);            
                $data= DATA::Ejecutar($sql, $param);   
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
                        f.fechaSalida,
                        f.motivoVisita,
                        f.otrosDetalles
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

    function ReadbyID(){        
        try {
            $sql="SELECT 	f.id, f.idEstado, f.idSala, s.idDataCenter, f.idTramitante, f.idAutorizador, 
                        f.idResponsable, f.consecutivo, f.fechaSolicitud, f.fechaIngreso,
                        f.fechaSalida, f.motivoVisita, f.otrosDetalles
                FROM formulario f
                INNER JOIN sala s
                ON f.idSala = s.id
                WHERE f.id = :id;";
            $param= array(':id'=>$this->id);            
            $data= DATA::Ejecutar($sql, $param);   
            if ($data){

                $this->id = $data[0]["id"];
                $this->idEstado = $data[0]["idEstado"];
                $this->idSala = $data[0]["idSala"];
                $this->idDataCenter = $data[0]["idDataCenter"];
                $this->idTramitante = $data[0]["idTramitante"];
                $this->idAutorizador = $data[0]["idAutorizador"];
                $this->idResponsable = $data[0]["idResponsable"];
                $this->consecutivo = $data[0]["consecutivo"];
                $this->fechaSolicitud = $data[0]["fechaSolicitud"];
                $this->fechaIngreso = $data[0]["fechaIngreso"];
                $this->fechaSalida = $data[0]["fechaSalida"];
                $this->motivoVisita = $data[0]["motivoVisita"];
                $this->otrosDetalles = $data[0]["otrosDetalles"];

                $sql="SELECT v.id, v.cedula, v.nombre text, v.empresa, v.fechaCreacion 
                FROM usuario_n v
                INNER JOIN visitante_formulario vf
                ON vf.idVisitante = v.id
                WHERE vf.idFormulario = :idFormulario;";
                $param= array(':idFormulario'=>$this->id);            
                $data= DATA::Ejecutar($sql, $param);   
                if ($data){
                    $this->arrayVisitantes=[];
                    foreach ($data as $value) {
                        array_push ( $this->arrayVisitantes , $value );
                    }
                    return $this;
                }
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