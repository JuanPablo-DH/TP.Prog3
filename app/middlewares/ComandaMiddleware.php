<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "modelos/Comanda.php";

class ComandaMiddleware
{
    public function validar_input_alta(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();
        
        try
        {
            Comanda::validar_lista_pedidos(json_decode($parametros["lista_pedidos"], true));
            Comanda::validar_nombre_cliente($parametros["nombre_cliente"]);
            Comanda::validar_cantidad_clientes($parametros["cantidad_clientes"]);

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $payload = $e->getMessage();
            $response->getBody()->write($payload);
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_traer_uno(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getQueryParams();

        $response = new Response();

        try
        {
            Comanda::validar_id($parametros["id"]);

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $payload = $e->getMessage();
            $response->getBody()->write($payload);
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
}