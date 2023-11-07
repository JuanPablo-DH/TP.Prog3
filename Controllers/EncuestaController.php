<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "Modelos/Encuesta.php";
require_once "Interfaces/IApiUsable.php";

class EncuestaController implements IApiUsable
{
    public function alta($request, $response, $args){}
    public function baja($request, $response, $args){}
    public function modificar($request, $response, $args){}
    public function traer_todos($request, $response, $args){}
    public function traer_uno($request, $response, $args){}
}

?>