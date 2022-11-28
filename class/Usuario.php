<?php
setlocale(LC_ALL,"es_ES");
date_default_timezone_set('America/Costa_Rica');
mb_internal_encoding('UTF-8');

if(isset($_POST["action"])){
    $opt= $_POST["action"];
    unset($_POST['action']);
    // Classes
    require_once("Conexion.php");
    require_once("Evento.php");
    // Session
    if (!isset($_SESSION))
        session_start();
    // Instance
    $usuario= new Usuario();
    switch($opt){
        case "Login":
            $usuario->email= $_POST["correo"];
            $usuario->password= $_POST["password"];
            echo json_encode($usuario->Login());
            break;
        case "CheckSession":
            $usuario->CheckSession();
            echo json_encode($_SESSION['userSession']);
            break;
        case "EndSession":
            $usuario->EndSession();
            break;
        case "ReadAll":
            echo json_encode($usuario->ReadAll());
            break;
        case "Create":
            echo json_encode($usuario->Create());
            break;
        case "Update":
            echo json_encode($usuario->Update());
            break;
        case "Delete":
            echo json_encode($usuario->Delete());
            break;
        case "load_inp_doc":
            echo json_encode($usuario->load_inp_doc());
            break;
        case "load_img_visitante":
            echo json_encode($usuario->load_img_visitante());
            break;
        case "UpdatePasswd":
            echo json_encode($usuario->UpdatePasswd());
            break;
        case "CheckToken":
            echo json_encode($usuario->CheckToken());
            break;
    }
}

abstract class userSessionStatus
{
    const invalido = 'invalido'; // login invalido
    const login = 'login'; // login ok; credencial ok
    const nocredencial= 'nocredencial'; // login ok; sin credenciales
    const inactivo= 'inactivo';
    const noexiste= 'noexiste';
    const cambiarPWD = 'cambiarPWD';
    const noip= 'noip';
}

class Usuario{
    public $id;
    public $username;
    public $password;
    public $authDetail;
    public $nombre;
    public $email;
    public $cedula;
    public $activo = 0;
    public $status = 0;
    public $listarol= array(); // array de roles del usuario.
    public $eventos= array(); // array de eventos asignados a la sesion de usuario.
    public $url;
    public $empresa;
    public $rol;
    public $img_visitante;
    public $inp_doc;
    public $newToken = '';

    function __construct(){
        // identificador único
        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }
        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
            require_once("UUID.php");
            $this->id= $obj["id"] ?? UUID::v4();
            $this->nombre= $obj["nombre"] ?? '';
            $this->username= $obj["usuario"] ?? '';
            $this->password= $obj["passwd"] ?? '';
            $this->authDetail= $obj["authDetail"] ?? '';
            $this->cedula= $obj["cedula"] ?? '';
            $this->email= $obj["correo"] ?? '';
            $this->telephonenumber= $obj["telephonenumber"] ?? '';
            $this->empresa= $obj["empresa"] ?? '';
            $this->estado= $obj["estado"] ?? '';
            $this->rol= $obj["rol"] ?? '';
            $this->inp_doc= $obj["inp_doc"] ?? '';
            $this->img_visitante= $obj["img_visitante"] ?? '';
            $this->idTarjeta= $obj["idTarjeta"] ?? '';
            $this->tarjeta_fija= $obj["tarjeta_fija"] ?? '';
            file_put_contents('../images/pic_user.png', $obj["img_visitante"] ?? '');

