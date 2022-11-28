<?php
if(isset($_POST["action"])){
    $opt= $_POST["action"];
    unset($_POST['action']);
    // Classes
    require_once("Conexion.php");
    // Session
    if (!isset($_SESSION))
        session_start();
    // Instance
    $evento= new Evento();
    switch($opt){
        case "ReadAll":
            echo json_encode($evento->ReadAll());
            break;
        case "Read":
            echo json_encode($evento->Read());
            break;
        case "readEventosxRol":
            echo json_encode($evento->readEventosxRol());
            break;
    }
}
class Evento{
    public $id;
    public $nombre;
    public $modulo;
    public $iconoModulo;
    public $menu;
    public $iconoMenu;
    public $opcion;


    function __construct(){

        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }

        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
            $this->id= $obj["id"] ?? UUID::v4();
        }
    }

    function ReadAll(){
        try {
            $sql='SELECT id, url, modulo, iconoModulo, menu, iconoMenu, opcion
                FROM     evento
                ORDER BY modulo asc';
            $data= DATA::Ejecutar($sql);
            return $data;
        }
        catch(Exception $e) { error_log("[ERROR]  (".$e->getCode()."): ". $e->getMessage());
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar la lista'))
            );
        }
    }

		function readEventosxRol(){
        try {
            $sql='SELECT e.id as idEvento, e.opcion
									FROM control_acceso_cdc_dbp.eventoXRol ev
									INNER JOIN evento e
									ON ev.idEvento = e.id
									WHERE idRol = :idRol
                	ORDER BY modulo asc';
            $param= array(':idRol'=>$this->id);
            $data= DATA::Ejecutar($sql, $param);
            return $data;
        }
        catch(Exception $e) { error_log("[ERROR]  (".$e->getCode()."): ". $e->getMessage());
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar la lista'))
            );
        }
    }
}
?>
