<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "bd/AccesoDatos.php";
require_once "modelos/Comanda.php";
require_once "modelos/Mesa.php";
require_once "utils/Input.php";

class Encuesta
{
    public $id;
    public $id_comanda;
    public $id_mesa;
    public $puntuacion_restaurante; 
    public $puntuacion_mozo;
    public $puntuacion_bartender;
    public $puntuacion_cervezero;
    public $puntuacion_cocinero;
    public $tipo_resenia;
    public $resenia;
    public $fecha_alta;

    private const DB_TABLA = "encuestas";



    
    public static function validar_id($p_id)
    { 
        $p_id = Input::numerico_es_mayor_igual($p_id, 1000);

        if($p_id === null)
        {
            throw new Exception(json_encode(["error_input_encuesta"=>"Id no valido - Debe ser mayor a 1.000 (mil)."]));
        }

        return (int)$p_id;
    }
    public static function validar_puntuacion_restaurante($p_puntuacion_restaurante)
    { 
        $p_puntuacion_restaurante = Input::numerico_esta_entre($p_puntuacion_restaurante, 1, 10);

        if($p_puntuacion_restaurante === null)
        {
            throw new Exception(json_encode(["error_input_encuesta"=>"Puntuacion de restaurante no valida - Debe estar entre 1 y 10 inclusive."]));
        }

        return (int)$p_puntuacion_restaurante;
    } 
    public static function validar_puntuacion_mozo($p_puntuacion_mozo)
    { 
        $p_puntuacion_mozo = Input::numerico_esta_entre($p_puntuacion_mozo, 1, 10);

        if($p_puntuacion_mozo === null)
        {
            throw new Exception(json_encode(["error_input_encuesta"=>"Puntuacion de mozo no valida - Debe estar entre 1 y 10 inclusive."]));
        }

        return (int)$p_puntuacion_mozo;
    } 
    public static function validar_puntuacion_bartender($p_puntuacion_bartender)
    { 
        $p_puntuacion_bartender = Input::numerico_esta_entre($p_puntuacion_bartender, 1, 10);

        if($p_puntuacion_bartender === null)
        {
            throw new Exception(json_encode(["error_input_encuesta"=>"Puntuacion de bartender no valida - Debe estar entre 1 y 10 inclusive."]));
        }

        return (int)$p_puntuacion_bartender;
    } 
    public static function validar_puntuacion_cervezero($p_puntuacion_cervezero)
    { 
        $p_puntuacion_cervezero = Input::numerico_esta_entre($p_puntuacion_cervezero, 1, 10);

        if($p_puntuacion_cervezero === null)
        {
            throw new Exception(json_encode(["error_input_encuesta"=>"Puntuacion de cervezero no valida - Debe estar entre 1 y 10 inclusive."]));
        }

        return (int)$p_puntuacion_cervezero;
    } 
    public static function validar_puntuacion_cocinero($p_puntuacion_cocinero)
    { 
        $p_puntuacion_cocinero = Input::numerico_esta_entre($p_puntuacion_cocinero, 1, 10);

        if($p_puntuacion_cocinero === null)
        {
            throw new Exception(json_encode(["error_input_encuesta"=>"Puntuacion de cocinero no valida - Debe estar entre 1 y 10 inclusive."]));
        }

        return (int)$p_puntuacion_cocinero;
    } 
    public static function validar_resenia($p_resenia)
    {
        $p_resenia = Input::cadena_longitud($p_resenia, 10, 66);

        if($p_resenia === null)
        {
            throw new Exception(json_encode(["error_input_encuesta"=>"Reseña no valida - Debe tener 10 caracteres como minimo y 66 como maximo."]));
        }

        return (int)$p_resenia;
    }

    public function set_id($p_id, $p_validar)
    {
        if($p_validar)
        {
            $this->id = self::validar_id($p_id);
        }
        else
        {
            $this->id = intval(Input::limpiar($p_id));
        }
    }
    public function set_id_comanda($p_id_comanda, $p_validar)
    {
        if($p_validar)
        {
            $this->id_comanda = Comanda::validar_id($p_id_comanda);
        }
        else
        {
            $this->id_comanda = intval(Input::limpiar($p_id_comanda));
        }
    }
    public function set_id_mesa($p_id_mesa, $p_validar)
    {
        if($p_validar)
        {
            $this->id_mesa = Mesa::validar_id($p_id_mesa);
        }
        else
        {
            $this->id_mesa = intval(Input::limpiar($p_id_mesa));
        }
    }
    public function set_puntuacion_restaurante($p_puntuacion_restaurante, $p_validar)
    {
        if($p_validar)
        {
            $this->puntuacion_restaurante = self::validar_puntuacion_restaurante($p_puntuacion_restaurante);
        }
        else
        {
            $this->puntuacion_restaurante = intval(Input::limpiar($p_puntuacion_restaurante));
        }
    }
    public function set_puntuacion_mozo($p_puntuacion_mozo, $p_validar)
    {
        if($p_validar)
        {
            $this->puntuacion_mozo = self::validar_puntuacion_mozo($p_puntuacion_mozo);
        }
        else
        {
            $this->puntuacion_mozo = intval(Input::limpiar($p_puntuacion_mozo));
        }
    }
    public function set_puntuacion_bartender($p_puntuacion_bartender, $p_validar)
    {
        if($p_validar)
        {
            $this->puntuacion_bartender = self::validar_puntuacion_restaurante($p_puntuacion_bartender);
        }
        else
        {
            $this->puntuacion_bartender = intval(Input::limpiar($p_puntuacion_bartender));
        }
    }
    public function set_puntuacion_cervezero($p_puntuacion_cervezero, $p_validar)
    {
        if($p_validar)
        {
            $this->puntuacion_cervezero = self::validar_puntuacion_restaurante($p_puntuacion_cervezero);
        }
        else
        {
            $this->puntuacion_cervezero = intval(Input::limpiar($p_puntuacion_cervezero));
        }
    }
    public function set_puntuacion_cocinero($p_puntuacion_cocinero, $p_validar)
    {
        if($p_validar)
        {
            $this->puntuacion_cocinero = self::validar_puntuacion_restaurante($p_puntuacion_cocinero);
        }
        else
        {
            $this->puntuacion_cocinero = intval(Input::limpiar($p_puntuacion_cocinero));
        }
    }
    private function set_tipo_resenia()
    {
        $suma_puntajes = $this->puntuacion_restaurante + $this->puntuacion_mozo + $this->puntuacion_bartender + $this->puntuacion_cervezero + $this->puntuacion_cocinero;
        $resultado = ($suma_puntajes / 50) * 100;

        if($resultado >= 45)
        {
            $this->tipo_resenia = "BUENA";
        }
        else
        {
            $this->tipo_resenia = "MALA";
        }
    }
    public function set_resenia($p_resenia, $p_validar)
    {
        if($p_validar)
        {
            $this->resenia = self::validar_resenia($p_resenia);
        }
        else
        {
            $this->resenia = strtolower(Input::limpiar($p_resenia));
        }
    }
    private function set_fecha_alta()
    {
        $this->fecha_alta = new DateTime("now");
    }

