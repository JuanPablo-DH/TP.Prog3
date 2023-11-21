<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "modelos/Pedido.php";
require_once "interfaces/IApiUsable.php";

class PedidoController implements IApiUsable
{
    private const VALIDAR_SETTER = false;

    private static function get_rol_empleado($request)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros["nombre_empleado"]) && isset($parametros["dni_empleado"]))
        {
            $nombre = strtolower(Input::limpiar($parametros["nombre_empleado"]));
            $dni = intval(Input::limpiar($parametros["dni_empleado"]));
            $empleado = Empleado::get_alta_por_dni_y_nombre($dni, $nombre);
            return $empleado->rol;
        }
        
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        return AutentificadorJWT::ObtenerData($token)->rol;
    }

    public function alta($request, $response, $args) {}
    public function baja($request, $response, $args) {}
    public function modificar($request, $response, $args) {}
    public function elaborar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $rol = self::get_rol_empleado($request);
        if(strcmp($rol, "cervezero") == 0)
        {
            $response->getBody()->write(json_encode(Pedido::cervezero_elaborar($parametros["numero_pedido"], $parametros["minutos_elaboracion"])));
        }
        else if(strcmp($rol, "bartender") == 0)
        {
            $response->getBody()->write(json_encode(Pedido::bartender_elaborar($parametros["numero_pedido"], $parametros["minutos_elaboracion"])));
        }
        else if(strcmp($rol, "cocinero") == 0)
        {
            $response->getBody()->write(json_encode(Pedido::cocinero_elaborar($parametros["numero_pedido"], $parametros["minutos_elaboracion"])));
        }

        return $response->withHeader("Content-Type", "application/json");
    }
    public function terminar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $rol = self::get_rol_empleado($request);
        if(strcmp($rol, "cervezero") == 0)
        {
            $response->getBody()->write(json_encode(Pedido::cervezero_terminar($parametros["numero_pedido"])));
        }
        else if(strcmp($rol, "bartender") == 0)
        {
            $response->getBody()->write(json_encode(Pedido::bartender_terminar($parametros["numero_pedido"])));
        }
        else if(strcmp($rol, "cocinero") == 0)
        {
            $response->getBody()->write(json_encode(Pedido::cocinero_terminar($parametros["numero_pedido"])));
        }

        return $response->withHeader("Content-Type", "application/json");
    }
    public function servir($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $response->getBody()->write(json_encode(Pedido::mozo_servir($parametros["numero_pedido"])));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_todos($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $pedido = new Pedido();

        $rol = self::get_rol_empleado($request);

        if(strcmp($rol, "bartender") == 0)
        {
            $response->getBody()->write(json_encode($pedido->traer_bebidas_sin_alcohol_alta_pendiente()));
        }
        else if(strcmp($rol, "cervezero") == 0)
        {
            $response->getBody()->write(json_encode($pedido->traer_bebidas_con_alcohol_alta_pendiente()));
        }
        else if(strcmp($rol, "cocinero") == 0)
        {
            $response->getBody()->write(json_encode($pedido->traer_comidas_alta_pendiente()));
        }
        else if(strcmp($rol, "mozo") == 0)
        {
            $response->getBody()->write(json_encode($pedido->traer_listos_para_servir_alta()));
        }
        else
        {
            $response->getBody()->write(json_encode($pedido->traer_todos()));
        }

        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $pedido = new Pedido();
        $pedido->set_numero_pedido($parametros["numero_pedido"], self::VALIDAR_SETTER);

        $response->getBody()->write(json_encode($pedido->traer_uno()));
        
        return $response->withHeader("Content-Type", "application/json");
    }
}

?>