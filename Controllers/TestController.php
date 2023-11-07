<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "Modelos/Input.php";

class TestController
{
    public function test($request, $response, $args)
    {
        if(isset($args["numero_test"]))
        {
            $nombre_test = "null";
            $ret["r"] = "null";
            
            switch($args["numero_test"])
            {
                case "1":
                    $nombre_test = "es numerico";
                    $ret[0] = "+3";
                    $ret_funcion = Input::es_numerico($ret[0]);
                    if($ret_funcion != null)
                    {
                        $ret["r"] = (double)$ret_funcion;
                    }
                    else
                    {
                        $ret["r"] = $ret_funcion;
                    }
                break;

                case "2":
                    $nombre_test = "es alfanumerico";
                    $ret[0] = "544GG";
                    $ret["longitud_min"] = 1;
                    $ret["longitud_max"] = 5;
                    $ret["r"] = Input::es_alfanumerico($ret[0], $ret["longitud_min"], $ret["longitud_max"]);
                break;
            }

            $response->getBody()->write(json_encode(array($nombre_test=>$ret)));
            return $response->withHeader("Content-Type", "application/json");
        }
        
        $response->getBody()->write(json_encode(["TestController Error"=>"Falto setear el numero_test"]));
        return $response->withHeader("Content-Type", "application/json");
    }
}

?>