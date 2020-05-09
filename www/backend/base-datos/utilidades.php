<?php

include "configuracion.php";

function chequear_operacion($conexion, $codigo_tecnico, $mensaje_tecnico, $mensaje_error) {
    if ($codigo_tecnico) {
        header("HTTP/ 400 Solicitud incorrecta");
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => "$mensaje_error: ($codigo_tecnico) $mensaje_tecnico"
        ));

        $conexion->close();

        exit;
    }
}

function conectar_a_base_datos() {
    $conexion = new mysqli(BD_SERVIDOR, BD_USUARIO, BD_CONTRASENA, BD_BASE_DATOS);
    chequear_operacion(
        $conexion, 
        $conexion->connect_errno, 
        $conexion->connect_error, 
        "No se ha podido conectar a la base de datos"
    );

    mysqli_set_charset($conexion, "utf8");

    return $conexion;
}

function preparar_sentencia($conexion, $sql, $mensaje) {
    $sql_preparada = $conexion->prepare($sql);
    chequear_operacion($conexion, $conexion->errno, $conexion->error, $mensaje);
    return $sql_preparada;
}

function ejecutar_sentencia($conexion, $sql_preparada, $mensaje) {
    $sql_preparada->execute();
    chequear_operacion($conexion, $sql_preparada->errno, $sql_preparada->error, $mensaje);
}

function convertir_resultado_sql_a_arreglo($resultado_sql) {
    $resultado = Array();
    while ($registro = $resultado_sql->fetch_assoc()) {
        array_push($resultado, $registro);
    }

    $resultado_sql->free();
    
    return $resultado;
}

function comprobar_existencia_campo($campo, $mensaje) {
    if (!isset($campo) || $campo == "") {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array("estado" => "error", "mensaje" => $mensaje));
        exit;
    }
}

function enviar_mensaje_exito_y_finalizar($mensaje, $resultado = NULL) {
    header('HTTP/ 200 Solicitud correcta');
    echo json_encode(array(
        "estado" => "exito", 
        "mensaje" => $mensaje,
        "resultado" => $resultado
    ));

    exit;
}

function chequear_manipula_una_fila($conexion, $sql_preparada, $mensaje) {
    if ($sql_preparada->affected_rows == 0) {
        header('HTTP/ 400 Solicitud incorrecta');
        echo json_encode(array(
            "estado" => "error", 
            "mensaje" => $mensaje
        ));

        $sql_preparada->close();
        $conexion->close();

        exit;
    }
}
?>