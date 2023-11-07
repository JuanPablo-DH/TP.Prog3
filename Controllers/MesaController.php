<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "Modelos/Mesa.php";
require_once "Interfaces/IApiUsable.php";

class MesaController implements IApiUsable
{
    public function alta($request, $response, $args)
    {
        $ret = "";

        $parametros = $request->getParsedBody();

        try
        {
            $mesa = new Mesa();
            $mesa->set_tipo($parametros["tipo"]);
            $mesa->set_cantidad_clientes_maxima($parametros["cantidad_clientes_maxima"]);
            $ret = $mesa->alta();
        }
        catch(Exception $e)
        {
            $ret = ["MesaController Exception"=>$e->getMessage()];
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
            $mesa = new Mesa();
            $mesa->set_numero_mesa($parametros["numero_mesa"]);
            $ret = $mesa->baja_logica();
        }
        catch(Exception $e)
        {
            $ret = ["MesaController Exception"=>$e->getMessage()];
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
            $mesa = new Mesa();
            $mesa->set_numero_mesa($parametros["numero_mesa"]);
            $mesa->set_tipo($parametros["tipo"]);
            $mesa->set_cantidad_clientes_maxima($parametros["cantidad_clientes_maxima"]);
            $mesa->set_baja($parametros["baja"]);
            $ret = $mesa->modificar();
        }
        catch(Exception $e)
        {
            $ret = ["MesaController Exception"=>$e->getMessage()];
        }

        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_todos($request, $response, $args)
    {
        $ret = "";

        $mesa = new Mesa();
        $ret = $mesa->traer_todos_sin_baja();

        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno($request, $response, $args)
    {
        $ret = "";

        try
        {
            $mesa = new Mesa();
            $mesa->set_numero_mesa($args["numero_mesa"]);
            $ret = $mesa->traer_uno();
        }
        catch(Exception $e)
        {
            $ret = ["MesaController Exception"=>$e->getMessage()];
        }
        
        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
}

?>