            //roles del usuario.
            if(isset($obj["listarol"] )){
                require_once("RolesXUsuario.php");
                //
                foreach ($obj["listarol"] as $idRol) {
                    $rolUsr= new RolesXUsuario();
                    $rolUsr->idRol= $idRol;
                    $rolUsr->idUsuario= $this->id;
                    array_push ($this->listarol, $rolUsr);
                }
            }
        }
    }

    function ValidarUsuarioLDAP (){
        $LDAP_servicio = DATA::getLDAP_Param();
        $LDAP_connect = ldap_connect($LDAP_servicio["LDAP_server"], $LDAP_servicio["LDAP_port"]);
        $LDAP_bind = @ldap_bind($LDAP_connect, $LDAP_servicio["LDAP_user"], $LDAP_servicio["LDAP_passwd"]);
        if ($LDAP_bind) {
            $LDAP_filter="(mail=$this->email)";
            $search_result=ldap_search($LDAP_connect,$LDAP_servicio["LDAP_base_dn"],$LDAP_filter);
            $LDAP_user_data = ldap_get_entries($LDAP_connect, $search_result);
            if($LDAP_user_data["count"] < 1){
                @ldap_close($LDAP_connect);
                return false;
            }
            $this->dn = utf8_encode($LDAP_user_data[0]["dn"]);
            $this->email= utf8_encode($LDAP_user_data[0]["mail"][0]);
            $this->nombre = utf8_encode($LDAP_user_data[0]["cn"][0]);
            $this->username = utf8_encode($LDAP_user_data[0]["samaccountname"][0]);
            // $this::BuscaRol();
            @ldap_close($LDAP_connect);

            $LDAP_connect = ldap_connect($LDAP_servicio["LDAP_server"], $LDAP_servicio["LDAP_port"]);
            ldap_set_option($LDAP_connect, LDAP_OPT_PROTOCOL_VERSION, 3);
            $LDAP_bind = @ldap_bind($LDAP_connect, $this->dn, $this->password);
            @ldap_close($LDAP_connect);
            if ($LDAP_bind)
                return true;
            else
                return false;
        } else {
            error_log("Falla el Bind: " . ldap_error($ldap));
            return false;
        }
    }

    function ReadAll(){
        try {
            $sql="SELECT u.id, u.usuario, u.passwd, u.cedula, u.nombre, u.correo, u.empresa, u.fechaCreacion, u.telephonenumber,
                    (SELECT GROUP_CONCAT(r.nombre SEPARATOR ', ') roles
                    FROM rol r
                    INNER JOIN usuario_rol ur
                    ON ur.idRol = r.id
                    WHERE ur.idUsuario = u.id) roles
                FROM usuario_n u
                ORDER BY nombre ASC;";
            $data= DATA::Ejecutar($sql);
            foreach ($data as $i=>$value) {
                $data[$i]['telephonenumber'] = preg_replace( '/[\W]/', '', $data[$i]['telephonenumber']);
            }
            preg_replace( '/[\W]/', '', '88986761');
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

    function Create(){
        try {
            $sql='SELECT * FROM usuario_n
            WHERE (usuario like :usuario AND usuario <> "")
            OR (cedula like :cedula AND cedula <> "")
            OR (correo like :correo AND correo <> "");';
            $param= array(':usuario'=>$this->username, ':cedula'=>$this->cedula, ':correo'=>$this->email);
            $data = DATA::Ejecutar($sql,$param);

            if (!$data){
                $this->telephonenumber = preg_replace( '/[\W]/', '', $this->telephonenumber);
                if($this->authDetail["authType"] == "LDAP"){
                  $this->password = "LDAP";
                  $this->estado = "activo";
                }else if ($this->authDetail["authType"] == "LOCAL"){
                  $newPsswd = $this->generateRandomPasswd();
                  require_once("SMS.php");
                  $sms = new SMS();
                  $sms->token= "tokenPRD";
                  $sms->num= $this->telephonenumber;
                  $sms->subject= "Clave de ingreso al SIDC";
                  $sms->message= "Su clave es: ".$newPsswd;
                  $sms->sendSMS();
                  $this->estado = "cambiarPWD";
                  $this->password = $newPsswd;
                }
                $dataLog = [];
                $sql='INSERT INTO usuario_n (id, usuario, passwd, cedula, nombre, correo, telephonenumber, empresa, estado, fechaCreacion, documento, imagen)
                    VALUES (:id, :usuario, :passwd, :cedula, :nombre, :correo, :telephonenumber, :empresa, :estado, NOW(), :documento, :imagen)';
                $param= array(':id'=>$this->id, ':usuario'=>$this->username, ':passwd'=>$this->password,
                    ':cedula'=>$this->cedula, ':nombre'=>$this->nombre, ':correo'=>$this->email, ':telephonenumber'=>$this->telephonenumber,
                    ':empresa'=>$this->empresa, ':estado'=>$this->estado, ':documento'=>$this->inp_doc, ':imagen'=>$this->img_visitante );
                $data = DATA::Ejecutar($sql,$param);

                array_push($dataLog, $param);

                if($this->rol?true:false){
                    $logRol = [];
                    foreach ($this->rol as $idRol) {
                        $sql='INSERT INTO usuario_rol (idRol, idUsuario) VALUES (:idRol, :idUsuario);';
                        $param= array(':idRol'=>$idRol, ':idUsuario'=>$this->id);
                        $data = DATA::Ejecutar($sql,$param,false);
                        array_push($logRol, $param);
                    }
                    $dataLog[0]["rol"] = $logRol;
                }
                require_once("Log.php");
                $appLog = new AppLog();
                $appLog->idUsuario = $_SESSION['userSession']->id;
                unset($dataLog[0][':imagen']);
                $appLog->detalle = "idUsuario: ".$_SESSION['userSession']->id. "; Action: Create; Detail: ".json_encode(array_values($dataLog));

                $appLog->WriteLog();



                if($this->tarjeta_fija){
                    $sql='SELECT idUsuario, idTarjeta
                          FROM control_acceso_cdc_dbp.usuario_has_tarjeta
                          WHERE idUsuario = :idUsuario;';
                    $param= array(':usuario'=>$this->id);
                    $data = DATA::Ejecutar($sql,$param);
                    if ($data){
                      $sql= 'UPDATE tarjeta
                            SET estado = 0
                            WHERE id = :idTarjeta;';
                      $param= array(':idTarjeta'=>$data["idTarjeta"]);
                      $data= DATA::Ejecutar($sql, $param, false);

                    }

                    $sql="DELETE FROM control_acceso_cdc_dbp.usuario_has_tarjeta
                        WHERE (idTarjeta != '' and idUsuario = :idUsuario);";
                    $param= array(':idUsuario'=>$this->id);
                    $data = DATA::Ejecutar($sql,$param);

                    $sql="DELETE FROM control_acceso_cdc_dbp.usuario_has_tarjeta
                        WHERE (idTarjeta = :idTarjeta and idUsuario != '');";
                    $param= array(':idTarjeta'=>$this->idTarjeta);
                    $data = DATA::Ejecutar($sql,$param);


                    $sql='INSERT INTO control_acceso_cdc_dbp.usuario_has_tarjeta (idUsuario, idTarjeta) VALUES (:idUsuario, :idTarjeta);';
                    $param= array(':idUsuario'=>$this->id, ':idTarjeta'=>$this->idTarjeta);
                    $data = DATA::Ejecutar($sql,$param);

                    $sql= 'UPDATE tarjeta
                            SET estado = 1
                            WHERE id = :idTarjeta;';
                    $param= array(':idTarjeta'=>$this->idTarjeta);
                    $data= DATA::Ejecutar($sql, $param, false);
                }

                return true;
            }
            else {
                return "5050";
            }
        }
        catch(Exception $e) { error_log("[ERROR]  (".$e->getCode()."): ". $e->getMessage());
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar la lista'))
            );
        }
    }

    function UpdatePasswd(){
        try {
            if(isset($_POST["id"]) && isset($_POST["password"])){
               $this->id= $_POST["id"];
               $this->password= $_POST["password"];

               $sql= 'UPDATE usuario_n
                  SET passwd = :passwd,
                    estado = "activo"
                  WHERE id = :id;';
                $param= array(':id'=>$this->id, ':passwd'=>$this->password);
                $data= DATA::Ejecutar($sql, $param, false);
                $this->url = "login.html";
                $this->status = "passwdOK";
                return $this;
            }
            else{
              return false;
            }
        }
        catch(Exception $e) { error_log("[ERROR]  (".$e->getCode()."): ". $e->getMessage());
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar la lista'))
            );
        }
    }

    function Update(){
        try {
            // $obj_authDetail = new stdClass();
            // $obj_authDetail->authType = $this->authDetail['authType'];
            // $obj_authDetail->authState = $this->authDetail['authState'];
            // $obj_authDetail->tokenDate = date("Y-m-d h:i:sa");

            $changePasswd = false;
            $this->telephonenumber = preg_replace( '/[\W]/', '', $this->telephonenumber);
            if($this->authDetail['authType'] == "LDAP"){
              $this->password = "LDAP";
              $this->estado = "activo";
              $changePasswd = true;
            }
            if($this->authDetail['authType'] == "LOCAL"){
              // if (isset($this->authDetail['action'])){
                // if ($this->authDetail['action'] == 'sendNewPasswd'){
                  $newPsswd = $this->generateRandomPasswd();
                  require_once("SMS.php");
                  $sms = new SMS();
                  $sms->token= "tokenPRD";
                  $sms->num= $this->telephonenumber;
                  $sms->subject= "Nueva clave de ingreso";
                  $sms->message= "Su clave es: ".$newPsswd;
                  $sms->sendSMS();
                  $this->password = $newPsswd;
                  // $obj_authDetail->token = $newPsswd;
                  // $this->authDetail['authState'] = "change";
                  $this->estado = "cambiarPWD";
                  $changePasswd = true;
                // }
              // }
            }
            // $autDetail = json_encode($obj_authDetail);

            $sql='UPDATE usuario_n
                SET usuario = :username,
                cedula = :cedula,
                nombre = :nombre,
                correo = :email,
                telephonenumber = :telephonenumber,
                empresa = :empresa,
                estado = :estado,
                documento = :documento,
                imagen = :imagen';
            $param= array(':id'=>$this->id,
                          ':username'=>$this->username,
                          ':cedula'=>$this->cedula,
                          ':nombre'=>$this->nombre,
                          ':email'=>$this->email,
                          ':telephonenumber'=>$this->telephonenumber,
                          ':empresa'=>$this->empresa,
                          ':estado'=>$this->estado,
                          ':documento'=>$this->inp_doc,
                          ':imagen'=>$this->img_visitante);
            if ($changePasswd){
              $sql .= ", passwd = :passwd";
              $param[':passwd'] = $this->password;
            }
            $sql .= " WHERE id=:id;";


            $data = DATA::Ejecutar($sql,$param);

            $sql='DELETE FROM usuario_rol
                WHERE idUsuario = :idUsuario';
            $param= array(':idUsuario'=>$this->id);
            $data = DATA::Ejecutar($sql,$param);

            foreach ($this->rol as $rol) {
                // $sql='SELECT id FROM rol
                //     WHERE nombre = :nombre;';
                // $param= array(':nombre'=>$rol );
                // $dataRol = DATA::Ejecutar($sql,$param);

                $sql='INSERT INTO usuario_rol (idRol, idUsuario) VALUES (:idRol, :idUsuario);';
                $param= array(':idRol'=>$rol, ':idUsuario'=>$this->id);
                $data = DATA::Ejecutar($sql,$param,false);

            }

            $sql='SELECT idUsuario, idTarjeta
                  FROM control_acceso_cdc_dbp.usuario_has_tarjeta
                  WHERE idUsuario = :idUsuario;';
            $param= array(':idUsuario'=>$this->id);
            $data = DATA::Ejecutar($sql,$param);
            if ($data){
              $sql= 'UPDATE tarjeta
                    SET estado = 0
                    WHERE id = :idTarjeta;';
              $param= array(':idTarjeta'=>$data[0]['idTarjeta']);
              $data= DATA::Ejecutar($sql, $param, false);

            }

            $sql="DELETE FROM control_acceso_cdc_dbp.usuario_has_tarjeta
                  WHERE (idTarjeta != '' and idUsuario = :idUsuario);";
            $param= array(':idUsuario'=>$this->id);
            $data = DATA::Ejecutar($sql,$param);

            $sql="DELETE FROM control_acceso_cdc_dbp.usuario_has_tarjeta
                WHERE (idTarjeta = :idTarjeta and idUsuario != '');";
            $param= array(':idTarjeta'=>$this->idTarjeta);
            $data = DATA::Ejecutar($sql,$param);

            if($this->tarjeta_fija){

                $sql='INSERT INTO control_acceso_cdc_dbp.usuario_has_tarjeta (idUsuario, idTarjeta) VALUES (:idUsuario, :idTarjeta);';
                $param= array(':idUsuario'=>$this->id, ':idTarjeta'=>$this->idTarjeta);
                $data = DATA::Ejecutar($sql,$param);

                $sql= 'UPDATE tarjeta
                        SET estado = 1
                        WHERE id = :idTarjeta;';
                $param= array(':idTarjeta'=>$this->idTarjeta);
                $data= DATA::Ejecutar($sql, $param, false);
            }

            return true;
        }
        catch(Exception $e) { error_log("[ERROR]  (".$e->getCode()."): ". $e->getMessage());
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar la lista'))
            );
        }
    }

    function CheckToken() {
      try {
        if(isset($_POST["id"]) && isset($_POST["token"])){
            $this->id= $_POST["id"];
            $this->token= $_POST["token"];

            $sql= 'SELECT auth_detail
                  FROM usuario_n
                  WHERE id = :id';
            $param= array(':id'=>$this->id);
            $data= DATA::Ejecutar($sql, $param);
            if ($data[0]["auth_detail"] ==  $this->token){
              $this->status = "TokenOK";
            }
            else
              $this->status = false;
            // $this->url = "login.html";
            return $this;
        }
        else{
          return false;
        }

        $sql= 'SELECT DISTINCT u.id idUsuario, u.usuario, u.cedula, u.nombre, u.telephonenumber, u.correo, u.empresa, u.estado, u.passwd,
                e.id idEvento, e.url, e.modulo, e.menu, e.opcion, e.iconoModulo, e.iconoMenu
                FROM usuario_n u
                INNER JOIN usuario_rol ur
                ON ur.idUsuario = u.id
                INNER JOIN rol r
                ON r.id = ur.idRol
                INNER JOIN eventoXRol er
                ON er.idRol = r.id
                INNER JOIN evento e
                ON e.id = er.idEvento
                WHERE u.correo= :correo
                OR u.cedula = :correo';
        $param= array(':correo'=>$this->email);
        $data= DATA::Ejecutar($sql, $param);
        // if($data){


      }
      catch(Exception $e) { error_log("[ERROR]  (".$e->getCode()."): ". $e->getMessage());
          header('HTTP/1.0 400 Bad error');
          die(json_encode(array(
              'code' => $e->getCode() ,
              'msg' => 'Error al cargar la lista'))
          );
      }

    }

    // login and user session
    function CheckSession(){
        if(isset($_SESSION["userSession"]->id)){
            // VALIDA SI TIENE CREDENCIALES PARA LA URL CONSULTADA
            $_SESSION['userSession']->status= userSessionStatus::nocredencial;
            $_SESSION['userSession']->url = $_POST["url"];
            $urlarr = explode('/', $_SESSION['userSession']->url);
            $myUrl = end($urlarr)==''?'index.html':end($urlarr);
            foreach ($_SESSION['userSession']->eventos as $evento) {
                if(strtolower($myUrl) == strtolower($evento->url)){
                    $_SESSION['userSession']->status= userSessionStatus::login;
                    break;
                }
            }
        }
        else {
            $this->status= userSessionStatus::invalido;
            $this->url = $_POST["url"];
            $_SESSION["userSession"]= $this;
        }
    }

    function EndSession(){
        // remove all session variables
        session_unset();
        // destroy the session
        session_destroy();
    }

    function Login(){
        try {
            $sql= 'SELECT DISTINCT u.id idUsuario, u.usuario, u.cedula, u.nombre, u.telephonenumber, u.correo, u.empresa, u.estado, u.passwd,
                e.id idEvento, e.url, e.modulo, e.menu, e.opcion, e.iconoModulo, e.iconoMenu
                FROM usuario_n u
                INNER JOIN usuario_rol ur
                ON ur.idUsuario = u.id
                INNER JOIN rol r
                ON r.id = ur.idRol
                INNER JOIN eventoXRol er
                ON er.idRol = r.id
                INNER JOIN evento e
                ON e.id = er.idEvento
                WHERE u.correo= :correo
                OR u.cedula = :correo';
            $param= array(':correo'=>$this->email);
            $data= DATA::Ejecutar($sql, $param);
            if($data){
                if($data[0]['estado']=="inactivo"){
                    unset($_SESSION["userSession"]);
                    $this->status= userSessionStatus::inactivo;
                    return "inactivo";
                }
                if($data[0]['estado']=="cambiarPWD"){
                    $this->id = $data[0]['idUsuario'];
                    $this->status= userSessionStatus::cambiarPWD;
                }
                if($data[0]['estado']=="activo") {
                    $this->id = $data[0]['idUsuario'];
                    $this->telephonenumber = $data[0]['telephonenumber'];
                    if($data[0]['passwd']=="LDAP"){
                        if ( $this->ValidarUsuarioLDAP() ){
                            $tempToken = $this->createToken();
                            $this->AsignaEventos($data);
                            $this->AsignaRoles($data);

                            error_log("Enviando correo...");
                            require_once("email.php");
                            $email = new SendEmail();
                            $email->email = $data[0]['correo'];
                            $email->token = $tempToken;
                            $email->Send();
                        }
                        else { // password invalido
                            unset($_SESSION["userSession"]);
                            $this->status= userSessionStatus::invalido;
                        }
                    }
                    elseif ($data[0]['passwd']==$this->password){
                        $tempToken = $this->createToken();
                        error_log("Enviando correo...");
                        require_once("email.php");
                        $email = new SendEmail();
                        $email->email = $data[0]['correo'];
                        $email->token = $tempToken;
                        $email->Send();
                        $this->AsignaEventos($data);
                        $this->AsignaRoles($data);
                    }
                    else { // password invalido
                        unset($_SESSION["userSession"]);
                        $this->status= userSessionStatus::invalido;
                    }
                }
            }
            else {
                unset($_SESSION["userSession"]);
                $this->status= userSessionStatus::noexiste;
            }
            // set user session.
            $_SESSION["userSession"] = $this;
            isset($this->dn)?$this->dn = utf8_encode($this->dn):false;
            return $this;
        }
        catch(Exception $e) { error_log("[ERROR]  (".$e->getCode()."): ". $e->getMessage());
            unset($_SESSION["userSession"]);
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => $e->getMessage()))
            );
        }
    }

    function createToken(){
      $newToken = $this->generateRandomPasswd();
      require_once("SMS.php");
      $sms = new SMS();
      $sms->token= "tokenPRD";
      $sms->num= $this->telephonenumber;
      $sms->subject= "Clave de ingreso al SIDC";
      $sms->message= "Su clave es: ".$newToken;
      $sms->sendSMS();

      $sql= "UPDATE usuario_n
            SET auth_detail = :newToken
            WHERE id=:id;";
      $param= array(':id'=>$this->id, ':newToken'=>$newToken);
      $data = DATA::Ejecutar($sql,$param);

      return $newToken;
    }

    function AsignaEventos($dataUsuario){
        foreach ($dataUsuario as $key => $value){
            // Session Datos del usuario y eventos relacionados a su rol
            $evento= new Evento(); // evento con credencial del usuario.
            if($key==0){
                $this->id = $value['idUsuario'];
                $this->username = $value['usuario'];
                $this->nombre = $value['nombre'];
                $this->estado = $value['estado'];
                $this->status = userSessionStatus::login;
                $this->url = isset($_SESSION['userSession']->url)? $_SESSION['userSession']->url : 'index.html'; // Url consultada
                //
                $evento->id= $value['idEvento'];
                $evento->url= $value['url'];
                $evento->modulo= $value['modulo'];
                $evento->menu= $value['menu'];
                $evento->opcion= $value['opcion'];
                $evento->iconoMenu= $value['iconoMenu'];
                $evento->iconoModulo= $value['iconoModulo'];
                $this->eventos = array($evento);
            }
            else {
                $evento->id= $value['idEvento'];
                $evento->url= $value['url'];
                $evento->modulo= $value['modulo'];
                $evento->menu= $value['menu'];
                $evento->opcion= $value['opcion'];
                $evento->iconoMenu= $value['iconoMenu'];
                $evento->iconoModulo= $value['iconoModulo'];
                array_push($this->eventos, $evento);
            }
        }
    }

    function AsignaRoles($dataUsuario){
        try {
            $sql='SELECT ur.idRol, rol.nombre
                  FROM usuario_rol ur
                  INNER JOIN rol
                  ON ur.idRol = rol.id
                  WHERE idUsuario =  :idUsuario;';
            $param= array(':idUsuario'=>$dataUsuario[0]["idUsuario"]);
            $data= DATA::Ejecutar($sql, $param);
            if($data){
                // return $data;
                $this->listarol = $data;
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

    function CheckUsername(){
        try{
            $sql="SELECT id
                FROM usuario
                WHERE username= :username";
            $param= array(':username'=>$this->username);
            $data= DATA::Ejecutar($sql, $param);
            if(count($data))
                $sessiondata['status']=1; // usuario duplicado
            else $sessiondata['status']=0; // usuario unico
            return $sessiondata;
        }
        catch(Exception $e){
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => $e->getMessage()))
            );
        }
    }

    private function CheckRelatedItems(){
        try{
            $sql="SELECT idUsuario
                FROM usuario_rol x
                WHERE x.idUsuario= :id";
            $param= array(':id'=>$this->id);
            $data= DATA::Ejecutar($sql, $param);
            if(count($data))
                return true;
            else return false;
        }
        catch(Exception $e){
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => $e->getMessage()))
            );
        }
    }

    function Delete(){
        try {
            if($this->CheckRelatedItems()){
                //$sessiondata array que devuelve si hay relaciones del objeto con otras tablas.
                $sessiondata['status']=1;
                $sessiondata['msg']='Registro en uso';
                return $sessiondata;
            }
            $sql='DELETE FROM usuario_n
                WHERE id= :id';
            $param= array(':id'=>$this->id);
            $data= DATA::Ejecutar($sql, $param, false);
            if($data){
                $sessiondata['status']=0;
                return $sessiondata;
            }
            else throw new Exception('Error al eliminar.', 978);
        }
        catch(Exception $e) { error_log("[ERROR]  (".$e->getCode()."): ". $e->getMessage());
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => $e->getMessage()))
            );
        }
    }

    function load_inp_doc(){
        try {
            $sql="SELECT documento
                FROM control_acceso_cdc_dbp.usuario_n
                WHERE id = :id;";
            $param= array(':id'=>$this->id);
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


    function load_img_visitante(){
        try {
            $sql="SELECT imagen
                FROM control_acceso_cdc_dbp.usuario_n
                WHERE id = :id;";
            $param= array(':id'=>$this->id);
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

    function getImgRH($cedula){
      try {
        $body = new stdClass();
        $body->token = "tokenPRD";
        $body->id = $cedula;
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://10.3.2.156:3100/api/user/',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>json_encode($body),
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);
        if (isset($response->recordsets[0][0]->Foto)){
          return 'data:image/png;base64,'.$response->recordsets[0][0]->Foto;
        }else
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

    function generateRandomPasswd($length = 4) {
      $characters = '123456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
          $randomString .= $characters[rand(0, $charactersLength - 1)];
      }
      return $randomString;
    }

}
?>
