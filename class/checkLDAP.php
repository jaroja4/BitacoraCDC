<?php //ACTION
setlocale(LC_ALL,"es_ES");
date_default_timezone_set('America/Costa_Rica');
mb_internal_encoding('UTF-8');

if( isset($_POST["action"])){        
    $opt= $_POST["action"];
    unset($_POST['action']);    
    // Classes
    require_once("Conexion.php");
    // 
    // Instance
    $searchLDAP = new SearchLDAP();
    switch($opt){
        case "BuscarLDAP":
            echo json_encode($searchLDAP->ValidarUsuarioLDAP());
            break;
    }
}

$searchLDAP= new SearchLDAP();

class SearchLDAP{
    public $searchValue = "";
    public $tipoFiltro = "";
    public $usuario = "";
    public $dn = "";
    public $email = "";
    public $nombre = "";
    public $cedula = "";
    public $telephonenumber = "";
    public $streetaddress = "";
    public $physicaldeliveryofficename = "";

	function __construct(){
        require_once("Conexion.php");
        
        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
            $this->tipoFiltro = $obj["tipoFiltro"];
            $this->searchValue = $obj["searchValue"];
        }
    }

    function ValidarUsuarioLDAP (){
        $LDAP_servicio = DATA::getLDAP_Param();
        $LDAP_connect = ldap_connect($LDAP_servicio["LDAP_server"], $LDAP_servicio["LDAP_port"]);
        $LDAP_bind = @ldap_bind($LDAP_connect, $LDAP_servicio["LDAP_user"], $LDAP_servicio["LDAP_passwd"]);
        if ($LDAP_bind) {
            $LDAP_filter="(".$this->tipoFiltro."=$this->searchValue)";
            $search_result=ldap_search($LDAP_connect,$LDAP_servicio["LDAP_base_dn"],$LDAP_filter);
            $LDAP_user_data = ldap_get_entries($LDAP_connect, $search_result);  
            // var_dump($LDAP_user_data);
            if($LDAP_user_data["count"] < 1){
                @ldap_close($LDAP_connect);
                return false;
            }       
            $this->dn = utf8_encode( $LDAP_user_data[0]["dn"] ?? null );
            $this->email= utf8_encode( $LDAP_user_data[0]["mail"][0] ?? null );
            $this->nombre = utf8_encode( $LDAP_user_data[0]["cn"][0] ?? null );
            $this->usuario = utf8_encode( $LDAP_user_data[0]["samaccountname"][0] ?? null );
            $this->cedula = utf8_encode( $LDAP_user_data[0]["description"][0] ?? null );
            $this->telephonenumber = utf8_encode( $LDAP_user_data[0]["telephonenumber"][0] ?? null );
            $this->streetaddress = utf8_encode( $LDAP_user_data[0]["streetaddress"][0] ?? null );
            $this->physicaldeliveryofficename = utf8_encode( $LDAP_user_data[0]["physicaldeliveryofficename"][0] ?? null );
            
            @ldap_close($LDAP_connect);
            return $this;
            
        } else {
            error_log("Falla el Bind: " . ldap_error($ldap));
            return false;  
        }
    }
}


?>