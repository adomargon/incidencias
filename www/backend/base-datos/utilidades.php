<?php

include "configuracion.php";

class Conexion {
    private $conexion = NULL;
    private $sql_preparada = NULL;

    //---------------------------------------------------------------------
    function chequear_operacion($conexion, $codigo_tecnico, $mensaje_tecnico, $mensaje_error) {
        if ($codigo_tecnico) {
            throw new Exception("$mensaje_error: ($codigo_tecnico) $mensaje_tecnico", $codigo_tecnico);            
        }
    }

    //---------------------------------------------------------------------
    function conectar_a_base_datos() {
        $this->conexion = new mysqli(BD_SERVIDOR, BD_USUARIO, BD_CONTRASENA, BD_BASE_DATOS);
        if ($conexion->connect_errno)
            throw new Exception($conexion->connect_error, $conexion->connect_errno);

        $this->conexion->set_charset("utf8");
    }

    //---------------------------------------------------------------------
    function preparar_sentencia($sql) {
        $this->sql_preparada = $this->conexion->prepare($sql);
        if ($conexion->errno)
            throw new Exception($conexion->error, $conexion->errno);
    }

    //---------------------------------------------------------------------
    function ligar_parametros($tipo, $parametros) {
        $this->sql_preparada->bind_param($tipo, $parametros);
        if ($sql_preparada->errno)
            throw new Exception($sql_preparada->error, $sql_preparada->errno);
    }

    //---------------------------------------------------------------------
    function ejecutar_sentencia() {
        $this->sql_preparada->execute();
        if ($this->sql_preparada->errno)
            throw new Exception($this->sql_preparada->error, $this->sql_preparada->errno);
    }

    //---------------------------------------------------------------------
    function conseguir_resultados() {
        $resultado_sql = $this->sql_preparada->get_result();

        $resultado = Array();
        while ($registro = $resultado_sql->fetch_assoc()) {
            array_push($resultado, $registro);
        }
    
        $resultado_sql->free();
        
        return $resultado;
    }

    //---------------------------------------------------------------------
    function cerrar() {
        if ($this->sql_preparada != NULL)
            $this->sql_preparada->close();

        if ($this->conexion != NULL)
            $this->conexion->close();
    }    

    // //---------------------------------------------------------------------
    // function chequear_manipula_una_fila() {
    //     if ($this->sql_preparada->affected_rows == 0) 
    //         throw new Exception("No se ha manipulado al menos un registro", 9999);
    // }
}
?>