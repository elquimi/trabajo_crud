<?php


interface generadorMonstruos{
     public function reset();
     public function addCabeza(string $tipo);
     public function addNumCabezas(int $cantidad);
      public function addTorso(string $tipo);
       public function addBrazos(string $tipo);
       public function addNumBrazos(int $cantidad);
       public function addPiernas(string $tipo);
       public function addNumPiernas(int $cantidad);
       public function getMonstruo(): Monstruo;

}












?>