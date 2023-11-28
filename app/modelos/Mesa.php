<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "bd/AccesoDatos.php";
require_once "utils/Input.php";
require_once "utils/TipoMesa.php";
require_once "utils/Movimiento.php";

class Mesa
{
    public $id;
    public $id_cliente;
    public $id_comanda;
    public $tipo_mesa;
    public $capacidad;
    public $cantidad_clientes;
    public $estado;
    public $fecha_alta;
    public $fecha_modificado;
    public $baja;

    private const DB_TABLA = "mesas";




    // #region Validadores
    public static function validar_id($p_id)
    {
        $p_id = Input::numerico_esta_entre($p_id, 10000, 99999);
        if($p_id === null)
        {
            throw new Exception(json_encode(["error_input_mesa"=>"Id no valido - Debe ser un numero de 5 cifras"]));
        }
        
        return (int)$p_id;
    }
    public static function validar_id_cliente($p_id_cliente)
    {
        $p_id_cliente = Input::es_alfanumerico($p_id_cliente, 6, 6);
        if($p_id_cliente === null)
        {
            throw new Exception(json_encode(["input_error_mesa"=>"Id de cliente no valido - Debe ser un alfanumerico de 6 cifras"]));
        }

        return $p_id_cliente;
    }
    public static function validar_id_comanda($p_id_comanda)
    {
        $p_id_comanda = Input::numerico_es_mayor_igual($p_id_comanda, 1);
        if($p_id_comanda === null)
        {
            throw new Exception(json_encode(["error_input_mesa"=>"Id de comanda no valido - Debe ser un numero positivo"]));
        }

        return (int)$p_id_comanda;
    }
    public static function validar_tipo_mesa($p_tipo_mesa)
    {
        $p_tipo_mesa = strtoupper(Input::limpiar($p_tipo_mesa));

        $tipo_mesa = TipoMesa::get_por_nombre($p_tipo_mesa);
        if($tipo_mesa === null)
        {
            throw new Exception(json_encode(["error_input_mesa"=>"Tipo de mesa no valido - No existe el tipo de mesa '$p_tipo_mesa'"]));
        }

        return $tipo_mesa->nombre;
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
    public function set_id_cliente($p_id_cliente, $p_validar)
    {
        if($p_validar)
        {
            $this->id_cliente = self::validar_id_cliente($p_id_cliente);
        }
        else
        {
            $this->id_cliente = Input::limpiar($p_id_cliente);
        }
    }
    public function set_id_comanda($p_id_comanda, $p_validar)
    {
        if($p_validar)
        {
            $this->id_comanda = self::validar_id_comanda($p_id_comanda);
        }
        else
        {
            $this->id_comanda = intval(Input::limpiar($p_id_comanda));
        }
    }
    public function set_tipo_mesa($p_tipo_mesa, $p_validar)
    {
        if($p_validar)
        {
            $this->tipo_mesa = self::validar_tipo_mesa($p_tipo_mesa);
        }
        else
        {
            $this->tipo_mesa = strtoupper(Input::limpiar($p_tipo_mesa));
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
    public static function add($p_mesa, $p_crear_id, $p_asignar_fecha_alta)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("INSERT INTO $db_tabla
                                                          (id,
                                                           id_tipo_mesa,
                                                           estado,
                                                           fecha_alta,
                                                           baja)
                                                    VALUES
                                                          (:id,
                                                           :id_tipo_mesa,
                                                           'CERRADA',
                                                           :fecha_alta,
                                                           '0')");
        if($p_crear_id)
        {
            $p_mesa->id = self::crear_id();
        }
        $consulta->bindParam(':id', $p_mesa->id);
        $id_tipo_mesa = TipoMesa::get_por_nombre($p_mesa->tipo_mesa)->id;
        $consulta->bindParam(":id_tipo_mesa", $id_tipo_mesa);
        if($p_asignar_fecha_alta)
        {
            $p_mesa->set_fecha_alta();
        }
        $fecha_alta_formato = $p_mesa->fecha_alta->format("Y-m-d H:i:s");
        $consulta->bindParam(':fecha_alta', $fecha_alta_formato);
        $consulta->execute();

        return (self::get($p_mesa->id) !== null);
    }
    public static function set($p_mesa)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET id_cliente = :id_cliente,
                                                      id_comanda = :id_comanda,
                                                      id_tipo_mesa = :id_tipo_mesa,
                                                      estado = :estado,
                                                      fecha_modificado = :fecha_modificado,
                                                      baja = :baja
                                                WHERE id = :id");
        $consulta->bindParam(":id", $p_mesa->id);
        $consulta->bindParam(":id_cliente", $p_mesa->id_cliente);
        $consulta->bindParam(":id_comanda", $p_mesa->id_comanda);
        $id_tipo_mesa = TipoMesa::get_por_nombre($p_mesa->tipo_mesa)->id;
        $consulta->bindParam(":id_tipo_mesa", $id_tipo_mesa);
        $consulta->bindParam(":estado", $p_mesa->estado);
        $p_mesa->set_fecha_modificado();
        $fecha_modificado_formato = $p_mesa->fecha_modificado->format("Y-m-d H:i:s");
        $consulta->bindParam(':fecha_modificado', $fecha_modificado_formato);
        $consulta->bindParam(":baja", $p_mesa->baja);
        return $consulta->execute();
    }
    public static function del($p_mesa)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("DELETE FROM $db_tabla
                                                     WHERE id = :id");
        $consulta->bindParam(":id", $p_mesa->id);
        return $consulta->rowCount();
    }
    public static function del_log($p_mesa)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET baja = '1'
                                                WHERE id = :id");
        $consulta->bindParam(":id", $p_mesa->id);
        return $consulta->execute();
    }
    public static function get($p_id)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      id_cliente,
                                                      id_comanda,
                                                      (SELECT mesa_tipos.nombre FROM mesa_tipos WHERE mesa_tipos.id = mesas.id_tipo_mesa) AS tipo_mesa,
                                                      (SELECT mesa_tipos.capacidad FROM mesa_tipos WHERE mesa_tipos.id = mesas.id_tipo_mesa) AS capacidad,
                                                      (SELECT comandas.cantidad_clientes FROM comandas WHERE comandas.id = mesas.id_comanda) AS cantidad_clientes,
                                                      estado,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla
                                                WHERE id = :id");
        $consulta->bindParam(":id", $p_id);
        $consulta->execute();

        $mesa = $consulta->fetchObject("Mesa");
        if($mesa !== false)
        {
            return $mesa;
        }
        
        return null;
    }
    public static function get_alta($p_id)
    {
        $mesa = self::get($p_id);
        if($mesa !== null && $mesa->baja === 0)
        {
            return $mesa;
        }

        return null;
    }
    public static function get_por_cantidad_clientes($p_cantidad_clientes)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      id_cliente,
                                                      id_comanda,
                                                      (SELECT mesa_tipos.nombre FROM mesa_tipos WHERE mesa_tipos.id = mesas.id_tipo_mesa) AS tipo_mesa,
                                                      (SELECT mesa_tipos.capacidad FROM mesa_tipos WHERE mesa_tipos.id = mesas.id_tipo_mesa) AS capacidad,
                                                      (SELECT comandas.cantidad_clientes FROM comandas WHERE comandas.id = mesas.id_comanda) AS cantidad_clientes,
                                                      estado,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                               FROM $db_tabla
                                               WHERE :cantidad_clientes <= (SELECT mesa_tipos.capacidad FROM mesa_tipos WHERE mesa_tipos.id = mesas.id_tipo_mesa)
                                                     AND
                                                     estado = 'CERRADA'");
        $consulta->bindParam(":cantidad_clientes", $p_cantidad_clientes);
        $consulta->execute();

