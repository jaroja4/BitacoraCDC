<?php
		//ACTION
		if( isset($_POST["action"])){
    $opt= $_POST["action"];
    unset($_POST['action']);
    // Classes
    require_once("Conexion.php");
    // Instance
    $appLog = new AppLog();
    switch($opt){
        case "writeLog":
            echo json_encode($appLog->writeLog());
            break;
    }
}

class AppLog{
    public $idUsuario="";
    public $detalle="";

    function __construct(){

        if(isset($_POST["idusuario"])){
            $this->idusuario= $_POST["idusuario"];
        }
				require_once("UUID.php");
				$this->id= $obj["id"] ?? UUID::v4();
    }

    function WriteLog(){
        try {
    				// Classes
				    require_once("Conexion.php");
						$sql='INSERT INTO appLog (id, idUsuario, detalle)
									VALUES (:id, :idUsuario, :detalle);';
						$param= array(':id'=>UUID::v4(), ':idUsuario'=>$this->idUsuario, ':detalle'=>$this->detalle);
						$data = DATA::Ejecutar($sql,$param,false);

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
