<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "modelos/Input.php";
require_once "modelos/Movimiento.php";
require_once "bd/AccesoDatos.php";

class Producto
{
    public $numero_producto; // Numerico mayor a 100
    public $tipo; // Bebida Con/Sin Alcohool | Comida
    public $nombre; // Nombre de la comida | bebida
    public $stock; // Cantidad de la comida | bebida
    public $precio_unidades; // Precio de la comida | bebida
    public $baja;

    
    private const DB_TABLA = "productos";


    private const NUMERO_PRODUCTO_MSJ_ERROR = ["input_error_producto"=>"Numero de producto no valido - Debe ser mayor a 100."];
    private const TIPO_MSJ_ERROR = ["input_error_producto"=>"Tipo de producto no valido - Debe ser 'bebida' o 'comida'."];
    private const NOMBRE_MSJ_ERROR = ["input_error_producto"=>"Nombre de producto no valido - Debe ser solo letras y/o con signo '-' (guion), y puede tener hasta 30 caracteres como maximo"];
    private const STOCK_MSJ_ERROR = ["input_error_producto"=>"Stock de producto no valido - Debe ser positivo o cero."];
    private const PRECIO_UNIDADES_MSJ_ERROR = ["input_error_producto"=>"Precio de unidades del producto no valido - Debe ser positivo."];
    private const BAJA_MSJ_ERROR = ["input_error_producto"=>"Estado baja de producto no valido. Debe ser '1' para [true] o '0' para [false]"];




