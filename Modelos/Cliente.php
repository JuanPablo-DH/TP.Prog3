<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "Modelos/Input.php";
require_once "DB/AccesoDatos.php";

class Cliente
{
    public $numero_cliente; // Alfanumerico Unico de 6 digitos
    public $nombre; // Hasta 30 caracteres
    public $baja; // true | false


    private const DB_TABLA = "clientes";


    private const NUMERO_CLIENTE_MSJ_ERROR = "Numero de cliente no valido. Debe ser un alfanumerico de 6 cifras";
    private const NOMBRE_MSJ_ERROR = "Nombre de cliente no valido. Debe ser solo letras, puede haber espacios y tener menos de 30 caracteres";
    private const BAJA_MSJ_ERROR = "Baja de cliente no valido. Debe ser 'true' o 'false'";


    

    // #region Setters
    public function set_numero_cliente($p_numero_cliente)
    {
        $p_numero_cliente = Input::es_alfanumerico($p_numero_cliente, 6, 6);

        if($p_numero_cliente === null)
        {
            throw new Exception(self::NUMERO_CLIENTE_MSJ_ERROR);
        }

        $this->numero_cliente = $p_numero_cliente;
    }
    public function set_nombre($p_nombre)
    {
        $p_nombre = Input::es_alias_con_espacios($p_nombre, 1, 30);

        if($p_nombre === null)
        {
            throw new Exception(self::NOMBRE_MSJ_ERROR);
        }
        
        $this->nombre = $p_nombre;
    }
    public function set_baja($p_baja)
    {
        $p_baja = Input::convertir_a_booleano($p_baja);

        if($p_baja === null)
        {
            throw new Exception(self::BAJA_MSJ_ERROR);
        }

        $this->baja = $p_baja;
    }
    // #endregion Setters




    // #region Utilidades
    private static function existe_cadena_por_igualdad($p_atributo, $p_valor)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("      SELECT numero_cliente
                                                       FROM $db_tabla
                                               WHERE BINARY $p_atributo=:$p_atributo");
        $consulta->bindParam(":$p_atributo" , $p_valor);
        $consulta->execute();

        if($consulta->rowCount() > 0)
        {
            return true;
        }

        return false;
    }
    private static function existe_numerico_por_igualdad($p_atributo, $p_valor)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT numero_cliente
                                                 FROM $db_tabla
                                                WHERE $p_atributo=:$p_atributo");
        $consulta->bindParam(":$p_atributo" , $p_valor);
        $consulta->execute();

        if($consulta->rowCount() > 0)
        {
            return true;
        }

