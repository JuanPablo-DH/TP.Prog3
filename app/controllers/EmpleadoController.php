<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "modelos/Empleado.php";
require_once "utils/AutentificadorJWT.php";
require_once "interfaces/IApiUsable.php";

class EmpleadoController implements IApiUsable
{
    private const VALIDAR_SETTER = false;
    
    public function alta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $empleado = new Empleado();
        $empleado->set_mail($parametros["mail"], self::VALIDAR_SETTER);
        $empleado->set_contrasenia($parametros["contrasenia"], self::VALIDAR_SETTER);
        $empleado->set_nombre($parametros["nombre"], self::VALIDAR_SETTER);
        $empleado->set_apellido($parametros["apellido"], self::VALIDAR_SETTER);
        $empleado->set_dni($parametros["dni"], self::VALIDAR_SETTER);
        $empleado->set_rol($parametros["rol"], self::VALIDAR_SETTER);
        $empleado->set_activo($parametros["activo"], self::VALIDAR_SETTER);

        $payload = json_encode($empleado->alta(LoginMiddleware::get_empleado_sin_validar($request)->id));

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function baja($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $empleado = new Empleado();
        $empleado->set_id($parametros["id"], self::VALIDAR_SETTER);

        $payload = json_encode($empleado->baja_logica(LoginMiddleware::get_empleado_sin_validar($request)->id));

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function modificar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $empleado = new Empleado();
        $empleado->set_id($parametros["id"], self::VALIDAR_SETTER);
        $empleado->set_mail($parametros["mail"], self::VALIDAR_SETTER);
        $empleado->set_contrasenia($parametros["contrasenia"], self::VALIDAR_SETTER);
        $empleado->set_nombre($parametros["nombre"], self::VALIDAR_SETTER);
        $empleado->set_apellido($parametros["apellido"], self::VALIDAR_SETTER);
        $empleado->set_dni($parametros["dni"], self::VALIDAR_SETTER);
        $empleado->set_rol($parametros["rol"], self::VALIDAR_SETTER);
        $empleado->set_activo($parametros["activo"], self::VALIDAR_SETTER);

        $payload = json_encode($empleado->modificar(LoginMiddleware::get_empleado_sin_validar($request)->id));

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_todos($request, $response, $args)
    {
        $empleado = new Empleado();

        $payload = json_encode($empleado->traer_todos_alta());

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $empleado = new Empleado();
        $empleado->set_id($parametros["id"], self::VALIDAR_SETTER);
        
        $payload = json_encode($empleado->traer_uno());

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_todos_por_rol($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $empleado = new Empleado();
        $empleado->set_rol($parametros["rol"], self::VALIDAR_SETTER);

        $payload = json_encode($empleado->traer_todos_por_rol_alta());

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno_por_rol($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $empleado = new Empleado();
        $empleado->set_id($parametros["id"], self::VALIDAR_SETTER);
        $empleado->set_rol($parametros["rol"], self::VALIDAR_SETTER);
        
        $payload = json_encode($empleado->traer_uno_por_rol());

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function crear_token($request, $response, $args)
    {
        $payload = "";

        try
        {
            $empleado = Empleado::get_alta(LoginMiddleware::get_empleado_sin_validar($request)->id);
            if($empleado !== null)
            {
                $datos = array("id"=>$empleado->id, "rol"=>$empleado->rol);
                $payload = json_encode(array("token"=>AutentificadorJWT::CrearToken($datos)));
            }
            else
            {
                $payload = json_encode(array("crear_token_error"=>"El empleado no existe"));
            }
            
        }
        catch(Exception $e)
        {
            $payload = $e->getMessage();
        }

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
}

?>