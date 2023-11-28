<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "modelos/Cliente.php";
require_once "modelos/Comanda.php";

class ClienteMiddleware
{
    public function validar_input_alta(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Cliente::validar_nombre($parametros["nombre"]);

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $payload = $e->getMessage();
            $response->getBody()->write($payload);
        }

        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_baja(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Cliente::validar_id($parametros["id"]);

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $payload = $e->getMessage();
            $response->getBody()->write($payload);
        }

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_modificar(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Cliente::validar_id($parametros["id"]);
            Cliente::validar_nombre($parametros["nombre"]);

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
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Cliente::validar_id($parametros["id"]);

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $payload = $e->getMessage();
            $response->getBody()->write($payload);
        }

        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_traer_pedidos(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Comanda::validar_id($parametros["id_comanda"]);
            Comanda::validar_id_mesa($parametros["id_mesa"]);

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

?>