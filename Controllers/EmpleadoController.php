<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "Modelos/Empleado.php";
require_once "Interfaces/IApiUsable.php";

class EmpleadoController implements IApiUsable
{
    public function alta($request, $response, $args)
    {
        $ret = "";

        $parametros = $request->getParsedBody();

        try
        {
            $empleado = new Empleado();
            $empleado->set_nombre($parametros["nombre"]);
            $empleado->set_apellido($parametros["apellido"]);
            $empleado->set_dni($parametros["dni"]);
            $empleado->set_rol($parametros["rol"]);
            $ret = $empleado->alta();
        }
        catch(Exception $e)
        {
            $ret = ["EmpleadoController Exception"=>$e->getMessage()];
        }

        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
    public function baja($request, $response, $args)
    {
        $ret = "";

        $parametros = $request->getParsedBody();

        try
        {
            $empleado = new Empleado();
            $empleado->set_numero_empleado($parametros["numero_empleado"]);
            $ret = $empleado->baja_logica();
        }
        catch(Exception $e)
        {
            $ret = ["EmpleadoController Exception"=>$e->getMessage()];
        }

        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
    public function modificar($request, $response, $args)
    {
        $ret = "";

        $parametros = $request->getParsedBody();

        try
        {
            $empleado = new Empleado();
            $empleado->set_numero_empleado($parametros["numero_empleado"]);
            $empleado->set_nombre($parametros["nombre"]);
            $empleado->set_apellido($parametros["apellido"]);
            $empleado->set_dni($parametros["dni"]);
            $empleado->set_rol($parametros["rol"]);
            $empleado->set_baja($parametros["baja"]);
            $ret = $empleado->modificar();
        }
        catch(Exception $e)
        {
            $ret = ["EmpleadoController Exception"=>$e->getMessage()];
        }

        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_todos($request, $response, $args)
    {
        $ret = "";

        $empleado = new Empleado();
        $ret = $empleado->traer_todos_sin_baja();

        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno($request, $response, $args)
    {
        $ret = "";

        try
        {
            $empleado = new Empleado();
            $empleado->set_numero_empleado($args["numero_empleado"]);
            $ret = $empleado->traer_uno();
        }
        catch(Exception $e)
        {
            $ret = ["EmpleadoController Exception"=>$e->getMessage()];
        }
        
        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_todos_por_rol($request, $response, $args)
    {
        $ret = "";

        try
        {
            $empleado = new Empleado();
            $empleado->set_rol($args["rol"]);
            $ret = $empleado->traer_todos_por_rol_sin_baja();
        }
        catch(Exception $e)
        {
            $ret = ["EmpleadoController Exception"=>$e->getMessage()];
        }

        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno_por_rol($request, $response, $args)
    {
        $ret = "";

        try
        {
            $empleado = new Empleado();
            $empleado->set_numero_empleado($args["numero_empleado"]);
            $empleado->set_rol($args["rol"]);
            $ret = $empleado->traer_uno_por_rol();
        }
        catch(Exception $e)
        {
            $ret = ["EmpleadoController Exception"=>$e->getMessage()];
        }
        
        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
}

?>