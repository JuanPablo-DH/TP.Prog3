<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "modelos/Pedido.php";

class PedidoMiddleware
{
    public function validar_input_traer_uno(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getQueryParams();

        $response = new Response();

        try
        {
            Pedido::validar_numero_pedido($parametros["numero_pedido"]);

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $response->getBody()->write($e->getMessage());
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_elaborar(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Pedido::validar_numero_pedido($parametros["numero_pedido"]);
            Pedido::validar_fecha_terminado($parametros["minutos_elaboracion"]);

            $empleado = Empleado::get_alta_por_dni($parametros["dni_empleado"]);

            if(strcmp($empleado->rol, "socio") == 0 || strcmp($empleado->rol, "mozo") == 0)
            {
                throw new Exception(json_encode(["acceso_elaborar_pedido"=>"Solo pueden acceder a elaborar un pedido el 'bartender', 'cervezero' o 'cocinero'"]));
            }

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $response->getBody()->write($e->getMessage());
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_terminar(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Pedido::validar_numero_pedido($parametros["numero_pedido"]);

            $empleado = Empleado::get_alta_por_dni($parametros["dni_empleado"]);

            if(strcmp($empleado->rol, "socio") == 0 || strcmp($empleado->rol, "mozo") == 0)
            {
                throw new Exception(json_encode(["acceso_terminar_pedido"=>"Solo pueden acceder a terminar un pedido el 'bartender', 'cervezero' o 'cocinero'"]));
            }

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $response->getBody()->write($e->getMessage());
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_servir(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Pedido::validar_numero_pedido($parametros["numero_pedido"]);

            $empleado = Empleado::get_alta_por_dni($parametros["dni_empleado"]);

            if(strcmp($empleado->rol, "mozo") != 0)
            {
                throw new Exception(json_encode(["acceso_servir_pedido"=>"Solo pueden acceder a servir un pedido el 'mozo'"]));
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