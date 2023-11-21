<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

interface IApiUsable
{
	public function alta($request, $response, $args);
	public function baja($request, $response, $args);
	public function modificar($request, $response, $args);
	public function traer_todos($request, $response, $args);
	public function traer_uno($request, $response, $args);
}

?>