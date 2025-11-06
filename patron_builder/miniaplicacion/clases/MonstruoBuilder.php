<?php
require_once 'Monstruo.php';

class MonstruoBuilder {
    protected $monstruo;

    public function __construct() {
        $this->reset();
    }

    public function reset() {
        $this->monstruo = new Monstruo();
    }

    public function addCabeza($tipo) {
        
        $this->monstruo->setTipoCabeza($tipo);
        return $this;
    }

    public function addNumCabezas($cantidad) {
        $this->monstruo->setCantidadCabezas($cantidad);
        return $this;
    }

    public function addTorso($tipo) {
        $this->monstruo->setTipoTorso($tipo);
        return $this;
    }

    public function addBrazos($tipo) {
        $this->monstruo->setTipoBrazos($tipo);
        return $this;
    }

    public function addNumBrazos($cantidad) {
        $this->monstruo->setCantidadBrazos($cantidad);
        return $this;
    }

    public function addPiernas($tipo) {
        $this->monstruo->setTipoPiernas($tipo);
        return $this;
    }

    public function addNumPiernas($cantidad) {
        $this->monstruo->setCantidadPiernas($cantidad);
        return $this;
    }

    public function getMonstruo() {
        $resultado = $this->monstruo;
        $this->reset();
        return $resultado;
    }
}
?>