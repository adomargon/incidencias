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
       
    include "../../backend/base-datos/configuracion.php";

    $conexion = new mysqli(BD_SERVIDOR, BD_USUARIO, BD_CONTRASENA, BD_BASE_DATOS);
        
    if ($conexion->connect_errno) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Incidencias no enviadas: ($conexion->connect_errno) $conexion->connect_error"
        ));
        exit;
    }
    
    mysqli_set_charset($conexion, "utf8");

    $sql = "SELECT id, descripcion_corta, descripcion_larga, fecha, aula_id, profesor_id FROM incidencias";
    if (isset($_GET["incidencia_id"])) {
        $sql = $sql . " WHERE id=?";
    }
    $sql_preparada = $conexion->prepare($sql);
    if ($conexion->errno) {        
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Incidencias no enviadas: ($conexion->errno) $conexion->error"
        ));
        $conexion->close();
        exit;
    }
    if (isset($_GET["incidencia_id"])) {
        $sql_preparada->bind_param("i", $_GET["incidencia_id"]);
    }

    $sql_preparada->execute();
    if ($sql_preparada->errno) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Incidencias no enviadas: ($sql_preparada->errno) $sql_preparada->error"
        ));
        $conexion->close();
        exit;
    }

    $resultado = $sql_preparada->get_result();

    //Comprobamos que ha seleccionado al menos una fila
    if (isset($_GET["incidencia_id"]) && $sql_preparada->affected_rows == 0) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "No se ha recuperado ninguna incidencia"
        ));
        $conexion->close();
        exit;
    }   

    $incidencias = Array();
    while ($registro = $resultado->fetch_assoc()) {
        array_push($incidencias, $registro);
    }

    $sql_preparada->close();
    $resultado->free();
    $conexion->close();

    header('HTTP/ 200 Solicitud correcta');
    echo json_encode(array(
        "estado" => "exito", 
        "mensaje" => "Incidencias enviadas", 
        "resultado" => $incidencias
    ));

    exit;
}

