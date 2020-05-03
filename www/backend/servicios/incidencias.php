<?php
header("Access-Control-Allow-Origin: *");
header ("Content-type: application/json; charset=utf-8"); 

//--------------------- GET ------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    //Si envían el código de incidencia solo recuperamos esa incidencia, en otro caso las recuperamos todas
    if (isset($_GET["incidencia_id"])) {
        if ($_GET["incidencia_id"] == "") {
            header('HTTP/ 400 Solicitud incorrecta');
            echo json_encode(array("estado" => "error", "mensaje" => "Falta el código de incidencia"));
            exit;
        }
    }
    
    $seleccion_sql = "SELECT * FROM incidencias";
    if (isset($_GET["incidencia_id"])) {
        $seleccion_sql = $seleccion_sql . " WHERE id=\"" . $_GET["incidencia_id"] . "\"";
    }
    
    include "{$_SERVER['DOCUMENT_ROOT']}/backend/base-datos/BDConexion.php";

    $conexion = new BDConexion();
    $resultado = $conexion->seleccionar($seleccion_sql);

    if ($conexion->error_numero) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Incidencias no enviadas: ($conexion->error_numero) $conexion->error_mensaje"
        ));
        exit;
    }

    //Comprobamos que ha seleccionado al menos una fila
    if (isset($_GET["incidencia_id"]) && $conexion->filas_afectadas == 0) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "No se ha recuperado ninguna incidencia"
        ));
        exit;
    }

    header('HTTP/ 200 Solicitud correcta');
    echo json_encode(array(
        "estado" => "exito", 
        "mensaje" => "Incidencias enviadas", 
        "resultado" => $conexion->resultado
    ));

    exit;
}

//--------------------- POST------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST["descripcion_corta"]) || $_POST["descripcion_corta"] == "") {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array("estado" => "error", "mensaje" => "Falta el campo de descripción corta"));
        exit;
    }
    
    if (!isset($_POST["fecha"]) || $_POST["fecha"] == "") {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array("estado" => "error", "mensaje" => "Falta el campo de fecha"));
        exit;
    }

    if (!isset($_POST["aula_id"]) || $_POST["aula_id"] == "") {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array("estado" => "error", "mensaje" => "Falta el campo de aula_id"));
        exit;
    }
    
    if (!isset($_POST["profesor_id"]) || $_POST["profesor_id"] == "") {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array("estado" => "error", "mensaje" => "Falta el campo de profesor_id"));
        exit;
    }
    
    $descripcion_corta = $_POST["descripcion_corta"];
    $fecha = $_POST["fecha"];
    $aula_id = $_POST["aula_id"];
    $profesor_id = $_POST["profesor_id"];

    //Campo opcional
    if (!isset($_POST["descripcion_larga"]) || $_POST["descripcion_larga"] == "")
        $descripcion_larga = NULL;
    else 
        $descripcion_larga = $_POST["descripcion_larga"];

    $insert_sql = "INSERT INTO `incidencias` " 
        . "(`descripcion_corta`, `descripcion_larga`, `fecha`, `aula_id`, `profesor_id`) " 
        . "VALUES "
        . "('$descripcion_corta', '$descripcion_larga', '$fecha', $aula_id, $profesor_id)";
        ;

    include "{$_SERVER['DOCUMENT_ROOT']}/backend/base-datos/BDConexion.php";

    $conexion = new BDConexion();
    $resultado = $conexion->insertar($insert_sql);
    
    if ($conexion->error_numero) {
        header('HTTP/ 400 Inserción incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Inserción no realizada: ($conexion->error_numero) $conexion->error_mensaje"
        ));
        exit;
    }

    header('HTTP/ 200 Inserción correcta');
    echo json_encode(array(
        "estado" => "exito", 
        "mensaje" => "Inserción realizada"
    ));

    exit;
}

