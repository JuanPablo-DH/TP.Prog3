<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "modelos/Mesa.php";

class MesaMiddleware
{
    public function validar_input_alta(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Mesa::validar_tipo($parametros["tipo"]);
            Mesa::validar_cantidad_clientes_maxima($parametros["cantidad_clientes_maxima"], $parametros["tipo"]);

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
            Mesa::validar_numero_mesa($parametros["numero_mesa"]);

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
            Mesa::validar_numero_mesa($parametros["numero_mesa"]);
            Mesa::validar_tipo($parametros["tipo"]);
            Mesa::validar_cantidad_clientes_maxima($parametros["cantidad_clientes_maxima"], $parametros["tipo"]);
            Mesa::validar_baja($parametros["baja"]);

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
            Mesa::validar_numero_mesa($parametros["numero_mesa"]);

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