<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

class Input
{
    public static function limpiar($p_input)
    {
        $p_input = strval($p_input);
        $p_input = trim($p_input);
        $p_input = preg_replace('/\s+/', ' ', $p_input);
        return $p_input;
    }
    public static function convertir_a_booleano($p_input)
    {
        $p_input = self::limpiar($p_input);
        $p_input = strtolower($p_input);

        if(strcmp($p_input, "1") == 0)
        {
            return true;
        }
        else if(strcmp($p_input, "0") == 0)
        {
            return false;
        }

        return null;
    }
    public static function es_numerico($p_input)
    {
        $p_input = self::limpiar($p_input);

        if(preg_match("/^(\+|-)?\d+(\.\d+)?$/", $p_input))
        {
            return $p_input;
        }
        
        return null;
    }
    public static function es_alfanumerico($p_input, $p_longitud_min, $p_longitud_max)
    {
        $p_input = self::limpiar($p_input);

        if($p_longitud_min <= strlen($p_input) && strlen($p_input) <= $p_longitud_max &&
           preg_match('/^[a-zA-Z0-9]+$/', $p_input))
        {
            return $p_input;
        }
        
        return null;
    }
    public static function es_alias_con_espacios($p_input, $p_longitud_min, $p_longitud_max)
    {
        $p_input = self::limpiar($p_input);
        $p_input = strtolower($p_input);

        if($p_longitud_min <= strlen($p_input) && strlen($p_input) <= $p_longitud_max &&
           preg_match("/^[a-z\s]+$/", $p_input))
        {
            return $p_input;
        }

        return null;
    }
    public static function es_alias_con_guiones($p_input, $p_longitud_min, $p_longitud_max)
    {
        $p_input = self::limpiar($p_input);
        $p_input = strtolower($p_input);

        if($p_longitud_min <= strlen($p_input) && strlen($p_input) <= $p_longitud_max &&
           preg_match("/^[a-z\-]+$/", $p_input))
        {
            return $p_input;
        }

        return null;
    }
    public static function es_mail($p_input)
    {
        $p_input = self::limpiar($p_input);

        if(preg_match("/^[a-zA-Z0-9_\-]+@[a-zA-Z0-9_\-]+\.[a-zA-Z0-9_\-]+$/", $p_input))
        {
            return $p_input;
        }

        return null;
    }
    public static function cadena_longitud($p_input, $p_longitud_min, $p_longitud_max)
    {
        $p_input = strtolower($p_input);

        if($p_longitud_min <= strlen($p_input) && strlen($p_input) <= $p_longitud_max)
        {
            return $p_input;
        }

        return null;
    }
    public static function numerico_es_mayor_igual($p_input, $p_valor_referencia)
    {
        $p_input = self::limpiar($p_input);

        if(!(self::es_numerico($p_input) === null))
        {
            $p_input = doubleval($p_input);
            if($p_input >= $p_valor_referencia)
            {
                return $p_input;
            }
        }
        
        return null;
    }
    public static function numerico_es_menor_igual($p_input, $p_valor_referencia)
    {
        $p_input = self::limpiar($p_input);

        if(!(self::es_numerico($p_input) === null))
        {
            $p_input = doubleval($p_input);
            if($p_input <= $p_valor_referencia)
            {
                return $p_input;
            }
        }
        
        return null;
    }
    public static function numerico_esta_entre($p_input, $p_valor_min_referencia, $p_valor_max_refetencia)
    {
        $p_input = self::limpiar($p_input);

        if(!(self::es_numerico($p_input) === null))
        {
            $p_input = doubleval($p_input);
            if($p_valor_min_referencia <= $p_input && $p_input <= $p_valor_max_refetencia)
            {
                return $p_input;
            }
        }
        
        return null;
    }
    public static function es_array($p_input, $p_longitud_min, $p_longitud_max)
    {
        if(is_array($p_input) && $p_longitud_min <= count($p_input) && count($p_input) <= $p_longitud_max)
        {
            return $p_input;
        }

        return null;
    }
}

?>