<?php
  $debugmode = false;
  if ($debugmode == true){
     error_log("SMS: 88986761@ice.go.cr, subject, message");
     sendSMS ("88986761", "subject", "09:23pm test");
  }else{
    if(isset($_POST["data"])){
      $param= $_POST["data"];
      unset($_POST['data']);
      try {
        $param = json_decode($param);
        if ($param->token == "tokenPRD"
            && strlen($param->num) == 8
            && $param->subject !== null
            && $param->message !== null
        ){
          sendSMS($param->num, $param->subject, $param->message);
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
    }else{
      error_log("POST DATA INDEFINIDO");
    }
  }

  function sendSMS ($num, $subject, $msg){
    $msg = wordwrap($msg,70);
    error_log("SMS: ".$num."@icecr.com".$subject.$msg);
    $headers = 'From: SIDC@ice.go.cr' . "\r\n";
    $headers .= "Reply-To: SIDC@ice.go.cr\r\n";
    $headers .= "X-Mailer: PHP/".phpversion();
    mail($num."@icecr.com",$subject,$msg, $headers, '-f SIDC@ice.go.cr');
  }
?>
