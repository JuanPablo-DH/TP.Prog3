<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "modelos/Input.php";
require_once "modelos/Movimiento.php";
require_once "bd/AccesoDatos.php";

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


    private const NUMERO_MESA_MSJ_ERROR = ["input_error_mesa"=>"Numero de mesa no valido - Debe ser un numero de 5 cifras"];
    private const NUMERO_CLIENTE_MSJ_ERROR = ["input_error_mesa"=>"Numero de cliente de mesa no valido - Debe ser un alfanumerico de 6 cifras"];
    private const NUMERO_COMANDA_MSJ_ERROR = ["input_error_mesa"=>"Numero de comanda de mesa no valido - Debe ser un numero positivo"];
    private const TIPO_MSJ_ERROR = ["input_error_mesa"=>"Tipo de mesa no valido - Debe ser 'chica' o 'grande'"];
    private const CANTIDAD_CLIENTES_MAXIMA_MSJ_ERROR = ["input_error_mesa"=>"Cantidad de clientes maximo de mesa no valido - Debe ser 2 o 4"];
    private const CANTIDAD_CLIENTES_MSJ_ERROR = ["input_error_mesa"=>"Cantidad de clientes de mesa no valido - Debe ser hasta 2 para 'chica' o hasta 4 para 'grande'"];
    private const ESTADO_MSJ_ERROR = ["input_error_mesa"=>"Estado de mesa no valido - Debe ser 'con cliente esperando pedido', 'con cliente comiendo', 'con cliente pagando' o 'cerrada'"];
    private const BAJA_MSJ_ERROR = ["input_error_mesa"=>"Estado baja de mesa no valido - Debe ser '1' para [true] o '0' para [false]"];

    private const CANTIDAD_CLIENTES_MAXIMA_FUERA_DE_RANGO_MESA_CHICA_MSJ_ERROR = ["input_error_mesa"=>"Cantidad de clientes maximo de mesa no valido - Para este tipo de mesa 'chica' Debe ser 2."];
    private const CANTIDAD_CLIENTES_MAXIMA_FUERA_DE_RANGO_MESA_GRANDE_MSJ_ERROR = ["input_error_mesa"=>"Cantidad de clientes maximo de mesa no valido - Para este tipo de mesa 'grande' Debe ser 4."];




    // #region Validadores
    public static function validar_numero_mesa($p_numero_mesa)
    {
        $p_numero_mesa = Input::numerico_esta_entre($p_numero_mesa, 10000, 99999);

        if($p_numero_mesa === null)
        {
            throw new Exception(json_encode(self::NUMERO_MESA_MSJ_ERROR));
        }
        
        return (int)$p_numero_mesa;
    }
    public static function validar_numero_cliente($p_numero_cliente)
    {
        $p_numero_cliente = Input::es_alfanumerico($p_numero_cliente, 6, 6);

        if($p_numero_cliente === null)
        {
            throw new Exception(json_encode(self::NUMERO_CLIENTE_MSJ_ERROR));
        }

        return $p_numero_cliente;
    }
    public static function validar_numero_comanda($p_numero_comanda)
    {
        $p_numero_comanda = Input::numerico_es_mayor_igual($p_numero_comanda, 1);

        if($p_numero_comanda === null)
        {
            throw new Exception(json_encode(self::NUMERO_COMANDA_MSJ_ERROR));
        }

        return (int)$p_numero_comanda;
    }
    public static function validar_tipo($p_tipo)
    {
        $p_tipo = Input::limpiar($p_tipo);
        $p_tipo = strtolower($p_tipo);

        if(strcmp($p_tipo, "chica") != 0 &&
           strcmp($p_tipo, "grande") != 0)
        {
            throw new Exception(json_encode(self::TIPO_MSJ_ERROR));
        }

        return $p_tipo;
    }
    public static function validar_cantidad_clientes_maxima($p_cantidad_clientes_maxima, $p_tipo)
    {
        $p_cantidad_clientes_maxima = Input::es_numerico($p_cantidad_clientes_maxima);

        if($p_cantidad_clientes_maxima === null)
        {
            throw new Exception(json_encode(self::CANTIDAD_CLIENTES_MAXIMA_MSJ_ERROR));
        }

        $p_cantidad_clientes_maxima = (int)$p_cantidad_clientes_maxima;
        $p_tipo = Input::limpiar($p_tipo);
        $p_tipo = strtolower($p_tipo);

        if($p_cantidad_clientes_maxima != 2 && $p_cantidad_clientes_maxima != 4)
        {
            throw new Exception(json_encode(self::CANTIDAD_CLIENTES_MAXIMA_MSJ_ERROR));
        }
        else if($p_cantidad_clientes_maxima == 4 && strcmp($p_tipo, "chica") == 0)
        {
            throw new Exception(json_encode(self::CANTIDAD_CLIENTES_MAXIMA_FUERA_DE_RANGO_MESA_CHICA_MSJ_ERROR));
        }
        else if($p_cantidad_clientes_maxima == 2 && strcmp($p_tipo, "grande") == 0)
        {
            throw new Exception(json_encode(self::CANTIDAD_CLIENTES_MAXIMA_FUERA_DE_RANGO_MESA_GRANDE_MSJ_ERROR));
        }

        return $p_cantidad_clientes_maxima;
    }
    public static function validar_cantidad_clientes($p_cantidad_clientes)
    {
        $p_cantidad_clientes = Input::numerico_esta_entre($p_cantidad_clientes, 1, 4);

        if($p_cantidad_clientes === null)
        {
            throw new Exception(json_encode(self::CANTIDAD_CLIENTES_MSJ_ERROR));
        }

        return $p_cantidad_clientes;
    }
    public static function validar_estado($p_estado)
    {
        $p_estado = Input::limpiar($p_estado);
        $p_estado = strtolower($p_estado);

        if(strcmp($p_estado, "con cliente esperando pedido") != 0 &&
           strcmp($p_estado, "con cliente comiendo") != 0 &&
           strcmp($p_estado, "con cliente pagando") != 0 &&
           strcmp($p_estado, "cerrada") != 0)
        {
            throw new Exception(json_encode(self::ESTADO_MSJ_ERROR));
        }

        return $p_estado;
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
    public function set_numero_mesa($p_numero_mesa, $p_validar)
    {
        if($p_validar)
        {
            $this->numero_mesa = self::validar_numero_mesa($p_numero_mesa);
        }
        else
        {
            $this->numero_mesa = intval(Input::limpiar($p_numero_mesa));
        }
    }
    public function set_numero_cliente($p_numero_cliente, $p_validar)
    {
        if($p_validar)
        {
            $this->numero_cliente = self::validar_numero_cliente($p_numero_cliente);
        }
        else
        {
            $this->numero_mesa = Input::limpiar($p_numero_cliente);
        }
    }
    public function set_numero_comanda($p_numero_comanda, $p_validar)
    {
        if($p_validar)
        {
            $this->numero_comanda = self::validar_numero_comanda($p_numero_comanda);
        }
        else
        {
            $this->numero_comanda = intval(Input::limpiar($p_numero_comanda));
        }
    }
    public function set_tipo($p_tipo, $p_validar)
    {
        if($p_validar)
        {
            $this->tipo = self::validar_tipo($p_tipo);
        }
        else
        {
            $this->tipo = strtolower(Input::limpiar($p_tipo));
        }
    }
    public function set_cantidad_clientes_maxima($p_cantidad_clientes_maxima, $p_validar)
    {
        if($p_validar)
        {
            $this->cantidad_clientes_maxima = self::validar_cantidad_clientes_maxima($p_cantidad_clientes_maxima, $this->tipo);
        }
        else
        {
            $this->cantidad_clientes_maxima = intval(Input::limpiar($p_cantidad_clientes_maxima));
        }
    }
    public function set_cantidad_clientes($p_cantidad_clientes, $p_validar)
    {
        if($p_validar)
        {
            $this->cantidad_clientes = self::validar_cantidad_clientes($p_cantidad_clientes);
        }
        else
        {
            $this->cantidad_clientes = intval(Input::limpiar($p_cantidad_clientes));
        }
    }
    public function set_estado($p_estado, $p_validar)
    {
        if($p_validar)
        {
            $this->estado = self::validar_estado($p_estado);
        }
        else
        {
            $this->estado = strtolower(Input::limpiar($p_estado));
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
        return $consulta->execute();
    }
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
    public function alta($p_dni_empleado)
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

        if(self::existe_numerico_por_igualdad("numero_mesa", $this->numero_mesa) === false)
        {
            return ["alta_mesa_error"=>"No se pudo hacer"];
        }

        Movimiento::add($p_dni_empleado, "Realizo el alta de la mesa '$this->numero_mesa'");
        return ["alta_mesa"=>"Realizado"];
    }
    public function baja($p_dni_empleado)
    {
        if(self::existe_numerico_por_igualdad("numero_mesa", $this->numero_mesa) === false)
        {
            return ["baja_mesa_error"=>"No se pudo hacer porque no existe el numero de mesa '$this->numero_mesa'"];
        }

        $mesa = self::get($this->numero_mesa);
        if(strcmp($mesa->estado, "cerrada") != 0)
        {
            return ["baja_mesa_error"=>"No se pudo hacer porque la mesa no esta cerrada"];
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
                Movimiento::add($p_dni_empleado, "Realizo la baja de la mesa '$this->numero_mesa'");
                return ["baja_mesa"=>"Realizado"];
            break;

            case 0:
                return ["baja_mesa_error"=>"No se pudo hacer"];
            break;

            default:
                return ["baja_mesa_error"=>"Se realizo, pero se eliminaron '$registros_afectados' registros"];
            break;
        }
    }
    public function baja_logica($p_dni_empleado)
    {
        if(self::existe_numerico_por_igualdad("numero_mesa", $this->numero_mesa) === false)
        {
            return ["baja_logica_mesa_error"=>"No se pudo hacer porque no existe el numero de mesa '$this->numero_mesa'"];
        }

        $mesa = self::get($this->numero_mesa);
        if(strcmp($mesa->estado, "cerrada") != 0)
        {
            return ["baja_logica_mesa_error"=>"No se pudo hacer porque la mesa no esta cerrada"];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET baja = '1'
                                                WHERE numero_mesa = :numero_mesa AND
                                                      estado = 'cerrada'");
        $consulta->bindParam(":numero_mesa", $this->numero_mesa);
        if($consulta->execute() === false)
        {
            return ["baja_logica_mesa_error"=>"No se pudo hacer"];
        }
        
        Movimiento::add($p_dni_empleado, "Realizo la baja logica de la mesa '$this->numero_mesa'");
        return ["baja_logica_mesa"=>"Realizado"];
    }
    public function modificar($p_dni_empleado)
    {
        if(self::existe_numerico_por_igualdad("numero_mesa", $this->numero_mesa) === false)
        {
            return ["modificar_mesa_error"=>"No se pudo hacer porque no existe el numero de mesa '$this->numero_mesa'"];
        }

        $mesa = self::get($this->numero_mesa);
        if(strcmp($mesa->estado, "cerrada") != 0)
        {
            return ["modificar_mesa_error"=>"No se pudo hacer porque la mesa no esta cerrada"];
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
        if($consulta->execute() === false)
        {
            return ["modificar_mesa_error"=>"No se pudo hacer"];
        }
        
        Movimiento::add($p_dni_empleado, "Realizo la modificacion de la mesa '$this->numero_mesa'");
        return ["modificar_mesa"=>"Realizado"];
    }
    public function traer_todos()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT * FROM $db_tabla");
        $consulta->execute();

        return ["lista_mesas"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Mesa")];
    }
    public function traer_todos_alta()
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
            return ["traer_una_mesa_error"=>"No se pudo hacer porque no existe el numero de mesa '$this->numero_mesa'"];
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

    public function cobrar()
    {
        if(self::existe_numerico_por_igualdad("numero_mesa", $this->numero_mesa) === false)
        {
            return ["cobrar_mesa_error"=>"No se pudo hacer porque no existe el numero de mesa '$this->numero_mesa'"];
        }

        $mesa = self::get($this->numero_mesa);

        if(strcmp($mesa->estado, "con cliente comiendo") != 0)
        {
            return ["cobrar_mesa_error"=>"Solo se pueden cobrar mesas que este en el estado 'con cliente comiendo'"];
        }

        $mesa->estado = "con cliente pagando";
        self::set($mesa);

        return ["cobrar_mesa"=>"Realizado"];
    }

    public function cerrar()
    {
        if(self::existe_numerico_por_igualdad("numero_mesa", $this->numero_mesa) === false)
        {
            return ["cerrar_mesa_error"=>"No se pudo hacer porque no existe el numero de mesa '$this->numero_mesa'"];
        }

        $mesa = self::get($this->numero_mesa);

        if(strcmp($mesa->estado, "con cliente pagando") != 0)
        {
            return ["cerrar_mesa_error"=>"Solo se pueden cerrar mesas que este en el estado 'con cliente pagando'"];
        }

        $mesa->estado = "cerrada";
        self::set($mesa);

        return ["cerrar_mesa"=>"Realizado"];
    }
}

?>