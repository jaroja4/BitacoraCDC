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
    }
}
class Evento{
    public $id;
    public $nombre;
    public $url;
    public $modulo;
    public $iconoModulo;
    public $menu;
    public $iconoMenu;
    public $opcion;

    function ReadAll(){
        try {
            $sql='SELECT id, nombre, url, modulo, iconoModulo, menu, iconoMenu, opcion
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
}
?>