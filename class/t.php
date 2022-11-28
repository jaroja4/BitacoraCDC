<?php

	require_once("Conexion.php");
	$email = "jrojasval@ice.go.cr";
	$midn = "CN=Rojas Valverde Jason,OU=No Hereda,OU=Gerencia Clientes,DC=icetel,DC=ice";
	$mipassword = "iceKbtpo19892";

	$adn = "CN=Saborío Campos Adriel Vinicio,OU=Usuarios,OU=DIC,DC=icetel,DC=ice";
	$apassword = "1948hl2p59DM";

	$LDAP_servicio = DATA::getLDAP_Param();
	$LDAP_connect = ldap_connect($LDAP_servicio["LDAP_server"], $LDAP_servicio["LDAP_port"]);
	ldap_set_option($LDAP_connect, LDAP_OPT_PROTOCOL_VERSION, 3);
	$LDAP_bind = @ldap_bind($LDAP_connect, $LDAP_servicio["LDAP_user"], $LDAP_servicio["LDAP_passwd"]);
	if ($LDAP_bind) {
			$LDAP_filter="(mail=$email)";
			$search_result=ldap_search($LDAP_connect,$LDAP_servicio["LDAP_base_dn"],$LDAP_filter);
			$LDAP_user_data = ldap_get_entries($LDAP_connect, $search_result);
			if($LDAP_user_data["count"] < 1){
					@ldap_close($LDAP_connect);
					return false;
			}
			$dn = utf8_encode($LDAP_user_data[0]["dn"]);
			$email= utf8_encode($LDAP_user_data[0]["mail"][0]);
			$nombre = utf8_encode($LDAP_user_data[0]["cn"][0]);
			$username = utf8_encode($LDAP_user_data[0]["samaccountname"][0]);
			// $this::BuscaRol();
			@ldap_close($LDAP_connect);

			$LDAP_connect = ldap_connect($LDAP_servicio["LDAP_server"], $LDAP_servicio["LDAP_port"]);
			ldap_set_option($LDAP_connect, LDAP_OPT_PROTOCOL_VERSION, 3);
			// $LDAP_bind = @ldap_bind($LDAP_connect, $midn, $mipassword);
			$LDAP_bind = @ldap_bind($LDAP_connect, $adn, $apassword);
			@ldap_close($LDAP_connect);
			if ($LDAP_bind)
					return true;
			else
					return false;
	} else {
			error_log("Falla el Bind: " . ldap_error($ldap));
			return false;
	}
?>
