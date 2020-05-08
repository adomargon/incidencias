<?php
header("Access-Control-Allow-Origin: *");
header ("Content-type: application/json; charset=utf-8"); 

include './utilidades.php';

//--------------------- GET ------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    //Si envían el código de profesor solo recuperamos esa profesor, en otro caso las recuperamos todas
    if (isset($_GET["profesor_id"])) {
        comprobar_existencia_campo($_GET["profesor_id"], "Falta el código del profesor");
    }
       
    $conexion = conectar_a_base_datos();

    $sql = "SELECT id, nombre, apellidos FROM profesores";
    if (isset($_GET["profesor_id"])) {
        $sql = $sql . " WHERE id=?";
    }
    $sql_preparada = preparar_sentencia($conexion, $sql, "Profesores no enviados");
    
    if (isset($_GET["profesor_id"])) {
        $sql_preparada->bind_param("i", $_GET["profesor_id"]);
    }

    ejecutar_sentencia($conexion, $sql_preparada, "Profesores no enviados");

    $resultado_sql = $sql_preparada->get_result();

    //Comprobamos que ha seleccionado al menos una fila
    if (isset($_GET["profesor_id"])) {
        chequear_manipula_una_fila($conexion, $sql_preparada, "Profesor no enviado");
    }   

    $resultado = convertir_resultado_sql_a_arreglo($resultado_sql);

    $sql_preparada->close();
    $conexion->close();

    enviar_mensaje_exito_y_finalizar("Profesores enviados", $resultado);
}

//--------------------- POST------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    comprobar_existencia_campo($_POST["nombre"], "Falta el nombre del profesor");
    comprobar_existencia_campo($_POST["apellidos"], "Faltan los apellidos del profesor");

    $conexion = conectar_a_base_datos();

    $sql = "INSERT INTO profesores " 
        . "(nombre, apellidos) " 
        . "VALUES (?, ?)";

    $sql_preparada = preparar_sentencia($conexion, $sql, "Profesor no creado");

    $sql_preparada->bind_param("ss", 
        $_POST["nombre"], 
        $_POST["apellidos"]
    );

    ejecutar_sentencia($conexion, $sql_preparada, "Profesor no creado");

    $sql_preparada->close();
    $conexion->close();

    enviar_mensaje_exito_y_finalizar("Profesor creado");
}

//--------------------- DELETE ---------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    comprobar_existencia_campo($_GET["profesor_id"], "Falta el código del profesor");
    
    $conexion = conectar_a_base_datos();
    
    $sql = "DELETE FROM profesores WHERE id=?";

    $sql_preparada = preparar_sentencia($conexion, $sql, "Profesor no eliminado");

    $sql_preparada->bind_param("i", $_GET["profesor_id"]);

    ejecutar_sentencia($conexion, $sql_preparada, "Profesor no eliminado");
    
    //Comprobamos que ha eliminado al menos una fila
    chequear_manipula_una_fila($conexion, $sql_preparada, "Profesor no eliminado");

    $sql_preparada->close();
    $conexion->close();

    enviar_mensaje_exito_y_finalizar("Profesor eliminado");
}

//--------------------- PUT ------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    parse_str(file_get_contents('php://input'), $_PUT);

    comprobar_existencia_campo($_PUT["profesor_id"], "Falta el código del profesor");

    $update_sql = "UPDATE profesores SET "; 
    $tipos = "";
    $parametros = Array();
    $poner_coma = false;

    if (isset($_PUT["nombre"]) && $_PUT["nombre"] != "") {
        $update_sql = $update_sql . "nombre=?";
        $tipos = $tipos . "s";
        array_push($parametros, $_PUT["nombre"]);
        $poner_coma = true;
    }
    
    if (isset($_PUT["apellidos"]) || $_PUT["apellidos"] != "") {
        if ($poner_coma)
            $update_sql = $update_sql . ", ";
        $update_sql = $update_sql . "apellidos=?";
        $tipos = $tipos . "s";
        array_push($parametros, $_PUT["apellidos"]);
        $poner_coma = true;
    }
    
    //No se ha enviado ninguna información
    if (!$poner_coma) {
        header('HTTP/ 400 Actualización incorrecta');
        echo json_encode(array("estado" => "error", "mensaje" => "Faltan parámetros"));
        exit;
    }
    
    $conexion = conectar_a_base_datos();
    
    $sql = $update_sql . " WHERE id=?";
    $tipos = $tipos . "i";
    array_push($parametros, $_PUT["profesor_id"]);

    $sql_preparada = preparar_sentencia($conexion, $sql, "Profesor no actualizado");

    $sql_preparada->bind_param($tipos, ...$parametros);

    ejecutar_sentencia($conexion, $sql_preparada, "Profesor no actualizado");
    
    $sql_preparada->close();
    $conexion->close();

    enviar_mensaje_exito_y_finalizar("Profesor actualizado");
}
?>