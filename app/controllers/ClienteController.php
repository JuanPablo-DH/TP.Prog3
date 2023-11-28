<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "modelos/Cliente.php";
require_once "modelos/Comanda.php";
require_once "interfaces/IApiUsable.php";

class ClienteController implements IApiUsable
{
    private const VALIDAR_SETTER = false;

    public function alta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $cliente = new Cliente();
        $cliente->set_nombre($parametros["nombre"], self::VALIDAR_SETTER);

        $payload = json_encode($cliente->alta(LoginMiddleware::get_empleado_sin_validar($request)->id));

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function baja($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $cliente = new Cliente();
        $cliente->set_id($parametros["id"], self::VALIDAR_SETTER);

        $payload = json_encode($cliente->baja_logica(LoginMiddleware::get_empleado_sin_validar($request)->id));

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function modificar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $cliente = new Cliente();
        $cliente->set_id($parametros["id"], self::VALIDAR_SETTER);
        $cliente->set_nombre($parametros["nombre"], self::VALIDAR_SETTER);

        $payload = json_encode($cliente->modificar(LoginMiddleware::get_empleado_sin_validar($request)->id));

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_todos($request, $response, $args)
    {
        $cliente = new Cliente();

        $payload = json_encode($cliente->traer_todos_alta());
        
        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $cliente = new Cliente();
        $cliente->set_id($parametros["numero_cliente"], self::VALIDAR_SETTER);
        
        $payload = json_encode($cliente->traer_uno());
        
        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_pedidos($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $comanda = new Comanda();
        $comanda->set_id($parametros["id_comanda"], self::VALIDAR_SETTER);
        $comanda->set_id_mesa($parametros["id_mesa"], self::VALIDAR_SETTER);

        $payload = json_encode($comanda->traer_lista_pedidos_por_id_comanda_y_id_mesa());
        
        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
}

?>