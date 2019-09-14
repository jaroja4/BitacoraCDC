<?php
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
    }
}

abstract class userSessionStatus
{
    const invalido = 'invalido'; // login invalido
    const login = 'login'; // login ok; credencial ok
    const nocredencial= 'nocredencial'; // login ok; sin credenciales
    const inactivo= 'inactivo';
    const noexiste= 'noexiste';
    const noip= 'noip';
}

class Usuario{
    public $id;
    public $username;
    public $password;
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
            $this->cedula= $obj["cedula"] ?? '';  
            $this->email= $obj["correo"] ?? '';  
            $this->empresa= $obj["empresa"] ?? '';
            $this->rol= $obj["rol"] ?? '';
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
            $this->dn = $LDAP_user_data[0]["dn"];
            $this->email= $LDAP_user_data[0]["mail"][0];
            $this->nombre = $LDAP_user_data[0]["cn"][0];
            $this->username = $LDAP_user_data[0]["samaccountname"][0];
            // $this::BuscaRol();
            @ldap_close($LDAP_connect);
            
            $LDAP_connect = ldap_connect($LDAP_servicio["LDAP_server"], $LDAP_servicio["LDAP_port"]);
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
            $sql="SELECT u.id, u.usuario, u.passwd, u.cedula, u.nombre, u.correo, u.empresa, u.fechaCreacion,
                    (SELECT GROUP_CONCAT(r.nombre SEPARATOR ', ') roles 
                    FROM rol r
                    INNER JOIN usuario_rol ur
                    ON ur.idRol = r.id
                    WHERE ur.idUsuario = u.id) roles
                FROM usuario_n u   
                ORDER BY nombre ASC;";
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

    function Create(){
        try {
            $sql='SELECT * FROM usuario_n
            WHERE (usuario like :usuario AND usuario <> "")
            OR (cedula like :cedula AND cedula <> "")
            OR (correo like :correo AND correo <> "");';
            $param= array(':usuario'=>$this->username, ':cedula'=>$this->cedula, ':correo'=>$this->email);  
            $data = DATA::Ejecutar($sql,$param);    
            
            if (!$data){
                $sql='INSERT INTO usuario_n (id, usuario, passwd, cedula, nombre, correo, empresa, fechaCreacion)
                    VALUES (:id, :usuario, :passwd, :cedula, :nombre, :correo, :empresa, NOW())';  
                $param= array(':id'=>$this->id, ':usuario'=>$this->username, ':passwd'=>$this->password, 
                    ':cedula'=>$this->cedula, ':nombre'=>$this->nombre, ':correo'=>$this->email, 
                    ':empresa'=>$this->empresa);  
                $data = DATA::Ejecutar($sql,$param);    
                
                if($this->rol?true:false){
                    foreach ($this->rol as $idRol) {                    
                        $sql='INSERT INTO usuario_rol (idRol, idUsuario) VALUES (:idRol, :idUsuario);';  
                        $param= array(':idRol'=>$idRol, ':idUsuario'=>$this->id);
                        $data = DATA::Ejecutar($sql,$param,false);                 
                    }                
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

    function Update(){
        try {            
            $sql='UPDATE usuario_n
                SET usuario = :username,
                cedula = :cedula,
                nombre = :nombre,
                correo = :email,
                empresa = :empresa
                WHERE id=:id;';  
            $param= array(':id'=>$this->id, ':username'=>$this->username, ':cedula'=>$this->cedula, 
                ':nombre'=>$this->nombre, ':email'=>$this->email, ':empresa'=>$this->empresa);  
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
            //Check activo & password.
            $sql= 'SELECT DISTINCT u.id idUsuario, u.usuario, u.cedula, u.nombre, u.correo, u.empresa, u.estado, u.passwd,
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
                if($data[0]['estado']!=1){
                    unset($_SESSION["userSession"]);
                    $this->status= userSessionStatus::inactivo;
                }
                else {
                    // usuario activo; check password
                    // if(password_verify($this->password, $data[0]['password'])){
                    if($data[0]['passwd']=="LDAP"){
                        if ( $this->ValidarUsuarioLDAP() ){
                            $this->AsignaEventos($data);
                        } 
                        else { // password invalido
                            unset($_SESSION["userSession"]);
                            $this->status= userSessionStatus::invalido;
                        }
                    }
                    elseif ($data[0]['passwd']==$this->password){
                        $this->AsignaEventos($data);
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

    function AsignaEventos($dataUsuario){
        foreach ($dataUsuario as $key => $value){
            // Session Datos del usuario y eventos relacionados a su rol
            $evento= new Evento(); // evento con credencial del usuario.
            if($key==0){
                $this->id = $value['idUsuario'];
                $this->username = $value['usuario'];
                $this->nombre = $value['nombre'];
                $this->activo = $value['estado'];
                $this->status = userSessionStatus::login;
                $this->url = isset($_SESSION['userSession']->url)? $_SESSION['userSession']->url : 'listaFormularios.html'; // Url consultada
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
                // $this->eventos = array($evento);
                array_push($this->eventos, $evento);
            }                    
        }
    }

    function Read(){
        try {
            $sql='SELECT u.id, u.nombre, u.username, u.password, email, activo, r.id as idRol, r.nombre as nombreRol
                FROM usuario  u LEFT JOIN rolesXUsuario ru on ru.idUsuario = u.id
                    LEFT JOIN rol r on r.id = ru.idRol
                where u.id=:id';
            $param= array(':id'=>$this->id);
            $data= DATA::Ejecutar($sql,$param);     
            
            foreach ($data as $key => $value){
                require_once("Rol.php");
                $rol= new Rol(); // crol del producto
                if($key==0){
                    $this->id = $value['id'];
                    $this->nombre = $value['nombre'];
                    $this->username = $value['username'];
                    $this->password = $value['password'];
                    $this->email = $value['email'];
                    $this->activo = $value['activo'];                    
                    //rol
                    if($value['idRol']!=null){
                        $rol->id = $value['idRol'];
                        $rol->nombre = $value['nombreRol'];
                        array_push ($this->listarol, $rol);
                    }
                }
                else {
                    $rol->id = $value['idRol'];
                    $rol->nombre = $value['nombreRol'];
                    array_push ($this->listarol, $rol);
                }
            }
            $this->bodegas= usuariosXBodega::Read($this->id);
            return $this;
        }     
        catch(Exception $e) { error_log("[ERROR]  (".$e->getCode()."): ". $e->getMessage());
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar el usuario'))
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
                FROM rolesXUsuario x
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
            $sql='DELETE FROM usuario
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

}




?>