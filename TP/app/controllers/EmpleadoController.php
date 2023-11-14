<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "modelos/Empleado.php";
require_once "interfaces/IApiUsable.php";

class EmpleadoController implements IApiUsable
{
    private const VALIDAR_SETTER = false;

    public function alta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $empleado = new Empleado();
        $empleado->set_nombre($parametros["nombre"], self::VALIDAR_SETTER);
        $empleado->set_apellido($parametros["apellido"], self::VALIDAR_SETTER);
        $empleado->set_dni($parametros["dni"], self::VALIDAR_SETTER);
        $empleado->set_rol($parametros["rol"], self::VALIDAR_SETTER);

        $response->getBody()->write(json_encode($empleado->alta($parametros["dni_empleado"])));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function baja($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $empleado = new Empleado();
        $empleado->set_numero_empleado($parametros["numero_empleado"], self::VALIDAR_SETTER);

        $response->getBody()->write(json_encode($empleado->baja_logica($parametros["dni_empleado"])));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function modificar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $empleado = new Empleado();
        $empleado->set_numero_empleado($parametros["numero_empleado"], self::VALIDAR_SETTER);
        $empleado->set_nombre($parametros["nombre"], self::VALIDAR_SETTER);
        $empleado->set_apellido($parametros["apellido"], self::VALIDAR_SETTER);
        $empleado->set_dni($parametros["dni"], self::VALIDAR_SETTER);
        $empleado->set_rol($parametros["rol"], self::VALIDAR_SETTER);
        $empleado->set_baja($parametros["baja"], self::VALIDAR_SETTER);

        $response->getBody()->write(json_encode($empleado->modificar($parametros["dni_empleado"])));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_todos($request, $response, $args)
    {
        $empleado = new Empleado();

        $response->getBody()->write(json_encode($empleado->traer_todos_alta()));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno($request, $response, $args)
    {
        $parametros = $request->getQueryParams();

        $empleado = new Empleado();
        $empleado->set_numero_empleado($parametros["numero_empleado"], self::VALIDAR_SETTER);
        
        $response->getBody()->write(json_encode($empleado->traer_uno()));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_todos_por_rol($request, $response, $args)
    {
        $parametros = $request->getQueryParams();

        $empleado = new Empleado();
        $empleado->set_rol($parametros["rol"], self::VALIDAR_SETTER);

        $response->getBody()->write(json_encode($empleado->traer_todos_por_rol_alta()));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno_por_rol($request, $response, $args)
    {
        $parametros = $request->getQueryParams();

        $empleado = new Empleado();
        $empleado->set_numero_empleado($parametros["numero_empleado"], self::VALIDAR_SETTER);
        $empleado->set_rol($parametros["rol"], self::VALIDAR_SETTER);
        
        $response->getBody()->write(json_encode($empleado->traer_uno_por_rol()));

        return $response->withHeader("Content-Type", "application/json");
    }
}

?>