        return false;
    }
    private static function crear_alfanumerico_aleatorio($p_longitud)
    {
        $caracteres = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $alfanumerico = "";
    
        $alfanumerico .= $caracteres[mt_rand(0, 9)]; // numero aleatorio
        $p_longitud--;
    
        $alfanumerico .= $caracteres[mt_rand(10, 61)]; // letra aleatoria
        $p_longitud--;
    
        for ($i = 0; $i < $p_longitud; $i++) {
            $indice = mt_rand(0, strlen($caracteres) - 1);
            $alfanumerico .= $caracteres[$indice]; // caracter aleatorio
        }
    
        return str_shuffle($alfanumerico);
    }
    public static function get($p_numero_cliente)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("      SELECT *
                                                       FROM $db_tabla
                                               WHERE BINARY :numero_cliente = numero_cliente");
        $consulta->bindParam(":numero_cliente", $p_numero_cliente);
        $consulta->execute();
        return $consulta->fetchObject("Cliente");
    }
    public static function crear_id()
    {
        while(true)
        {
            $alfanumerico = self::crear_alfanumerico_aleatorio(6);

            if(self::existe_cadena_por_igualdad("numero_cliente", $alfanumerico) === false)
            {
                return $alfanumerico;
            }
        }
    }
    // #endregion Utilidades




    // #region Funcionalidades
    public function alta()
    {
        $this->numero_cliente = self::crear_id();

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("INSERT INTO $db_tabla
                                                           (numero_cliente,
                                                            nombre,
                                                            baja)
                                                    VALUES
                                                           (:numero_cliente,
                                                            :nombre,
                                                            '0')");
        $consulta->bindParam(':numero_cliente', $this->numero_cliente);
        $consulta->bindParam(':nombre', $this->nombre);
        $consulta->execute();

        if(self::existe_numerico_por_igualdad("numero_cliente", $this->numero_cliente) === false)
        {
            return ["Alta Cliente Error"=>"No se pudo hacer."];
        }

        return ["Alta Cliente"=>"Realizado."];
    }
    public function baja()
    {
        if(self::existe_cadena_por_igualdad("numero_cliente", $this->numero_cliente) === false)
        {
            return ["Baja Cliente Error"=>"No se pudo hacer, porque no existe el Numero de Cliente."];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("DELETE FROM $db_tabla
                                               WHERE numero_cliente = :numero_cliente");
        $consulta->bindParam(":numero_cliente", $this->numero_cliente);
        $consulta->execute();
        $registros_afectados = $consulta->rowCount();
        switch($registros_afectados)
        {
            case 1:
                return ["Baja Cliente"=>"Realizado."];
            break;

            case 0:
                return ["Baja Cliente Error"=>"No se pudo hacer."];
            break;

            default:
                return ["Baja Cliente Error"=>"Se realizo, pero se eliminaron $registros_afectados registros."];
            break;
        }
    }
    public function baja_logica()
    {
        if(self::existe_cadena_por_igualdad("numero_cliente", $this->numero_cliente) === false)
        {
            return ["Baja Logica Cliente Error"=>"No se pudo hacer, porque no existe el Numero de Cliente."];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET baja = '1'
                                                WHERE numero_cliente = :numero_cliente");
        $consulta->bindParam(":numero_cliente", $this->numero_cliente);
        $consulta->execute();
        $registros_afectados = $consulta->rowCount();
        switch($registros_afectados)
        {
            case 1:
                return ["Baja Logica Cliente"=>"Realizado."];
            break;

            case 0:
                return ["Baja Logica Cliente Error"=>"No se pudo hacer."];
            break;

            default:
                return ["Baja Logica Cliente Error"=>"Se realizo, pero se eliminaron logicamente $registros_afectados registros."];
            break;
        }
    }
    public function modificar()
    {
        if(self::existe_cadena_por_igualdad("numero_cliente", $this->numero_cliente) === false)
        {
            return ["Modificar Cliente Error"=>"No se pudo hacer, porque no existe el Numero de Cliente."];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                               SET nombre = :nombre,
                                                   baja = :baja
                                               WHERE numero_cliente = :numero_cliente");
        $consulta->bindParam(":numero_cliente", $this->numero_cliente);
        $consulta->bindParam(":nombre", $this->nombre);
        $consulta->bindParam(":baja", $this->baja);
        $consulta->execute();
        $registros_afectados = $consulta->rowCount();
        switch($registros_afectados)
        {
            case 1:
                return ["Modificar Cliente"=>"Realizado."];
            break;

            case 0:
                return ["Modificar Cliente Error"=>"No se pudo hacer."];
            break;

            default:
                return ["Modificar Cliente Error"=>"Se realizo, pero se modificaron $registros_afectados registros."];
            break;
        }
    }
    public function traer_todos()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT * FROM $db_tabla");
        $consulta->execute();
        return ["lista_clientes"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Cliente")];
    }
    public function traer_todos_sin_baja()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE baja = '0'");
        $consulta->execute();
        return ["lista_clientes"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Cliente")];
    }
    public function traer_uno()
    {
        if(self::existe_numerico_por_igualdad("numero_cliente", $this->numero_cliente) === false)
        {
            return ["Traer Uno Cliente Error"=>"No se pudo hacer, porque no existe el Numero de Cliente."];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                               FROM $db_tabla
                                               WHERE BINARY numero_cliente = :numero_cliente");
        $consulta->bindParam(":numero_cliente", $this->numero_cliente);
        $consulta->execute();
        return ["cliente"=>$consulta->fetchObject("Cliente")];
    }
    // #endregion Funcionalidades
}

?>