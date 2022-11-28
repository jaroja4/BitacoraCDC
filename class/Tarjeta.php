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
        case "Create":
            echo json_encode($tarjeta->Create());
            break;
        case "Update":
            echo json_encode($tarjeta->Update());
            break;
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
        case "ReadBySala":
            echo json_encode($tarjeta->ReadBySala());
            break;
        case "Check_usuario_has_tarjeta":
            echo json_encode($tarjeta->Check_usuario_has_tarjeta());
            break;
        case "ValidarTarjeta":
            echo json_encode($tarjeta->ValidarTarjeta());
            break;
        case "Search_by_id":
            echo json_encode($tarjeta->Search_by_id());
            break;
    }
}

class Tarjeta{
    public $id="";
    public $idDataCenter="";
    public $idSala="";
    public $value="";
    public $consecutivo="";

    function __construct(){

        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }

        if(isset($_POST["value"])){
            $this->value= $_POST["value"];
        }

        if(isset($_POST["obj"])){
						require_once("UUID.php");
            $obj= json_decode($_POST["obj"],true);
						$this->id = $obj["id"] ?? UUID::v4();
            $this->consecutivo = $obj["consecutivo"] ?? "";
            $this->idSala = $obj["idSala"] ?? "";
            $this->estado = $obj["estado"] ?? "";
        }
    }

		function Create(){
			try {
            $sql='INSERT INTO control_acceso_cdc_dbp.tarjeta (id, idSala, estado, consecutivo)
									VALUES (:id, :idSala, :estado, :consecutivo);';
            $param= array(':id'=>$this->id, ':idSala'=>$this->idSala, ':estado'=>$this->estado, ':consecutivo'=>$this->consecutivo);
            $data = DATA::Ejecutar($sql, $param, false);
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
		function Update(){
			try {
            $sql='UPDATE control_acceso_cdc_dbp.tarjeta
									SET
									idSala = :idSala,
									estado = :estado,
									consecutivo = :consecutivo
									WHERE id = :id;';
						$param= array(':id'=>$this->id, ':idSala'=>$this->idSala, ':estado'=>$this->estado,
									':consecutivo'=>$this->consecutivo,);
            $data = DATA::Ejecutar($sql, $param, false);
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

		function Search_by_id(){
			try {

            $sql='SELECT t.id, t.estado, t.consecutivo, t.idSala,	s.nombre nombreSala, dc.id idDC, dc.nombre nombreDC
									FROM tarjeta t
									INNER JOIN sala s
									ON s.id = t.idSala
									INNER JOIN dataCenter dc
									ON dc.id = s.idDataCenter
									WHERE t.id = :id;';
            $param= array(':id'=>$this->id);
            $data = DATA::Ejecutar($sql, $param);
						if($data){
								return $data[0];
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


		function Check_usuario_has_tarjeta(){
        try {
            $sql = 'SELECT * FROM control_acceso_cdc_dbp.usuario_has_tarjeta
											WHERE idUsuario = :idUsuario;';
                $param= array(':idUsuario'=>json_decode($this->id));
                $data= DATA::Ejecutar($sql, $param);
                if (isset($data[0]["idTarjeta"])){
									$sql = 'SELECT t.id t_id, t.estado t_estado, t.consecutivo t_consecutivo, s.id s_id, s.nombre s_nombre, d.id d_id, d.nombre d_nombre
												FROM tarjeta t
												INNER JOIN sala s
												ON s.id = t.idSala
												INNER JOIN dataCenter d
												ON d.id = s.idDataCenter
												WHERE t.id = :idTarjeta
												ORDER BY t.consecutivo ASC;';
									$param= array(':idTarjeta'=>$data[0]["idTarjeta"]);
									$data= DATA::Ejecutar($sql, $param);
									if ($data){
                    return $data[0];
									}
									else
											return "error";
								}
								else
										return false;


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

    function ReadAll(){
        try {
            $sql='SELECT t.id, t.idSala, t.estado, t.consecutivo,
                    s.nombre nombreSala,
                    dc.nombre nombreDC
                FROM tarjeta t
                INNER JOIN sala s
                ON s.id = t.idSala
                INNER JOIN dataCenter dc
                ON dc.id = s.idDataCenter
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

    function ReadBySala(){
        try {
						$idSala = json_decode($_POST["obj"],true);
            $sql='SELECT t.id, t.estado, t.consecutivo, s.nombre nombreSala, s.id idSala
								FROM tarjeta t
								INNER JOIN sala s
								ON s.id = t.idSala
								WHERE s.id = :id
								ORDER BY t.consecutivo ASC;';
            $param= array(':id'=>$idSala);
            $data = DATA::Ejecutar($sql, $param);
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
                    return $data[0];
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

		function BuscarAsignada($idUsuario){
			try {
					$sql = 'SELECT uht.idUsuario, uht.idTarjeta id, t.idSala, t.estado, t.consecutivo
									FROM usuario_has_tarjeta uht
									INNER JOIN tarjeta t
									ON uht.idTarjeta = t.id
									WHERE uht.idUsuario = :idUsuario';
					$param= array(':idUsuario'=>$idUsuario);
					$data= DATA::Ejecutar($sql, $param);
					if ($data){
							return $data[0];
					}
					else
							return false;

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
                return $this->RecibirConCedula();
            }
            else{
                return $this->RecibirConTarjeta();
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

    function RecibirConCedula(){
        try {
            $sql = 'SELECT b.id, b.idFormulario, b.idVisitante, b.idTarjeta, b.entrada
                FROM bitacora b
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


								$sql = 'SELECT u.usuario, u.cedula, u.nombre, t.consecutivo
										FROM usuario_n u
										INNER JOIN usuario_has_tarjeta ut
										ON u.id = ut.idUsuario
										WHERE cedula = :cedula;';
								$param= array(':cedula'=>$this->value);
                $tarjetaAsignada= DATA::Ejecutar($sql, $param, false);

								$liberaTarjeta = true;
								if (isset($tarjetaAsignada[0]["consecutivo"])){
									if( $tarjetaAsignada[0]["consecutivo"] != $dataFormulario[0]["idTarjeta"]){
										$liberaTarjeta = false;
									}
								}

								if ($liberaTarjeta){
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

    function RecibirConTarjeta(){
        try {
            $sql = 'SELECT b.id, b.idFormulario, b.idVisitante, b.idTarjeta, b.entrada
            FROM bitacora b
            INNER JOIN usuario_n u
            ON u.id = b.idVisitante
            INNER JOIN tarjeta t
            ON t.id = b.idTarjeta
            WHERE t.consecutivo = :consecutivo
            AND salida IS NULL
            ORDER BY entrada DESC;';
            $param= array(':consecutivo'=>$this->value);
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
        catch(Exception $e) {
            error_log("[ERROR]  (".$e->getCode()."): ". $e->getMessage());
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar la lista'))
            );
        }
    }

		function ValidarTarjeta(){
			 try {
             $sql = 'SELECT id, consecutivo
								FROM tarjeta
								WHERE consecutivo = :consecutivo
								LIMIT 1;';
                $param= array(':consecutivo'=>$this->consecutivo);
                $data= DATA::Ejecutar($sql, $param);
                if ($data){
                    return $data[0];
                }
                else
                    return 'noexiste';

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
