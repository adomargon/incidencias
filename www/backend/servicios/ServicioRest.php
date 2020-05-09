<?php
include "../base-datos/utilidades.php";

class ServicioRest {
    private $tabla;

    public function __construct($tabla) {
        $this->tabla =  $tabla;
    }

    public function responder() {
        switch($_SERVER["REQUEST_METHOD"]) {
            case "GET":
                return $this->conseguir_metodo_get();
            case "POST":
                return $this->conseguir_metodo_post();
            case "DELETE":
                return $this->conseguir_metodo_delete();
            case "PUT":
                return $this->conseguir_metodo_put();
        }
    }

    public function conseguir_metodo_get() {
        //Si envían una clave recuperamos el campo de dicha clave, en otro caso las recuperamos todos los campos
        if (isset($_GET[$this->tabla->conseguir_clave()])) {
            comprobar_existencia_campo(
                $_GET[$this->tabla->conseguir_clave()], 
                "Tabla " . $this->tabla->conseguir_nombre() . ". Falta el valor de la clave " . $this->tabla->conseguir_clave()
            );
        }
            
        $conexion = conectar_a_base_datos();
        $sql = "SELECT * FROM " . $this->tabla->conseguir_nombre();

        if (isset($_GET[$this->tabla->conseguir_clave()])) {
            $sql = $sql . " WHERE id=?";
        }
        $sql_preparada = preparar_sentencia(
            $conexion, 
            $sql, 
            "Tabla " . $this->tabla->conseguir_nombre() . ". Registros no enviados"
        );
        
        if (isset($_GET[$this->tabla->conseguir_clave()])) {
            $sql_preparada->bind_param("i", $_GET[$this->tabla->conseguir_clave()]);
        }
    
        ejecutar_sentencia($conexion, $sql_preparada, "Tabla " . $this->tabla->conseguir_nombre() . ". Registros no enviados");
    
        $resultado_sql = $sql_preparada->get_result();
    
        //Comprobamos que ha seleccionado al menos una fila
        if (isset($_GET[$this->tabla->conseguir_clave()])) {
            chequear_manipula_una_fila($conexion, $sql_preparada, "Tabla " . $this->tabla->conseguir_nombre() . ". Registro no enviado");
        }   
    
        $resultado = convertir_resultado_sql_a_arreglo($resultado_sql);
    
        $sql_preparada->close();
        $conexion->close();
    
        enviar_mensaje_exito_y_finalizar("Tabla " . $this->tabla->conseguir_nombre() . ". Registros enviados", $resultado);
    }

    public function conseguir_metodo_delete() {
        comprobar_existencia_campo(
            $_GET[$this->tabla->conseguir_clave()], 
            "Falta el valor de la clave " . $this->tabla->conseguir_clave()
        );
    
        $conexion = conectar_a_base_datos();
        
        $sql = "DELETE FROM profesores WHERE id=?";
    
        $sql_preparada = preparar_sentencia($conexion, $sql, "Registro no eliminado");
    
        $sql_preparada->bind_param($this->tabla->conseguir_campo($this->tabla->conseguir_clave())->conseguir_tipo(), $_GET[$this->tabla->conseguir_clave()]);
    
        ejecutar_sentencia($conexion, $sql_preparada, "Registro no eliminado");
        
        //Comprobamos que ha eliminado al menos una fila
        chequear_manipula_una_fila($conexion, $sql_preparada, "Registro no eliminado");
    
        $sql_preparada->close();
        $conexion->close();
    
        enviar_mensaje_exito_y_finalizar("Registro eliminado");
    }

    public function conseguir_metodo_post() {
        //Comprobamos que se han suministrado en la petición todos los campos obligatorios
        foreach ($this->tabla->conseguir_campos() as $campo) {
            if ($campo->conseguir_nombre() == $this->tabla->conseguir_clave())
                continue;

            if ($campo->es_obligatorio())  {
                comprobar_existencia_campo(
                    $_POST[$campo->conseguir_nombre()], 
                    "Falta el campo " . $campo->conseguir_nombre()
                );
            }
        }

        $conexion = conectar_a_base_datos();

        //Preparamos la consulta concatenando los campos de los que tenemos información
        $campos = "";
        $interrogantes = "";
        $tipos = "";
        $parametros = Array();
        $poner_coma = false;
        foreach ($_POST as $clave => $valor) {
            if ($poner_coma) {
                $campos = "$campos , $clave";
                $interrogantes = "$interrogantes, ?";
            } else {
                $campos = $clave;
                $interrogantes = "?";
            }
            $tipos = $tipos . $this->tabla->conseguir_campo($clave)->conseguir_tipo();
            array_push($parametros, $valor);
            $poner_coma = true;
        }

        $sql = "INSERT INTO " . $this->tabla->conseguir_nombre() 
            . " ($campos)" 
            . " VALUES ($interrogantes)";
        $sql_preparada = preparar_sentencia($conexion, $sql, "Registro no creado");

        $sql_preparada->bind_param($tipos, ...$parametros);

        ejecutar_sentencia($conexion, $sql_preparada, "Registro no creado");

        $sql_preparada->close();
        $conexion->close();

        enviar_mensaje_exito_y_finalizar("Registro creado");
    }

    public function conseguir_metodo_put() {
        parse_str(file_get_contents('php://input'), $_PUT);

        comprobar_existencia_campo(
            $_PUT[$this->tabla->conseguir_clave()], 
            "Falta el valor de la clave " . $this->tabla->conseguir_clave()
        );
    
        $tipos = "";
        $parametros = Array();
        $poner_coma = false;

        //Preparamos la consulta concatenando los campos de los que tenemos información        
        $update_sql = "UPDATE " . $this->tabla->conseguir_nombre() . " SET "; 
        $tipos = "";
        $parametros = Array();
        $poner_coma = false;
        foreach ($_PUT as $clave => $valor) {
            if ($poner_coma) {
                $update_sql = $update_sql . ", $clave=?";
            } else {
                $update_sql = $update_sql . "$clave=?";
            }
            $tipos = $tipos . $this->tabla->conseguir_campo($clave)->conseguir_tipo();
            array_push($parametros, $valor);
            $poner_coma = true;
        }
                
        $sql = $update_sql . " WHERE id=?";
        $tipos = $tipos . "i";
        array_push($parametros, $_PUT[$this->tabla->conseguir_clave()]);

        //No se ha enviado ninguna información
        if (!$poner_coma) {
            header('HTTP/ 400 Actualización incorrecta');
            echo json_encode(array("estado" => "error", "mensaje" => "Faltan parámetros"));
            exit;
        }

        $conexion = conectar_a_base_datos();
    
        $sql_preparada = preparar_sentencia($conexion, $sql, "Registro no actualizado");
        $sql_preparada->bind_param($tipos, ...$parametros);
    
        ejecutar_sentencia($conexion, $sql_preparada, "Registro no actualizado");
        
        $sql_preparada->close();
        $conexion->close();
    
        enviar_mensaje_exito_y_finalizar("Registro actualizado");
    }
}
?>