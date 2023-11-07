<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "Modelos/Producto.php";
require_once "Interfaces/IApiUsable.php";

class ProductoController implements IApiUsable
{
    public function alta($request, $response, $args)
    {
        $ret = "";

        $parametros = $request->getParsedBody();

        try
        {
            $producto = new Producto();
            $producto->set_nombre($parametros["nombre"]);
            $producto->set_tipo($parametros["tipo"]);
            $producto->set_stock($parametros["stock"]);
            $producto->set_precio_unidades($parametros["precio_unidades"]);
            $ret = $producto->alta();
        }
        catch(Exception $e)
        {
            $ret = ["ProductoController Exception"=>$e->getMessage()];
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
            $producto = new Producto();
            $producto->set_numero_producto($parametros["numero_producto"]);
            $ret = $producto->baja_logica();
        }
        catch(Exception $e)
        {
            $ret = ["ProductoController Exception"=>$e->getMessage()];
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
            $producto = new Producto();
            $producto->set_numero_producto($parametros["numero_producto"]);
            $producto->set_tipo($parametros["tipo"]);
            $producto->set_nombre($parametros["nombre"]);
            $producto->set_stock($parametros["stock"]);
            $producto->set_precio_unidades($parametros["precio_unidades"]);
            $producto->set_baja($parametros["baja"]);
            $ret = $producto->modificar();
        }
        catch(Exception $e)
        {
            $ret = ["ProductoController Exception"=>$e->getMessage()];
        }

        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_todos($request, $response, $args)
    {
        $ret = "";

        $producto = new Producto();
        $ret = $producto->traer_todos_sin_baja();

        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno($request, $response, $args)
    {
        $ret = "";

        try
        {
            $producto = new Producto();
            $producto->set_numero_producto($args["numero_producto"]);
            $ret = $producto->traer_uno();
        }
        catch(Exception $e)
        {
            $ret = ["ProductoController Exception"=>$e->getMessage()];
        }
        
        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
}


?>