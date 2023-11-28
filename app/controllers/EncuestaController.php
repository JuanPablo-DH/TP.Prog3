<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "modelos/Encuesta.php";
require_once "interfaces/IApiUsable.php";

class EncuestaController implements IApiUsable
{
    private const VALIDAR_SETTER = false;

    public function alta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $encuesta = new Encuesta();
        $encuesta->set_id_comanda($parametros["id_comanda"], self::VALIDAR_SETTER);
        $encuesta->set_id_mesa($parametros["id_mesa"], self::VALIDAR_SETTER);
        $encuesta->set_puntuacion_cervezero($parametros["puntuacion_cervezero"], self::VALIDAR_SETTER);
        $encuesta->set_puntuacion_bartender($parametros["puntuacion_bartender"], self::VALIDAR_SETTER);
        $encuesta->set_puntuacion_mozo($parametros["puntuacion_mozo"], self::VALIDAR_SETTER);
        $encuesta->set_puntuacion_cocinero($parametros["puntuacion_cocinero"], self::VALIDAR_SETTER);
        $encuesta->set_puntuacion_restaurante($parametros["puntuacion_restaurante"], self::VALIDAR_SETTER);
        $encuesta->set_resenia($parametros["resenia"], self::VALIDAR_SETTER);

        $payload = json_encode($encuesta->alta());

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
    public function baja($request, $response, $args){}
    public function modificar($request, $response, $args){}
    public function traer_todos($request, $response, $args){}
    public function traer_uno($request, $response, $args){}

    public function traer_mejores_comentarios($request, $response, $args)
    {
        $payload = json_encode(Encuesta::traer_mejores_comentarios());
        
        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }
}

?>