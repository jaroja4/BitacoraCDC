<?php
//ACTION
if( isset($_POST["action"])){
    $opt= $_POST["action"];
    unset($_POST['action']);
    // Classes
    require_once("Conexion.php");
    //
    // Instance
    $tramitante = new Tramitante();
    switch($opt){
        case "Search":
            $tramitante->search_value= $_POST["search_value"] ?? "";
            echo json_encode($tramitante->Search());
            break;
        case "ReadAll_list":
            echo json_encode($tramitante->ReadAll_list());
            break;
    }
}

class Tramitante{
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
                        ur.idRol = "2"
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

    function ReadAll_list(){
        try {
            $sql='SELECT u.id, u.usuario, u.cedula, u.nombre, u.correo, u.empresa, u.fechaCreacion
                FROM usuario_n u
                INNER JOIN usuario_rol ur
                ON ur.idUsuario = u.id
                INNER JOIN rol r
                ON r.id = ur.idRol
                WHERE r.nombre = "Tramitante";';
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
