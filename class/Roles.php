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
        case "LoadbyUser":
            echo json_encode($roles->LoadbyUser());
            break;
        case "Update":
            echo json_encode($roles->Update());
            break;
    }
}

class Roles{
    //Componente
    public $id="";
    public $nombre="";
    public $listaEventos=array();



    function __construct(){

        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }
        if(isset($_POST["idUsuario"])){
            $this->idUsuario= $_POST["idUsuario"];
        }

        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
            $this->id= $obj["id"] ?? false;
            $this->nombre= $obj["nombre"] ?? false;
						if(isset($obj["eventosXrol"] )){
                require_once("Evento.php");
                foreach ($obj["eventosXrol"] as $evento) {
                    array_push($this->listaEventos, $evento);
                }
            }
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


    function LoadbyUser(){
        try {
            $sql='SELECT idRol
                FROM usuario_rol
                WHERE idUsuario = :idUsuario;';
            $param= array(':idUsuario'=>$this->idUsuario);
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

    function Update(){
        try {

            $sql="DELETE FROM control_acceso_cdc_dbp.eventoXRol
									WHERE idRol = :idRol and idEvento LIKE '%-%';";
            $param= array(':idRol'=>$this->id);
            $data= DATA::Ejecutar($sql, $param);
						
						foreach ($this->listaEventos as $evento) {
							$sql="INSERT INTO control_acceso_cdc_dbp.eventoXRol (idEvento, idRol)
									VALUES (:idEvento, :idRol);";
							$param= array(':idEvento'=>$evento, ':idRol'=>$this->id);
							$data= DATA::Ejecutar($sql, $param);
						}

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
