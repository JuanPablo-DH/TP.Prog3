<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "bd/AccesoDatos.php";
require_once "utils/Input.php";
require_once "utils/Movimiento.php";

class Cliente
{
    public $id;
    public $nombre;
    public $fecha_alta;
    public $fecha_modificado;
    public $baja;

    private const DB_TABLA = "clientes";




    // #region Validadores
    public static function validar_id($p_id)
    {
        $p_id = Input::es_alfanumerico($p_id, 6, 6);

        if($p_id === null)
        {
            throw new Exception(json_encode(["error_input_cliente"=>"Id no valido - Debe ser un alfanumerico de 6 cifras"]));
        }

        return $p_id;
    }
    public static function validar_nombre($p_nombre)
    {
        $p_nombre = Input::es_alias_con_espacios($p_nombre, 1, 30);

        if($p_nombre === null)
        {
            throw new Exception(json_encode(["error_input_cliente"=>"Nombre no valido - Debe ser solo letras, puede haber espacios y tener menos de 30 caracteres"]));
        }
        
        return $p_nombre;
    }
    // #endregion Validadores


    

    // #region Setters
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
    private function set_fecha_alta()
    {
        $this->fecha_alta = new DateTime("now");
    }
    private function set_fecha_modificado()
    {
        $this->fecha_modificado = new DateTime("now");
    }
    // #endregion Setters




    // #region Utilidades
    public static function add($p_cliente, $p_crear_id)
    {
        if($p_crear_id)
        {
            $p_cliente->id = self::crear_id();
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("INSERT INTO $db_tabla
                                                           (id,
                                                            nombre,
                                                            fecha_alta,
                                                            baja)
                                                    VALUES
                                                           (:id,
                                                            :nombre,
                                                            :fecha_alta,
                                                            '0')");
        $consulta->bindParam(':id', $p_cliente->id);
        $consulta->bindParam(':nombre', $p_cliente->nombre);
        $p_cliente->set_fecha_alta();
        $fecha_alta_formato = $p_cliente->fecha_alta->format('Y-m-d H:i:s');
        $consulta->bindParam(':fecha_alta', $fecha_alta_formato);
        $consulta->execute();

        return (self::get($p_cliente->id) !== null);
    }
    public static function set($p_cliente)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET nombre = :nombre,
                                                      fecha_modificado = :fecha_modificado,
                                                      baja = :baja
                                                WHERE id = :id");
        $consulta->bindParam(':id', $p_cliente->id);
        $consulta->bindParam(':nombre', $p_cliente->nombre);
        $p_cliente->set_fecha_modificado();
        $fecha_modificado_formato = $p_cliente->fecha_modificado->format('Y-m-d H:i:s');
        $consulta->bindParam(':fecha_modificado', $fecha_modificado_formato);
        $consulta->bindParam(':baja', $p_cliente->baja);
        return $consulta->execute();
    }
    public static function del($p_cliente)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("DELETE FROM $db_tabla
                                                     WHERE id = :id");
        $consulta->bindParam(':id', $p_cliente->id);
        return $consulta->rowCount();
    }
    public static function del_log($p_cliente)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET baja = '1'
                                                WHERE id = :id");
        $consulta->bindParam(':id', $p_cliente->id);
        return $consulta->execute();
    }
    public static function get($p_id)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("      SELECT *
                                                       FROM $db_tabla
                                               WHERE BINARY :id = id");
        $consulta->bindParam(":id", $p_id);
        $consulta->execute();

        $cliente = $consulta->fetchObject("Cliente");
        if($cliente !== false)
        {
            return $cliente;
        }

        return null;
    }
    public static function get_alta($p_numero_cliente)
    {
        $cliente = self::get($p_numero_cliente);

        if($cliente->baja === 0)
        {
            return $cliente;
        }

        return null;
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
            $cliente = self::get($alfanumerico);
            if($cliente === null)
            {
                return $alfanumerico;
            }
        }
    }
    // #endregion Utilidades




    // #region Funcionalidades
    public function alta($p_dni_empleado)
    {
        if(self::add($this, true) === false)
        {
            return ["alta_cliente_error"=>"No se pudo hacer"];
        }

        Movimiento::add($p_dni_empleado, "Realizo el alta del cliente '$this->id'");
        return ["alta_cliente"=>"Realizado"];
    }
    public function baja($p_dni_empleado)
    {
        $cliente = self::get($this->id);
        if($cliente === null)
        {
            return ["error_baja_cliente"=>"No existe el cliente '$this->id'"];
        }

        $registros_afectados = self::del($this);
        switch($registros_afectados)
        {
            case 1:
                Movimiento::add($p_dni_empleado, "Realizo la baja del cliente '$this->id'");
                return ["baja_cliente"=>"Realizado"];
            break;

            case 0:
                return ["error_baja_cliente"=>"No se pudo hacer"];
            break;

            default:
                return ["error_baja_cliente"=>"Se realizo, pero se eliminaron '$registros_afectados' registros"];
            break;
        }
    }
    public function baja_logica($p_dni_empleado)
    {
        $cliente = self::get($this->id);
        if($cliente === null)
        {
            return ["error_baja_logica_cliente"=>"No existe el cliente '$this->id'"];
        }

        if(self::del_log($this) === false)
        {
            return ["error_baja_logica_cliente"=>"No se pudo hacer"];
        }
        
        Movimiento::add($p_dni_empleado, "Realizo la baja logica del cliente '$this->id'");
        return ["baja_logica_cliente"=>"Realizado"];
    }
    public function modificar($p_dni_empleado)
    {
        $cliente = self::get($this->id);
        if($cliente === null)
        {
            return ["error_modificar_cliente"=>"No existe el cliente '$this->id'"];
        }

        if(self::set($this) === false)
        {
            return ["error_modificar_cliente"=>"No se pudo hacer"];
        }
        
        Movimiento::add($p_dni_empleado, "Realizo la modificacion del cliente '$this->id'");
        return ["modificar_cliente"=>"Realizado", "cliente_antes"=>$cliente, "cliente_despues"=>$this];
    }
    public function traer_todos()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla");
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
        $cliente = self::get($this->id);
        if($cliente === null)
        {
            return ["error_modificar_cliente"=>"No existe cliente '$this->id'"];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE BINARY numero_cliente = :numero_cliente");
        $consulta->bindParam(":id", $this->id);
        $consulta->execute();

        return ["cliente"=>$consulta->fetchObject("Cliente")];
    }
    // #endregion Funcionalidades
}

?>