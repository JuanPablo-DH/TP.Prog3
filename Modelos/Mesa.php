<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "Modelos/Input.php";
require_once "DB/AccesoDatos.php";

class Mesa
{
    public $numero_mesa; // numerico Unico de 5 digitos
    public $numero_cliente; // alfanumerico unico de 6 digitos
    public $numero_comanda; // numerico unico positivo
    public $tipo; // chica o grande
    public $cantidad_clientes_maxima; // Chica [2] | Mediana [4] | Grande [6]
    public $cantidad_clientes; // Chica [2] | Grande [4] 
    public $estado; // "con cliente esperando pedido" | "con cliente comiendo" | "con cliente pagando" | "cerrada"
    public $baja;


    private const DB_TABLA = "mesas";


    private const NUMERO_MESA_MSJ_ERROR = "Numero de mesa no valido. Debe ser un numero de 5 cifras";
    private const NUMERO_CLIENTE_MSJ_ERROR = "Numero de cliente no valido. Debe ser un alfanumerico de 6 cifras";
    private const NUMERO_COMANDA_MSJ_ERROR = "Numero de comanda no valido. Debe ser un numero positivo";
    private const TIPO_MSJ_ERROR = "Tipo de mesa no valida. Debe ser 'chica' o 'grande'";
    private const CANTIDAD_CLIENTES_MAXIMA_MSJ_ERROR = "Cantidad de clientes maximo no valido. Debe ser 2 o 4";
    private const CANTIDAD_CLIENTES_MSJ_ERROR = "Cantidad de clientes no valida. Debe ser hasta 2 para 'chica' o hasta 4 para 'grande'";
    private const ESTADO_MSJ_ERROR = "Estado no valido. Debe ser 'con cliente esperando pedido', 'con cliente comiendo', 'con cliente pagando' o 'cerrada'";
    private const BAJA_MSJ_ERROR = "Baja de mesa no valido. Debe ser 'true' o 'false'";

    private const CANTIDAD_CLIENTES_MAXIMA_FUERA_DE_RANGO_MESA_CHICA_MSJ_ERROR = "Cantidad de clientes maximo no valido. Para este tipo de mesa 'chica' Debe ser 2.";
    private const CANTIDAD_CLIENTES_MAXIMA_FUERA_DE_RANGO_MESA_GRANDE_MSJ_ERROR = "Cantidad de clientes maximo no valido. Para este tipo de mesa 'grande' Debe ser 4.";




