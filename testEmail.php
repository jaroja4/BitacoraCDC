<?php

$sendEmail = new SendEmail();
$sendEmail->email = $_GET['mail'] ?? '';
$sendEmail->token = $_GET['token'] ?? '';
$sendEmail->Send();

class SendEmail{
    public $email="";
    public $token="";

    function __construct(){

        if(isset($_POST["email"])){
            $this->email= $_POST["email"];
        }
				if(isset($_POST["token"])){
            $this->token= $_POST["token"];
        }
    }

    function Send(){
        try {
					if ($this->email != "" && $this->token != ""){
						$to = "{$this->email}";
						$subject = "Asunto: TOKEN SIDC";

						$message = "<html>
													<head>
														<title>TOKEN SIDC</title>
													</head>
													<body>
														<p></p>
														<div>
															<p style='
															text-align: center;'>
																El siguiente TOKEN solo será efectivo una vez:</p>
																<h5 style='
																		text-align: center;
																		color: mediumblue;
																		font-size: 22px;'>
																			TOKEN: {$this->token}
															</h5>
														</div>
													</body>
												</html>";

						// Always set content-type when sending HTML email
						$headers = "MIME-Version: 1.0" . "\r\n";
						$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
						$headers .= 'From: <SIDC@ice.go.cr>' . "\r\n";

						error_log($this->token);

						$v = mail($to,$subject,$message,$headers);
						if($v) {
								echo "Success";
								error_log("Success email");
						} else {
								echo "Error";
								error_log("Error email");
						}
					}
					else{
						echo "email o token invalido. Ejemplo: http://operacionesti.icetel.ice/class/testMail.php?mail=jrojasval@ice.go.CR&token=abcd";
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
