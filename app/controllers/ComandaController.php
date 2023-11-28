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

    public function alta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $archivos = $request->getUploadedFiles();

        $comanda = new Comanda();
        $comanda->set_lista_pedidos(json_decode($parametros["lista_pedidos"], true), self::VALIDAR_SETTER);
        $comanda->set_nombre_cliente($parametros["nombre_cliente"], self::VALIDAR_SETTER);
        $comanda->set_cantidad_clientes($parametros["cantidad_clientes"], self::VALIDAR_SETTER);
        $comanda->set_imagen($archivos["imagen"]);

        $payload = json_encode($comanda->alta(LoginMiddleware::get_empleado_sin_validar($request)->id));

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function baja($request, $response, $args){}
    public function modificar($request, $response, $args){}
    public function traer_todos($request, $response, $args)
    {
        $comanda = new Comanda();

        $payload = json_encode($comanda->traer_todos());

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno($request, $response, $args)
    {
        $parametros = $request->getQueryParams();

        $comanda = new Comanda();
        $comanda->set_id($parametros["id"], self::VALIDAR_SETTER);
        
        $payload = json_encode($comanda->traer_uno());

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
}


?>