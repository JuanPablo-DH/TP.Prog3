<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "Modelos/Comanda.php";
require_once "Interfaces/IApiUsable.php";

class ComandaController implements IApiUsable
{
    public function alta($request, $response, $args)
    {
        $ret = "";

        $parametros = $request->getParsedBody();

        $lista_pedidos = array();
        $contador = 1;
        foreach($parametros as $elemento)
        {
            if(isset($parametros["tipo$contador"]) &&
               isset($parametros["nombre$contador"]) &&
               isset($parametros["cantidad_unidades$contador"]) &&
               isset($parametros["precio_unidades$contador"]))
            {
                array_push($lista_pedidos, array("tipo"=> $parametros["tipo$contador"],
                                                 "nombre"=> $parametros["nombre$contador"],
                                                 "cantidad_unidades"=> $parametros["cantidad_unidades$contador"],
                                                 "precio_unidades"=> $parametros["precio_unidades$contador"]));
                $contador++;
            }
        }
        
        try
        {
            $comanda = new Comanda();
            $comanda->set_lista_pedidos($lista_pedidos);
            $comanda->set_nombre_cliente($parametros["nombre_cliente"]);
            $comanda->set_cantidad_clientes($parametros["cantidad_clientes"]);
            $ret = $comanda->alta();
        }
        catch(Exception $e)
        {
            $ret = ["ComandaController Exception"=>$e->getMessage()];
        }

        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
    public function baja($request, $response, $args){}
    public function modificar($request, $response, $args){}
    public function traer_todos($request, $response, $args)
    {
        $ret = "";

        $comanda = new Comanda();
        $ret = $comanda->traer_todos_sin_baja();

        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno($request, $response, $args)
    {
        $ret = "";

        try
        {
            $comanda = new Comanda();
            $comanda->set_numero_comanda($args["numero_comanda"]);
            $ret = $comanda->traer_uno();
        }
        catch(Exception $e)
        {
            $ret = ["ComandaController Exception"=>$e->getMessage()];
        }
        
        $response->getBody()->write(json_encode($ret));
        return $response->withHeader("Content-Type", "application/json");
    }
}


?>