<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "Modelos/Input.php";
require_once "DB/AccesoDatos.php";

class Producto
{
    public $numero_producto; // Numerico mayor a 100
    public $tipo; // Bebida Con/Sin Alcohool | Comida
    public $nombre; // Nombre de la comida | bebida
    public $stock; // Cantidad de la comida | bebida
    public $precio_unidades; // Precio de la comida | bebida
    public $baja;

    
    private const DB_TABLA = "productos";


    private const NUMERO_PRODUCTO_MSJ_ERROR = "Numero de producto no valido. Debe ser mayor a 100.";
    private const TIPO_MSJ_ERROR = "Tipo no valido. Debe ser 'bebida' o 'comida'.";
    private const NOMBRE_MSJ_ERROR = "Nombre no valido. Debe ser solo letras y/o con signo '-' (guion), y puede tener hasta 30 caracteres como maximo";
    private const STOCK_MSJ_ERROR = "Stock no valido. Debe ser positivo o cero.";
    private const PRECIO_UNIDADES_MSJ_ERROR = "Precio de unidades no valido. Debe ser positivo.";
    private const BAJA_MSJ_ERROR = "Baja de producto no valido. Debe ser 'true' o 'false'";




    // #region Setters
    public function set_numero_producto($p_numero_producto)
    {
        $p_numero_producto = Input::numerico_es_mayor_igual($p_numero_producto, 100);

        if($p_numero_producto === null)
        {
            throw new Exception(self::NUMERO_PRODUCTO_MSJ_ERROR);
        }

        $this->numero_producto = (int)$p_numero_producto;
    }
    public function set_tipo($p_tipo)
    {
        $p_tipo = Input::limpiar($p_tipo);
        $p_tipo = strtolower($p_tipo);

        if(strcmp($p_tipo, "bebida") != 0 &&
           strcmp($p_tipo, "comida") != 0)
        {
            throw new Exception(self::TIPO_MSJ_ERROR);
        }

        $this->tipo = $p_tipo;
    }
    public function set_nombre($p_nombre)
    {
        $p_nombre = Input::es_alias_con_guiones($p_nombre, 1, 30);

        if($p_nombre === null)
        {
            throw new Exception(self::NOMBRE_MSJ_ERROR);
        }

        $this->nombre = $p_nombre;
    }
    public function set_stock($p_stock)
    {
        $p_stock = Input::numerico_es_mayor_igual($p_stock, 0);

        if($p_stock === null)
        {
            throw new Exception(self::STOCK_MSJ_ERROR);
        }

        $this->stock = (int)$p_stock;
    }
    public function set_precio_unidades($p_precio_unidades)
    {
        $p_precio_unidades = Input::numerico_es_mayor_igual($p_precio_unidades, 1);

        if($p_precio_unidades === null)
        {
            throw new Exception(self::PRECIO_UNIDADES_MSJ_ERROR);
        }
        
        $this->precio_unidades = (float)$p_precio_unidades;
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
    private static function get($p_numero_producto)
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
    public function alta()
    {
        if(self::existe_cadena_por_igualdad("nombre", $this->nombre) === true)
        {
            return ["Alta Producto Error"=>"No se pudo hacer. Porque ya existe el nombre '$this->nombre'"];
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
            return ["Alta Producto Error"=>"No se pudo hacer."];
        }
        
        return ["Alta Producto"=>"Realizado."];
    }
    public function baja()
    {
        if(self::existe_numerico_por_igualdad("numero_producto", $this->numero_producto) === false)
        {
            return ["Baja Producto Error"=>"No se pudo hacer, porque no existe el Numero de Producto."];
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
                return ["Baja Producto"=>"Realizado."];
            break;

            case 0:
                return ["Baja Producto Error"=>"No se pudo hacer."];
            break;

            default:
                return ["Baja Producto Error"=>"Se realizo, pero se eliminaron $registros_afectados registros."];
            break;
        }
    }
    public function baja_logica()
    {
        if(self::existe_numerico_por_igualdad("numero_producto", $this->numero_producto) === false)
        {
            return ["Baja Logica Producto Error"=>"No se pudo hacer, porque no existe el Numero de Producto."];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET baja = '1'
                                                WHERE numero_producto = :numero_producto");
        $consulta->bindParam(":numero_producto", $this->numero_producto);
        $consulta->execute();
        $registros_afectados = $consulta->rowCount();
        switch($registros_afectados)
        {
            case 1:
                return ["Baja Logica Producto"=>"Realizado."];
            break;

            case 0:
                return ["Baja Logica Producto Error"=>"No se pudo hacer."];
            break;

            default:
                return ["Baja Logica Producto Error"=>"Se realizo, pero se eliminaron logicamente $registros_afectados registros."];
            break;
        }
    }
    public function modificar()
    {
        if(self::existe_numerico_por_igualdad("numero_producto", $this->numero_producto) === false)
        {
            return ["Modificar Producto Error"=>"No se pudo hacer, porque no existe el Numero de Producto."];
        }

        $producto = Producto::get($this->numero_producto);
        $producto->baja_logica();

        if(self::existe_alta_cadena_por_igualdad("nombre", $this->nombre) === true)
        {
            return ["Modificar Producto Error"=>"No se pudo hacer, porque ya existe un producto con el nombre $this->nombre."];
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
        $consulta->execute();
        $registros_afectados = $consulta->rowCount();
        switch($registros_afectados)
        {
            case 1:
                return ["Modificar Producto"=>"Realizado."];
            break;

            case 0:
                return ["Modificar Producto Error"=>"No se pudo hacer."];
            break;

            default:
                return ["Modificar Producto Error"=>"Se realizo, pero se modificaron $registros_afectados registros."];
            break;
        }
    }
    public function traer_todos()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT * FROM $db_tabla");
        $consulta->execute();
        return ["lista_productos"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Producto")];
    }
    public function traer_todos_sin_baja()
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
            return ["Traer Uno Producto Error"=>"No se pudo hacer, porque no existe el Numero de Producto."];
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
    public static function validar_pedido($p_pedido)
    {
        if(self::existe_cadena_por_igualdad("tipo", $p_pedido->tipo) === false)
        {
            return "No existe un producto con el tipo '$p_pedido->tipo'.";
        }

        if(self::existe_cadena_por_igualdad("nombre", $p_pedido->nombre) === false)
        {
            return "No existe un producto con el nombre '$p_pedido->nombre'.";
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                               FROM $db_tabla
                                               WHERE tipo = :tipo
                                                     AND
                                                     nombre = :nombre
                                                     AND
                                                     :cantidad_unidades <= stock
                                                     AND
                                                     estado = '0'");
        $consulta->bindParam(":tipo", $p_pedido->tipo);
        $consulta->bindParam(":nombre", $p_pedido->nombre);
        $consulta->bindParam(":cantidad_unidades", $p_pedido->cantidad_unidades);
        $consulta->execute();
        if($consulta->rowCount() === 1)
        {
            return true;
        }

        return "No hay stock suficiente del producto '$p_pedido->nombre' para $p_pedido->cantidad_unidades unidades.";
    }
    public static function set_pedido($p_pedido)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE tipo = :tipo
                                                      AND
                                                      nombre = :nombre
                                                      AND
                                                      :cantidad_unidades <= stock
                                                      AND
                                                      estado = '0'");
        $consulta->bindParam(":tipo", $p_pedido->tipo);
        $consulta->bindParam(":nombre", $p_pedido->nombre);
        $consulta->bindParam(":cantidad_unidades", $p_pedido->cantidad_unidades);
        $consulta->execute();
        $producto = $consulta->fetchObject("Producto");
        if($producto != false)
        {
            $producto->stock -= $p_pedido->cantidad_unidades;
            $producto->modificar();
            $p_pedido->precio_unidades = $producto->precio_unidades;
            return true;
        }

        return false;
    }
    // #region Funcionalidades
}

?>