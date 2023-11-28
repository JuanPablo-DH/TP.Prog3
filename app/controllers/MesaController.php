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

    public function alta($request, $response, $args)
    {
        $ret = "";

        $parametros = $request->getParsedBody();

        $mesa = new Mesa();
        $mesa->set_tipo_mesa($parametros["tipo_mesa"], self::VALIDAR_SETTER);

        $payload = json_encode($mesa->alta(LoginMiddleware::get_empleado_sin_validar($request)->id));
        
        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function baja($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $mesa = new Mesa();
        $mesa->set_id($parametros["numero_mesa"], self::VALIDAR_SETTER);

        $payload = json_encode($mesa->baja_logica(LoginMiddleware::get_empleado_sin_validar($request)->id));

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function modificar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $mesa = new Mesa();
        $mesa->set_id($parametros["numero_mesa"], self::VALIDAR_SETTER);
        $mesa->set_tipo_mesa($parametros["tipo_mesa"], self::VALIDAR_SETTER);

        $payload = json_encode($mesa->modificar(LoginMiddleware::get_empleado_sin_validar($request)->id));

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_todos($request, $response, $args)
    {
        $mesa = new Mesa();

        $payload = json_encode($mesa->traer_todos_alta());

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $mesa = new Mesa();
        $mesa->set_id($parametros["numero_mesa"], self::VALIDAR_SETTER);
        
        $payload = json_encode($mesa->traer_uno());

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function cobrar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $mesa = new Mesa();
        $mesa->set_id($parametros["id"], self::VALIDAR_SETTER);
        
        $payload = json_encode($mesa->cobrar());

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function cerrar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $mesa = new Mesa();
        $mesa->set_id($parametros["id"], self::VALIDAR_SETTER);
        
        $payload = json_encode($mesa->cerrar());

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_mas_usada($request, $response, $args)
    {
        $mesa = new Mesa();
        $payload = json_encode($mesa->traer_mas_usada());

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
}

?>