<?php
class Monstruo {
    
    private $tipo_cabeza = "";
    private $cantidad_cabezas = 0;
    private $tipo_torso = "";
    private $tipo_brazos = "";
    private $cantidad_brazos = 0;
    private $tipo_piernas = "";
    private $cantidad_piernas = 0;

    
    public function setTipoCabeza($tipo) {
        $this->tipo_cabeza = $tipo;
    }

    public function setCantidadCabezas($cantidad) {
        
        $this->cantidad_cabezas = $cantidad;
    }

    public function setTipoTorso($tipo) {
        $this->tipo_torso = $tipo;
    }

    public function setTipoBrazos($tipo) {
        $this->tipo_brazos = $tipo;
    }

    public function setCantidadBrazos($cantidad) {
        $this->cantidad_brazos = $cantidad;
    }

    public function setTipoPiernas($tipo) {
        $this->tipo_piernas = $tipo;
    }

    public function setCantidadPiernas($cantidad) {
        $this->cantidad_piernas = $cantidad;
    }

    public function mostrar() {
        echo "<h3>--- MONSTRUO CREADO ---</h3>";

        // CABEZA
        if ($this->cantidad_cabezas < 1) {
            echo "Monstruo sin cabeza<br>";
        } else if ($this->cantidad_cabezas == 1) {
            echo "Cabeza: Una de " . $this->tipo_cabeza . "<br>";
        } else {
            echo "Cabezas: " . $this->cantidad_cabezas . " de  " . $this->tipo_cabeza . "<br>";
        }

        // TORSO
        echo "Torso: " . $this->tipo_torso . "<br>";

        // BRAZOS
        if ($this->cantidad_brazos < 1) {
            echo "Brazos: Ninguno<br>";
        } else if ($this->cantidad_brazos == 1) {
            echo "Brazos: Uno " . $this->tipo_brazos . "<br>";
        } else {
            echo "Brazos: " . $this->cantidad_brazos . " de tipo " . $this->tipo_brazos . "<br>";
        }

        // PIERNAS
        if ($this->cantidad_piernas < 1) {
            echo "Piernas: Ninguna<br>";
        } else if ($this->cantidad_piernas == 1) {
            echo "Piernas: Un  " . $this->tipo_piernas . "<br>";
        } else {
            echo "Piernas: " . $this->cantidad_piernas . " de  tipo " . $this->tipo_piernas . "<br>";
        }
        echo "<hr>";
    }
}
?>