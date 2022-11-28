<?php
// Cambiar imagen.txt por el resultado del API
	$data = file_get_contents('imagen.txt');
	print '<div style="text-align:center"><img src="data:image/png;base64,'.$data.'" alt="..."</div>';
?>
