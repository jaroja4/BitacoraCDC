<?php
//ACTION
if( isset($_POST["action"])){        
    $opt= $_POST["action"];
    unset($_POST['action']);    
    // Classes
    require_once("Conexion.php");
    // 
    // Instance
    $tarjeta = new Tarjeta();
    switch($opt){
        case "ReadAll":
            echo json_encode($tarjeta->ReadAll());
            break;
        case "BuscarDisponible":
            echo json_encode($tarjeta->BuscarDisponible());
            break;
        case "Entregar":
            echo json_encode($tarjeta->Entregar());
            break;
        case "Recibir":
            echo json_encode($tarjeta->Recibir());
            break;
    }
}

class Tarjeta{
    public $id="";
    public $idDataCenter=""; 
    public $idSala="";  
    public $value="";            

    function __construct(){

        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }

        if(isset($_POST["value"])){
            $this->value= $_POST["value"];
        }
        
        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
        }
    }

    function ReadAll(){
        try {
            $sql='SELECT t.id, t.idSala, s.nombre nombreSala, t.estado, t.consecutivo 
            FROM tarjeta t
            INNER JOIN sala s
            ON s.id = t.idSala
            ORDER BY t.consecutivo ASC;';  
            $data = DATA::Ejecutar($sql);       
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

    function BuscarDisponible(){
        try {
            $sql = 'SELECT id, consecutivo, estado
                    FROM tarjeta
                    WHERE idSala = :idSala
                    AND estado = 0
                    ORDER BY consecutivo ASC
                    LIMIT 1;';
                $param= array(':idSala'=>$this->idSala);            
                $data= DATA::Ejecutar($sql, $param);
                if ($data){
                    return $data[0];;
                }
                else
                    return "notarjeta";

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

    function Entregar(){
        try {
            $sql= 'UPDATE tarjeta
                    SET estado = 1
                    WHERE id = :id;';
            $param= array(':id'=>$this->id);            
            $data= DATA::Ejecutar($sql, $param, false);
            if ($data){
                if(isset($_POST["obj"])){
                    require_once("Bitacora.php");
                    $bitacora = new Bitacora;
                    $obj= json_decode($_POST["obj"],true);
                    $bitacora->idFormulario = $obj["id"];   
                    $bitacora->idVisitante = $obj["idVisitante"];   
                    $bitacora->idTarjeta = $this->id; 
                    return $bitacora->Create(); 
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

    function Recibir(){
        try {
            if( strlen( $this->value ) > 4){
                $sql = 'SELECT b.id, b.idFormulario, b.idVisitante, b.idTarjeta, b.entrada FROM bitacora b
                    INNER JOIN usuario_n u
                    ON u.id = b.idVisitante
                    WHERE u.cedula = :cedula
                    AND salida IS NULL
                    ORDER BY entrada DESC;';
                $param= array(':cedula'=>$this->value);            
                $dataFormulario= DATA::Ejecutar($sql, $param, true);

                if ($dataFormulario){

                    $sql= 'UPDATE bitacora
                        SET salida = NOW()
                        WHERE id = :id;';
                    $param= array(':id'=>$dataFormulario[0]["id"]);            
                    $data= DATA::Ejecutar($sql, $param, false);


                    $sql= 'UPDATE tarjeta
                        SET estado = 0
                        WHERE id = :id;';
                    $param= array(':id'=>$dataFormulario[0]["idTarjeta"]);            
                    $data= DATA::Ejecutar($sql, $param, false);
                    if ($data){
                        return true;
                    }
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