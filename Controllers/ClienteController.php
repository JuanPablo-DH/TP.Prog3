<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "Modelos/Cliente.php";
require_once "Interfaces/IApiUsable.php";

class ClienteController implements IApiUsable
{
    public function alta($request, $response, $args)
    {
        $ret = "";

        $parametros = $request->getParsedBody();

        try
        {
            $cliente = new Cliente();
            $cliente->set_nombre($parametros["nombre"]);
            $ret = $cliente->alta();
        }
        catch(Exception $e)
        {
            $ret = ["ClienteController Exception"=>$e->getMessage()];
        }

        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
    public function baja($request, $response, $args)
    {
        $ret = "";

        $parametros = $request->getParsedBody();

        try
        {
            $cliente = new Cliente();
            $cliente->set_numero_cliente($parametros["numero_cliente"]);
            $ret = $cliente->baja_logica();
        }
        catch(Exception $e)
        {
            $ret = ["ClienteController Exception"=>$e->getMessage()];
        }

        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
    public function modificar($request, $response, $args)
    {
        $ret = "";

        $parametros = $request->getParsedBody();

        try
        {
            $cliente = new Cliente();
            $cliente->set_numero_cliente($parametros["numero_cliente"]);
            $cliente->set_nombre($parametros["nombre"]);
            $cliente->set_baja($parametros["baja"]);
            $ret = $cliente->modificar();
        }
        catch(Exception $e)
        {
            $ret = ["ClienteController Exception"=>$e->getMessage()];
        }

        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_todos($request, $response, $args)
    {
        $ret = "";

        $cliente = new Cliente();
        $ret = $cliente->traer_todos_sin_baja();

        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno($request, $response, $args)
    {
        $ret = "";

        try
        {
            $cliente = new Cliente();
            $cliente->set_numero_cliente($args["numero_cliente"]);
            $ret = $cliente->traer_uno();
        }
        catch(Exception $e)
        {
            $ret = ["ClienteController Exception"=>$e->getMessage()];
        }
        
        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
}

?>