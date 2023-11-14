<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "modelos/Producto.php";
require_once "interfaces/IApiUsable.php";

class ProductoController implements IApiUsable
{
    private const VALIDAR_SETTER = false;

    public function alta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $producto = new Producto();
        $producto->set_tipo($parametros["tipo"], self::VALIDAR_SETTER);
        $producto->set_nombre($parametros["nombre"], self::VALIDAR_SETTER);
        $producto->set_stock($parametros["stock"], self::VALIDAR_SETTER);
        $producto->set_precio_unidades($parametros["precio_unidades"], self::VALIDAR_SETTER);

        $response->getBody()->write(json_encode($producto->alta($parametros["dni_empleado"])));
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function baja($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $producto = new Producto();
        $producto->set_numero_producto($parametros["numero_producto"], self::VALIDAR_SETTER);

        $response->getBody()->write(json_encode($producto->baja_logica($parametros["dni_empleado"])));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function modificar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $producto = new Producto();
        $producto->set_numero_producto($parametros["numero_producto"], self::VALIDAR_SETTER);
        $producto->set_tipo($parametros["tipo"], self::VALIDAR_SETTER);
        $producto->set_nombre($parametros["nombre"], self::VALIDAR_SETTER);
        $producto->set_stock($parametros["stock"], self::VALIDAR_SETTER);
        $producto->set_precio_unidades($parametros["precio_unidades"], self::VALIDAR_SETTER);
        $producto->set_baja($parametros["baja"], self::VALIDAR_SETTER);

        $response->getBody()->write(json_encode($producto->modificar($parametros["dni_empleado"])));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_todos($request, $response, $args)
    {
        $producto = new Producto();

        $response->getBody()->write(json_encode($producto->traer_todos_alta()));

        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno($request, $response, $args)
    {
        $parametros = $request->getQueryParams();

        $producto = new Producto();
        $producto->set_numero_producto($parametros["numero_producto"], self::VALIDAR_SETTER);
        
        $response->getBody()->write(json_encode($producto->traer_uno()));

        return $response->withHeader("Content-Type", "application/json");
    }
}


?>