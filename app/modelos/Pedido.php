<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "bd/AccesoDatos.php";
require_once "utils/Input.php";
require_once "utils/TipoProducto.php";
require_once "utils/Movimiento.php";

class Pedido
{
    public $id;
    public $id_comanda;
    public $id_producto;
    public $tipo_producto;
    public $nombre_producto;
    public $cantidad_unidades;
    public $precio_unidades;
    public $duracion_estimada;
    public $fecha_inicio_elaboracion;
    public $fecha_fin_elaboracion;
    public $duracion_real;
    public $estado; 
    public $fecha_alta;
    public $fecha_modificado;
    public $baja;

    private const DB_TABLA = "pedidos";




    // #region Validadores
    public static function validar_id($pd_id)
    {
        $pd_id = Input::numerico_es_mayor_igual($pd_id, 1000);
        if($pd_id === null)
        {
            throw new Exception(json_encode(["error_input_pedido"=>"Id no valido - Debe ser mayor igual a 1.000 (mil)."]));
        }

        return (int)$pd_id;
    }
    public static function validar_id_comanda($p_id_comanda)
    {
        $p_id_comanda = Input::numerico_es_mayor_igual($p_id_comanda, 1);
        if($p_id_comanda === null)
        {
            throw new Exception(json_encode(["error_input_pedido"=>"Id de comanda no valido - Debe ser mayor a cero."]));
        }

        return (int)$p_id_comanda;
    }
    public static function validar_id_producto($p_id_producto)
    {
        $p_id_producto = Input::numerico_es_mayor_igual($p_id_producto, 100);
        if($p_id_producto === null)
        {
            throw new Exception(json_encode(["error_input_pedido"=>"Id de producto no valido - Debe ser mayor o igual a 100 (cien)."]));
        }

        return (int)$p_id_producto;
    }
    public static function validar_tipo_producto($p_tipo_producto)
    {
        $p_tipo_producto = strtoupper(Input::limpiar($p_tipo_producto));

        $tipo_producto = TipoProducto::get_por_nombre($p_tipo_producto);
        if($tipo_producto === null)
        {
            throw new Exception(json_encode(["error_input_pedido"=>"Tipo de producto no valido - No existe el tipo de producto '$p_tipo_producto'"]));
        }

        return $tipo_producto->nombre;
    }
    public static function validar_nombre_producto($p_nombre_producto)
    {
        $p_nombre_producto = Input::es_alias_con_guiones($p_nombre_producto, 1, 30);
        if($p_nombre_producto === null)
        {
            throw new Exception(json_encode(["error_input_pedido"=>"Nombre de producto no valido - Debe ser solo letras y/o con signo '-' (guion), y puede tener hasta 30 caracteres como maximo"]));
        }

        return $p_nombre_producto;
    }
    public static function validar_cantidad_unidades($p_cantidad_unidades)
    {
        $p_cantidad_unidades = Input::numerico_es_mayor_igual($p_cantidad_unidades, 1);
        if($p_cantidad_unidades === null)
        {
            throw new Exception(json_encode(["error_input_pedido"=>"Cantidad de unidades no valida - Debe ser positivo"]));
        }

        return (int)$p_cantidad_unidades;
    }
    public static function validar_precio_unidades($p_precio_unidades)
    {
        $p_precio_unidades = Input::numerico_es_mayor_igual($p_precio_unidades, 1);

        if($p_precio_unidades === null)
        {
            throw new Exception(json_encode(["error_input_pedido"=>"Precio de unidades no valido - Debe ser positivo"]));
        }

        return (float)$p_precio_unidades;
    }
    // #endregion Validadores




