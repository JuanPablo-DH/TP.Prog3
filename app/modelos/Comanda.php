<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "bd/AccesoDatos.php";
require_once "bd/Archivador.php";
require_once "modelos/Mesa.php";
require_once "modelos/Cliente.php";
require_once "modelos/Pedido.php";
require_once "modelos/Producto.php";
require_once "utils/Input.php";
require_once "utils/TipoMesa.php";
require_once "utils/Movimiento.php";

class Comanda
{
    public $id;
    public $id_cliente;
    public $nombre_cliente;
    public $lista_pedidos;
    public $precio_total;
    public $id_mesa;
    public $tipo_mesa;
    public $cantidad_clientes;
    public $fecha_alta;
    public $fecha_modificado;
    public $baja;
    private $imagen;

    private const DB_TABLA = "comandas";




    public function __construct()
    {
        $this->lista_pedidos = array();
    }




    // #region Validadores
    public static function validar_id($p_id)
    {
        $p_id = Input::numerico_es_mayor_igual($p_id, 1);
        if($p_id === null)
        {
            throw new Exception(json_encode(["error_input_comanda"=>"Id no valido - Debe ser positivo."]));
        }

        return (int)$p_id;
    }
    public static function validar_lista_pedidos($p_lista_pedidos)
    {
        $p_lista_pedidos = Input::es_array($p_lista_pedidos, 1, 20);
        if($p_lista_pedidos === null)
        {
            throw new Exception(json_encode(["error_input_comanda"=>"Lista de pedidos no valida - Debe estar seteada."]));
        }

        return $p_lista_pedidos;
    }
    public static function validar_id_mesa($p_id_mesa)
    {
        $p_id_mesa = Input::numerico_esta_entre($p_id_mesa, 10000, 99999);
        if($p_id_mesa === null)
        {
            throw new Exception(json_encode(["error_input_comanda"=>"Id de mesa no valido - Debe ser un numero de 5 cifras"]));
        }

        $mesa = Mesa::get_alta($p_id_mesa);
        if($mesa === null)
        {
            throw new Exception(json_encode(["error_input_comanda"=>"Id de mesa no valido - No existe la mesa '$p_id_mesa'"]));
        }
        
        return $mesa->id;
    }
    public static function validar_tipo_mesa($p_tipo_mesa)
    {
        $p_tipo_mesa = strtoupper(Input::limpiar($p_tipo_mesa));

        $tipo_mesa = TipoMesa::get_por_nombre($p_tipo_mesa);
        if($tipo_mesa === null)
        {
            throw new Exception(json_encode(["error_input_comanda"=>"Tipo de mesa no valido - No existe el tipo de mesa '$p_tipo_mesa'"]));
        }

        return $tipo_mesa->nombre;
    }
    public static function validar_id_cliente($p_id_cliente)
    {
        $p_id_cliente = Input::es_alfanumerico($p_id_cliente, 6, 6);
        if($p_id_cliente === null)
        {
            throw new Exception(json_encode(["error_input_comanda"=>"Id de cliente no valido - Debe ser un alfanumerico de 6 cifras"]));
        }
        
        return $p_id_cliente;
    }
    public static function validar_nombre_cliente($p_nombre_cliente)
    {
        $p_nombre_cliente = Input::es_alias_con_espacios($p_nombre_cliente, 1, 30);
        if($p_nombre_cliente === null)
        {
            throw new Exception(json_encode(["error_input_comanda"=>"Nombre de cliente no valido - Debe ser solo letras, puede haber espacios y tener hasta de 30 caracteres"]));
        }
        
        return $p_nombre_cliente;
    }
    public static function validar_cantidad_clientes($p_cantidad_clientes)
    {
        $p_cantidad_clientes = Input::numerico_esta_entre($p_cantidad_clientes, 1, 4);
        if($p_cantidad_clientes === null)
        {
            throw new Exception(json_encode(["error_input_comanda"=>"Cantidad de clientes no valido - Deben ser 1 o hasta 4 en total por comanda"]));
        }

        return (int)$p_cantidad_clientes;
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
    public function set_lista_pedidos($p_lista_pedidos, $p_validar)
    {
        if($p_validar)
        {
            $p_lista_pedidos = self::validar_lista_pedidos($p_lista_pedidos);
        }

        if(!($p_lista_pedidos[0] instanceof Pedido))
        {
            foreach($p_lista_pedidos as $elemento)
            {
                array_push($this->lista_pedidos, Pedido::convertir_array_asociativo_a_pedido_sin_numero_comanda($elemento));
            }
        }
        else
        {
            $this->lista_pedidos = $p_lista_pedidos;
        }
    }
    private function set_precio_total()
    {
        $acumulador = 0;

        foreach($this->lista_pedidos as $elemento)
        {
            $acumulador += ($elemento->precio_unidades * $elemento->cantidad_unidades);
        }

        $this->precio_total = $acumulador;
    }
    public function set_id_mesa($p_id_mesa, $p_validar)
    {
        if($p_validar)
        {
            $this->id_mesa = self::validar_id_mesa($p_id_mesa);
        }
        else
        {
            $this->id_mesa = intval(Input::limpiar($p_id_mesa));
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
    public function set_nombre_cliente($p_nombre_cliente, $p_validar)
    {
        if($p_validar)
        {
            $this->nombre_cliente = self::validar_nombre_cliente($p_nombre_cliente);
        }
        else
        {
            $this->nombre_cliente = strtolower(Input::limpiar($p_nombre_cliente));
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
    private function set_fecha_alta()
    {
        $this->fecha_alta = new DateTime("now");
    }
    private function set_fecha_modificado()
    {
        $this->fecha_modificado = new DateTime("now");
    }
    public function set_imagen($p_imagen)
    {
        $this->imagen = $p_imagen;
    }
    // #endregion Setters



    // #region Utilidades
    private static function add($p_comanda, $p_crear_id, $p_asignar_fecha_alta)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("INSERT INTO $db_tabla
                                               (id,
                                                id_cliente,
                                                id_mesa,
                                                cantidad_clientes,
                                                precio_total,
                                                fecha_alta,
                                                baja)
                                               VALUES
                                               (:id,
                                                :id_cliente,
                                                :id_mesa,
                                                :cantidad_clientes,
                                                :precio_total,
                                                :fecha_alta,
                                                '0')");
        if($p_crear_id)
        {
            $p_comanda->id = self::crear_id();
        }
        $consulta->bindParam(':id', $p_comanda->id);
        $consulta->bindParam(':id_cliente', $p_comanda->id_cliente);
        $consulta->bindParam(':id_mesa', $p_comanda->id_mesa);
        $consulta->bindParam(':cantidad_clientes', $p_comanda->cantidad_clientes);
        $consulta->bindParam(':precio_total', $p_comanda->precio_total);
        if($p_asignar_fecha_alta)
        {
            $p_comanda->set_fecha_alta();
        }
        $fecha_alta_formato = $p_comanda->fecha_alta->format('Y-m-d H:i:s');
        $consulta->bindParam(':fecha_alta', $fecha_alta_formato);
        $consulta->execute();

        return (self::get($p_comanda->id) !== null);
    }
    private static function get($p_id)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      id_cliente,
                                                      (SELECT clientes.nombre FROM clientes WHERE clientes.id = comandas.id_cliente) AS nombre_cliente,
                                                      id_mesa,
                                                      (SELECT mesa_tipos.nombre FROM mesa_tipos WHERE mesa_tipos.id = (SELECT mesas.id_tipo_mesa FROM mesas WHERE mesas.id = comandas.id_mesa)) AS tipo_mesa,
                                                      cantidad_clientes,
                                                      precio_total,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla
                                                WHERE id = :id");
        $consulta->bindParam(":id", $p_id);
        $consulta->execute();

        $comanda = $consulta->fetchObject("Comanda");
        if($comanda !== false)
        {
            $comanda->lista_pedidos = Pedido::get_alta_por_id_comanda($comanda->id);
            $comanda->set_precio_total();
            return $comanda;
        }

        return null;
    }
    public static function get_alta($p_id)
    {
        $comanda = self::get($p_id);
        if($comanda !== null && $comanda->baja === 0)
        {
            return $comanda;
        }

        return null;
    }
    public static function crear_id()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id
                                               FROM $db_tabla
                                               ORDER BY id DESC
                                               LIMIT 1");
        $consulta->execute();

        $registro = $consulta->fetchObject("Comanda");
        if($registro != false)
        {
            return ($registro->id + 1);
        }

        return 1;
    }
    // #endregion Utilidades




    // #region Funcionalidades
    public function alta($p_id_empleado)
    {
        $this->id = self::crear_id();

        // Valido los pedidos por lo que yo tengo en los productos
        foreach($this->lista_pedidos as $elemento)
        {
            $ret_validacion_pedido = Producto::validar_pedido($elemento);
            if(!($ret_validacion_pedido === true))
            {
                return ["alta_comanda_error"=>$ret_validacion_pedido];
            }
        }

        // Busco una mesa que pueda tener la cantidad de clientes solicitada
        $mesa = Mesa::get_por_cantidad_clientes($this->cantidad_clientes);
        if($mesa === null)
        {
            return ["error_alta_comanda"=>"No hay mesas disponibles"];
        }

        // Valido que se haya subido la imagen
        if(Archivador::slim_archivo_subir_a_directorio_modificando_nombre($this->imagen, "bd/imagenes/comandas/", "COMANDA-". $this->id) === false)
        {
            return ["error_alta_comanda"=>"No se pudo subir la imagen"];
        }

        // Inserto el cliente en la tabla clientes
        $cliente = new Cliente();
        $cliente->nombre = $this->nombre_cliente;
        if(Cliente::add($cliente, true) === false)
        {
            return ["error_alta_comanda"=>"No se pudo dar de alta el cliente"];
        }

        // Realizo el vinculo entre mesa-cliente con la comanda, y actualizo la tabla mesas
        $this->id_cliente = $cliente->id;
        $this->id_mesa = $mesa->id;
        $mesa->id_cliente = $cliente->id;
        $mesa->id_comanda = $this->id;
        $mesa->estado = "CON CLIENTE ESPERANDO PEDIDO";
        if(Mesa::set($mesa) === false)
        {
            return ["error_alta_comanda"=>"No se pudo modificar la mesa"];
        }

        // Seteo la fecha de registro de la comanda
        $this->set_fecha_alta();

        // Realizo el vinculo entre pedido con la comanda, tambien actualizo mi stock, e inserto todos los pedidos en la tabla pedidos
        foreach($this->lista_pedidos as $elemento)
        {
            $elemento->id_comanda = $this->id;
            $elemento->fecha_alta = $this->fecha_alta;
            $elemento->estado = "PENDIENTE";
            Producto::set_pedido($elemento);
            Pedido::add($elemento, true, false);
        }

        // Seteo el precio total de la comanda
        $this->set_precio_total();

        // Inserto la comanda a la tabla comandas y Verifico que se haya insertado
        if(self::add($this, false, false) === false)
        {
            ["error_alta_comanda"=>"No se pudo hacer"];
        }

        

        Movimiento::add($p_id_empleado, "Realizo el alta de la comanda '$this->id'");
        return ["alta_comanda"=>"Realizado"];
    }
    public function traer_todos()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      id_cliente,
                                                      (SELECT clientes.nombre FROM clientes WHERE clientes.id = comandas.id_cliente) AS nombre_cliente,
                                                      id_mesa,
                                                      (SELECT mesa_tipos.nombre FROM mesa_tipos WHERE mesa_tipos.id = (SELECT mesas.id_tipo_mesa FROM mesas WHERE mesas.id = comandas.id_mesa)) AS tipo_mesa,
                                                      cantidad_clientes,
                                                      precio_total,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla");
        $consulta->execute();

        $lista_comandas = $consulta->fetchAll(PDO::FETCH_CLASS, "Comanda");
        if(count($lista_comandas) > 0)
        {
            foreach($lista_comandas as $elemento)
            {
                $elemento->lista_pedidos = Pedido::get_alta_por_id_comanda($elemento->id);
                $elemento->set_precio_total();
            }
        }

        return ["lista_comandas"=>$lista_comandas];
    }
    public function traer_todos_alta()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      id_cliente,
                                                      (SELECT clientes.nombre FROM clientes WHERE clientes.id = comandas.id_cliente) AS nombre_cliente,
                                                      id_mesa,
                                                      (SELECT mesa_tipos.nombre FROM mesa_tipos WHERE mesa_tipos.id = (SELECT mesas.id_tipo_mesa FROM mesas WHERE mesas.id = comandas.id_mesa)) AS tipo_mesa,
                                                      cantidad_clientes,
                                                      precio_total,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla
                                                WHERE baja = '0'");
        $consulta->execute();

        $lista_comandas = $consulta->fetchAll(PDO::FETCH_CLASS, "Comanda");
        if(count($lista_comandas) > 0)
        {
            foreach($lista_comandas as $elemento)
            {
                $elemento->lista_pedidos = Pedido::get_alta_por_id_comanda($elemento->id);
                $elemento->set_precio_total();
            }
        }

        return ["lista_comandas"=>$lista_comandas];
    }
    public function traer_uno()
    {
        $comanda = self::get_alta($this->id);
        if($comanda === null)
        {
            return ["traer_una_comanda_error"=>"No existe la comanda '$this->id'"];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      id_cliente,
                                                      (SELECT clientes.nombre FROM clientes WHERE clientes.id = comandas.id_cliente) AS nombre_cliente,
                                                      id_mesa,
                                                      (SELECT mesa_tipos.nombre FROM mesa_tipos WHERE mesa_tipos.id = (SELECT mesas.id_tipo_mesa FROM mesas WHERE mesas.id = comandas.id_mesa)) AS tipo_mesa,
                                                      cantidad_clientes,
                                                      precio_total,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla
                                                WHERE id = :id");
        $consulta->bindParam(":id", $this->id);
        $consulta->execute();

        $comanda = $consulta->fetchObject("Comanda");
        if($comanda !== false)
        {
            $comanda->lista_pedidos = Pedido::get_alta_por_id_comanda($comanda->id);
            $comanda->set_precio_total();
        }
        
        return ["comanda"=>$comanda];
    }
    public function traer_lista_pedidos_por_id_comanda_y_id_mesa()
    {
        $comanda = self::get_alta($this->id);
        if($comanda === null)
        {
            return ["traer_pedidos_de_comanda_error"=>"No existe la comanda '$this->id'"];
        }
        
        if($comanda->id_mesa !== $this->id_mesa)
        {
            return ["traer_pedidos_de_comanda_error"=>"No coincide la comanda '$this->id' con la mesa '$this->id_mesa'"];
        }

        $duracion_pedidos = array();
        foreach($comanda->lista_pedidos as $pedido)
        {
            if(!isset($pedido->duracion_real))
            {
                array_push($duracion_pedidos, array($pedido->nombre_producto=>$pedido->duracion_estimada));
            }
            else
            {
                array_push($duracion_pedidos, array($pedido->nombre_producto=>$pedido->duracion_real));
            }
        }

        return ["duracion_pedidos"=>$duracion_pedidos];
    }
    public function verificar_si_todos_los_pedidos_estan_servidos()
    {
        foreach($this->lista_pedidos as $elemento)
        {
            if(strcmp($elemento->estado, "SERVIDO") != 0)
            {
                return false;
            }
        }
        
        return true;
    }
    // #endregion Funcionalidades
}

?>