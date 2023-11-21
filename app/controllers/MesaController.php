<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "modelos/Mesa.php";
require_once "interfaces/IApiUsable.php";

class MesaController implements IApiUsable
{
    private const VALIDAR_SETTER = false;

    private static function get_dni_empleado($request)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros["nombre_empleado"]) && isset($parametros["dni_empleado"]))
        {
            return intval(Input::limpiar($parametros["dni_empleado"]));
        }
        
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        return AutentificadorJWT::ObtenerData($token)->dni;
    }

    public function alta($request, $response, $args)
    {
        $ret = "";

        $parametros = $request->getParsedBody();

        $mesa = new Mesa();
        $mesa->set_tipo($parametros["tipo"], self::VALIDAR_SETTER);
        $mesa->set_cantidad_clientes_maxima($parametros["cantidad_clientes_maxima"], self::VALIDAR_SETTER);

        $response->getBody()->write(json_encode($mesa->alta(self::get_dni_empleado($request))));
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function baja($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $mesa = new Mesa();
        $mesa->set_numero_mesa($parametros["numero_mesa"], self::VALIDAR_SETTER);

        $response->getBody()->write(json_encode($mesa->baja_logica(self::get_dni_empleado($request))));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function modificar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $mesa = new Mesa();
        $mesa->set_numero_mesa($parametros["numero_mesa"], self::VALIDAR_SETTER);
        $mesa->set_tipo($parametros["tipo"], self::VALIDAR_SETTER);
        $mesa->set_cantidad_clientes_maxima($parametros["cantidad_clientes_maxima"], self::VALIDAR_SETTER);
        $mesa->set_baja($parametros["baja"], self::VALIDAR_SETTER);

        $response->getBody()->write(json_encode($mesa->modificar(self::get_dni_empleado($request))));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_todos($request, $response, $args)
    {
        $mesa = new Mesa();

        $response->getBody()->write(json_encode($mesa->traer_todos_alta()));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno($request, $response, $args)
    {
        $parametros = $request->getQueryParams();

        $mesa = new Mesa();
        $mesa->set_numero_mesa($parametros["numero_mesa"], self::VALIDAR_SETTER);
        
        $response->getBody()->write(json_encode($mesa->traer_uno()));

        return $response->withHeader("Content-Type", "application/json");
    }

    public function cobrar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $mesa = new Mesa();
        $mesa->set_numero_mesa($parametros["numero_mesa"], self::VALIDAR_SETTER);
        
        $response->getBody()->write(json_encode($mesa->cobrar()));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function cerrar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $mesa = new Mesa();
        $mesa->set_numero_mesa($parametros["numero_mesa"], self::VALIDAR_SETTER);
        
        $response->getBody()->write(json_encode($mesa->cerrar()));

        return $response->withHeader("Content-Type", "application/json");
    }
}

?>