//--------------------- POST------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST["descripcion_corta"]) || $_POST["descripcion_corta"] == "") {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array("estado" => "error", "mensaje" => "Falta el campo descripción corta"));
        exit;
    }
    
    if (!isset($_POST["fecha"]) || $_POST["fecha"] == "") {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array("estado" => "error", "mensaje" => "Falta el campo fecha"));
        exit;
    }

    if (!isset($_POST["aula_id"]) || $_POST["aula_id"] == "") {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array("estado" => "error", "mensaje" => "Falta el campo aula_id"));
        exit;
    }
    
    if (!isset($_POST["profesor_id"]) || $_POST["profesor_id"] == "") {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array("estado" => "error", "mensaje" => "Falta el campo profesor_id"));
        exit;
    }
    
    //Campo opcional
    if (!isset($_POST["descripcion_larga"]) || $_POST["descripcion_larga"] == "")
        $descripcion_larga = NULL;
    else 
        $descripcion_larga = $_POST["descripcion_larga"];

    include "../../backend/base-datos/configuracion.php";

    $conexion = new mysqli(BD_SERVIDOR, BD_USUARIO, BD_CONTRASENA, BD_BASE_DATOS);        
    if ($conexion->connect_errno) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Incidencia no creada: ($conexion->connect_errno) $conexion->connect_error"
        ));
        exit;
    }
        
    mysqli_set_charset($conexion, "utf8");

    $sql = "INSERT INTO `incidencias` " 
        . "(`descripcion_corta`, `descripcion_larga`, `fecha`, `aula_id`, `profesor_id`) " 
        . "VALUES (?, ?, ?, ?, ?)";

    $sql_preparada = $conexion->prepare($sql);
    if ($conexion->errno) {        
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Incidencia no creada: ($conexion->errno) $conexion->error"
        ));
        $conexion->close();
        exit;
    }

    $sql_preparada->bind_param("sssii", 
        $_POST["descripcion_corta"], 
        $_POST["descripcion_larga"], 
        $_POST["fecha"], 
        $_POST["aula_id"], 
        $_POST["profesor_id"]
    );

    $sql_preparada->execute();
    if ($sql_preparada->errno) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Incidencia no creada: ($sql_preparada->errno) $sql_preparada->error"
        ));
        $conexion->close();
        exit;
    }

    $sql_preparada->close();
    $conexion->close();

    header('HTTP/ 200 Solicitud correcta');
    echo json_encode(array(
        "estado" => "exito", 
        "mensaje" => "Incidencia creada"
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
    
    include "../../backend/base-datos/configuracion.php";
    
    $conexion = new mysqli(BD_SERVIDOR, BD_USUARIO, BD_CONTRASENA, BD_BASE_DATOS);
    
    if ($conexion->connect_errno) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Incidencia no eliminada: ($conexion->connect_errno) $conexion->connect_error"
        ));
        exit;
    }
    
    mysqli_set_charset($conexion, "utf8");
    
    $sql = "DELETE FROM incidencias WHERE id=?";

    $sql_preparada = $conexion->prepare($sql);
    if ($conexion->errno) {        
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Incidencia no eliminada: ($conexion->errno) $conexion->error"
        ));
        $conexion->close();
        exit;
    }

    $sql_preparada->bind_param("i", $_GET["incidencia_id"]);
    $sql_preparada->execute();
    if ($sql_preparada->errno) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Incidencia no eliminada: ($sql_preparada->errno) $sql_preparada->error"
        ));
        $conexion->close();
        exit;
    }
    
    //Comprobamos que ha eliminado al menos una fila
    if ($sql_preparada->affected_rows == 0) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Incidencia no eliminada"
        ));
        $conexion->close();
        exit;
    }   

    $sql_preparada->close();
    $conexion->close();

    header('HTTP/ 200 Solicitud correcta');
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
    $tipos = "";
    $poner_coma = false;

    if (isset($_PUT["descripcion_corta"]) && $_PUT["descripcion_corta"] != "") {
        $update_sql = $update_sql . "descripcion_corta=?";
        $tipos = $tipos . "s";
        $poner_coma = true;
    }
    
    if (isset($_PUT["descripcion_larga"]) || $_PUT["descripcion_larga"] != "") {
        if ($poner_coma)
            $update_sql = $update_sql . ", ";
        $update_sql = $update_sql . "descripcion_larga=?";
        $tipos = $tipos . "s";
        $poner_coma = true;
    }

    if (isset($_PUT["fecha"]) || $_PUT["fecha"] != "") {
        if ($poner_coma)
            $update_sql = $update_sql . ", ";
        $update_sql = $update_sql . "fecha=?";
        $tipos = $tipos . "s";
        $poner_coma = true;
    }

    if (isset($_PUT["aula_id"]) || $_PUT["aula_id"] != "") {
        if ($poner_coma)
            $update_sql = $update_sql . ", ";
        $update_sql = $update_sql . "aula_id=?";
        $tipos = $tipos . "i";
        $poner_coma = true;
    }
    
    if (isset($_PUT["profesor_id"]) || $_PUT["profesor_id"] != "") {
        if ($poner_coma)
            $update_sql = $update_sql . ", ";
        $update_sql = $update_sql . "profesor_id=?";
        $tipos = $tipos . "i";
        $poner_coma = true;
    }
    
    //No se ha enviado ninguna información
    if (!$poner_coma) {
        header('HTTP/ 400 Actualización incorrecta');
        echo json_encode(array("estado" => "error", "mensaje" => "Faltan parámetros"));
        exit;
    }
    
    include "../../backend/base-datos/configuracion.php";
    
    $conexion = new mysqli(BD_SERVIDOR, BD_USUARIO, BD_CONTRASENA, BD_BASE_DATOS);
    
    if ($conexion->connect_errno) {
        header('HTTP/ 400 Actualización incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Actualización no realizada: ($conexion->connect_errno) $conexion->connect_error"
        ));
        exit;
    }
    
    mysqli_set_charset($conexion, "utf8");
    
    $sql = $update_sql . " WHERE id=?";
    $tipos = $tipos . "i";

    $sql_preparada = $conexion->prepare($sql);
    if ($conexion->errno) {        
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Incidencia no actualizada: ($conexion->errno) $conexion->error"
        ));
        $conexion->close();
        exit;
    }

    $sql_preparada->bind_param($tipos, 
        $_PUT["descripcion_corta"], 
        $_PUT["descripcion_larga"], 
        $_PUT["fecha"], 
        $_PUT["aula_id"], 
        $_PUT["profesor_id"],
        $_PUT["incidencia_id"]
    );

    $sql_preparada->execute();
    if ($sql_preparada->errno) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Incidencia no actualizada: ($sql_preparada->errno) $sql_preparada->error"
        ));
        $conexion->close();
        exit;
    }

    // //Comprobamos que ha actualizado al menos una fila
    // if ($sql_preparada->affected_rows == 0) {
    //     header('HTTP/ 400 Actualización incorrecta');
    //     echo json_encode(array(
    //         "estado" => "error", 
    //         "mensaje" => "No se ha actualizado ninguna incidencia"
    //     ));
    //     $conexion->close();
    //     exit;
    // }

    $sql_preparada->close();
    $conexion->close();

    header('HTTP/ 200 Solicitud correcta');
    echo json_encode(array(
        "estado" => "exito", 
        "mensaje" => "Incidencia actualizada"
    ));

    exit;    
}
?>