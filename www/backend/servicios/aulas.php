<?php
header("Access-Control-Allow-Origin: *");
header ("Content-type: application/json; charset=utf-8"); 

include "../base-datos/interfaces-clases.php";
include './ServicioRest.php';

$tabla = new Tabla(
    "aulas",
    [
        new Campo("id", "i", true),
        new Campo("descripcion_corta", "s", true),
        new Campo("descripcion_larga", "s", true),
        new Campo("capacidad", "s", true)
    ],
    "id"
);

$servicio = new ServicioRest($tabla);
$servicio->responder();
?>