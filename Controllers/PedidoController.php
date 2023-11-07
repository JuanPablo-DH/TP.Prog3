<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "Modelos/Pedido.php";
require_once "Interfaces/IApiUsable.php";

class PedidoController implements IApiUsable
{
    public function alta($request, $response, $args) {}
    public function baja($request, $response, $args) {}
    public function modificar($request, $response, $args) {}
    public function traer_todos($request, $response, $args)
    {
        $ret = "";

        $pedido = new Pedido();
        $ret = $pedido->traer_todos_sin_baja();

        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno($request, $response, $args)
    {
        $ret = "";

        try
        {
            $pedido = new Pedido();
            $pedido->set_numero_pedido($args["numero_pedido"]);
            $ret = $pedido->traer_uno();
        }
        catch(Exception $e)
        {
            $ret = ["MesaController Exception"=>$e->getMessage()];
        }
        
        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
}
    

?>