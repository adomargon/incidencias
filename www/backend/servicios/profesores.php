<?php
header("Access-Control-Allow-Origin: *");
header ("Content-type: application/json; charset=utf-8"); 

//--------------------- GET ------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    //Si envían el código de profesor solo recuperamos esa profesor, en otro caso las recuperamos todas
    if (isset($_GET["profesor_id"])) {
        if ($_GET["profesor_id"] == "") {
            header('HTTP/ 400 Solicitud incorrecta');
            echo json_encode(array("estado" => "error", "mensaje" => "Falta el código de profesor"));
            exit;
        }
    }
       
    include "../../backend/base-datos/configuracion.php";

    $conexion = new mysqli(BD_SERVIDOR, BD_USUARIO, BD_CONTRASENA, BD_BASE_DATOS);
        
    if ($conexion->connect_errno) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Profesores no enviados: ($conexion->connect_errno) $conexion->connect_error"
        ));
        exit;
    }
    
    mysqli_set_charset($conexion, "utf8");

    $sql = "SELECT id, nombre, apellidos FROM profesores";
    if (isset($_GET["profesor_id"])) {
        $sql = $sql . " WHERE id=?";
    }
    $sql_preparada = $conexion->prepare($sql);
    if ($conexion->errno) {        
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Profesores no enviados: ($conexion->errno) $conexion->error"
        ));
        $conexion->close();
        exit;
    }
    if (isset($_GET["profesor_id"])) {
        $sql_preparada->bind_param("i", $_GET["profesor_id"]);
    }

    $sql_preparada->execute();
    if ($sql_preparada->errno) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Profesores no enviados: ($sql_preparada->errno) $sql_preparada->error"
        ));
        $conexion->close();
        exit;
    }

    $resultado = $sql_preparada->get_result();

    //Comprobamos que ha seleccionado al menos una fila
    if (isset($_GET["profesor_id"]) && $sql_preparada->affected_rows == 0) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "No se ha recuperado ningún profesor"
        ));
        $conexion->close();
        exit;
    }   

    $profesores = Array();
    while ($registro = $resultado->fetch_assoc()) {
        array_push($profesores, $registro);
    }

    $sql_preparada->close();
    $resultado->free();
    $conexion->close();

    header('HTTP/ 200 Solicitud correcta');
    echo json_encode(array(
        "estado" => "exito", 
        "mensaje" => "Profesores enviados", 
        "resultado" => $profesores
    ));

    exit;
}

//--------------------- POST------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST["nombre"]) || $_POST["nombre"] == "") {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array("estado" => "error", "mensaje" => "Falta el campo nombre"));
        exit;
    }
    
    if (!isset($_POST["apellidos"]) || $_POST["apellidos"] == "") {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array("estado" => "error", "mensaje" => "Falta el campo apellidos"));
        exit;
    }

    include "../../backend/base-datos/configuracion.php";

    $conexion = new mysqli(BD_SERVIDOR, BD_USUARIO, BD_CONTRASENA, BD_BASE_DATOS);        
    if ($conexion->connect_errno) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Profesor no creado: ($conexion->connect_errno) $conexion->connect_error"
        ));
        exit;
    }
        
    mysqli_set_charset($conexion, "utf8");

    $sql = "INSERT INTO profesores " 
        . "(nombre, apellidos) " 
        . "VALUES (?, ?)";

    $sql_preparada = $conexion->prepare($sql);
    if ($conexion->errno) {        
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Profesor no creado: ($conexion->errno) $conexion->error"
        ));
        $conexion->close();
        exit;
    }

    $sql_preparada->bind_param("ss", 
        $_POST["nombre"], 
        $_POST["apellidos"]
    );

    $sql_preparada->execute();
    if ($sql_preparada->errno) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Profesor no creado: ($sql_preparada->errno) $sql_preparada->error"
        ));
        $conexion->close();
        exit;
    }

    $sql_preparada->close();
    $conexion->close();

    header('HTTP/ 200 Solicitud correcta');
    echo json_encode(array(
        "estado" => "exito", 
        "mensaje" => "Profesor creado"
    ));

    exit;
}

//--------------------- DELETE ---------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    if (!isset($_GET["profesor_id"]) || $_GET["profesor_id"] == "") {
        header('HTTP/ 400 Eliminación incorrecta');
        echo json_encode(array("estado" => "error", "mensaje" => "Falta el código de profesor"));
        exit;
    }
    
    include "../../backend/base-datos/configuracion.php";
    
    $conexion = new mysqli(BD_SERVIDOR, BD_USUARIO, BD_CONTRASENA, BD_BASE_DATOS);
    
    if ($conexion->connect_errno) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Profesor no eliminado: ($conexion->connect_errno) $conexion->connect_error"
        ));
        exit;
    }
    
    mysqli_set_charset($conexion, "utf8");
    
    $sql = "DELETE FROM profesores WHERE id=?";

    $sql_preparada = $conexion->prepare($sql);
    if ($conexion->errno) {        
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Profesor no eliminado: ($conexion->errno) $conexion->error"
        ));
        $conexion->close();
        exit;
    }

    $sql_preparada->bind_param("i", $_GET["profesor_id"]);
    $sql_preparada->execute();
    if ($sql_preparada->errno) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Profesor no eliminado: ($sql_preparada->errno) $sql_preparada->error"
        ));
        $conexion->close();
        exit;
    }
    
    //Comprobamos que ha eliminado al menos una fila
    if ($sql_preparada->affected_rows == 0) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Profesor no eliminado"
        ));
        $conexion->close();
        exit;
    }   

    $sql_preparada->close();
    $conexion->close();

    header('HTTP/ 200 Solicitud correcta');
    echo json_encode(array(
        "estado" => "exito", 
        "mensaje" => "Profesor eliminado"
    ));

    exit;
}


//--------------------- PUT ------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    parse_str(file_get_contents('php://input'), $_PUT);

    if (!isset($_PUT["profesor_id"]) || $_PUT["profesor_id"] == "") {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array("estado" => "error", "mensaje" => "Falta el código de profesor"));
        exit;
    }

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
    array_push($parametros, $_PUT["profesor_id"]);

    $sql_preparada = $conexion->prepare($sql);
    if ($conexion->errno) {        
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Profesor no actualizado: ($conexion->errno) $conexion->error"
        ));
        $conexion->close();
        exit;
    }
    $sql_preparada->bind_param($tipos, ...$parametros);

    $sql_preparada->execute();
    if ($sql_preparada->errno) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "Profesor no actualizado: ($sql_preparada->errno) $sql_preparada->error"
        ));
        $conexion->close();
        exit;
    }

    $sql_preparada->close();
    $conexion->close();

    header('HTTP/ 200 Solicitud correcta');
    echo json_encode(array(
        "estado" => "exito", 
        "mensaje" => "Profesor actualizado"
    ));

    exit;    
}
?>