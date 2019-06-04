<?php
    ini_set('max_execution_time', 1800); //300 seconds = 5 minutes
    $debug = false;
    validarDataBase();
    require_once( __DIR__ . DIRECTORY_SEPARATOR .".." . DIRECTORY_SEPARATOR ."class". DIRECTORY_SEPARATOR."Conexion.php");
    loadData();
    unirTabla_Visitante_Responsable_Usuario();
    asignaRoles();

    function validarDataBase(){
        $db_con = new PDO('mysql:host=10.3.2.156; port=3306; charset=utf8', 'operti', 'SanPedro1');    
        echo "Conexión Exitosa" . PHP_EOL;
        $sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'control_acceso_cdc_dbp'";
        $st=$db_con->prepare($sql);
        $db_con->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
        $db_con->beginTransaction(); 
        $db_con->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
        if($st->execute()){
            $db_con->commit();
            $existeDB =  isset( ($st->fetchAll())[0]["SCHEMA_NAME"] ) ? true:false;
            // $db_con = NULL;	
        }
        if (!$existeDB){  
            echo "Base de datos NO encontrada" . PHP_EOL;
            // $db_con = new PDO('mysql:host=10.3.2.156; port=3306; charset=utf8', 'operti', 'SanPedro1');
            // $sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'control_acceso_cdc_dbp'";
            $sql = "CREATE DATABASE IF NOT EXISTS control_acceso_cdc_dbp;";
            $st=$db_con->prepare($sql);
            $db_con->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
            $db_con->beginTransaction(); 
            $db_con->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
            if($st->execute()){
                $db_con->commit();
                echo "Base de datos CREADA" . PHP_EOL;

                $sql = "USE control_acceso_cdc_dbp;";
                $st=$db_con->prepare($sql);
                $db_con->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
                $db_con->beginTransaction(); 
                $db_con->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
                if($st->execute()){
                    $db_con->commit();
                    echo "Base de datos SELECCIONADA" . PHP_EOL;

                    $sql = file_get_contents('esquema.sql');
                    $st=$db_con->prepare($sql);
                    $db_con->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
                    $db_con->beginTransaction(); 
                    $db_con->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
                    if($st->execute()){
                        $db_con->commit();
                        echo "Cargando Esquema..." . PHP_EOL;
                        echo "Esquema Cargado" . PHP_EOL;
                    }
                }
            }
        }
        $db_con = null;

    }

    function loadData(){
        // BITACORA
        echo "Cargando Datos de Bitacora..." . PHP_EOL;
        $sql='SELECT idvisitante, idformulario, entrada, salida, idtarjeta, id, fechacreacion FROM controlaccesocdc_dbp.bitacora;';
        $data = DATA::Ejecutar($sql);
        if($data){
            foreach ($data as $key => $value) {
                $count= $key;
                $sql='INSERT IGNORE INTO control_acceso_cdc_dbp.bitacora (id, idFormulario, idVisitante, idTarjeta, entrada, 
                    salida, fechaCreacion) values(:id, :idFormulario, :idVisitante, :idTarjeta, :entrada, :salida, :fechaCreacion);';   
                $param= array(':id'=>$value["id"], ':idFormulario'=>$value["idformulario"], ':idVisitante'=>$value["idvisitante"], 
                    ':idTarjeta'=>$value["idtarjeta"], ':entrada'=>$value["entrada"], ':salida'=>$value["salida"], 
                    ':fechaCreacion'=>$value["fechacreacion"]);
                $data = DATA::Ejecutar($sql, $param);
                if ($GLOBALS['debug']){
                    print_r($value);
                    echo "<br>";
                }
            }
            echo ($count+1)." datos ingresados a la tabla bitacora" . PHP_EOL;
        }
        // DataCenter
        echo "Cargando Datos de DataCenter..." . PHP_EOL;
        $sql='SELECT id, nombre FROM controlaccesocdc_dbp.datacenter;';
        $data = DATA::Ejecutar($sql);
        if($data){
            foreach ($data as $key => $value) {
                $count= $key;
                $sql='INSERT IGNORE INTO control_acceso_cdc_dbp.dataCenter (id, nombre) values(:id, :nombre);';   
                $param= array(':id'=>$value["id"], ':nombre'=>$value["nombre"]);
                $data = DATA::Ejecutar($sql, $param);
                if ($GLOBALS['debug']){
                    print_r($value);
                    echo "<br>";
                }
            }
            echo ($count+1)." datos ingresados a la tabla DataCenter" . PHP_EOL;
        }

        // Estado
        echo "Cargando Datos de Estado..." . PHP_EOL;
        $sql='SELECT id, nombre FROM controlaccesocdc_dbp.estado;';
        $data = DATA::Ejecutar($sql);
        if($data){
            foreach ($data as $key => $value) {
                $count= $key;
                $sql='INSERT IGNORE INTO control_acceso_cdc_dbp.estado (id, nombre) values(:id, :nombre);';   
                $param= array(':id'=>$value["id"], ':nombre'=>$value["nombre"]);
                $data = DATA::Ejecutar($sql, $param);
                if ($GLOBALS['debug']){
                    print_r($value);
                    echo "<br>";
                }
            }
            echo ($count+1)." datos ingresados a la tabla DataCenter" . PHP_EOL;
        }

        // Formulario
        echo "Cargando Datos de Formulario..." . PHP_EOL;
        $sql='SELECT fechaingreso, fechasalida, idtramitante, fechasolicitud, id, idautorizador, 
                    idresponsable, placavehiculo, detalleequipo, motivovisita, idestado, idsala, consecutivo, rfc 
                FROM controlaccesocdc_dbp.formulario;';
        $data = DATA::Ejecutar($sql);
        if($data){
            foreach ($data as $key => $value) {
                $count= $key;
                $sql='INSERT IGNORE INTO control_acceso_cdc_dbp.formulario 
                        (id, consecutivo, idTramitante, idAutorizador, idResponsable, idEstado, idSala, 
                        fechaSolicitud, fechaIngreso, fechaSalida, motivoVisita, otrosDetalles) 

                        values(:id, :consecutivo, :idTramitante, :idAutorizador, :idResponsable, :idEstado, 
                        :idSala, :fechaSolicitud, :fechaIngreso, :fechaSalida, :motivoVisita, :otrosDetalles);';   

                $param= array(':id'=>$value["id"], ':consecutivo'=>$value["consecutivo"], ':idTramitante'=>$value["idtramitante"],
                        ':idAutorizador'=>$value["idautorizador"], ':idResponsable'=>$value["idresponsable"], ':idEstado'=>$value["idestado"],
                        ':idSala'=>$value["idsala"], ':fechaSolicitud'=>$value["fechasolicitud"], ':fechaIngreso'=>$value["fechaingreso"], 
                        ':fechaSalida'=>$value["fechasalida"], ':motivoVisita'=>$value["motivovisita"], 
                        ':otrosDetalles'=>( "RFC: ".$value["rfc"]." Placa: ".$value["rfc"]." Detalle Equipo: ".$value["detalleequipo"]) );
                $data = DATA::Ejecutar($sql, $param);
                if ($GLOBALS['debug']){
                    print_r($value);
                    echo "<br>";
                }
            }
            echo ($count+1)." datos ingresados a la tabla DataCenter" . PHP_EOL;
        }

        // Rol
        echo "Cargando Datos de Rol..." . PHP_EOL;
        $sql='SELECT id, nombre FROM controlaccesocdc_dbp.rol;';
        $data = DATA::Ejecutar($sql);
        if($data){
            foreach ($data as $key => $value) {
                $count= $key;
                $sql='INSERT IGNORE INTO control_acceso_cdc_dbp.rol (id, nombre) values(:id, :nombre);';   
                $param= array(':id'=>$value["id"], ':nombre'=>$value["nombre"]);
                $data = DATA::Ejecutar($sql, $param);
                if ($GLOBALS['debug']){
                    print_r($value);
                    echo "<br>";
                }
            }
            echo ($count+1)." datos ingresados a la tabla DataCenter" . PHP_EOL;
        }
                
        // SALA
        echo "Cargando Datos de Sala..." . PHP_EOL;
        $sql='SELECT id, iddatacenter, nombre FROM controlaccesocdc_dbp.tarjeta;';
        $data = DATA::Ejecutar($sql);
        if($data){
            foreach ($data as $key => $value) {
                $count= $key;
                $sql='INSERT IGNORE INTO control_acceso_cdc_dbp.sala (id, idDataCenter, nombre) values(:id, :idDataCenter, :nombre);';   
                $param= array(':id'=>$value["id"], ':idDataCenter'=>$value["idDataCenter"], ':nombre'=>$value["nombre"]);
                $data = DATA::Ejecutar($sql, $param);
                if ($GLOBALS['debug']){
                    print_r($value);
                    echo "<br>";
                }
            }
            echo ($count+1)." datos ingresados a la tabla Sala" . PHP_EOL;
        }

        // Tajeta
        echo "Cargando Datos de Tajeta..." . PHP_EOL;
        $sql='SELECT id, idSala, estado, consecutivo FROM controlaccesocdc_dbp.tarjeta;';
        $data = DATA::Ejecutar($sql);
        if($data){
            foreach ($data as $key => $value) {
                $count= $key;
                $sql='INSERT IGNORE INTO control_acceso_cdc_dbp.tarjeta (id, idSala, estado, consecutivo) values(:id, :idSala, :estado, :consecutivo);';   
                $param= array(':id'=>$value["id"], ':idSala'=>$value["idSala"], ':estado'=>$value["estado"], ':consecutivo'=>$value["consecutivo"]);
                $data = DATA::Ejecutar($sql, $param);
                if ($GLOBALS['debug']){
                    print_r($value);
                    echo "<br>";
                }
            }
            echo ($count+1)." datos ingresados a la tabla DataCenter" . PHP_EOL;
        }

        // Visitante por Formulario
        echo "Cargando Datos de Visitante por Formulario..." . PHP_EOL;
        $sql='SELECT id, idvisitante, idformulario FROM controlaccesocdc_dbp.visitanteporformulario;';
        $data = DATA::Ejecutar($sql);
        if($data){
            foreach ($data as $key => $value) {
                $count= $key;
                $sql='INSERT IGNORE INTO control_acceso_cdc_dbp.visitante_formulario (id, idVisitante, idFormulario) values(:id, :idVisitante, :idFormulario);';   
                $param= array(':id'=>$value["id"], ':idVisitante'=>$value["idvisitante"], ':idFormulario'=>$value["idformulario"]);
                $data = DATA::Ejecutar($sql, $param);
                if ($GLOBALS['debug']){
                    print_r($value);
                    echo "<br>";
                }
            }
            echo ($count+1)." datos ingresados a la tabla DataCenter" . PHP_EOL;
        }
    }

    function unirTabla_Visitante_Responsable_Usuario(){
        // VISITANTE
        echo "Cargando Visitantes..." . PHP_EOL;
        $sql='SELECT id, nombre, cedula, empresa, fechacreacion fechaCreacion FROM controlaccesocdc_dbp.visitante;';
        $data = DATA::Ejecutar($sql);
        if($data){
            foreach ($data as $key => $value) {
                $count= $key;
                $sql='INSERT IGNORE INTO control_acceso_cdc_dbp.usuario_n (id, nombre, cedula, empresa, fechaCreacion) values(:id, :nombre, :cedula, :empresa, :fechaCreacion);';   
                $param= array(':id'=>$value["id"], ':nombre'=>$value["nombre"], ':cedula'=>$value["cedula"], ':empresa'=>$value["empresa"], ':fechaCreacion'=>$value["fechaCreacion"]);  
                $data = DATA::Ejecutar($sql, $param);
                if ($GLOBALS['debug']){
                    print_r($value);
                    echo "<br>";
                }
            }
            echo $count." datos recolectados de la tabla visitante" . PHP_EOL;
        }

        // RESPONSABLE
        $sql='SELECT id, nombre, cedula, empresa FROM responsable WHERE cedula NOT IN(SELECT cedula FROM control_acceso_cdc_dbp.usuario_n);';
        $data = DATA::Ejecutar($sql);    
        $count = 0;
        if($data){
            foreach ($data as $key => $value) {
                $count= $key;
                $sql='INSERT IGNORE INTO control_acceso_cdc_dbp.usuario_n (id, nombre, cedula, empresa) values(:id, :nombre, :cedula, :empresa);';   
                $param= array(':id'=>$value["id"], ':nombre'=>$value["nombre"], ':cedula'=>$value["cedula"], ':empresa'=>$value["empresa"]);  
                $data = DATA::Ejecutar($sql, $param);
                if ($GLOBALS['debug']){
                    print_r($value);
                    echo "<br>";
                }
            }
            echo $count." datos recolectados de la tabla responsable" . PHP_EOL;
        }

        // USUARIO
        $sql='SELECT id, nombre, usuario, contrasena, email, fechaCreacion FROM controlaccesocdc_dbp.usuario WHERE nombre NOT IN(SELECT nombre FROM control_acceso_cdc_dbp.usuario_n);';
        $data = DATA::Ejecutar($sql);    
        $count = 0;
        if($data){
            foreach ($data as $key => $value) {
                $count= $key;
                $sql='INSERT IGNORE INTO control_acceso_cdc_dbp.usuario_n (id, nombre, usuario, passwd, correo, fechaCreacion) values(:id, :nombre, :usuario, :passwd, :correo, :fechaCreacion);';   
                $param= array(':id'=>$value["id"], ':nombre'=>$value["nombre"], ':usuario'=>$value["usuario"], ':passwd'=>$value["contrasena"], ':correo'=>$value["email"], ':fechaCreacion'=>$value["fechaCreacion"]);  
                $data = DATA::Ejecutar($sql, $param);
                if ($GLOBALS['debug']){
                    print_r($value);
                    echo "<br>";
                }
            }
            echo $count." datos recolectados de la tabla USUARIO" . PHP_EOL;
        }
    }

    function asignaRoles() {
        // Asinga Rol de usuario al usuario correspondiente
        $sql='SELECT un.id FROM control_acceso_cdc_dbp.usuario_n un 
            INNER JOIN controlaccesocdc_dbp.usuario u 
            ON u.nombre = un.nombre;';
        $data = DATA::Ejecutar($sql);    
        $count = 0;
        if($data){
            foreach ($data as $key => $value) {
                $count= $key;
                $sql='INSERT IGNORE INTO control_acceso_cdc_dbp.usuario_rol (idUsuario, idRol) values(:id, :idRol);';   
                $param= array(':id'=>$value["id"] ?? "", ':idRol'=> "97b3927c-41de-47fd-871a-3eb6d2a57758");  
                $data = DATA::Ejecutar($sql, $param);
                if ($GLOBALS['debug']){
                    print_r($value);
                    echo "<br>";
                }
            }
            echo $count." datos actualizados de la tabla usuario" . PHP_EOL;
        }

        // Asinga Rol de Responsable al usuario correspondiente
        $sql='SELECT un.id FROM control_acceso_cdc_dbp.usuario_n un
            INNER JOIN controlaccesocdc_dbp.responsable r
            ON r.cedula = un.cedula;';
        $data = DATA::Ejecutar($sql);    
        $count = 0;
        if($data){
            foreach ($data as $key => $value) {
                $count= $key;
                $sql='INSERT IGNORE INTO control_acceso_cdc_dbp.usuario_rol (idUsuario, idRol) values(:id, :idRol);';   
                $param= array(':id'=>$value["id"] ?? "", ':idRol'=> "bcec0ea4-4a0b-4e69-b9f5-744530a765ed");  
                $data = DATA::Ejecutar($sql, $param);
                if ($GLOBALS['debug']){
                    print_r($value);
                    echo "<br>";
                }
            }
            echo $count." datos actualizados de la tabla Responsable" . PHP_EOL;
        }

        // Asinga Rol de Visitante al usuario correspondiente
        $sql='SELECT un.id FROM control_acceso_cdc_dbp.usuario_n un
        INNER JOIN controlaccesocdc_dbp.visitante v
        ON v.cedula = un.cedula;';
        $data = DATA::Ejecutar($sql);    
        $count = 0;
        if($data){
            foreach ($data as $key => $value) {
                $count= $key;
                $sql='INSERT IGNORE INTO control_acceso_cdc_dbp.usuario_rol (idUsuario, idRol) values(:id, :idRol);';   
                $param= array(':id'=>$value["id"] ?? "", ':idRol'=> "c4d35117-d57e-4cad-b3bc-b589f002814d");  
                $data = DATA::Ejecutar($sql, $param);
                if ($GLOBALS['debug']){
                    print_r($value);
                    echo "<br>";
                }
            }
            echo $count." datos actualizados de la tabla Visitante" . PHP_EOL;
        }
    }
?>