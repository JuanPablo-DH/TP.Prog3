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

        $producto = new Producto();
        $producto->set_tipo($parametros["tipo"], self::VALIDAR_SETTER);
        $producto->set_nombre($parametros["nombre"], self::VALIDAR_SETTER);
        $producto->set_stock($parametros["stock"], self::VALIDAR_SETTER);
        $producto->set_precio_unidades($parametros["precio_unidades"], self::VALIDAR_SETTER);

        $response->getBody()->write(json_encode($producto->alta(self::get_dni_empleado($request))));
        
        return $response->withHeader("Content-Type", "application/json");
    }
    public function baja($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $producto = new Producto();
        $producto->set_numero_producto($parametros["numero_producto"], self::VALIDAR_SETTER);

        $response->getBody()->write(json_encode($producto->baja_logica(self::get_dni_empleado($request))));

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

        $response->getBody()->write(json_encode($producto->modificar(self::get_dni_empleado($request))));

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