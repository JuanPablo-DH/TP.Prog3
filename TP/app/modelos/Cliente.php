<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "modelos/Input.php";
require_once "modelos/Movimiento.php";
require_once "bd/AccesoDatos.php";

class Cliente
{
    public $numero_cliente; // Alfanumerico Unico de 6 digitos
    public $nombre; // Hasta 30 caracteres
    public $baja; // true | false


    private const DB_TABLA = "clientes";


    private const NUMERO_CLIENTE_MSJ_ERROR = ["input_error_cliente"=>"Numero de cliente no valido - Debe ser un alfanumerico de 6 cifras"];
    private const NOMBRE_MSJ_ERROR = ["input_error_cliente"=>"Nombre de cliente no valido - Debe ser solo letras, puede haber espacios y tener menos de 30 caracteres"];
    private const BAJA_MSJ_ERROR = ["input_error_cliente"=>"Estado baja de cliente no valido - Debe ser '1' para [true] o '0' para [false]"];


    

    // #region Validadores
    public static function validar_numero_cliente($p_numero_cliente)
    {
        $p_numero_cliente = Input::es_alfanumerico($p_numero_cliente, 6, 6);

        if($p_numero_cliente === null)
        {
            throw new Exception(json_encode(self::NUMERO_CLIENTE_MSJ_ERROR));
        }

        return $p_numero_cliente;
    }
    public static function validar_nombre($p_nombre)
    {
        $p_nombre = Input::es_alias_con_espacios($p_nombre, 1, 30);

        if($p_nombre === null)
        {
            throw new Exception(json_encode(self::NOMBRE_MSJ_ERROR));
        }
        
        return $p_nombre;
    }
    public static function validar_baja($p_baja)
    {
        $p_baja = Input::convertir_a_booleano($p_baja);

        if($p_baja === null)
        {
            throw new Exception(json_encode(self::BAJA_MSJ_ERROR));
        }

        return $p_baja;
    }
    // #endregion Validadores


    

    // #region Setters
    public function set_numero_cliente($p_numero_cliente, $p_validar)
    {
        if($p_validar)
        {
            $this->numero_cliente = self::validar_numero_cliente($p_numero_cliente);
        }
        else
        {
            $this->numero_cliente = Input::limpiar($p_numero_cliente);
        }
    }
    public function set_nombre($p_nombre, $p_validar)
    {
        if($p_validar)
        {
            $this->nombre = self::validar_nombre($p_nombre);
        }
        else
        {
            $this->nombre = strtolower(Input::limpiar($p_nombre));
        }
    }
    public function set_baja($p_baja, $p_validar)
    {
        if($p_validar)
        {
            $this->baja = self::validar_baja($p_baja);
        }
        else
        {
            $this->baja = boolval(Input::limpiar($p_baja));
        }
    }
    // #endregion Setters




    // #region Utilidades
    public static function add($p_cliente)
    {
        $p_cliente->numero_cliente = self::crear_id();

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
        $consulta->bindParam(':numero_cliente', $p_cliente->numero_cliente);
        $consulta->bindParam(':nombre', $p_cliente->nombre);
        $consulta->execute();

        return self::existe_numerico_por_igualdad("numero_cliente", $p_cliente->numero_cliente);
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

        if($consulta->rowCount() === 1)
        {
            return $consulta->fetchObject("Cliente");
        }

        return null;
    }
    public static function set($p_cliente)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("INSERT INTO $db_tabla
                                                           (numero_cliente,
                                                            nombre,
                                                            baja)
                                                    VALUES
                                                           (:numero_cliente,
                                                            :nombre,
                                                            :baja)");
        $consulta->bindParam(':numero_cliente', $p_cliente->numero_cliente);
        $consulta->bindParam(':nombre', $p_cliente->nombre);
        $consulta->bindParam(':baja', $p_cliente->baja);
        return $consulta->execute();
    }
    public static function get_alta($p_numero_cliente)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("      SELECT *
                                                       FROM $db_tabla
                                               WHERE BINARY :numero_cliente = numero_cliente
                                                        AND baja = '0'");
        $consulta->bindParam(":numero_cliente", $p_numero_cliente);
        $consulta->execute();

        if($consulta->rowCount() === 1)
        {
            return $consulta->fetchObject("Cliente");
        }

        return null;
    }

