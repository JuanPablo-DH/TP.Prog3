<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "modelos/Empleado.php";

class LoginMiddleware
{
    // Utilidades
    private static function token_esta_seteado(Request $request)
    {
        $header = $request->getHeaderLine('Authorization');
        $explode = explode("Bearer", $header);
        if(isset($explode[1]))
        {
            return true;
        }
        return ["login_empleado_error"=>"No esta seteado"];
    }
    private static function empleado_esta_seteado(Request $request)
    {
        $parametros = $request->getParsedBody();

        try
        {
            if(isset($parametros["nombre_empleado"]) && isset($parametros["dni_empleado"]))
            {
                return true;
            }

            return ["login_empleado_error"=>"No esta seteado"];
        }
        catch(Exception $e)
        {
            return $e->getMessage();
        }
    }
    private static function validar_token(Request $request)
    {
        $respuesta_seteo = self::token_esta_seteado($request);

        if($respuesta_seteo !== true)
        {
            return array("ret"=>false,
                         "error"=>array("login_token_error"=>$respuesta_seteo));
        }

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try
        {
            AutentificadorJWT::verificarToken($token);
            return array("ret"=>true,
                         "token"=>$token);
        }
        catch (Exception $e)
        {
            return array("ret"=>false,
                         "error"=>array("login_token_error"=>$e->getMessage()));
        }
    }
    private static function validar_empleado(Request $request)
    {
        $respuesta_seteo = self::empleado_esta_seteado($request);

        if($respuesta_seteo !== true)
        {
            return array("ret"=>false,
                         "error"=>array("login_empleado_error"=>$respuesta_seteo));
        }

        $parametros = $request->getParsedBody();

        try
        {
            $dni = Empleado::validar_dni($parametros["dni_empleado"]);
            $nombre = Empleado::validar_nombre($parametros["nombre_empleado"]);

            $empleado = Empleado::get_alta_por_dni_y_nombre($dni, $nombre);
            if($empleado === null)
            {
                return array("ret"=>false,
                             "error"=>array("login_empleado_error"=>"No existe el empleado"));
            }

            return array("ret"=>true,
                         "empleado"=>$empleado);
        }
        catch (Exception $e)
        {
            return $e->getMessage();
        }
    }
    private static function get_rol(Request $request)
    {
        if(self::token_esta_seteado($request) === true)
        {
            $token = self::validar_token($request);
            if($token["ret"])
            {
                $rol = AutentificadorJWT::ObtenerData($token["token"])->rol;
                return ["ret"=>true, "rol"=>$rol];
            }
            else
            {
                return ["ret"=>false, "error"=>json_encode($token["error"])];
            }
        }
        else if(self::empleado_esta_seteado($request) === true)
        {
            $empleado = self::validar_empleado($request);
            if($empleado["ret"])
            {
                $rol = $empleado["empleado"]->rol;
                return ["ret"=>true, "rol"=>$rol];
            }
            else
            {
                return ["ret"=>false, "error"=>json_encode($empleado["error"])];
            }
        }

        return ["ret"=>false, "error"=>"Es necesario que este seteado el token o el empleado"];
    }
    // Utilidades




    public function acceso_sistema_mesa(Request $request, RequestHandler $handler)
    {
        $rol = self::get_rol($request);

        $response = new Response();

        if($rol["ret"] === true)
        {
            if(strcmp($rol["rol"], "socio") == 0 || strcmp($rol["rol"], "mozo") == 0)
            {
                $response = $handler->handle($request);
            }
            else
            {
                $response->getBody()->write(json_encode(["acceso_sistema_mesa_error"=>"Solo el empleado con rol de socio o mozo puede acceder al sistema de mesa"]));
            }
        }
        else
        {
            $response->getBody()->write(json_encode(array("acceso_sistema_mesa_error"=>$rol["error"])));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function acceso_sistema_producto(Request $request, RequestHandler $handler)
    {
        $rol = self::get_rol($request);

        $response = new Response();

        if($rol["ret"] === true)
        {
            if(strcmp($rol["rol"], "socio") == 0)
            {
                $response = $handler->handle($request);
            }
            else
            {
                $response->getBody()->write(json_encode(["acceso_sistema_producto_error"=>"Solo el empleado con rol de socio puede acceder al sistema de producto"]));
            }
        }
        else
        {
            $response->getBody()->write(json_encode(array("acceso_sistema_producto_error"=>$rol["error"])));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function acceso_sistema_pedido(Request $request, RequestHandler $handler)
    {
        $rol = self::get_rol($request);

        $response = new Response();

        if($rol["ret"] === true)
        {
            $response = $handler->handle($request);
        }
        else
        {
            $response->getBody()->write(json_encode(array("acceso_sistema_producto_error"=>$rol["error"])));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function acceso_sistema_comanda(Request $request, RequestHandler $handler)
    {
        $rol = self::get_rol($request);

        $response = new Response();

        if($rol["ret"] === true)
        {
            if(strcmp($rol["rol"], "socio") == 0 || strcmp($rol["rol"], "mozo") == 0)
            {
                $response = $handler->handle($request);
            }
            else
            {
                $response->getBody()->write(json_encode(["acceso_sistema_comanda_error"=>"Solo los empleados con rol de socio o mozo pueden acceder al sistema de comanda"]));
            }
        }
        else
        {
            $response->getBody()->write(json_encode(array("acceso_sistema_comanda_error"=>$rol["error"])));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function acceso_sistema_cliente(Request $request, RequestHandler $handler)
    {
        $rol = self::get_rol($request);

        $response = new Response();

        if($rol["ret"] === true)
        {
            $response = $handler->handle($request);
        }
        else
        {
            $response->getBody()->write(json_encode(array("acceso_sistema_producto_error"=>$rol["error"])));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function acceso_sistema_empleado(Request $request, RequestHandler $handler)
    {

        $rol = self::get_rol($request);

        $response = new Response();

        if($rol["ret"] === true)
        {
            if(strcmp($rol["rol"], "socio") == 0)
            {
                $response = $handler->handle($request);
            }
            else
            {
                $response->getBody()->write(json_encode(["acceso_sistema_empleado_error"=>"Solo el empleado con rol de socio puede acceder al sistema de empleado"]));
            }
        }
        else
        {
            $response->getBody()->write(json_encode(array("acceso_sistema_empleado_error"=>$rol["error"])));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
}

?>