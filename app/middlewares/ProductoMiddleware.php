<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "modelos/Producto.php";

class ProductoMiddleware
{
    public function validar_input_alta(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Producto::validar_nombre($parametros["nombre"]);
            Producto::validar_tipo($parametros["tipo"]);
            Producto::validar_stock($parametros["stock"]);
            Producto::validar_precio_unidades($parametros["precio_unidades"]);

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $response->getBody()->write($e->getMessage());
        }

        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_baja(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Producto::validar_numero_producto($parametros["numero_producto"]);

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $response->getBody()->write($e->getMessage());
        }

        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_modificar(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Producto::validar_numero_producto($parametros["numero_producto"]);
            Producto::validar_tipo($parametros["tipo"]);
            Producto::validar_nombre($parametros["nombre"]);
            Producto::validar_stock($parametros["stock"]);
            Producto::validar_precio_unidades($parametros["precio_unidades"]);
            Producto::validar_baja($parametros["baja"]);

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $response->getBody()->write($e->getMessage());
        }

        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_traer_uno(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getQueryParams();

        $response = new Response();

        try
        {
            Producto::validar_numero_producto($parametros["numero_producto"]);

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $response->getBody()->write($e->getMessage());
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
}

?>