    private static function crear_id()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id
                                               FROM $db_tabla
                                               ORDER BY id DESC
                                               LIMIT 1");
        $consulta->execute();

        if($consulta->rowCount() > 0)
        {
            $encuesta = $consulta->fetchObject("Encuesta");
            return ($encuesta->id + 1000);
        }
            
        return 1000;
    }
    private static function existe_numerico_por_igualdad($p_atributo, $p_valor)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id
                                               FROM $db_tabla
                                               WHERE $p_atributo = :$p_atributo");
        $consulta->bindParam(":$p_atributo", $p_valor);
        $consulta->execute();

        if($consulta->rowCount() > 0)
        {
            return true;
        }

        return false;
    }
    private static function add($p_encuesta)
    {
         $p_encuesta->id = self::crear_id();
         $accesoDatos = AccesoDatos::GetPdo();
         $db_tabla = self::DB_TABLA;
         $consulta = $accesoDatos->GetConsulta("INSERT INTO $db_tabla
                                                            (id,
                                                             id_comanda,
                                                             id_mesa,
                                                             puntuacion_cervezero,
                                                             puntuacion_bartender,
                                                             puntuacion_mozo,
                                                             puntuacion_cocinero,
                                                             puntuacion_restaurante,
                                                             tipo_resenia,
                                                             resenia,
                                                             fecha_alta)
                                                      VALUES
                                                             (:id,
                                                              :id_comanda,
                                                              :id_mesa,
                                                              :puntuacion_cervezero,
                                                              :puntuacion_bartender,
                                                              :puntuacion_mozo,
                                                              :puntuacion_cocinero,
                                                              :puntuacion_restaurante,
                                                              :tipo_resenia,
                                                              :resenia,
                                                              :fecha_alta)");
        $consulta->bindParam(':id', $p_encuesta->id);
        $consulta->bindParam(':id_comanda', $p_encuesta->id_comanda);
        $consulta->bindParam(':id_mesa', $p_encuesta->id_mesa);
        $consulta->bindParam(':puntuacion_cervezero', $p_encuesta->puntuacion_cervezero);
        $consulta->bindParam(':puntuacion_bartender', $p_encuesta->puntuacion_bartender);
        $consulta->bindParam(':puntuacion_mozo', $p_encuesta->puntuacion_mozo);
        $consulta->bindParam(':puntuacion_cocinero', $p_encuesta->puntuacion_cocinero);
        $consulta->bindParam(':puntuacion_restaurante', $p_encuesta->puntuacion_restaurante);
        $p_encuesta->set_tipo_resenia();
        $consulta->bindParam(':tipo_resenia', $p_encuesta->tipo_resenia);
        $consulta->bindParam(':resenia', $p_encuesta->resenia);
        $p_encuesta->set_fecha_alta();
        $fecha_alta_formato = $p_encuesta->fecha_alta->format('Y-m-d H:i:s');
        $consulta->bindParam(':fecha_alta', $fecha_alta_formato);
        $consulta->execute();

        return self::existe_numerico_por_igualdad("id", $p_encuesta->id);
    }

    public function alta()
    {
        $comanda = Comanda::get_alta($this->id_comanda);
        if($comanda === null)
        {
            return ["error_alta_encuesta"=>"No existe el id de comanda '$this->id'"];
        }

        if($comanda->id_mesa !== $this->id_mesa)
        {
            return ["error_alta_encuesta"=>"No coincide la mesa '$this->id_mesa' con la comanda '$this->id_comanda'"];
        }

        if(self::add($this) === false)
        {
            return ["error_alta_encuesta"=>"No se pudo hacer"];
        }

        return ["alta_encuesta"=>"Realizado"];
    }
    public static function traer_mejores_comentarios()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE tipo_resenia = 'buena'");
        $consulta->execute();

        return ["mejores_reseñas"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Encuesta")];
    }
}

?>