<?php
header("Access-Control-Allow-Origin: *");
header ("Content-type: application/json; charset=utf-8"); 

include "../base-datos/interfaces-clases.php";
include './ServicioRest.php';

$tabla = new Tabla(
    "incidencias",
    [
        new Campo("id", "i", true),
        new Campo("descripcion_corta", "s", true),
        new Campo("descripcion_larga", "s", false),
        new Campo("fecha", "s", true),
        new Campo("aula_id", "i", true),
        new Campo("profesor_id", "i", true),
    ],
    "id"
);

$servicio = new ServicioRest($tabla);
$servicio->responder();
?>