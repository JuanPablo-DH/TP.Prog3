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
        $producto->set_duracion_estimada($parametros["duracion_estimada"], self::VALIDAR_SETTER);
        $producto->set_stock($parametros["stock"], self::VALIDAR_SETTER);
        $producto->set_precio_unidades($parametros["precio_unidades"], self::VALIDAR_SETTER);

        $payload = json_encode($producto->alta(LoginMiddleware::get_empleado_sin_validar($request)->id));
        
        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function baja($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $producto = new Producto();
        $producto->set_id($parametros["id"], self::VALIDAR_SETTER);

        $payload = json_encode($producto->baja_logica(LoginMiddleware::get_empleado_sin_validar($request)->id));

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function modificar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $producto = new Producto();
        $producto->set_id($parametros["id"], self::VALIDAR_SETTER);
        $producto->set_tipo($parametros["tipo"], self::VALIDAR_SETTER);
        $producto->set_nombre($parametros["nombre"], self::VALIDAR_SETTER);
        $producto->set_duracion_estimada($parametros["duracion_estimada"], self::VALIDAR_SETTER);
        $producto->set_stock($parametros["stock"], self::VALIDAR_SETTER);
        $producto->set_precio_unidades($parametros["precio_unidades"], self::VALIDAR_SETTER);

        $payload = json_encode($producto->modificar(LoginMiddleware::get_empleado_sin_validar($request)->id));

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_todos($request, $response, $args)
    {
        $producto = new Producto();

        $payload = json_encode($producto->traer_todos_alta());

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function traer_uno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $producto = new Producto();
        $producto->set_id($parametros["id"], self::VALIDAR_SETTER);
        
        $payload = json_encode($producto->traer_uno());

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function cargar_csv($request, $response, $args)
    {
        $archivos = $request->getUploadedFiles();
        $contenido_csv = Archivador::slim_archivo_obtener_contenido($archivos["csv_productos"]);

        $payload = json_encode(Producto::cargar_csv($contenido_csv, false));

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function crear_csv($request, $response, $args)
    {
        $payload = Producto::crear_csv();

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "text/csv");
    }
}


?>