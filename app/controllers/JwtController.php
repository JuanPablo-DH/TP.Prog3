<?php

require_once "modelos/Empleado.php";
require_once "utils/AutentificadorJWT.php";

class JwtController
{
    public function crear_token($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        try
        {
            $dni = Empleado::validar_dni($parametros["dni"]);
            $nombre = Empleado::validar_nombre($parametros["nombre"]);
            $empleado = Empleado::get_alta_por_dni_y_nombre($dni, $nombre);
            if($empleado !== null)
            {
                $datos = array("nombre"=>$empleado->nombre, "dni"=>$empleado->dni, "rol"=>$empleado->rol);
                $response->getBody()->write(json_encode(array("token"=>AutentificadorJWT::CrearToken($datos))));
            }
            else
            {
                $response->getBody()->write(json_encode(array("crear_token_error"=>"El empleado no existe")));
            }
            
        }
        catch(Exception $e)
        {
            $response->getBody()->write($e->getMessage());
        }
        
        return $response->withHeader("Content-Type", "application/json");
    }
}

?>