<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "modelos/Producto.php";

class ProductoMiddleware
{
    public function validar_input_alta(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Producto::validar_nombre($parametros["nombre"]);
            Producto::validar_duracion_estimada($parametros["duracion_estimada"]);
            Producto::validar_tipo($parametros["tipo"]);
            Producto::validar_stock($parametros["stock"]);
            Producto::validar_precio_unidades($parametros["precio_unidades"]);

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $payload = $e->getMessage();
            $response->getBody()->write($payload);
        }

        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_baja(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Producto::validar_id($parametros["id"]);

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $payload = $e->getMessage();
            $response->getBody()->write($payload);
        }

        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_modificar(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Producto::validar_id($parametros["id"]);
            Producto::validar_tipo($parametros["tipo"]);
            Producto::validar_nombre($parametros["nombre"]);
            Producto::validar_duracion_estimada($parametros["duracion_estimada"]);
            Producto::validar_stock($parametros["stock"]);
            Producto::validar_precio_unidades($parametros["precio_unidades"]);

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $payload = $e->getMessage();
            $response->getBody()->write($payload);
        }

        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_traer_uno(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Producto::validar_id($parametros["id"]);

            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $payload = $e->getMessage();
            $response->getBody()->write($payload);
        }

        return $response->withHeader("Content-Type", "application/json");
    }
    public function validar_input_cargar_csv(Request $request, RequestHandler $handler)
    {
        $archivos = $request->getUploadedFiles();

        $response = new Response();

        try
        {
            $contenido_csv = Archivador::slim_archivo_obtener_contenido($archivos["csv_productos"]);
            Producto::validar_csv($contenido_csv);
            $response = $handler->handle($request);
        }
        catch(Exception $e)
        {
            $payload = $e->getMessage();
            $response->getBody()->write($payload);
        }

        return $response->withHeader("Content-Type", "application/json");
    }
}

?>