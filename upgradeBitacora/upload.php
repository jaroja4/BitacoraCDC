<?php
$target_dir = ".".DIRECTORY_SEPARATOR;
$target_file = $target_dir . "esquema.sql";
if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
} else {
    echo "Sorry, there was an error uploading your file.";
}

$oldURL = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$arrayURL = explode("/", $oldURL);
unset($arrayURL[sizeof($arrayURL)-1]);
unset($arrayURL[sizeof($arrayURL)-1]);

$newURL = '';
foreach ($arrayURL as $itemURL) {
    $newURL = $newURL.$itemURL.'/';
}
$newURL = $newURL."gestion.html";



echo "<script> location.href='$newURL'; </script>";

?>