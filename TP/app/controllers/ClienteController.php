<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "modelos/Cliente.php";
require_once "modelos/Movimiento.php";
require_once "interfaces/IApiUsable.php";

class ClienteController implements IApiUsable
{
    private const VALIDAR_SETTER = false;

    public function alta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $cliente = new Cliente();
        $cliente->set_nombre($parametros["nombre"], self::VALIDAR_SETTER);

        $response->getBody()->write(json_encode($cliente->alta($parametros["dni_empleado"])));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function baja($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $cliente = new Cliente();
        $cliente->set_numero_cliente($parametros["numero_cliente"], self::VALIDAR_SETTER);

        $response->getBody()->write(json_encode($cliente->baja_logica($parametros["dni_empleado"])));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function modificar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $cliente = new Cliente();
        $cliente->set_numero_cliente($parametros["numero_cliente"], self::VALIDAR_SETTER);
        $cliente->set_nombre($parametros["nombre"], self::VALIDAR_SETTER);
        $cliente->set_baja($parametros["baja"], self::VALIDAR_SETTER);

        $response->getBody()->write(json_encode($cliente->modificar($parametros["dni_empleado"])));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_todos($request, $response, $args)
    {
        $cliente = new Cliente();

        $response->getBody()->write(json_encode($cliente->traer_todos_alta()));
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno($request, $response, $args)
    {
        $parametros = $request->getQueryParams();

        $cliente = new Cliente();
        $cliente->set_numero_cliente($parametros["numero_cliente"], self::VALIDAR_SETTER);
        
        $response->getBody()->write(json_encode($cliente->traer_uno()));
        
        return $response->withHeader("Content-Type", "application/json");
    }
}

?>