    // #region Setters
    public function set_numero_mesa($p_numero_mesa)
    {
        $p_numero_mesa = Input::numerico_esta_entre($p_numero_mesa, 10000, 99999);

        if($p_numero_mesa === null)
        {
            throw new Exception(self::NUMERO_MESA_MSJ_ERROR);
        }
        
        $this->numero_mesa = (int)$p_numero_mesa;
    }
    public function set_numero_cliente($p_numero_cliente)
    {
        $p_numero_cliente = Input::es_alfanumerico($p_numero_cliente, 6, 6);

        if($p_numero_cliente === null)
        {
            throw new Exception(self::NUMERO_CLIENTE_MSJ_ERROR);
        }

        $this->numero_cliente = $p_numero_cliente;
    }
    public function set_numero_comanda($p_numero_comanda)
    {
        $p_numero_comanda = Input::numerico_es_mayor_igual($p_numero_comanda, 1);

        if($p_numero_comanda === null)
        {
            throw new Exception(self::NUMERO_COMANDA_MSJ_ERROR);
        }

        $this->numero_comanda = (int)$p_numero_comanda;
    }
    public function set_tipo($p_tipo)
    {
        $p_tipo = Input::limpiar($p_tipo);
        $p_tipo = strtolower($p_tipo);

        if(strcmp($p_tipo, "chica") != 0 &&
           strcmp($p_tipo, "grande") != 0)
        {
            throw new Exception(self::TIPO_MSJ_ERROR);
            
        }

        $this->tipo = $p_tipo;
    }
    public function set_cantidad_clientes_maxima($p_cantidad_clientes_maxima)
    {
        $p_cantidad_clientes_maxima = Input::es_numerico($p_cantidad_clientes_maxima);

        if($p_cantidad_clientes_maxima === null)
        {
            throw new Exception(self::CANTIDAD_CLIENTES_MAXIMA_MSJ_ERROR);
        }

        $p_cantidad_clientes_maxima = (int)$p_cantidad_clientes_maxima; 

        if($p_cantidad_clientes_maxima != 2 && $p_cantidad_clientes_maxima != 4)
        {
            throw new Exception(self::CANTIDAD_CLIENTES_MAXIMA_MSJ_ERROR);
        }
        else if($p_cantidad_clientes_maxima == 4 && strcmp($this->tipo, "chica") == 0)
        {
            throw new Exception(self::CANTIDAD_CLIENTES_MAXIMA_FUERA_DE_RANGO_MESA_CHICA_MSJ_ERROR);
        }
        else if($p_cantidad_clientes_maxima == 2 && strcmp($this->tipo, "grande") == 0)
        {
            throw new Exception(self::CANTIDAD_CLIENTES_MAXIMA_FUERA_DE_RANGO_MESA_GRANDE_MSJ_ERROR);
        }

        $this->cantidad_clientes_maxima = $p_cantidad_clientes_maxima;
    }
    public function set_cantidad_clientes($p_cantidad_clientes)
    {
        $p_cantidad_clientes = Input::numerico_esta_entre($p_cantidad_clientes, 1, 4);

        if($p_cantidad_clientes === null)
        {
            throw new Exception(self::CANTIDAD_CLIENTES_MSJ_ERROR);
        }

        $this->cantidad_clientes = $p_cantidad_clientes;
    }
    public function set_estado($p_estado)
    {
        $p_estado = Input::limpiar($p_estado);
        $p_estado = strtolower($p_estado);

        if(strcmp($p_estado, "con cliente esperando pedido") != 0 &&
           strcmp($p_estado, "con cliente comiendo") != 0 &&
           strcmp($p_estado, "con cliente pagando") != 0 &&
           strcmp($p_estado, "cerrada") != 0)
        {
            throw new Exception(self::ESTADO_MSJ_ERROR);
        }

        $this->estado = $p_estado;
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
    public static function get($p_numero_mesa)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                               FROM $db_tabla
                                               WHERE numero_mesa = :numero_mesa");
        $consulta->bindParam(":numero_mesa", $p_numero_mesa);
        $consulta->execute();
        return $consulta->fetchObject("Mesa");
    }
    public static function get_por_cantidad_clientes($p_cantidad_clientes)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                               FROM $db_tabla
                                               WHERE :cantidad_clientes <= cantidad_clientes_maxima
                                                     AND
                                                     estado = 'cerrada'");
        $consulta->bindParam(":cantidad_clientes", $p_cantidad_clientes);
        $consulta->execute();
        return $consulta->fetchObject("Mesa");
    }
    private static function existe_cadena_por_igualdad($p_atributo, $p_valor)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT numero_mesa
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
        $consulta = $accesoDatos->GetConsulta("SELECT numero_mesa
                                               FROM $db_tabla
                                               WHERE $p_atributo = :$p_atributo");
        $consulta->bindParam(":$p_atributo" , $p_valor);
        $consulta->execute();

        if($consulta->rowCount() > 0)
        {
            return true;
        }

        return false;
    }
    private static function crear_id()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT numero_mesa
                                               FROM $db_tabla
                                               ORDER BY numero_mesa DESC
                                               LIMIT 1");
        $consulta->execute();
        $registro = $consulta->fetchObject("Mesa");
        if($registro != false)
        {
            return ($registro->numero_mesa + 1);
        }
            
        return 10000;
    }
    // #endregion Utilidades



    
    // #region Funcionalidades
    public function alta()
    {
        $this->numero_mesa = self::crear_id();
        $this->numero_cliente = "000000";
        $this->numero_comanda = 0;
        $this->cantidad_clientes = 0;
        $this->estado = "cerrada";

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("INSERT INTO $db_tabla
                                                           (numero_mesa,
                                                            numero_cliente,
                                                            numero_comanda,
                                                            tipo,
                                                            cantidad_clientes_maxima,
                                                            cantidad_clientes,
                                                            estado,
                                                            baja)
                                                    VALUES
                                                           (:numero_mesa,
                                                            :numero_cliente,
                                                            :numero_comanda,
                                                            :tipo,
                                                            :cantidad_clientes_maxima,
                                                            :cantidad_clientes,
                                                            :estado,
                                                            '0')");
        $consulta->bindParam(':numero_mesa', $this->numero_mesa);
        $consulta->bindParam(':numero_cliente', $this->numero_cliente);
        $consulta->bindParam(':numero_comanda', $this->numero_comanda);
        $consulta->bindParam(':tipo', $this->tipo);
        $consulta->bindParam(':cantidad_clientes_maxima', $this->cantidad_clientes_maxima);
        $consulta->bindParam(':cantidad_clientes', $this->cantidad_clientes);
        $consulta->bindParam(':estado', $this->estado);
        $consulta->execute();

        if(self::existe_numerico_por_igualdad("numero_mesa", $this->numero_mesa) == false)
        {
            return ["Alta Mesa Error"=>"No se pudo hacer."];
        }

        return ["Alta Mesa"=>"Realizado."];
    }
    public function baja()
    {
        if(self::existe_numerico_por_igualdad("numero_mesa", $this->numero_mesa) === false)
        {
            return ["Baja Mesa Error"=>"No se pudo hacer, porque no existe el Numero de Mesa."];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("DELETE FROM $db_tabla
                                                     WHERE numero_mesa = :numero_mesa
                                                           AND
                                                           estado = 'cerrada'");
        $consulta->bindParam(":numero_mesa", $this->numero_mesa);
        $consulta->execute();
        $registros_afectados = $consulta->rowCount();
        switch($registros_afectados)
        {
            case 1:
                return ["Baja Mesa"=>"Realizado."];
            break;

            case 0:
                return ["Baja Mesa Error"=>"No se pudo hacer."];
            break;

            default:
                return ["Baja Mesa Error"=>"Se realizo, pero se eliminaron $registros_afectados registros."];
            break;
        }
    }
    public function baja_logica()
    {
        if(self::existe_numerico_por_igualdad("numero_mesa", $this->numero_mesa) === false)
        {
            return ["Baja Logica Mesa Error"=>"No se pudo hacer, porque no existe el Numero de Mesa."];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET baja = '1'
                                                WHERE numero_mesa = :numero_mesa AND
                                                      estado = 'cerrada'");
        $consulta->bindParam(":numero_mesa", $this->numero_mesa);
        $consulta->execute();
        $registros_afectados = $consulta->rowCount();
        switch($registros_afectados)
        {
            case 1:
                return ["Baja Logica Mesa"=>"Realizado."];
            break;

            case 0:
                return ["Baja Logica Mesa Error"=>"No se pudo hacer."];
            break;

            default:
                return ["Baja Logica Mesa Error"=>"Se realizo, pero se eliminaron logicamente $registros_afectados registros."];
            break;
        }
    }
    public function modificar()
    {
        if(self::existe_numerico_por_igualdad("numero_mesa", $this->numero_mesa) === false)
        {
            return ["Modificar Mesa Error"=>"No se pudo hacer, porque no existe el Numero de Mesa."];
        }

        $mesa = Mesa::get($this->numero_mesa);

        if(strcmp($mesa->estado, "cerrada") != 0)
        {
            return ["Modificar Mesa Error"=>"No se pudo hacer. Porque la mesa no esta 'cerrada'."];
        }


        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET tipo = :tipo,
                                                      cantidad_clientes_maxima = :cantidad_clientes_maxima,
                                                      baja = :baja
                                                WHERE numero_mesa = :numero_mesa
                                                      AND
                                                      estado = 'cerrada'");
        $consulta->bindParam(":numero_mesa", $this->numero_mesa);
        $consulta->bindParam(":tipo", $this->tipo);
        $consulta->bindParam(":cantidad_clientes_maxima", $this->cantidad_clientes_maxima);
        $consulta->bindParam(":baja", $this->baja);
        $consulta->execute();
        $registros_afectados = $consulta->rowCount();
        switch($registros_afectados)
        {
            case 1:
                return ["Modificar Mesa"=>"Realizado."];
            break;

            case 0:
                return ["Modificar Mesa Error"=>"No se pudo hacer."];
            break;

            default:
                return ["Modificar Mesa Error"=>"Se realizo, pero se modificaron $registros_afectados registros."];
            break;
        }
    }
    public static function set($p_mesa)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET numero_cliente = :numero_cliente,
                                                      numero_comanda = :numero_comanda,
                                                      cantidad_clientes = :cantidad_clientes,
                                                      estado = :estado
                                                WHERE numero_mesa = :numero_mesa");
        $consulta->bindParam(":numero_mesa", $p_mesa->numero_mesa);
        $consulta->bindParam(":numero_cliente", $p_mesa->numero_cliente);
        $consulta->bindParam(":numero_comanda", $p_mesa->numero_comanda);
        $consulta->bindParam(":cantidad_clientes", $p_mesa->cantidad_clientes);
        $consulta->bindParam(":estado", $p_mesa->estado);
        $consulta->execute();
        $registros_afectados = $consulta->rowCount();

        if($registros_afectados === 1)
        {
            return true;
        }

        return false;
    }
    public function traer_todos()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT * FROM $db_tabla");
        $consulta->execute();
        return ["lista_mesas"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Mesa")];
    }
    public function traer_todos_sin_baja()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE baja = '0'");
        $consulta->execute();
        return ["lista_mesas"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Mesa")];
    }
    public function traer_uno()
    {
        if(self::existe_numerico_por_igualdad("numero_mesa", $this->numero_mesa) === false)
        {
            return ["Traer Uno Mesa Error"=>"No se pudo hacer, porque no existe el Numero de Mesa."];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE numero_mesa = :numero_mesa");
        $consulta->bindParam(":numero_mesa", $this->numero_mesa);
        $consulta->execute();
        return ["mesa"=>$consulta->fetchObject("Mesa")];
    }
    // #endregion Funcionalidades
}

?>