    private static function existe_cadena_por_igualdad($p_atributo, $p_valor)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("      SELECT numero_cliente
                                                       FROM $db_tabla
                                               WHERE BINARY $p_atributo = :$p_atributo");
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
    private static function existe_alta_cadena_por_igualdad($p_atributo, $p_valor)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("      SELECT numero_cliente
                                                       FROM $db_tabla
                                               WHERE BINARY $p_atributo = :$p_atributo
                                                        AND baja = '0'");
        $consulta->bindParam(":$p_atributo" , $p_valor);
        $consulta->execute();

        if($consulta->rowCount() > 0)
        {
            return true;
        }

        return false;
    }
    private static function existe_alta_numerico_por_igualdad($p_atributo, $p_valor)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT numero_cliente
                                                 FROM $db_tabla
                                                WHERE $p_atributo=:$p_atributo
                                                  AND baja = '0'");
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
    
        return str_shuffle($alfanumerico); // cambio el orden del string
    }
    private static function crear_id()
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
    public function alta($p_dni_empleado)
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
            return ["alta_cliente_error"=>"No se pudo hacer"];
        }

        Movimiento::add($p_dni_empleado, "Realizo el alta del cliente '$this->numero_cliente'");
        return ["alta_cliente"=>"Realizado"];
    }
    public function baja($p_dni_empleado)
    {
        if(self::existe_cadena_por_igualdad("numero_cliente", $this->numero_cliente) === false)
        {
            return ["baja_cliente_error"=>"No se pudo hacer porque no existe el numero de cliente '$this->numero_cliente'"];
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
                Movimiento::add($p_dni_empleado, "Realizo la baja del cliente '$this->numero_cliente'");
                return ["baja_cliente"=>"Realizado"];
            break;

            case 0:
                return ["baja_cliente_error"=>"No se pudo hacer"];
            break;

            default:
                return ["baja_cliente_error"=>"Se realizo, pero se eliminaron '$registros_afectados' registros"];
            break;
        }
    }
    public function baja_logica($p_dni_empleado)
    {
        if(self::existe_cadena_por_igualdad("numero_cliente", $this->numero_cliente) === false)
        {
            return ["baja_logica_cliente_error"=>"No se pudo hacer porque no existe el numero de cliente '$this->numero_cliente'"];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET baja = '1'
                                                WHERE numero_cliente = :numero_cliente");
        $consulta->bindParam(":numero_cliente", $this->numero_cliente);
        if($consulta->execute() === false)
        {
            return ["baja_logica_cliente"=>"No se pudo hacer"];
        }
        
        Movimiento::add($p_dni_empleado, "Realizo la baja logica del cliente '$this->numero_cliente'");
        return ["baja_logica_cliente"=>"Realizado"];
    }
    public function modificar($p_dni_empleado)
    {
        if(self::existe_cadena_por_igualdad("numero_cliente", $this->numero_cliente) === false)
        {
            return ["modificar_cliente_error"=>"No se pudo hacer porque no existe el numero de cliente '$this->numero_cliente'"];
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
        if($consulta->execute() === false)
        {
            return ["modificar_cliente"=>"No se pudo hacer"];
        }
        
        Movimiento::add($p_dni_empleado, "Realizo la modificacion del cliente '$this->numero_cliente'");
        return ["modificar_cliente"=>"Realizado"];
    }
    public function traer_todos()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT * FROM $db_tabla");
        $consulta->execute();
        
        return ["lista_clientes"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Cliente")];
    }
    public function traer_todos_alta()
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
        if(self::existe_cadena_por_igualdad("numero_cliente", $this->numero_cliente) === false)
        {
            return ["traer_un_cliente_error"=>"No se pudo hacer porque no existe el numero de cliente '$this->numero_cliente'"];
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