<?php
  require_once("Log.php");
	$appLog = new AppLog();

	$appLog->idUsuario = "Miusuario";
	$appLog->detalle = "detalle";
	$appLog->WriteLog();

?>