//--------------------- DELETE ---------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    if (!isset($_GET["incidencia_id"]) || $_GET["incidencia_id"] == "") {
        header('HTTP/ 400 Eliminación incorrecta');
        echo json_encode(array("estado" => "error", "mensaje" => "Falta el código de incidencia"));
        exit;
    }
    
    $seleccion_sql = "DELETE FROM incidencias WHERE id=\"" . $_GET["incidencia_id"] . "\"";
    
    include "{$_SERVER['DOCUMENT_ROOT']}/backend/base-datos/BDConexion.php";

    $conexion = new BDConexion();
    $resultado = $conexion->eliminar($seleccion_sql);

    if ($conexion->error_numero) {
        header('HTTP/ 400 Eliminación incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Incidencia no eliminada: ($conexion->error_numero) $conexion->error_mensaje"
        ));
        exit;
    }

    //Comprobamos que ha eliminado al menos una fila
    if ($conexion->filas_afectadas == 0) {
        header('HTTP/ 400 Eliminación incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "No se ha eliminado ninguna incidencia"
        ));
        exit;
    }

    header('HTTP/ 200 Eliminación correcta');
    echo json_encode(array(
        "estado" => "exito", 
        "mensaje" => "Incidencia eliminada"
    ));

    exit;
}


//--------------------- PUT ------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    parse_str(file_get_contents('php://input'), $_PUT);

    if (!isset($_PUT["incidencia_id"]) || $_PUT["incidencia_id"] == "") {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array("estado" => "error", "mensaje" => "Falta el código de incidencia"));
        exit;
    }

    $update_sql = "UPDATE `incidencias` SET "; 
    $poner_coma = false;

    if (isset($_PUT["descripcion_corta"]) && $_PUT["descripcion_corta"] != "") {
        $update_sql = $update_sql . "descripcion_corta='" . $_PUT["descripcion_corta"] . "'";
        $poner_coma = true;
    }
    
    if (isset($_PUT["fecha"]) || $_PUT["fecha"] != "") {
        if ($poner_coma)
            $update_sql = $update_sql . ", ";
        $update_sql = $update_sql . "fecha='" . $_PUT["fecha"] . "'";
        $poner_coma = true;
    }

    if (isset($_PUT["aula_id"]) || $_PUT["aula_id"] != "") {
        if ($poner_coma)
            $update_sql = $update_sql . ", ";
        $update_sql = $update_sql . "aula_id=" . $_PUT["aula_id"];
        $poner_coma = true;
    }
    
    if (isset($_PUT["profesor_id"]) || $_PUT["profesor_id"] != "") {
        if ($poner_coma)
            $update_sql = $update_sql . ", ";
        $update_sql = $update_sql . "profesor_id=" . $_PUT["profesor_id"];
        $poner_coma = true;
    }
    
    if (isset($_PUT["descripcion_larga"]) || $_PUT["descripcion_larga"] != "") {
        if ($poner_coma)
            $update_sql = $update_sql . ", ";
        $update_sql = $update_sql . "descripcion_larga='" . $_PUT["descripcion_larga"] . "'";
        $poner_coma = true;
    }

    //No se ha enviado ninguna información
    if (!$poner_coma) {
        header('HTTP/ 400 Actualización incorrecta');
        echo json_encode(array("estado" => "error", "mensaje" => "Faltan parámetros"));
        exit;
    }

    $update_sql = $update_sql . " WHERE id=" . $_PUT["incidencia_id"];

    include "{$_SERVER['DOCUMENT_ROOT']}/backend/base-datos/BDConexion.php";

    $conexion = new BDConexion();
    $resultado = $conexion->actualizar($update_sql);
    
    if ($conexion->error_numero) {
        header('HTTP/ 400 Actualización incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Actualización no realizada: ($conexion->error_numero) $conexion->error_mensaje"
        ));
        exit;
    }

    //Comprobamos que ha actualizado al menos una fila
    if ($conexion->filas_afectadas == 0) {
        header('HTTP/ 400 Actualización incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "No se ha actualizado ninguna incidencia"
        ));
        exit;
    }

    header('HTTP/ 200 Actualización correcta');
    echo json_encode(array(
        "estado" => "exito", 
        "mensaje" => "Actualización realizada"
    ));

    exit;
}


?>

//if ($_SERVER["REQUEST_METHOD"] == "PUT") {
 //  parse_str(file_get_contents('php://input'), $_POST);
   // var_dump($_POST);
    //exit;
//}
// $email = $_POST["email"]