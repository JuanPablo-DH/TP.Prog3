<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "bd/AccesoDatos.php";
require_once "modelos/Pedido.php";
require_once "utils/Input.php";
require_once "utils/TipoProducto.php";
require_once "utils/Movimiento.php";

class Producto
{
    public $id;
    public $tipo_producto;
    public $nombre;
    public $duracion_estimada;
    public $stock;
    public $precio_unidades;
    public $fecha_alta;
    public $fecha_modificado;
    public $baja;

    private const DB_TABLA = "productos";




    // #region Validadores
    public static function validar_id($p_id)
    {
        $p_id = Input::numerico_es_mayor_igual($p_id, 100);
        if($p_id === null)
        {
            throw new Exception(json_encode(["error_input_producto"=>"Id no valido - Debe ser mayor o igual a 100"]));
        }

        return (int)$p_id;
    }
    public static function validar_tipo($p_tipo)
    {
        $p_tipo = strtoupper(Input::limpiar($p_tipo));

        $tipo_producto = TipoProducto::get_por_nombre($p_tipo);
        if($tipo_producto === null)
        {
            throw new Exception(json_encode(["error_input_producto"=>"Tipo no valido - No existe el tipo '$p_tipo'"]));
        }

        return $tipo_producto->nombre;
    }
    public static function validar_nombre($p_nombre)
    {
        $p_nombre = Input::es_alias_con_guiones($p_nombre, 1, 30);
        if($p_nombre === null)
        {
            throw new Exception(json_encode(["error_input_producto"=>"Nombre no valido - Debe ser solo letras y/o con signo '-' (guion), y puede tener hasta 30 caracteres como maximo"]));
        }

        return $p_nombre;
    }
    public static function validar_duracion_estimada($p_duracion_estimada)
    {
        $p_duracion_estimada = Input::es_numerico($p_duracion_estimada, 1, 90);

        if($p_duracion_estimada === null)
        {
            throw new Exception(json_encode(["error_input_producto"=>"Duracion estimada no valida - Debe ser un valor en minutos entre 1 y 90 inclusive"]));
        }

        return (int)$p_duracion_estimada;
    }
    public static function validar_stock($p_stock)
    {
        $p_stock = Input::numerico_es_mayor_igual($p_stock, 0);

        if($p_stock === null)
        {
            throw new Exception(json_encode(["error_input_producto"=>"Stock no valido - Debe ser positivo o cero."]));
        }

        return (int)$p_stock;
    }
    public static function validar_precio_unidades($p_precio_unidades)
    {
        $p_precio_unidades = Input::numerico_es_mayor_igual($p_precio_unidades, 1);

        if($p_precio_unidades === null)
        {
            throw new Exception(json_encode(["error_input_producto"=>"Precio de unidades no valido - Debe ser positivo."]));
        }
        
        return (float)$p_precio_unidades;
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
    public function set_tipo($p_tipo, $p_validar)
    {
        if($p_validar)
        {
            $this->tipo_producto = self::validar_tipo($p_tipo);
        }
        else
        {
            $this->tipo_producto = strtoupper(Input::limpiar($p_tipo));
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
    public function set_duracion_estimada($p_duracion_estimada, $p_validar)
    {
        if($p_validar)
        {
            $this->duracion_estimada = self::validar_duracion_estimada($p_duracion_estimada);
        }
        else
        {
            $this->duracion_estimada = intval(Input::limpiar($p_duracion_estimada));
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
    
    public static function add($p_producto, $p_crear_id, $p_asignar_fecha_alta)
    {
        if($p_crear_id)
        {
            $p_producto->id = self::crear_id();
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("INSERT INTO $db_tabla
                                                           (id,
                                                            id_tipo_producto,
                                                            nombre,
                                                            duracion_estimada,
                                                            stock,
                                                            precio_unidades,
                                                            fecha_alta,
                                                            baja)
                                                    VALUES
                                                           (:id,
                                                            :id_tipo_producto,
                                                            :nombre,
                                                            :duracion_estimada,
                                                            :stock,
                                                            :precio_unidades,
                                                            :fecha_alta,
                                                            '0')");
        $consulta->bindParam(':id', $p_producto->id);
        $id_tipo_producto = TipoProducto::get_por_nombre($p_producto->tipo_producto)->id;
        $consulta->bindParam(':id_tipo_producto', $id_tipo_producto);
        $consulta->bindParam(":nombre", $p_producto->nombre);
        $consulta->bindParam(":duracion_estimada", $p_producto->duracion_estimada);
        $consulta->bindParam(":stock", $p_producto->stock);
        $consulta->bindParam(":precio_unidades", $p_producto->precio_unidades);
        if($p_asignar_fecha_alta)
        {
            $p_producto->set_fecha_alta();
        }
        $fecha_alta_formato = $p_producto->fecha_alta->format("Y-m-d H:i:s");
        $consulta->bindParam(':fecha_alta', $fecha_alta_formato);
        $consulta->execute();

        return (self::get($p_producto->id) !== null);
    }
    public static function set($p_producto)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET id_tipo_producto = :id_tipo_producto,
                                                      nombre = :nombre,
                                                      duracion_estimada = :duracion_estimada,
                                                      stock = :stock,
                                                      precio_unidades = :precio_unidades,
                                                      fecha_modificado = :fecha_modificado,
                                                      baja = :baja
                                                WHERE id = :id");
        $consulta->bindParam(":id", $p_producto->id);
        $id_tipo_producto = TipoProducto::get_por_nombre($p_producto->tipo_producto)->id;
        $consulta->bindParam(':id_tipo_producto', $id_tipo_producto);
        $consulta->bindParam(":nombre", $p_producto->nombre);
        $consulta->bindParam(":duracion_estimada", $p_producto->duracion_estimada);
        $consulta->bindParam(":stock", $p_producto->stock);
        $consulta->bindParam(":precio_unidades", $p_producto->precio_unidades);
        $p_producto->set_fecha_modificado();
        $fecha_modificado_formato = $p_producto->fecha_modificado->format("Y-m-d H:i:s");
        $consulta->bindParam(':fecha_modificado', $fecha_modificado_formato);
        $consulta->bindParam(":baja", $p_producto->baja);
        return $consulta->execute();
    }
    public static function del($p_producto)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("DELETE FROM $db_tabla
                                                     WHERE id = :id");
        $consulta->bindParam(":id", $p_producto->id);
        return $consulta->rowCount();
    }
    public static function del_log($p_producto)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET baja = '1'
                                                WHERE id = :id");
        $consulta->bindParam(":id", $p_producto->id);

        return $consulta->execute();
    }
    public static function get($p_id)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      (SELECT producto_tipos.nombre FROM producto_tipos WHERE producto_tipos.id = productos.id_tipo_producto) AS tipo_producto,
                                                      nombre,
                                                      duracion_estimada,
                                                      stock,
                                                      precio_unidades,
                                                      fecha_alta,
                                                      baja
                                               FROM $db_tabla
                                               WHERE id = :id");
        $consulta->bindParam(":id", $p_id);
        $consulta->execute();

        $producto = $consulta->fetchObject("Producto");
        if($producto !== false)
        {
            return $producto;
        }

        return null;
    }
    public static function get_alta($p_id)
    {
        $producto = self::get($p_id);

        if($producto !== null && $producto->baja === 0)
        {
            return $producto;
        }

        return null;
    }
    public static function get_por_nombre($p_nombre)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      (SELECT producto_tipos.nombre FROM producto_tipos WHERE producto_tipos.id = productos.id_tipo_producto) AS tipo_producto,
                                                      nombre,
                                                      duracion_estimada,
                                                      stock,
                                                      precio_unidades,
                                                      fecha_alta,
                                                      baja
                                               FROM $db_tabla
                                               WHERE nombre = :nombre");
        $consulta->bindParam(":nombre", $p_nombre);
        $consulta->execute();

        $producto = $consulta->fetchObject("Producto");
        if($producto !== false)
        {
            return $producto;
        }

        return null;
    }
    public static function get_por_nombre_alta($p_nombre)
    {
        $producto = self::get_por_nombre($p_nombre);

        if($producto !== null && $producto->baja === 0)
        {
            return $producto;
        }

        return null;
    }
    public static function get_all_alta()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      (SELECT producto_tipos.nombre FROM producto_tipos WHERE producto_tipos.id = productos.id_tipo_producto) AS tipo_producto,
                                                      nombre,
                                                      duracion_estimada,
                                                      stock,
                                                      precio_unidades,
                                                      fecha_alta,
                                                      baja
                                               FROM $db_tabla
                                               WHERE baja = '0'");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Producto");
    }

    private static function existe_cadena_por_igualdad($p_atributo, $p_valor)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id
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
        $consulta = $accesoDatos->GetConsulta("SELECT id
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
        $consulta = $accesoDatos->GetConsulta("SELECT id
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

    private static function crear_id()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("  SELECT id
                                                   FROM $db_tabla
                                               ORDER BY id DESC
                                                  LIMIT 1");
        $consulta->execute();
        $registro = $consulta->fetchObject("Producto");
        if($registro != false)
        {
            return ($registro->id + 1);
        }

        return 100;
    }

    public static function validar_pedido($p_pedido)
    {
        if(self::existe_alta_cadena_por_igualdad("nombre", $p_pedido->nombre_producto) === false)
        {
            return "No existe un producto con el nombre '$p_pedido->nombre_producto'";
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
        $consulta->bindParam(":nombre", $p_pedido->nombre_producto);
        $consulta->bindParam(":cantidad_unidades", $p_pedido->cantidad_unidades);
        $consulta->execute();
        
        if($consulta->rowCount() === 1)
        {
            return true;
        }

        return "No hay stock suficiente del producto '$p_pedido->nombre_producto' para '$p_pedido->cantidad_unidades' unidades.";
    }
    public static function set_pedido($p_pedido)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      (SELECT producto_tipos.nombre FROM producto_tipos WHERE producto_tipos.id = productos.id_tipo_producto) AS tipo_producto,
                                                      nombre,
                                                      duracion_estimada,
                                                      stock,
                                                      precio_unidades,
                                                      fecha_alta,
                                                      baja
                                                 FROM $db_tabla
                                                WHERE nombre = :nombre
                                                      AND
                                                      :cantidad_unidades <= stock
                                                      AND
                                                      baja = '0'");
        $consulta->bindParam(":nombre", $p_pedido->nombre_producto);
        $consulta->bindParam(":cantidad_unidades", $p_pedido->cantidad_unidades);
        $consulta->execute();
        $producto = $consulta->fetchObject("Producto");
        if($producto != false)
        {
            $producto->stock -= $p_pedido->cantidad_unidades;
            self::set($producto);
            $p_pedido->id_producto = $producto->id;
            $p_pedido->precio_unidades = $producto->precio_unidades;
            $p_pedido->duracion_estimada = $producto->duracion_estimada;
            return true;
        }

        return false;
    }

    public static function crear_csv()
    {
        $lista_productos = self::get_all_alta();
        $contenido_csv = "id,tipo_producto,nombre,duracion_estimada,stock,precio_unidades\n";

        if(count($lista_productos) > 0)
        {
            foreach($lista_productos as $producto)
            {
                $contenido_csv .= "$producto->id,$producto->tipo_producto,$producto->nombre,$producto->duracion_estimada,$producto->stock,$producto->precio_unidades\n";
            }
        }
        
        return $contenido_csv;
    }
    public static function cargar_csv($p_contenido, $p_validar)
    {
        $lista_productos = array();
        $contador_cargas = 0;

        $lista_lineas = explode("\n", $p_contenido);
        if($lista_lineas !== false && count($lista_lineas) > 0)
        {
            for($i=1; $i<count($lista_lineas); $i++)
            {
                $lista_atributos = explode(',', $lista_lineas[$i]);

                if($lista_atributos !== false && count($lista_atributos) === 6)
                {
                    $producto = new Producto();
                    $producto->set_id($lista_atributos[0], $p_validar);
                    $producto->set_tipo($lista_atributos[1], $p_validar);
                    $producto->set_nombre($lista_atributos[2], $p_validar);
                    $producto->set_duracion_estimada($lista_atributos[3], $p_validar);
                    $producto->set_stock($lista_atributos[4], $p_validar);
                    $producto->set_precio_unidades($lista_atributos[5], $p_validar);
                    array_push($lista_productos, $producto);
                }
            }
        }

        if(count($lista_productos) > 0)
        {
            foreach($lista_productos as $producto)
            {
                if(self::existe_numerico_por_igualdad("id", $producto->id))
                {
                    self::set($producto);
                    $contador_cargas++;
                }
                else
                {
                    self::add($producto, false, true);
                    $contador_cargas++;
                }
            }
        }

        if($contador_cargas > 0)
        {
            return ["producto_cargar_csv"=>"Realizado"];
        }
        
        return ["producto_cargar_csv"=>"No se realizo ninguna carga"];
    }
    public static function validar_csv($p_contenido)
    {
        $lista_lineas = explode("\n", $p_contenido);
        $contador_exitos = 0;

        for($i=1; $i<count($lista_lineas); $i++)
        {
            $lista_atributos = explode(',', $lista_lineas[$i]);
            if($lista_atributos !== false && count($lista_atributos) === 6)
            {
                self::validar_id($lista_atributos[0]);
                self::validar_tipo($lista_atributos[1]);
                self::validar_nombre($lista_atributos[2]);
                self::validar_duracion_estimada($lista_atributos[3]);
                self::validar_stock($lista_atributos[4]);
                self::validar_precio_unidades($lista_atributos[5]);
                $contador_exitos++;
            }
        }

        if($contador_exitos > 0)
        { 
            return true;
        }

        return false;
    }
    // #endregion Utilidades




    // #region Funcionalidades
    public function alta($p_id_empleado)
    {
        $producto = self::get_por_nombre_alta($this->nombre);
        if($producto !== null)
        {
            return ["error_alta_producto"=>"No se pudo hacer porque ya existe el nombre '$this->nombre'"];
        }

        if(self::add($this, true, true) === false)
        {
            return ["alta_producto_error"=>"No se pudo hacer"];
        }
        
        Movimiento::add($p_id_empleado, "Realizo el alta del producto '$this->id'");
        return ["alta_producto"=>"Realizado"];
    }
    public function baja($p_id_empleado)
    {
        $producto = self::get_alta($this->id);
        if($producto === null)
        {
            return ["error_baja_producto"=>"No existe el producto '$this->id'"];
        }

        $registros_afectados = self::del($this);
        switch($registros_afectados)
        {
            case 1:
                Movimiento::add($p_id_empleado, "Realizo la baja del producto '$this->id'");
                return ["baja_producto"=>"Realizado"];
            break;

            case 0:
                return ["error_baja_producto"=>"No se pudo hacer"];
            break;

            default:
                return ["error_baja_producto"=>"Se realizo, pero se eliminaron '$registros_afectados' registros"];
            break;
        }
    }
    public function baja_logica($p_id_empleado)
    {
        $producto = self::get_alta($this->id);
        if($producto === null)
        {
            return ["error_baja_logica_producto"=>"No existe el producto '$this->id'"];
        }

        if(self::del_log($this) === false)
        {
            return ["error_baja_logica_producto"=>"No se pudo hacer"];
        }
        
        Movimiento::add($p_id_empleado, "Realizo la baja logica del producto '$this->id'");
        return ["error_baja_logica_producto"=>"Realizado"];
    }
    public function modificar($p_dni_empleado)
    {
        $producto = self::get_alta($this->id);
        if($producto === null)
        {
            return ["error_modificar_producto"=>"No existe el producto '$this->id'"];
        }

        if(self::del_log($producto) === false)
        {
            return ["error_modificar_producto"=>"No se pudo hacer"];
        }

        if(self::existe_alta_cadena_por_igualdad("nombre", $this->nombre) === true)
        {
            return ["error_modificar_producto"=>"No se pudo hacer porque ya existe un producto con el nombre '$this->nombre'"];
        }

        $producto->baja = false;

        if(self::set($this) === false)
        {
            return ["error_modificar_producto"=>"No se pudo hacer"];
        }
        
        Movimiento::add($p_dni_empleado, "Realizo la modificacion del producto '$this->id'");
        return ["modificar_producto"=>"Realizado"];
    }
    public function traer_todos()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      (SELECT producto_tipos.nombre FROM producto_tipos WHERE producto_tipos.id = productos.id_tipo_producto) AS tipo_producto,
                                                      nombre,
                                                      duracion_estimada,
                                                      stock,
                                                      precio_unidades,
                                                      fecha_alta,
                                                      baja
                                                 FROM $db_tabla");
        $consulta->execute();

        return ["lista_productos"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Producto")];
    }
    public function traer_todos_alta()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      (SELECT producto_tipos.nombre FROM producto_tipos WHERE producto_tipos.id = productos.id_tipo_producto) AS tipo_producto,
                                                      nombre,
                                                      duracion_estimada,
                                                      stock,
                                                      precio_unidades,
                                                      fecha_alta,
                                                      baja
                                                 FROM $db_tabla
                                                 WHERE baja = '0'");
        $consulta->execute();

        return ["lista_productos"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Producto")];
    }
    public function traer_uno()
    {
        $producto = self::get_alta($this->id);
        if($producto === null)
        {
            return ["error_baja_producto"=>"No existe el producto '$this->id'"];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      (SELECT producto_tipos.nombre FROM producto_tipos WHERE producto_tipos.id = productos.id_tipo_producto) AS tipo_producto,
                                                      nombre,
                                                      duracion_estimada,
                                                      stock,
                                                      precio_unidades,
                                                      fecha_alta,
                                                      baja
                                               FROM $db_tabla
                                               WHERE id = :id");
        $consulta->bindParam(":id", $this->id);
        $consulta->execute();

        return ["producto"=>$consulta->fetchObject("Producto")];
    }
    // #region Funcionalidades
}

?>