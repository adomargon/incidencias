<?php
header("Access-Control-Allow-Origin: *");
header ("Content-type: application/json; charset=utf-8"); 

include "../base-datos/interfaces-clases.php";
include './ServicioRest.php';

$tabla = new Tabla(
    "profesores",
    [
        new Campo("id", "i", true),
        new Campo("nombre", "s", true),
        new Campo("apellidos", "s", true)
    ],
    "id"
);

$servicio = new ServicioRest($tabla);
$servicio->responder();

?>