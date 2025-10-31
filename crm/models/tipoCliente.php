<?php
require_once 'Cliente.php';
class tipoCliente{
    public function __construct(
    protected int $id, 
    protected string $tipo, 
    protected string $notas){}
}
?>