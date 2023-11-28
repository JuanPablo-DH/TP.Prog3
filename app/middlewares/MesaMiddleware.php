<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

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
            Mesa::validar_tipo_mesa($parametros["tipo_mesa"]);

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
            Mesa::validar_id($parametros["id"]);

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $payload = $e->getMessage();
            $response->getBody()->write($payload);
        }

        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_modificar(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Mesa::validar_id($parametros["id"]);
            Mesa::validar_tipo_mesa($parametros["tipo_mesa"]);

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
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Mesa::validar_id($parametros["id"]);

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $payload = $e->getMessage();
            $response->getBody()->write($payload);
        }

        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_cobrar(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Mesa::validar_id($parametros["id"]);

            $rol = LoginMiddleware::get_empleado_sin_validar($request)->rol;

            if(strcmp($rol, "MOZO") != 0)
            {
                throw new Exception(json_encode(["acceso_cobrar_mesa"=>"Solo pueden acceder a cobrar una mesa el 'MOZO'"]));
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
    public function validar_input_cerrar(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Mesa::validar_id($parametros["id"]);

            $rol = LoginMiddleware::get_empleado_sin_validar($request)->rol;

            if(strcmp($rol, "SOCIO") != 0)
            {
                throw new Exception(json_encode(["acceso_cobrar_mesa"=>"Solo pueden acceder a cerrar una mesa el 'SOCIO'"]));
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

?>