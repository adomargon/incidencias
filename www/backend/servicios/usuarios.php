
<?php
//docker run -d --port 80:80 --volume ../../www/:/var/www/html/ apache_php_server

header("Access-Control-Allow-Origin: *");
header ("Content-type: application/json; charset=utf-8"); 

// if (!isset($_GET["operacion"]) || $_GET["operacion"]=="") {
// 	header('HTTP/ 400 Solicitud incorrecta');
//     echo json_encode(array("estado" => "error", "tipo" => "Código de operación incorrecto"));
//     exit();
// }


?>