    // #region Setters
    public function set_id($p_numero_pedido, $p_validar)
    {
        if($p_validar)
        {
            $this->id = self::validar_id($p_numero_pedido);
        }
        else
        {
            $this->id = intval(Input::limpiar($p_numero_pedido));
        }
    }
    public function set_id_comanda($p_numero_comanda, $p_validar)
    {
        if($p_validar)
        {
            $this->id_comanda = self::validar_id_comanda($p_numero_comanda);
        }
        else
        {
            $this->id_comanda = intval(Input::limpiar($p_numero_comanda));
        }
    }
    public function set_id_producto($p_id_producto, $p_validar)
    {
        if($p_validar)
        {
            $this->id_comanda = self::validar_id_producto($p_id_producto);
        }
        else
        {
            $this->id_producto = intval(Input::limpiar($p_id_producto));
        }
    }
    public function set_tipo_producto($p_tipo_producto, $p_validar)
    {
        if($p_validar)
        {
            $this->tipo_producto = self::validar_tipo_producto($p_tipo_producto);
        }
        else
        {
            $this->tipo_producto = strtoupper(Input::limpiar($p_tipo_producto));
        }
    }
    public function set_cantidad_unidades($p_cantidad_unidades, $p_validar)
    {
        if($p_validar)
        {
            $this->cantidad_unidades = self::validar_cantidad_unidades($p_cantidad_unidades);
        }
        else
        {
            $this->cantidad_unidades = intval(Input::limpiar($p_cantidad_unidades));
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
    private function set_fecha_inicio_elaboracion()
    {
        $this->fecha_inicio_elaboracion = new DateTime("now");
    }
    private function set_fecha_fin_elaboracion()
    {
        $this->fecha_fin_elaboracion = new DateTime("now");

        if(!($this->fecha_inicio_elaboracion instanceof DateTime))
        {
            $this->fecha_inicio_elaboracion = DateTime::createFromFormat("Y-m-d H:i:s", $this->fecha_inicio_elaboracion);
            
        }

        $this->duracion_real = $this->fecha_inicio_elaboracion->diff($this->fecha_fin_elaboracion)->i;
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
    public static function add($p_pedido, $p_crear_id, $p_asignar_fecha_alta)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("INSERT INTO $db_tabla
                                                           (id,
                                                            id_comanda,
                                                            id_producto,
                                                            precio_unidades,
                                                            cantidad_unidades,
                                                            duracion_estimada,
                                                            estado,
                                                            fecha_alta,
                                                            baja)
                                                    VALUES
                                                           (:id,
                                                            :id_comanda,
                                                            :id_producto,
                                                            :precio_unidades,
                                                            :cantidad_unidades,
                                                            :duracion_estimada,
                                                            :estado,
                                                            :fecha_alta,
                                                            '0')");
        
        if($p_crear_id)
        {
            $p_pedido->id = self::crear_id();
        }
        $consulta->bindParam(':id', $p_pedido->id);
        $consulta->bindParam(':id_comanda', $p_pedido->id_comanda);
        $consulta->bindParam(':id_producto', $p_pedido->id_producto);
        $consulta->bindParam(':cantidad_unidades', $p_pedido->cantidad_unidades);
        $producto = Producto::get_alta($p_pedido->id_producto);
        $consulta->bindParam(':precio_unidades', $producto->precio_unidades);
        $consulta->bindParam(':duracion_estimada', $producto->duracion_estimada);
        $consulta->bindParam(':estado', $p_pedido->estado);
        if($p_asignar_fecha_alta)
        {
            $p_pedido->set_fecha_alta();
        }
        $fecha_alta_formato = $p_pedido->fecha_alta->format("Y-m-d H:i:s");
        $consulta->bindParam(':fecha_alta', $fecha_alta_formato);
        $consulta->execute();

        return (self::get($p_pedido->id) !== null);
    }
    public static function set($p_pedido)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET id_comanda = :id_comanda,
                                                      id_producto = :id_producto,
                                                      precio_unidades = :precio_unidades,
                                                      cantidad_unidades = :cantidad_unidades,
                                                      duracion_estimada = :duracion_estimada,
                                                      fecha_inicio_elaboracion = :fecha_inicio_elaboracion,
                                                      fecha_fin_elaboracion = :fecha_fin_elaboracion,
                                                      duracion_real = :duracion_real,
                                                      estado = :estado,
                                                      fecha_modificado = :fecha_modificado,
                                                      baja = :baja
                                                WHERE id = :id");
        $consulta->bindParam(':id', $p_pedido->id);
        $consulta->bindParam(':id_comanda', $p_pedido->id_comanda);
        $consulta->bindParam(':id_producto', $p_pedido->id_producto);
        $consulta->bindParam(':cantidad_unidades', $p_pedido->cantidad_unidades);
        $producto = Producto::get_alta($p_pedido->id_producto);
        $consulta->bindParam(':precio_unidades', $producto->precio_unidades);
        $consulta->bindParam(':duracion_estimada', $producto->duracion_estimada);
        if($p_pedido->fecha_inicio_elaboracion instanceof DateTime)
        {
            $fecha_inicio_elaboracion_formato = $p_pedido->fecha_inicio_elaboracion->format("Y-m-d H:i:s");
            $consulta->bindParam(':fecha_inicio_elaboracion', $fecha_inicio_elaboracion_formato);
        }
        else
        {
            $consulta->bindParam(':fecha_inicio_elaboracion', $p_pedido->fecha_inicio_elaboracion);
        }
        if($p_pedido->fecha_fin_elaboracion instanceof DateTime)
        {
            $fecha_fin_elaboracion_formato = $p_pedido->fecha_fin_elaboracion->format("Y-m-d H:i:s");
            $consulta->bindParam(':fecha_fin_elaboracion', $fecha_fin_elaboracion_formato);
        }
        else
        {
            $consulta->bindParam(':fecha_fin_elaboracion', $p_pedido->fecha_fin_elaboracion);
        }
        $consulta->bindParam(':duracion_real', $p_pedido->duracion_real);
        $consulta->bindParam(':estado', $p_pedido->estado);
        $p_pedido->set_fecha_modificado();
        $fecha_modificado_formato = $p_pedido->fecha_modificado->format("Y-m-d H:i:s");
        $consulta->bindParam(':fecha_modificado', $fecha_modificado_formato);
        $consulta->bindParam(':baja', $p_pedido->baja);
        return $consulta->execute();
    }
    public static function get($p_id)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      id_comanda,
                                                      id_producto,
                                                      (SELECT producto_tipos.nombre FROM producto_tipos WHERE producto_tipos.id = (SELECT productos.id_tipo_producto FROM productos WHERE productos.id = pedidos.id_producto)) AS tipo_producto,
                                                      (SELECT productos.nombre FROM productos WHERE productos.id = pedidos.id_producto) AS nombre_producto,
                                                      cantidad_unidades,
                                                      precio_unidades,
                                                      duracion_estimada,
                                                      fecha_inicio_elaboracion,
                                                      fecha_fin_elaboracion,
                                                      duracion_real,
                                                      estado,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla
                                                WHERE id = :id");
        $consulta->bindParam(":id", $p_id);
        $consulta->execute();

        $pedido = $consulta->fetchObject("Pedido");
        if($pedido !== false)
        {
            return $pedido;
        }

        return null;
    }
    public static function get_alta($p_id)
    {
        $pedido = self::get($p_id);

        if($pedido !== null && $pedido->baja === 0)
        {
            return $pedido;
        }

        return null;
    }
    public static function get_por_id_comanda($p_id_comanda)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      id_comanda,
                                                      id_producto,
                                                      (SELECT producto_tipos.nombre FROM producto_tipos WHERE producto_tipos.id = (SELECT productos.id_tipo_producto FROM productos WHERE productos.id = pedidos.id_producto)) AS tipo_producto,
                                                      (SELECT productos.nombre FROM productos WHERE productos.id = pedidos.id_producto) AS nombre_producto,
                                                      cantidad_unidades,
                                                      precio_unidades,
                                                      duracion_estimada,
                                                      fecha_inicio_elaboracion,
                                                      fecha_fin_elaboracion,
                                                      duracion_real,
                                                      estado,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla
                                                WHERE pedidos.id_comanda = :id_comanda");
        $consulta->bindParam(":id_comanda", $p_id_comanda);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
    }
    public static function get_alta_por_id_comanda($p_id_comanda)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      id_comanda,
                                                      id_producto,
                                                      (SELECT producto_tipos.nombre FROM producto_tipos WHERE producto_tipos.id = (SELECT productos.id_tipo_producto FROM productos WHERE productos.id = pedidos.id_producto)) AS tipo_producto,
                                                      (SELECT productos.nombre FROM productos WHERE productos.id = pedidos.id_producto) AS nombre_producto,
                                                      cantidad_unidades,
                                                      precio_unidades,
                                                      duracion_estimada,
                                                      fecha_inicio_elaboracion,
                                                      fecha_fin_elaboracion,
                                                      duracion_real,
                                                      estado,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla
                                                WHERE pedidos.id_comanda = :id_comanda
                                                      AND
                                                      baja = '0'");
        $consulta->bindParam(":id_comanda", $p_id_comanda);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
    }
    public static function convertir_array_asociativo_a_pedido_sin_numero_comanda($p_array_asociativo)
    {
        $pedido = new Pedido();
        $pedido->nombre_producto = $p_array_asociativo["nombre_producto"];
        $pedido->cantidad_unidades = $p_array_asociativo["cantidad_unidades"];
        return $pedido;
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
        $registro = $consulta->fetchObject("Pedido");
        if($registro != false)
        {
            return ($registro->id + 1);
        }
            
        return 1000;
    }
    // #endregion Utilidades




    // #region Funcionalidades
    public function alta($p_dni_empleado)
    {
        if(self::add($this, true, true) === false)
        {
            ["error_alta_pedido"=>"No se pudo hacer"];
        }

        Movimiento::add($p_dni_empleado, "Realizo el alta del pedido '$this->id'");
        return ["alta_pedido"=>"Realizado"];
    }
    public function traer_todos()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      id_comanda,
                                                      id_producto,
                                                      (SELECT producto_tipos.nombre FROM producto_tipos WHERE producto_tipos.id = (SELECT productos.id_tipo_producto FROM productos WHERE productos.id = pedidos.id_producto)) AS tipo_producto,
                                                      (SELECT productos.nombre FROM productos WHERE productos.id = pedidos.id_producto) AS nombre_producto,
                                                      cantidad_unidades,
                                                      precio_unidades,
                                                      duracion_estimada,
                                                      fecha_inicio_elaboracion,
                                                      fecha_fin_elaboracion,
                                                      duracion_real,
                                                      estado,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla");
        $consulta->execute();

        return ["lista_pedidos"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Pedido")];
    }
    public function traer_todos_alta()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      id_comanda,
                                                      id_producto,
                                                      (SELECT producto_tipos.nombre FROM producto_tipos WHERE producto_tipos.id = (SELECT productos.id_tipo_producto FROM productos WHERE productos.id = pedidos.id_producto)) AS tipo_producto,
                                                      (SELECT productos.nombre FROM productos WHERE productos.id = pedidos.id_producto) AS nombre_producto,
                                                      cantidad_unidades,
                                                      precio_unidades,
                                                      duracion_estimada,
                                                      fecha_inicio_elaboracion,
                                                      fecha_fin_elaboracion,
                                                      duracion_real,
                                                      estado,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla
                                                 WHERE baja = '0'");
        $consulta->execute();

        return ["lista_pedidos"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Pedido")];
    }
    public function traer_pendientes_por_tipo_producto_alta()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      id_comanda,
                                                      id_producto,
                                                      (SELECT producto_tipos.nombre FROM producto_tipos WHERE producto_tipos.id = (SELECT productos.id_tipo_producto FROM productos WHERE productos.id = pedidos.id_producto)) AS tipo_producto,
                                                      (SELECT productos.nombre FROM productos WHERE productos.id = pedidos.id_producto) AS nombre_producto,
                                                      cantidad_unidades,
                                                      precio_unidades,
                                                      duracion_estimada,
                                                      fecha_inicio_elaboracion,
                                                      fecha_fin_elaboracion,
                                                      duracion_real,
                                                      estado,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla
                                                 WHERE baja = '0'
                                                       AND
                                                       (SELECT producto_tipos.nombre FROM producto_tipos WHERE producto_tipos.id = (SELECT productos.id_tipo_producto FROM productos WHERE productos.id = pedidos.id_producto)) = :tipo_producto
                                                       AND
                                                       (estado = 'PENDIENTE' OR estado = 'EN PREPARACION')");
        $consulta->bindParam(":tipo_producto", $this->tipo_producto);                                     
        $consulta->execute();

        return ["lista_pedidos"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Pedido")];
    }
    public function traer_listos_para_servir_alta()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      id_comanda,
                                                      id_producto,
                                                      (SELECT producto_tipos.nombre FROM producto_tipos WHERE producto_tipos.id = (SELECT productos.id_tipo_producto FROM productos WHERE productos.id = pedidos.id_producto)) AS tipo_producto,
                                                      (SELECT productos.nombre FROM productos WHERE productos.id = pedidos.id_producto) AS nombre_producto,
                                                      cantidad_unidades,
                                                      precio_unidades,
                                                      duracion_estimada,
                                                      fecha_inicio_elaboracion,
                                                      fecha_fin_elaboracion,
                                                      duracion_real,
                                                      estado,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla
                                                 WHERE baja = '0'
                                                       AND
                                                       estado = 'LISTO PARA SERVIR'");
        $consulta->execute();

        return ["lista_pedidos"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Pedido")];
    }
    public function traer_uno()
    {
        $pedido = self::get($this->id);
        if($pedido === null)
        {
            return ["error_traer_un_pedido"=>"No existe el pedido '$this->id'"];
        }
        
        return ["pedido"=>$pedido];
    }
    public function cervezero_elaborar()
    {
        $pedido = self::get_alta($this->id);
        if($pedido === null)
        {
            return ["error_cervezero_elaborar"=>"No existe el pedido '$this->id'"];
        }

        if(strcmp($pedido->tipo_producto, "BEBIDA-ALCOHOL") != 0)
        {
            return ["error_cervezero_elaborar"=>"El cervezero solo puede elaborar pedidos de tipo 'BEBIDA-ALCOHOL'"];
        }

        if(strcmp($pedido->estado, "PENDIENTE") != 0)
        {
             return ["error_cervezero_elaborar"=>"El cervezero solo puede elaborar pedidos que esten en el estado 'PENDIENTE'"];
        }

        $pedido->set_fecha_inicio_elaboracion();
        $pedido->estado = "EN PREPARACION";

        if(self::set($pedido) === false)
        {
            return ["error_cervezero_elaborar"=>"No se pudo hacer"];
        }

        return ["cervezero_elaborar"=>"Realizado"];
    }
    public function bartender_elaborar()
    {
        $pedido = self::get_alta($this->id);
        if($pedido === null)
        {
            return ["error_bartender_elaborar"=>"No existe el pedido '$$this->id'"];
        }

        if(strcmp($pedido->tipo_producto, "BEBIDA") != 0)
        {
            return ["error_bartender_elaborar"=>"El bartender solo puede elaborar pedidos de tipo 'BEBIDA'"];
        }

        if(strcmp($pedido->estado, "PENDIENTE") != 0)
        {
             return ["error_bartender_elaborar"=>"El bartender solo puede elaborar pedidos que esten en el estado 'PENDIENTE'"];
        }

        $pedido->set_fecha_inicio_elaboracion();
        $pedido->estado = "EN PREPARACION";

        if(self::set($pedido) === false)
        {
            return ["error_bartender_elaborar"=>"No se pudo hacer"];
        }

        return ["bartender_elaborar"=>"Realizado"];
    }
    public function cocinero_elaborar()
    {
        $pedido = self::get_alta($this->id);
        if($pedido === null)
        {
            return ["error_cocinero_elaborar"=>"No existe el pedido '$$this->id'"];
        }

        if(strcmp($pedido->tipo_producto, "COMIDA") != 0)
        {
            return ["error_cocinero_elaborar"=>"El cocinero solo puede elaborar pedidos de tipo 'COMIDA'"];
        }

        if(strcmp($pedido->estado, "PENDIENTE") != 0)
        {
             return ["error_cocinero_elaborar"=>"El cocinero solo puede elaborar pedidos que esten en el estado 'PENDIENTE'"];
        }

        $pedido->set_fecha_inicio_elaboracion();
        $pedido->estado = "EN PREPARACION";

        if(self::set($pedido) === false)
        {
            return ["error_cocinero_elaborar"=>"No se pudo hacer"];
        }

        return ["cocinero_elaborar"=>"Realizado"];
    }
    public function cervezero_terminar()
    {
        $pedido = self::get_alta($this->id);
        if($pedido === null)
        {
            return ["error_cervezero_elaborar"=>"No existe el pedido '$this->id'"];
        }
        
        if(strcmp($pedido->tipo_producto, "BEBIDA-ALCOHOL") != 0)
        {
            return ["error_cervezero_terminar"=>"El cervezero solo puede terminar pedidos de tipo 'BEBIDA-ALCOHOL'"];
        }

        if(strcmp($pedido->estado, "EN PREPARACION") != 0)
        {
             return ["error_cervezero_terminar"=>"El cervezero solo puede terminar pedidos que esten en el estado 'EN PREPARACION'"];
        }

        $pedido->set_fecha_fin_elaboracion();
        $pedido->estado = "LISTO PARA SERVIR";

        if(self::set($pedido) === false)
        {
            return ["error_cervezero_terminar"=>"No se pudo hacer"];
        }

        return ["cervezero_terminar"=>"Realizado"];
    }
    public function bartender_terminar()
    {
        $pedido = self::get_alta($this->id);
        if($pedido === null)
        {
            return ["error_bartender_terminar"=>"No existe el pedido '$$this->id'"];
        }

        if(strcmp($pedido->tipo_producto, "BEBIDA") != 0)
        {
            return ["error_bartender_terminar"=>"El bartender solo puede terminar pedidos de tipo 'BEBIDA'"];
        }

        if(strcmp($pedido->estado, "EN PREPARACION") != 0)
        {
             return ["error_bartender_terminar"=>"El bartender solo puede terminar pedidos que esten en el estado 'EN PREPARACION'"];
        }

        $pedido->set_fecha_fin_elaboracion();
        $pedido->estado = "LISTO PARA SERVIR";

        if(self::set($pedido) === false)
        {
            return ["error_bartender_terminar"=>"No se pudo hacer"];
        }

        return ["bartender_terminar"=>"Realizado"];
    }
    public function cocinero_terminar()
    {
        $pedido = self::get($this->id);
        if($pedido === null)
        {
            return ["error_cocinero_terminar"=>"No existe el pedido '$$this->id'"];
        }

        if(strcmp($pedido->tipo_producto, "COMIDA") != 0)
        {
            return ["error_cocinero_terminar"=>"El cocinero solo puede terminar pedidos de tipo 'COMIDA'"];
        }

        if(strcmp($pedido->estado, "EN PREPARACION") != 0)
        {
             return ["error_cocinero_terminar"=>"El cocinero solo puede terminar pedidos que esten en el estado 'EN PREPARACION'"];
        }

        $pedido->set_fecha_fin_elaboracion();
        $pedido->estado = "LISTO PARA SERVIR";

        if(self::set($pedido) === false)
        {
            return ["error_cocinero_terminar"=>"No se pudo hacer"];
        }

        return ["cocinero_terminar"=>"Realizado"];
    }
    public function mozo_servir()
    {
        $pedido = self::get($this->id);
        if($pedido === null)
        {
            return ["error_mozo_servir"=>"No existe el pedido '$$this->id'"];
        }

        if(strcmp($pedido->estado, "LISTO PARA SERVIR") != 0)
        {
             return ["error_mozo_servir"=>"El mozo solo puede servir pedidos que esten en el estado 'LISTO PARA SERVIR'"];
        }

        $pedido->estado = "SERVIDO";

        if(self::set($pedido) === false)
        {
            return ["error_mozo_servir"=>"No se pudo hacer"];
        }

        $comanda = Comanda::get_alta($pedido->id_comanda);
        $comanda->lista_pedidos = Pedido::get_por_id_comanda($comanda->id);

        if($comanda->verificar_si_todos_los_pedidos_estan_servidos() === true)
        {
            $mesa = Mesa::get($comanda->id_mesa);
            $mesa->estado = "CON CLIENTE COMIENDO";
            Mesa::set($mesa);
        }

        return ["mozo_servir"=>"Realizado"];
    }
    // #endregion Funcionalidades
}
    

?>