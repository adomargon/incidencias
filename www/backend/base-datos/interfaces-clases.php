<?php
interface TablaInterface {
    public function conseguir_nombre();
    public function conseguir_campos();
    public function conseguir_campo($nombre);
    public function conseguir_clave();
}

interface CampoInterface {
    public function conseguir_nombre();
    public function conseguir_tipo();
    public function es_obligatorio();
}

class Tabla implements TablaInterface {
    private $nombre, $campos, $clave;

    public function __construct($nombre = NULL, $campos = NULL, $clave = NULL) {
        $this->nombre = $nombre;
        $this->campos = $campos;
        $this->clave = $clave;
    }

    public function conseguir_nombre() {
        return $this->nombre;
    }

    public function conseguir_campos() {
        return $this->campos;
    }

    public function conseguir_campo($nombre) {
        foreach ($this->campos as $campo) {
            if ($campo->conseguir_nombre() == $nombre)
                return $campo;
        }
        return NULL;
    }

    public function conseguir_clave() {
        return $this->clave;
    }
}

class Campo implements CampoInterface {
    private $nombre, $tipo, $oblitatorio;

    public function __construct($nombre = NULL, $tipo = NULL, $obligatorio = false) {
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->obligatorio = $obligatorio;
    }

    public function conseguir_nombre() {
        return $this->nombre;
    }

    public function conseguir_tipo() {
        return $this->tipo;
    }

    public function es_obligatorio() {
        return $this->obligatorio;
    }
}
?>