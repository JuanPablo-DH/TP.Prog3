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

    public function alta($request, $response, $args) {}
    public function baja($request, $response, $args) {}
    public function modificar($request, $response, $args) {}
    public function elaborar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $pedido = new Pedido();
        $pedido->set_id($parametros["id"], self::VALIDAR_SETTER);

        $rol = LoginMiddleware::get_empleado_sin_validar($request)->rol;
        if(strcmp($rol, "CERVEZERO") == 0)
        {
            $payload = json_encode($pedido->cervezero_elaborar());
        }
        else if(strcmp($rol, "BARTENDER") == 0)
        {
            $payload = json_encode($pedido->bartender_elaborar());
        }
        else if(strcmp($rol, "COCINERO") == 0)
        {
            $payload = json_encode($pedido->cocinero_elaborar());
        }

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function terminar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $pedido = new Pedido();
        $pedido->set_id($parametros["id"], self::VALIDAR_SETTER);

        $rol = LoginMiddleware::get_empleado_sin_validar($request)->rol;
        if(strcmp($rol, "CERVEZERO") == 0)
        {
            $payload = json_encode($pedido->cervezero_terminar());
        }
        else if(strcmp($rol, "BARTENDER") == 0)
        {
            $payload = json_encode($pedido->bartender_terminar());
        }
        else if(strcmp($rol, "COCINERO") == 0)
        {
            $payload = json_encode($pedido->cocinero_terminar());
        }

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function servir($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $pedido = new Pedido();
        $pedido->set_id($parametros["id"], self::VALIDAR_SETTER);

        $payload = json_encode($pedido->mozo_servir());

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_todos($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $pedido = new Pedido();

        $rol = LoginMiddleware::get_empleado_sin_validar($request)->rol;

        if(strcmp($rol, "BARTENDER") == 0)
        {
            $pedido->set_tipo_producto("BEBIDA", self::VALIDAR_SETTER);
            
            $payload = json_encode($pedido->traer_pendientes_por_tipo_producto_alta());
        }
        else if(strcmp($rol, "CERVEZERO") == 0)
        {
            $pedido->set_tipo_producto("BEBIDA-ALCOHOL", self::VALIDAR_SETTER);
            $payload = json_encode($pedido->traer_pendientes_por_tipo_producto_alta());
        }
        else if(strcmp($rol, "COCINERO") == 0)
        {
            $pedido->set_tipo_producto("COMIDA", self::VALIDAR_SETTER);
            $payload = json_encode($pedido->traer_pendientes_por_tipo_producto_alta());
        }
        else if(strcmp($rol, "MOZO") == 0)
        {
            $payload = json_encode($pedido->traer_listos_para_servir_alta());
        }
        else
        {
            $payload = json_encode($pedido->traer_todos());
        }

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $pedido = new Pedido();
        $pedido->set_id($parametros["id"], self::VALIDAR_SETTER);

        $payload = json_encode($pedido->traer_uno());
        
        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
}

?>