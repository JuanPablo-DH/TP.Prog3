<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "modelos/Empleado.php";

class EmpleadoMiddleware
{
    public function validar_input_alta(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Empleado::validar_nombre($parametros["nombre"]);
            Empleado::validar_apellido($parametros["apellido"]);
            Empleado::validar_dni($parametros["dni"]);
            Empleado::validar_rol($parametros["rol"]);

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
            Empleado::validar_id($parametros["numero_empleado"]);

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
            Empleado::validar_id($parametros["id"]);
            Empleado::validar_mail($parametros["mail"]);
            Empleado::validar_contrasenia($parametros["contrasenia"]);
            Empleado::validar_nombre($parametros["nombre"]);
            Empleado::validar_apellido($parametros["apellido"]);
            Empleado::validar_dni($parametros["dni"]);
            Empleado::validar_rol($parametros["rol"]);
            Empleado::validar_activo($parametros["activo"]);

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
            Empleado::validar_id($parametros["id"]);

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $payload = $e->getMessage();
            $response->getBody()->write($payload);
        }

        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_traer_todos_por_rol(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Empleado::validar_rol($parametros["rol"]);

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $payload = $e->getMessage();
            $response->getBody()->write($payload);
        }

        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_traer_uno_por_rol(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Empleado::validar_id($parametros["id"]);
            Empleado::validar_rol($parametros["rol"]);

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