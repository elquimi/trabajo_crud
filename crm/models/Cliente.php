<?php

class Cliente{

public function __construct(
    protected int $id, 
    protected string $nombre, 
    protected string $email, 
    protected string $telefono, 
    protected string $direccion, 
    protected string $etiqueta, 
    protected string $imagen, 
    protected string $nombre_fisico_imagen, 
    protected int $tipo_id, 
    protected string $comentarios){}


}

?>