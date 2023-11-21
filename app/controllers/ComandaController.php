<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "modelos/Comanda.php";
require_once "interfaces/IApiUsable.php";

class ComandaController implements IApiUsable
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
        
        $comanda = new Comanda();
        $comanda->set_lista_pedidos(json_decode($parametros["lista_pedidos"], true), self::VALIDAR_SETTER);
        $comanda->set_nombre_cliente($parametros["nombre_cliente"], self::VALIDAR_SETTER);
        $comanda->set_cantidad_clientes($parametros["cantidad_clientes"], self::VALIDAR_SETTER);

        $response->getBody()->write(json_encode($comanda->alta(self::get_dni_empleado($request), $_FILES["imagen"])));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function baja($request, $response, $args)
    {
        
    }
    public function modificar($request, $response, $args)
    {

    }
    public function traer_todos($request, $response, $args)
    {
        $comanda = new Comanda();

        $response->getBody()->write(json_encode($comanda->traer_todos_alta()));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno($request, $response, $args)
    {
        $parametros = $request->getQueryParams();

        $comanda = new Comanda();
        $comanda->set_numero_comanda($parametros["numero_comanda"], self::VALIDAR_SETTER);
        
        $response->getBody()->write(json_encode($comanda->traer_uno()));

        return $response->withHeader("Content-Type", "application/json");
    }
}


?>