    // #region Validadores
    public static function validar_numero_producto($p_numero_producto)
    {
        $p_numero_producto = Input::numerico_es_mayor_igual($p_numero_producto, 100);

        if($p_numero_producto === null)
        {
            throw new Exception(json_encode(self::NUMERO_PRODUCTO_MSJ_ERROR));
        }

        return (int)$p_numero_producto;
    }
    public static function validar_tipo($p_tipo)
    {
        $p_tipo = Input::limpiar($p_tipo);
        $p_tipo = strtolower($p_tipo);

        if(strcmp($p_tipo, "bebida") != 0 &&
           strcmp($p_tipo, "bebida-alcohol") != 0 &&
           strcmp($p_tipo, "comida") != 0)
        {
            throw new Exception(json_encode(self::TIPO_MSJ_ERROR));
        }

        return $p_tipo;
    }
    public static function validar_nombre($p_nombre)
    {
        $p_nombre = Input::es_alias_con_guiones($p_nombre, 1, 30);

        if($p_nombre === null)
        {
            throw new Exception(json_encode(self::NOMBRE_MSJ_ERROR));
        }

        return $p_nombre;
    }
    public static function validar_stock($p_stock)
    {
        $p_stock = Input::numerico_es_mayor_igual($p_stock, 0);

        if($p_stock === null)
        {
            throw new Exception(json_encode(self::STOCK_MSJ_ERROR));
        }

        return (int)$p_stock;
    }
    public static function validar_precio_unidades($p_precio_unidades)
    {
        $p_precio_unidades = Input::numerico_es_mayor_igual($p_precio_unidades, 1);

        if($p_precio_unidades === null)
        {
            throw new Exception(json_encode(self::PRECIO_UNIDADES_MSJ_ERROR));
        }
        
        return (float)$p_precio_unidades;
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
    public function set_numero_producto($p_numero_producto, $p_validar)
    {
        if($p_validar)
        {
            $this->numero_producto = self::validar_numero_producto($p_numero_producto);
        }
        else
        {
            $this->numero_producto = intval(Input::limpiar($p_numero_producto));
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
    public function set_stock($p_stock, $p_validar)
    {
        if($p_validar)
        {
            $this->stock = self::validar_stock($p_stock);
        }
        else
        {
            $this->stock = intval(Input::limpiar($p_stock));
        }
    }
    public function set_precio_unidades($p_precio_unidades, $p_validar)
    {
        if($p_validar)
        {
            $this->precio_unidades = self::validar_precio_unidades($p_precio_unidades);
        }
        else
        {
            $this->precio_unidades = floatval(Input::limpiar($p_precio_unidades));
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
    public static function validar_pedido($p_pedido)
    {
        if(self::existe_alta_cadena_por_igualdad("nombre", $p_pedido->nombre) === false)
        {
            return "No existe un producto con el nombre '$p_pedido->nombre'";
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                               FROM $db_tabla
                                               WHERE nombre = :nombre
                                                     AND
                                                     :cantidad_unidades <= stock
                                                     AND
                                                     baja = '0'");
        $consulta->bindParam(":nombre", $p_pedido->nombre);
        $consulta->bindParam(":cantidad_unidades", $p_pedido->cantidad_unidades);
        $consulta->execute();
        if($consulta->rowCount() === 1)
        {
            return true;
        }

        return "No hay stock suficiente del producto '$p_pedido->nombre' para '$p_pedido->cantidad_unidades' unidades.";
    }
    public static function set_pedido($p_pedido)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE nombre = :nombre
                                                      AND
                                                      :cantidad_unidades <= stock
                                                      AND
                                                      baja = '0'");
        $consulta->bindParam(":nombre", $p_pedido->nombre);
        $consulta->bindParam(":cantidad_unidades", $p_pedido->cantidad_unidades);
        $consulta->execute();
        $producto = $consulta->fetchObject("Producto");
        if($producto != false)
        {
            $producto->stock -= $p_pedido->cantidad_unidades;
            self::set($producto);
            $p_pedido->tipo = $producto->tipo;
            $p_pedido->precio_unidades = $producto->precio_unidades;
            return true;
        }

        return false;
    }
    public static function set($p_producto)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET tipo = :tipo,
                                                      nombre = :nombre,
                                                      stock = :stock,
                                                      precio_unidades = :precio_unidades,
                                                      baja = :baja
                                                WHERE numero_producto = :numero_producto");
        $consulta->bindParam(":numero_producto", $p_producto->numero_producto);
        $consulta->bindParam(':tipo', $p_producto->tipo);
        $consulta->bindParam(":nombre", $p_producto->nombre);
        $consulta->bindParam(":stock", $p_producto->stock);
        $consulta->bindParam(":precio_unidades", $p_producto->precio_unidades);
        $consulta->bindParam(":baja", $p_producto->baja);
        return $consulta->execute();
    }
    public static function get($p_numero_producto)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                               FROM $db_tabla
                                               WHERE numero_producto = :numero_producto");
        $consulta->bindParam(":numero_producto", $p_numero_producto);
        $consulta->execute();
        return $consulta->fetchObject("Producto");
    }
    private static function existe_cadena_por_igualdad($p_atributo, $p_valor)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT numero_producto
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
        $consulta = $accesoDatos->GetConsulta("SELECT numero_producto
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
    private static function existe_alta_cadena_por_igualdad($p_atributo, $p_valor)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT numero_producto
                                                 FROM $db_tabla
                                                WHERE baja = '0'
                                                      AND
                                                      BINARY $p_atributo = :$p_atributo");
        $consulta->bindParam(":$p_atributo" , $p_valor);
        $consulta->execute();

        if($consulta->rowCount() > 0)
        {
            return true;
        }

        return false;
    }
    private static function existe_alta_cadena_por_dos_igualdades($p_atributo_uno, $p_valor_uno, $p_atributo_dos, $p_valor_dos)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT numero_producto
                                                 FROM $db_tabla
                                                WHERE baja = '0'
                                                      AND
                                                      BINARY $p_atributo_uno = :$p_atributo_uno
                                                      AND
                                                      BINARY $p_atributo_dos = :$p_atributo_dos");
        $consulta->bindParam(":$p_atributo_uno" , $p_valor_uno);
        $consulta->bindParam(":$p_atributo_dos" , $p_valor_dos);
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
        $consulta = $accesoDatos->GetConsulta("SELECT numero_producto
                                                 FROM $db_tabla
                                                WHERE baja = '0'
                                                      AND
                                                      $p_atributo = :$p_atributo");
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
        $consulta = $accesoDatos->GetConsulta("  SELECT numero_producto
                                                   FROM $db_tabla
                                               ORDER BY numero_producto DESC
                                                  LIMIT 1");
        $consulta->execute();
        $registro = $consulta->fetchObject("Producto");
        if($registro != false)
        {
            return ($registro->numero_producto + 1);
        }

        return 100;
    }
    // #endregion Utilidades




    // #region Funcionalidades
    public function alta($p_dni_empleado)
    {
        if(self::existe_cadena_por_igualdad("nombre", $this->nombre) === true)
        {
            return ["alta_producto_error"=>"No se pudo hacer porque ya existe el nombre '$this->nombre'"];
        }

        $this->numero_producto = self::crear_id();

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("INSERT INTO $db_tabla
                                                           (numero_producto,
                                                           tipo,
                                                           nombre,
                                                           stock,
                                                           precio_unidades,
                                                           baja)
                                                    VALUES
                                                           (:numero_producto,
                                                           :tipo,
                                                           :nombre,
                                                           :stock,
                                                           :precio_unidades,
                                                           '0')");
        $consulta->bindParam(':numero_producto', $this->numero_producto);
        $consulta->bindParam(':tipo', $this->tipo);
        $consulta->bindParam(":nombre", $this->nombre);
        $consulta->bindParam(":stock", $this->stock);
        $consulta->bindParam(":precio_unidades", $this->precio_unidades);
        $consulta->execute();

        if(self::existe_numerico_por_igualdad("numero_producto", $this->numero_producto) === false)
        {
            return ["alta_producto_error"=>"No se pudo hacer"];
        }
        
        Movimiento::add($p_dni_empleado, "Realizo el alta del producto '$this->numero_producto'");
        return ["alta_producto"=>"Realizado"];
    }
    public function baja($p_dni_empleado)
    {
        if(self::existe_numerico_por_igualdad("numero_producto", $this->numero_producto) === false)
        {
            return ["baja_producto_error"=>"No se pudo hacer porque no existe el numero de producto '$this->numero_producto'"];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("DELETE FROM $db_tabla
                                               WHERE numero_producto = :numero_producto");
        $consulta->bindParam(":numero_producto", $this->numero_producto);
        $consulta->execute();
        $registros_afectados = $consulta->rowCount();
        switch($registros_afectados)
        {
            case 1:
                Movimiento::add($p_dni_empleado, "Realizo la baja del producto '$this->numero_producto'");
                return ["baja_producto"=>"Realizado"];
            break;

            case 0:
                return ["baja_producto_error"=>"No se pudo hacer"];
            break;

            default:
                return ["baja_producto_error"=>"Se realizo, pero se eliminaron '$registros_afectados' registros"];
            break;
        }
    }
    public function baja_logica($p_dni_empleado)
    {
        if(self::existe_numerico_por_igualdad("numero_producto", $this->numero_producto) === false)
        {
            return ["baja_logica_producto_error"=>"No se pudo hacer porque no existe el numero de producto '$this->numero_producto'"];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET baja = '1'
                                                WHERE numero_producto = :numero_producto");
        $consulta->bindParam(":numero_producto", $this->numero_producto);
        if($consulta->execute() === false)
        {
            return ["baja_logica_producto_error"=>"No se pudo hacer"];
        }
        
        Movimiento::add($p_dni_empleado, "Realizo la baja logica del producto '$this->numero_producto'");
        return ["baja_logica_producto"=>"Realizado"];
    }
    public function modificar($p_dni_empleado)
    {
        if(self::existe_numerico_por_igualdad("numero_producto", $this->numero_producto) === false)
        {
            return ["modificar_producto_error"=>"No se pudo hacer porque no existe el numero de producto '$this->numero_producto'"];
        }

        $producto = Producto::get($this->numero_producto);
        $producto->baja_logica($p_dni_empleado);

        if(self::existe_alta_cadena_por_igualdad("nombre", $this->nombre) === true)
        {
            return ["modificar_producto_error"=>"No se pudo hacer porque ya existe un producto con el nombre '$this->nombre'"];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET tipo = :tipo,
                                                      nombre = :nombre,
                                                      stock = :stock,
                                                      precio_unidades = :precio_unidades,
                                                      baja = :baja
                                                WHERE numero_producto = :numero_producto");
        $consulta->bindParam(":numero_producto", $this->numero_producto);
        $consulta->bindParam(':tipo', $this->tipo);
        $consulta->bindParam(":nombre", $this->nombre);
        $consulta->bindParam(":stock", $this->stock);
        $consulta->bindParam(":precio_unidades", $this->precio_unidades);
        $consulta->bindParam(":baja", $this->baja);
        if($consulta->execute() === false)
        {
            return ["modificar_producto_error"=>"No se pudo hacer"];
        }
        
        Movimiento::add($p_dni_empleado, "Realizo la modificacion del producto '$this->numero_producto'");
        return ["modificar_producto"=>"Realizado"];
    }
    public function traer_todos()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT * FROM $db_tabla");
        $consulta->execute();

        return ["lista_productos"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Producto")];
    }
    public function traer_todos_alta()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                 WHERE baja = '0'");
        $consulta->execute();

        return ["lista_productos"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Producto")];
    }
    public function traer_uno()
    {
        if(self::existe_numerico_por_igualdad("numero_producto", $this->numero_producto) === false)
        {
            return ["traer_un_producto_error"=>"No se pudo hacer porque no existe el numero de producto '$this->numero_producto'"];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                               FROM $db_tabla
                                               WHERE numero_producto = :numero_producto");
        $consulta->bindParam(":numero_producto", $this->numero_producto);
        $consulta->execute();

        return ["producto"=>$consulta->fetchObject("Producto")];
    }
    // #region Funcionalidades
}

?>