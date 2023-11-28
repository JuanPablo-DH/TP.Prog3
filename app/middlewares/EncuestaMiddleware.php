<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "modelos/Encuesta.php";

class EncuestaMiddleware
{
    public function validar_input_alta(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();

        try
        {
            Comanda::validar_id($parametros["id_comanda"]);
            Comanda::validar_id_mesa($parametros["id_mesa"]);
            Encuesta::validar_puntuacion_cervezero($parametros["puntuacion_cervezero"]);
            Encuesta::validar_puntuacion_bartender($parametros["puntuacion_bartender"]);
            Encuesta::validar_puntuacion_mozo($parametros["puntuacion_mozo"]);
            Encuesta::validar_puntuacion_cocinero($parametros["puntuacion_cocinero"]);
            Encuesta::validar_puntuacion_restaurante($parametros["puntuacion_restaurante"]);
            Encuesta::validar_resenia($parametros["resenia"]);

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