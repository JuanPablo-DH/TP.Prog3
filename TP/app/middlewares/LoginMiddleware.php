<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "modelos/Empleado.php";

class LoginMiddleware
{
    public function acceso_sistema_mesa(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            $dni = Empleado::validar_dni($parametros["dni_empleado"]);
            $nombre = Empleado::validar_nombre($parametros["nombre_empleado"]);
            $empleado = Empleado::get_alta_por_dni_y_nombre($dni, $nombre);

            if($empleado === null)
            {
                throw new Exception(json_encode(["login_error_empleado"=>"No existe el empleado"]));
            }

            if(strcmp($empleado->rol, "socio") != 0)
            {
                throw new Exception(json_encode(["acceso_error_empleado"=>"Solo el empleado con rol de socio puede acceder al sistema de mesa"]));
            }

            $response = $handler->handle($request);

        }
        catch(Exception $e)
        {
            $response->getBody()->write($e->getMessage());
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function acceso_sistema_producto(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            $dni = Empleado::validar_dni($parametros["dni_empleado"]);
            $nombre = Empleado::validar_nombre($parametros["nombre_empleado"]);
            $empleado = Empleado::get_alta_por_dni_y_nombre($dni, $nombre);

            if($empleado === null)
            {
                throw new Exception(json_encode(["login_error_empleado"=>"No existe el empleado"]));
            }

            if(strcmp($empleado->rol, "socio") != 0)
            {
                throw new Exception(json_encode(["acceso_error_empleado"=>"Solo el empleado con rol de socio puede acceder al sistema de producto"]));
            }

            $response = $handler->handle($request);

        }
        catch(Exception $e)
        {
            $response->getBody()->write($e->getMessage());
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function acceso_sistema_pedido(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            $dni = Empleado::validar_dni($parametros["dni_empleado"]);
            $nombre = Empleado::validar_nombre($parametros["nombre_empleado"]);
            $empleado = Empleado::get_alta_por_dni_y_nombre($dni, $nombre);

            if($empleado === null)
            {
                throw new Exception(json_encode(["login_error_empleado"=>"No existe el empleado"]));
            }

            $response = $handler->handle($request);

        }
        catch(Exception $e)
        {
            $response->getBody()->write($e->getMessage());
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function acceso_sistema_comanda(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            $dni = Empleado::validar_dni($parametros["dni_empleado"]);
            $nombre = Empleado::validar_nombre($parametros["nombre_empleado"]);
            $empleado = Empleado::get_alta_por_dni_y_nombre($dni, $nombre);

            if($empleado === null)
            {
                throw new Exception(json_encode(["login_error_empleado"=>"No existe el empleado"]));
            }

            if(strcmp($empleado->rol, "socio") != 0 &&
               strcmp($empleado->rol, "mozo") != 0)
            {
                throw new Exception(json_encode(["acceso_error_empleado"=>"Solo los empleados con rol de socio o mozo pueden acceder al sistema de comanda"]));
            }

            $response = $handler->handle($request);

        }
        catch(Exception $e)
        {
            $response->getBody()->write($e->getMessage());
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function acceso_sistema_cliente(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            $dni = Empleado::validar_dni($parametros["dni_empleado"]);
            $nombre = Empleado::validar_nombre($parametros["nombre_empleado"]);
            $empleado = Empleado::get_alta_por_dni_y_nombre($dni, $nombre);

            if($empleado === null)
            {
                throw new Exception(json_encode(["login_error_empleado"=>"No existe el empleado"]));
            }

            $response = $handler->handle($request);

        }
        catch(Exception $e)
        {
            $response->getBody()->write($e->getMessage());
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function acceso_sistema_empleado(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            $dni = Empleado::validar_dni($parametros["dni_empleado"]);
            $nombre = Empleado::validar_nombre($parametros["nombre_empleado"]);
            $empleado = Empleado::get_alta_por_dni_y_nombre($dni, $nombre);

            if($empleado === null)
            {
                throw new Exception(json_encode(["login_error_empleado"=>"No existe el empleado"]));
            }

            if(strcmp($empleado->rol, "socio") != 0)
            {
                throw new Exception(json_encode(["acceso_error_empleado"=>"Solo el empleado con rol de socio puede acceder al sistema de empleado"]));
            }

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