<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "modelos/Mesa.php";

class MesaMiddleware
{
    private static function get_rol_empleado($request)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros["nombre_empleado"]) && isset($parametros["dni_empleado"]))
        {
            $nombre = strtolower(Input::limpiar($parametros["nombre_empleado"]));
            $dni = intval(Input::limpiar($parametros["dni_empleado"]));
            $empleado = Empleado::get_alta_por_dni_y_nombre($dni, $nombre);
            return $empleado->rol;
        }
        
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        return AutentificadorJWT::ObtenerData($token)->rol;
    }

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
    public function validar_input_cobrar(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Mesa::validar_numero_mesa($parametros["numero_mesa"]);

            $rol = self::get_rol_empleado($request);

            if(strcmp($rol, "mozo") != 0)
            {
                throw new Exception(json_encode(["acceso_cobrar_mesa"=>"Solo pueden acceder a cobrar una mesa el 'mozo'"]));
            }

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $response->getBody()->write($e->getMessage());
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_cerrar(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Mesa::validar_numero_mesa($parametros["numero_mesa"]);

            $rol = self::get_rol_empleado($request);

            if(strcmp($rol, "socio") != 0)
            {
                throw new Exception(json_encode(["acceso_cobrar_mesa"=>"Solo pueden acceder a cerrar una mesa el 'socio'"]));
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