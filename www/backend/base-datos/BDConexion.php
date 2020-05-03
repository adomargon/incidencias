<?php
include "../base-datos/configuracion.php";

class BDConexion {
    private $conexion;
    public $error_numero = 0;
    public $error_mensaje = "";
    public $filas_afectadas = 0;
    public $resultado = NULL;

    //------------------------------------------------------------------------------------
    private function abrir_conexion() {
        $this->conexion = new mysqli(BD_SERVIDOR, BD_USUARIO, BD_CONTRASENA, BD_BASE_DATOS);
        
        if ($this->conexion->connect_errno) {
            $this->error_numero = $this->conexion->connect_errno;
            $this->error_mensaje = $this->conexion->connect_error;
            return false;
        } 
        
        mysqli_set_charset($this->conexion, "utf8");
        return true;
    }
    
    //------------------------------------------------------------------------------------
    private function cerrar_conexion() {
        mysqli_close($this->conexion);
    }

    //------------------------------------------------------------------------------------
    public function seleccionar($sql) {
        $this->abrir_conexion();
        if ($this->error_numero) {
            return false;
        }

        $resultado = $this->conexion->query($sql);
        if (!$resultado) {
            $this->error_numero = $this->conexion->errno;
            $this->error_mensaje = $this->conexion->error;
            return false;
        }

        $this->filas_afectadas = $this->conexion->affected_rows;

        $this->resultado = Array();
        while ($registro = $resultado->fetch_assoc()) {
            array_push($this->resultado, $registro);
        }
        $resultado->free();

        $this->cerrar_conexion(); 
        return true;       
    }

    //------------------------------------------------------------------------------------
    public function insertar($sql) {
        $this->abrir_conexion();
        if ($this->error_numero) {
            return false;
        }

        $resultado = $this->conexion->query($sql);
        if (!$resultado) {
            $this->error_numero = $this->conexion->errno;
            $this->error_mensaje = $this->conexion->error;
            return false;
        }

        $this->filas_afectadas = $resultado->num_rows;

        $this->cerrar_conexion(); 
        return true;
    }

    //------------------------------------------------------------------------------------
    public function eliminar($sql) {
        $this->abrir_conexion();
        if ($this->error_numero) {
            return false;
        }

        $resultado = $this->conexion->query($sql);
        if (!$resultado) {
            $this->error_numero = $this->conexion->errno;
            $this->error_mensaje = $this->conexion->error;
            return false;
        }
        
        $this->filas_afectadas = $this->conexion->affected_rows;

        $this->cerrar_conexion(); 
        return true;       
    }

    //------------------------------------------------------------------------------------
    public function actualizar($sql) {
        $this->abrir_conexion();
        if ($this->error_numero) {
            return false;
        }

        $resultado = $this->conexion->query($sql);
        if (!$resultado) {
            $this->error_numero = $this->conexion->errno;
            $this->error_mensaje = $this->conexion->error;
            return false;
        }
        
        $this->filas_afectadas = $this->conexion->affected_rows;

        $this->cerrar_conexion(); 
        return true;       
    }
}
?>