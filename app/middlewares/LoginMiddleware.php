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
        return ["login_token_error"=>"No esta seteado"];
    }
    private static function empleado_esta_seteado(Request $request)
    {
        $parametros = $request->getParsedBody();

        try
        {
            if(isset($parametros["mail"]) && isset($parametros["contrasenia"]))
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
                         "error"=>$respuesta_seteo);
        }

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try
        {
            AutentificadorJWT::verificarToken($token);
            $token_data = AutentificadorJWT::ObtenerData($token);
            $empleado = Empleado::get_alta($token_data->id);
            if($empleado !== null)
            {
                return array("ret"=>true,
                             "empleado"=>$empleado);
            }

            return array("ret"=>false,
                         "error"=>array("login_token_error"=>"No existe el empleado"));
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
                         "error"=>$respuesta_seteo);
        }

        $parametros = $request->getParsedBody();

        try
        {
            $mail = Empleado::validar_mail($parametros["mail"]);
            $contrasenia = Empleado::validar_contrasenia($parametros["contrasenia"]);

            $lista_empleados = Empleado::get_all_alta();
            if(count($lista_empleados) > 0)
            {
                foreach($lista_empleados as $empleado)
                {
                    if(strcmp($mail, $empleado->mail) == 0 && password_verify($contrasenia, $empleado->contrasenia))
                    {
                        return array("ret"=>true,
                                     "empleado"=>$empleado);
                    }
                }
            }

            return array("ret"=>false,
                         "error"=>array("login_empleado_error"=>"No existe el empleado"));
        }
        catch (Exception $e)
        {
            return array("ret"=>false, "error"=>$e->getMessage());
        }
    }
    private static function get_empleado_validando(Request $request)
    {
        if(self::token_esta_seteado($request) === true)
        {
            $respuesta = self::validar_token($request);
            if($respuesta["ret"])
            {
                return ["ret"=>true, "empleado"=>$respuesta["empleado"]];
            }
            else
            {
                return ["ret"=>false, "error"=>$respuesta["error"]];
            }
        }
        else if(self::empleado_esta_seteado($request) === true)
        {
            $respuesta = self::validar_empleado($request);
            if($respuesta["ret"])
            {
                return ["ret"=>true, "empleado"=>$respuesta["empleado"]];
            }
            else
            {
                return ["ret"=>false, "error"=>$respuesta["error"]];
            }
        }

        return ["ret"=>false, "error"=>["login_error"=>"Es necesario que este seteado el token o el empleado"]];
    }
    public static function get_empleado_sin_validar(Request $request)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros["mail"]) && isset($parametros["contrasenia"]))
        {
            $lista_empleados = Empleado::get_all_alta();
            if(count($lista_empleados) > 0)
            {
                foreach($lista_empleados as $empleado)
                {
                    if(strcmp($parametros["mail"], $empleado->mail) == 0 && password_verify($parametros["contrasenia"], $empleado->contrasenia))
                    {
                        return $empleado;
                    }
                }
            }
        }
        
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        return AutentificadorJWT::ObtenerData($token);
    }
    // Utilidades




    public function acceso_sistema_mesa(Request $request, RequestHandler $handler)
    {
        $resultado_validacion = self::get_empleado_validando($request);

        $response = new Response();

        if($resultado_validacion["ret"] === true)
        {
            $rol = $resultado_validacion["empleado"]->rol;
            if(strcmp($rol, "SOCIO") == 0 || strcmp($rol, "MOZO") == 0)
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
            $response->getBody()->write(json_encode($resultado_validacion["error"]));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function acceso_sistema_producto(Request $request, RequestHandler $handler)
    {
        $resultado_validacion = self::get_empleado_validando($request);

        $response = new Response();

        if($resultado_validacion["ret"] === true)
        {
            $rol = $resultado_validacion["empleado"]->rol;
            if(strcmp($rol, "SOCIO") == 0)
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
            $response->getBody()->write(json_encode($resultado_validacion["error"]));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function acceso_sistema_pedido(Request $request, RequestHandler $handler)
    {
        $resultado_validacion = self::get_empleado_validando($request);

        $response = new Response();

        if($resultado_validacion["ret"] === true)
        {
            $response = $handler->handle($request);
        }
        else
        {
            $response->getBody()->write(json_encode($resultado_validacion["error"]));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function acceso_sistema_comanda(Request $request, RequestHandler $handler)
    {
        $resultado_validacion = self::get_empleado_validando($request);

        $response = new Response();

        if($resultado_validacion["ret"] === true)
        {
            $rol = $resultado_validacion["empleado"]->rol;
            if(strcmp($rol, "SOCIO") == 0 || strcmp($rol, "MOZO") == 0)
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
            $response->getBody()->write(json_encode($resultado_validacion["error"]));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function acceso_sistema_cliente(Request $request, RequestHandler $handler)
    {
        $resultado_validacion = self::get_empleado_validando($request);

        $response = new Response();

        if($resultado_validacion["ret"] === true)
        {
            $response = $handler->handle($request);
        }
        else
        {
            $response->getBody()->write(json_encode($resultado_validacion["error"]));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function acceso_sistema_empleado(Request $request, RequestHandler $handler)
    {
        $resultado_validacion = self::get_empleado_validando($request);

        $response = new Response();

        if($resultado_validacion["ret"] === true)
        {
            $rol = $resultado_validacion["empleado"]->rol;
            if(strcmp($rol["rol"], "SOCIO") == 0)
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
            $response->getBody()->write(json_encode($resultado_validacion["error"]));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function acceso_sistema_crear_token(Request $request, RequestHandler $handler)
    {
        $resultado_validacion = self::get_empleado_validando($request);

        $response = new Response();

        if($resultado_validacion["ret"] === true)
        {
            $response = $handler->handle($request);
        }
        else
        {
            $response->getBody()->write(json_encode($resultado_validacion["error"]));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
}

?>