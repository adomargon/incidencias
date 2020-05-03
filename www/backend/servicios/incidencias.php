<?php
header("Access-Control-Allow-Origin: *");
header ("Content-type: application/json; charset=utf-8"); 

//--------------------- GET ------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    //Si envían el código de incidencia solo recuperamos esa incidencia, en otro caso las recuperamos todas
    if (isset($_GET["incidencia"])) {
        if ($_GET["incidencia"] == "") {
            header('HTTP/ 400 Solicitud incorrecta');
            echo json_encode(array("estado" => "error", "mensaje" => "Falta el código de incidencia"));
            exit;
        }

        $seleccion_sql = "SELECT * FROM incidencias WHERE id=\"" . $_GET["incidencia"] . "\"";
    } else {
        $seleccion_sql = "SELECT * FROM incidencias";
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

    header('HTTP/ 200 Solicitud correcta');
    echo json_encode(array(
        "estado" => "exito", 
        "mensaje" => "Incidencias enviadas", 
        "resultado" => $resultado
    ));

    exit;
}

//--------------------- POST------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST["fecha"]) || $_POST["fecha"] == "") {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array("estado" => "error", "mensaje" => "Falta el campo de fecha"));
        exit;
    }

    if (!isset($_POST["descripcion_corta"]) || $_POST["descripcion_corta"] == "") {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array("estado" => "error", "mensaje" => "Falta el campo de descripción corta"));
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

//--------------------- GET ------------------------------------------------
//--------------------- GET ------------------------------------------------


?>

//if ($_SERVER["REQUEST_METHOD"] == "PUT") {
 //  parse_str(file_get_contents('php://input'), $_POST);
   // var_dump($_POST);
    //exit;
//}
// $email = $_POST["email"]