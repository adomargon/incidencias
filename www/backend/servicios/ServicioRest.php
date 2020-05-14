<?php
include "../base-datos/Conexion.php";

class ServicioRest {
    private $tabla;

    //---------------------------------------------------------------------
    public function __construct($tabla) {
        $this->tabla =  $tabla;
    }

    //---------------------------------------------------------------------
    public function responder() {
        switch($_SERVER["REQUEST_METHOD"]) {
            case "GET":
                return $this->responder_metodo_get();
            case "POST":
                return $this->responder_metodo_post();
            case "DELETE":
                return $this->responder_metodo_delete();
            case "PUT":
                return $this->responder_metodo_put();
        }
    }

    //---------------------------------------------------------------------
    private function responder_metodo_get() {
        try {
            //Si envían una clave recuperamos el campo de dicha clave, en otro caso las recuperamos todos los campos
            if (isset($_GET[$this->tabla->conseguir_clave()])) {
                $this->comprobar_existencia_campo(
                    $_GET[$this->tabla->conseguir_clave()], 
                    "Falta el valor de la clave " . $this->tabla->conseguir_clave()
                );
            }
                        
            $conexion = new Conexion;
            $conexion->conectar_a_base_datos();

            $sql = "SELECT * FROM " . $this->tabla->conseguir_nombre();            
            if (isset($_GET[$this->tabla->conseguir_clave()])) {
                $sql = $sql . " WHERE id=?";
            }
            $conexion->preparar_sentencia($sql);
            
            if (isset($_GET[$this->tabla->conseguir_clave()])) {
                $conexion->ligar_parametros("i", $_GET[$this->tabla->conseguir_clave()]);
            }            
            $conexion->ejecutar_sentencia();            
            $resultado = $conexion->conseguir_resultados();
            
            //Comprobamos que ha seleccionado al menos una fila
            //Si es un elemento no se envía un arreglo
            if (isset($_GET[$this->tabla->conseguir_clave()])) {
                $this->chequear_recupera_una_fila($resultado);
                $this->enviar_mensaje_exito("Registro enviado", $resultado[0]);
            } else {
                $this->enviar_mensaje_exito("Registros enviados", $resultado);
            }
        } catch (ConexionException $exc) {
            header("HTTP/ 400 Solicitud incorrecta");
            echo json_encode(array(
                "estado" => "error", 
                "mensaje" => "($exc.getCode()) $exc.getMessage()"
            ));
        } 
        finally {
            $conexion->cerrar();
            exit;
        }
    }
        
    //---------------------------------------------------------------------
    private function responder_metodo_delete() {
        try {
            $this->comprobar_existencia_campo(
                $_GET[$this->tabla->conseguir_clave()], 
                "Falta el valor de la clave " . $this->tabla->conseguir_clave()
            );
            
            $conexion = new Conexion;
            $conexion->conectar_a_base_datos();
            
            $sql = "DELETE FROM " . $this->tabla->conseguir_nombre() . " WHERE id=?";
                
            $conexion->preparar_sentencia($sql, "Registro no eliminado");
        
            $conexion->ligar_parametros(
                $this->tabla->conseguir_campo($this->tabla->conseguir_clave())->conseguir_tipo(), 
                $_GET[$this->tabla->conseguir_clave()]
            );
        
            $conexion->ejecutar_sentencia("Registro no eliminado");
            
            //Comprobamos que ha eliminado al menos una fila
            $conexion->chequear_manipula_una_fila("Registro no eliminado");
        
            $this->enviar_mensaje_exito("Registro eliminado");
        } catch (ConexionException $exc) {
            header("HTTP/ 400 Solicitud incorrecta");
            echo json_encode(array(
                "estado" => "error", 
                "mensaje" => "($exc.getCode()) $exc.getMessage()"
            ));
        } 
        finally {
            $conexion->cerrar();
            exit;
        }
    }

    //---------------------------------------------------------------------
    private function responder_metodo_post() {
        try {
            //Comprobamos que se han suministrado en la petición todos los campos obligatorios
            foreach ($this->tabla->conseguir_campos() as $campo) {
                if ($campo->conseguir_nombre() == $this->tabla->conseguir_clave())
                    continue;

                if ($campo->es_obligatorio())  {
                    $this->comprobar_existencia_campo(
                        $_POST[$campo->conseguir_nombre()], 
                        "Falta el campo " . $campo->conseguir_nombre()
                    );
                }
            }

            $conexion = new Conexion;
            $conexion->conectar_a_base_datos();

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
            $conexion->preparar_sentencia($sql, "Registro no creado");
            $conexion->ligar_parametros($tipos, ...$parametros);
            $conexion->ejecutar_sentencia("Registro no creado");

            $this->enviar_mensaje_exito("Registro creado");
        } catch (ConexionException $exc) {
            header("HTTP/ 400 Solicitud incorrecta");
            echo json_encode(array(
                "estado" => "error", 
                "mensaje" => "($exc.getCode()) $exc.getMessage()"
            ));
        } 
        finally {
            $conexion->cerrar();
            exit;
        }
    }

    //---------------------------------------------------------------------
    private function responder_metodo_put() {
        try {
            parse_str(file_get_contents('php://input'), $_PUT);

            $this->comprobar_existencia_campo(
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

            $conexion = new Conexion;
            $conexion->conectar_a_base_datos();
        
            $conexion->preparar_sentencia($sql, "Registro no actualizado");
            $conexion->ligar_parametros($tipos, ...$parametros);
        
            $conexion->ejecutar_sentencia("Registro no actualizado");
            
            // //Comprobamos que ha actualizado al menos una fila
            // $conexion->chequear_manipula_una_fila("Registro no actualizado");

            $this->enviar_mensaje_exito("Registro actualizado");
        } catch (ConexionException $exc) {
            header("HTTP/ 400 Solicitud incorrecta");
            echo json_encode(array(
                "estado" => "error", 
                "mensaje" => "($exc.getCode()) $exc.getMessage()"
            ));
        } 
        finally {
            $conexion->cerrar();
            exit;
        }
    }

    //---------------------------------------------------------------------
    private function comprobar_existencia_campo($campo, $mensaje) {
        if (!isset($campo) || $campo == "") {
            header('HTTP/ 400 Solicitud incorrecta');
            echo json_encode(array(
                "estado" => "error", 
                "mensaje" => "Falta el campo $campo"
            ));
            exit;
        }
    }

    //---------------------------------------------------------------------
    private function chequear_recupera_una_fila($resultado) {
        if (count($resultado) == 0) {
            header('HTTP/ 400 Solicitud incorrecta');
            echo json_encode(array(
                "estado" => "error", 
                "mensaje" => "No se ha recuperado ningún registro"
            ));
            exit;
        }
    }

    //---------------------------------------------------------------------
    private function enviar_mensaje_exito($mensaje, $resultado = NULL) {
        header('HTTP/ 200 Solicitud correcta');
        echo json_encode(array(
            "estado" => "exito", 
            "mensaje" => $mensaje,
            "resultado" => $resultado
        ));
    }
}
?>