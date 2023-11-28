<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "modelos/Pedido.php";

class PedidoMiddleware
{
    public function validar_input_traer_uno(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Pedido::validar_id($parametros["id"]);

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $payload = $e->getMessage();
            $response->getBody()->write($payload);
        }

        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_elaborar(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Pedido::validar_id($parametros["id"]);

            $rol = LoginMiddleware::get_empleado_sin_validar($request)->rol;

            if(strcmp($rol, "SOCIO") == 0 || strcmp($rol, "MOZO") == 0)
            {
                throw new Exception(json_encode(["acceso_elaborar_pedido"=>"Solo pueden acceder a elaborar un pedido el 'bartender', 'cervezero' o 'cocinero'"]));
            }

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $payload = $e->getMessage();
            $response->getBody()->write($payload);
        }

        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_terminar(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Pedido::validar_id($parametros["id"]);

            $rol = LoginMiddleware::get_empleado_sin_validar($request)->rol;

            if(strcmp($rol, "SOCIO") == 0 || strcmp($rol, "MOZO") == 0)
            {
                throw new Exception(json_encode(["acceso_terminar_pedido"=>"Solo pueden acceder a terminar un pedido el 'bartender', 'cervezero' o 'cocinero'"]));
            }

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $payload = $e->getMessage();
            $response->getBody()->write($payload);
        }

        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_servir(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Pedido::validar_id($parametros["id"]);

            $rol = LoginMiddleware::get_empleado_sin_validar($request)->rol;

            if(strcmp($rol, "MOZO") != 0)
            {
                throw new Exception(json_encode(["acceso_servir_pedido"=>"Solo pueden acceder a servir un pedido el 'mozo'"]));
            }

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