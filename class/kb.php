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
        case "ReadAll":
            echo json_encode($sala->ReadAll());
            break;
    }
}

class Sala{
    public $fechaInicio="";
    public $fechaFin="";
		public $proyecto="";

    function __construct(){
        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
            require_once("UUID.php");
            $this->id= $obj["id"] ?? UUID::v4();
        }
    }

    function ReadAll(){
        try {
						$sql='SELECT t.id, t.title, month(FROM_UNIXTIME(t.date_creation)) date_creation, t.creator_id c_id, t.owner_id o_id, u.name creator, o.name owner
							FROM kanboard.tasks t
							INNER JOIN users u
							ON t.creator_id = u.id
							INNER JOIN users o
							ON t.owner_id = o.id
							WHERE project_id = 18
							AND date_creation BETWEEN 1641016800 AND 1667282400
							GROUP BY date_creation DESC;';
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
