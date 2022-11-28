<?php
	class SMS {
		public $token;
		public $num;
		public $subject;
		public $message;

		function __construct(){

		}

		function sendSMS(){
			$data["data"] = json_encode($this);

			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://10.149.137.121/nagiosxi/sendSMS.php',
				// CURLOPT_URL => 'localhost/BitacoraCDC/sendSMS.php',
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $data,
			));

			$response = curl_exec($curl);

			curl_close($curl);
		}

	}

?>
