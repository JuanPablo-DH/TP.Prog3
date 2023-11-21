<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "modelos/Cliente.php";
require_once "modelos/Movimiento.php";
require_once "interfaces/IApiUsable.php";

class ClienteController implements IApiUsable
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
        $parametros = $request->getParsedBody();

        $cliente = new Cliente();
        $cliente->set_nombre($parametros["nombre"], self::VALIDAR_SETTER);

        $response->getBody()->write(json_encode($cliente->alta(self::get_dni_empleado($request))));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function baja($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $cliente = new Cliente();
        $cliente->set_numero_cliente($parametros["numero_cliente"], self::VALIDAR_SETTER);

        $response->getBody()->write(json_encode($cliente->baja_logica(self::get_dni_empleado($request))));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function modificar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $cliente = new Cliente();
        $cliente->set_numero_cliente($parametros["numero_cliente"], self::VALIDAR_SETTER);
        $cliente->set_nombre($parametros["nombre"], self::VALIDAR_SETTER);
        $cliente->set_baja($parametros["baja"], self::VALIDAR_SETTER);

        $response->getBody()->write(json_encode($cliente->modificar(self::get_dni_empleado($request))));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_todos($request, $response, $args)
    {
        $cliente = new Cliente();

        $response->getBody()->write(json_encode($cliente->traer_todos_alta()));
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno($request, $response, $args)
    {
        $parametros = $request->getQueryParams();

        $cliente = new Cliente();
        $cliente->set_numero_cliente($parametros["numero_cliente"], self::VALIDAR_SETTER);
        
        $response->getBody()->write(json_encode($cliente->traer_uno()));
        
        return $response->withHeader("Content-Type", "application/json");
    }
}

?>