        $mesa = $consulta->fetchObject("Mesa");
        if($mesa !== false)
        {
            return $mesa;
        }
        
        return null;
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
        $registro = $consulta->fetchObject("Mesa");
        if($registro != false)
        {
            return ($registro->id + 1);
        }
            
        return 10000;
    }
    // #endregion Utilidades



    
    // #region Funcionalidades
    public function alta($p_id_empleado)
    {
        if(self::add($this, true, true) === false)
        {
            return ["error_alta_mesa"=>"No se pudo hacer"];
        }

        Movimiento::add($p_id_empleado, "Realizo el alta de la mesa '$this->id'");
        return ["alta_mesa"=>"Realizado"];
    }
    public function baja($p_id_empleado)
    {
        $mesa = self::get($this->id);
        if($mesa === null)
        {
            return ["error_baja_mesa_error"=>"No existe la mesa '$this->id'"];
        }

        if(strcmp($mesa->estado, "CERRADA") != 0)
        {
            return ["error_baja_mesa_error"=>"No se pudo hacer porque la mesa no esta cerrada"];
        }

        $registros_afectados = self::del($this);
        switch($registros_afectados)
        {
            case 1:
                Movimiento::add($p_id_empleado, "Realizo la baja de la mesa '$this->id'");
                return ["baja_mesa"=>"Realizado"];
            break;

            case 0:
                return ["error_baja_mesa_error"=>"No se pudo hacer"];
            break;

            default:
                return ["error_baja_mesa_error"=>"Se realizo, pero se eliminaron '$registros_afectados' registros"];
            break;
        }
    }
    public function baja_logica($p_id_empleado)
    {
        $mesa = self::get($this->id);
        if($mesa === null)
        {
            return ["error_baja_logica_mesa_error"=>"No existe la mesa '$this->id'"];
        }

        if(strcmp($mesa->estado, "CERRADA") != 0)
        {
            return ["error_baja_logica_mesa_error"=>"No se pudo hacer porque la mesa no esta cerrada"];
        }

        if(self::del_log($this) === false)
        {
            return ["error_baja_logica_mesa_error"=>"No se pudo hacer"];
        }
        
        Movimiento::add($p_id_empleado, "Realizo la baja logica de la mesa '$this->id'");
        return ["baja_logica_mesa"=>"Realizado"];
    }
    public function modificar($p_id_empleado)
    {
        $mesa = self::get($this->id);
        if($mesa === null)
        {
            return ["error_modificar_mesa_error"=>"No existe la mesa '$this->id'"];
        }

        if(strcmp($mesa->estado, "CERRADA") != 0)
        {
            return ["error_modificar_mesa_error"=>"No se pudo hacer porque la mesa no esta cerrada"];
        }

        if(self::set($this) === false)
        {
            return ["error_modificar_mesa_error"=>"No se pudo hacer"];
        }
        
        Movimiento::add($p_id_empleado, "Realizo la modificacion de la mesa '$this->id'");
        return ["modificar_mesa"=>"Realizado"];
    }
    public function traer_todos()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      id_cliente,
                                                      id_comanda,
                                                      (SELECT mesa_tipos.nombre FROM mesa_tipos WHERE mesa_tipos.id = mesas.id_tipo_mesa) AS tipo_mesa,
                                                      (SELECT mesa_tipos.capacidad FROM mesa_tipos WHERE mesa_tipos.id = mesas.id_tipo_mesa) AS capacidad,
                                                      (SELECT comandas.cantidad_clientes FROM comandas WHERE comandas.id = mesas.id_comanda) AS cantidad_clientes,
                                                      estado,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla");
        $consulta->execute();

        return ["lista_mesas"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Mesa")];
    }
    public function traer_todos_alta()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      id_cliente,
                                                      id_comanda,
                                                      (SELECT mesa_tipos.nombre FROM mesa_tipos WHERE mesa_tipos.id = mesas.id_tipo_mesa) AS tipo_mesa,
                                                      (SELECT mesa_tipos.capacidad FROM mesa_tipos WHERE mesa_tipos.id = mesas.id_tipo_mesa) AS capacidad,
                                                      (SELECT comandas.cantidad_clientes FROM comandas WHERE comandas.id = mesas.id_comanda) AS cantidad_clientes,
                                                      estado,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla
                                                WHERE baja = '0'");
        $consulta->execute();

        return ["lista_mesas"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Mesa")];
    }
    public function traer_uno()
    {
        $mesa = self::get($this->id);
        if($mesa === null)
        {
            return ["error_traer_una_mesa"=>"No existe la mesa '$this->id'"];
        }
        
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      id_cliente,
                                                      id_comanda,
                                                      (SELECT mesa_tipos.nombre FROM mesa_tipos WHERE mesa_tipos.id = mesas.id_tipo_mesa) AS tipo_mesa,
                                                      (SELECT mesa_tipos.capacidad FROM mesa_tipos WHERE mesa_tipos.id = mesas.id_tipo_mesa) AS capacidad,
                                                      (SELECT comandas.cantidad_clientes FROM comandas WHERE comandas.id = mesas.id_comanda) AS cantidad_clientes,
                                                      estado,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla
                                                WHERE id = :id");
        $consulta->bindParam(":id", $this->id);
        $consulta->execute();

        return ["mesa"=>$consulta->fetchObject("Mesa")];
    }
    public function traer_mas_usada()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta(" SELECT id,
                                                       id_cliente,
                                                       id_comanda,
                                                       (SELECT mesa_tipos.nombre FROM mesa_tipos WHERE mesa_tipos.id = mesas.id_tipo_mesa) AS tipo_mesa,
                                                       (SELECT mesa_tipos.capacidad FROM mesa_tipos WHERE mesa_tipos.id = mesas.id_tipo_mesa) AS capacidad,
                                                       (SELECT comandas.cantidad_clientes FROM comandas WHERE comandas.id = mesas.id_comanda) AS cantidad_clientes,
                                                       estado,
                                                       fecha_alta,
                                                       fecha_modificado,
                                                       baja,
                                                       (SELECT COUNT(comandas.id_mesa) FROM comandas WHERE mesas.id = comandas.id_mesa) as veces_usada
                                                  FROM $db_tabla
                                              ORDER BY veces_usada DESC LIMIT 1");
        $consulta->execute();

        $mesa = $consulta->fetchObject("Mesa");
        if($mesa !== false)
        {
            return $mesa;
        }
        
        return null;
    }
    public function cobrar()
    {
        $mesa = self::get($this->id);
        if($mesa === null)
        {
            return ["error_cobrar_mesa"=>"No existe la mesa '$this->id'"];
        }

        if(strcmp($mesa->estado, "CON CLIENTE COMIENDO") != 0)
        {
            return ["error_cobrar_mesa"=>"Solo se pueden cobrar mesas que este en el estado 'con cliente comiendo'"];
        }

        $mesa->estado = "CON CLIENTE PAGANDO";

        if(self::set($mesa) === false)
        {
            return ["error_cobrar_mesa"=>"No se pudo hacer"];
        }

        return ["cobrar_mesa"=>"Realizado"];
    }
    public function cerrar()
    {
        $mesa = self::get($this->id);
        if($mesa === null)
        {
            return ["error_cerrar_mesa"=>"No existe la mesa '$this->id'"];
        }

        if(strcmp($mesa->estado, "CON CLIENTE PAGANDO") != 0)
        {
            return ["error_cerrar_mesa"=>"Solo se pueden cerrar mesas que este en el estado 'con cliente pagando'"];
        }

        $mesa->id_cliente = null;
        $mesa->id_comanda = null;
        $mesa->estado = "CERRADA";
        
        if(self::set($mesa) === false)
        {
            return ["error_cerrar_mesa"=>"No se pudo hacer"];
        }

        return ["cerrar_mesa"=>"Realizado"];
    }
    // #endregion Funcionalidades

    
}

?>