<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "modelos/Input.php";
require_once "bd/AccesoDatos.php";
require_once "bd/Archivador.php";
require_once "modelos/Mesa.php";
require_once "modelos/Cliente.php";
require_once "modelos/Pedido.php";
require_once "modelos/Producto.php";
require_once "modelos/Movimiento.php";

class Comanda
{
    public $numero_comanda;
    public $lista_pedidos;
    public $precio_total;
    public $numero_mesa;
    public $tipo_mesa;
    public $numero_cliente;
    public $nombre_cliente;
    public $cantidad_clientes;
    public $fecha_registro;
    private $imagen;


    private const DB_TABLA = "comandas";


    private const NUMERO_COMANDA_MSJ_ERROR = ["input_error_comanda"=>"Numero de comanda no valido - Debe ser mayor a cero."];
    private const LISTA_PEDIDOS_MSJ_ERROR = ["input_error_comanda"=>"Lista de pedidos no valida - Debe estar seteada."];
    private const NUMERO_MESA_MSJ_ERROR = ["input_error_comanda"=>"Numero de mesa no valido - Debe ser un numero de 5 cifras"];
    private const TIPO_MESA_MSJ_ERROR = ["input_error_comanda"=>"Tipo de mesa no valido - Debe ser 'chica' o 'grande'"];
    private const NUMERO_CLIENTE_MSJ_ERROR = ["input_error_comanda"=>"Numero de cliente no valido - Debe ser un alfanumerico de 6 cifras"];
    private const NOMBRE_CLIENTE_MSJ_ERROR = ["input_error_comanda"=>"Nombre de cliente no valido - Debe ser solo letras, puede haber espacios y tener menos de 30 caracteres"];
    private const CANTIDAD_CLIENTES_MSJ_ERROR = ["input_error_comanda"=>"Cantidad de clientes no valido - Deben ser 1 o hasta 4 en total por comanda"];




    public function __construct()
    {
        $this->lista_pedidos = array();
    }




    // #region Validadores
    public static function validar_numero_comanda($p_numero_comanda)
    {
        $p_numero_comanda = Input::numerico_es_mayor_igual($p_numero_comanda, 1);

        if($p_numero_comanda === null)
        {
            throw new Exception(json_encode(self::NUMERO_COMANDA_MSJ_ERROR));
        }

        return (int)$p_numero_comanda;
    }
    public static function validar_lista_pedidos($p_lista_pedidos)
    {
        $p_lista_pedidos = Input::es_array($p_lista_pedidos, 1, 20);

        if($p_lista_pedidos === null)
        {
            throw new Exception(json_encode(self::LISTA_PEDIDOS_MSJ_ERROR));
        }

        return $p_lista_pedidos;
    }
    public static function validar_numero_mesa($p_numero_mesa)
    {
        $p_numero_mesa = Input::numerico_esta_entre($p_numero_mesa, 10000, 99999);

        if($p_numero_mesa === null)
        {
            throw new Exception(json_encode(self::NUMERO_MESA_MSJ_ERROR));
        }
        
        return (int)$p_numero_mesa;
    }
    public static function validar_tipo_mesa($p_tipo_mesa)
    {
        $p_tipo_mesa = Input::limpiar($p_tipo_mesa);
        $p_tipo_mesa = strtolower($p_tipo_mesa);

        if(strcmp($p_tipo_mesa, "chica") != 0 &&
           strcmp($p_tipo_mesa, "grande") != 0)
        {
            throw new Exception(json_encode(self::TIPO_MESA_MSJ_ERROR));
            
        }

        return $p_tipo_mesa;
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
    public static function validar_nombre_cliente($p_nombre_cliente)
    {
        $p_nombre_cliente = Input::es_alias_con_espacios($p_nombre_cliente, 1, 30);

        if($p_nombre_cliente === null)
        {
            throw new Exception(json_encode(self::NOMBRE_CLIENTE_MSJ_ERROR));
        }
        
        return $p_nombre_cliente;
    }
    public static function validar_cantidad_clientes($p_cantidad_clientes)
    {
        $p_cantidad_clientes = Input::numerico_esta_entre($p_cantidad_clientes, 1, 4);

        if($p_cantidad_clientes === null)
        {
            throw new Exception(json_encode(self::CANTIDAD_CLIENTES_MSJ_ERROR));
        }

        return (int)$p_cantidad_clientes;
    }
    // #endregion Validadores


    

    // #region Setters
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
    public function set_lista_pedidos($p_lista_pedidos, $p_validar)
    {
        if($p_validar)
        {
            $p_lista_pedidos = self::validar_lista_pedidos($p_lista_pedidos);
        }
        else
        {
            $p_lista_pedidos = $p_lista_pedidos;
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
    public function set_precio_total()
    {
        $acumulador = 0;

        foreach($this->lista_pedidos as $elemento)
        {
            $acumulador += ($elemento->precio_unidades * $elemento->cantidad_unidades);
        }

        $this->precio_total = $acumulador;
    }
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
    public function set_tipo_mesa($p_tipo_mesa, $p_validar)
    {
        if($p_validar)
        {
            $this->tipo_mesa = self::validar_tipo_mesa($p_tipo_mesa);
        }
        else
        {
            $this->tipo_mesa = strtolower(Input::limpiar($p_tipo_mesa));
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
            $this->numero_cliente = Input::limpiar($p_numero_cliente);
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
    public function set_fecha_registro()
    {
        $this->fecha_registro = new DateTime("now");
    }
    // #endregion Setters



    // #region Utilidades
    public static function get_alta($p_numero_comanda)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE numero_comanda = :numero_comanda
                                                      AND
                                                      baja = '0'");
        $consulta->bindParam(":numero_comanda", $p_numero_comanda);
        $consulta->execute();
        return $consulta->fetchObject("Comanda");
    }
    private static function existe_cadena_por_igualdad($p_atributo, $p_valor)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT numero_comanda
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
        $consulta = $accesoDatos->GetConsulta("SELECT numero_comanda
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
    public static function crear_id()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT numero_comanda
                                               FROM $db_tabla
                                               ORDER BY numero_comanda DESC
                                               LIMIT 1");
        $consulta->execute();
        $registro = $consulta->fetchObject("Comanda");
        if($registro != false)
        {
            return ($registro->numero_comanda + 1);
        }
        else
        {
            return 1;
        }
    }
    // #endregion Utilidades




    // #region Funcionalidades
    public function alta($p_dni_empleado, $imagen)
    {
        $this->numero_comanda = self::crear_id();

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
        if($mesa === false)
        {
            return ["alta_comanda_error"=>"No se pudo completar. No hay mesas disponibles"];
        }

        // Valido que se haya subido la imagen
        if(Archivador::archivo_subir_a_directorio_modificando_nombre($imagen, "bd/imagenes/comandas/", $this->numero_comanda) === false)
        {
            return ["alta_comanda_error"=>"No se pudo completar. No se pudo subir la imagen"];
        }

        // Inserto el cliente en la base de datos clientes
        $cliente = new Cliente();
        $cliente->nombre = $this->nombre_cliente;
        if(Cliente::add($cliente) === false)
        {
            return ["alta_comanda_error"=>"No se pudo completar. Hubo un problema al dar de alta al cliente"];
        }
        
        // Realizo el vinculo entre mesa-cliente con la comanda, y actualizo la base de datos de mesas
        
        $this->numero_cliente = $cliente->numero_cliente;
        $this->numero_mesa = $mesa->numero_mesa;
        $this->tipo_mesa = $mesa->tipo;

        $mesa->numero_cliente = $this->numero_cliente;
        $mesa->numero_comanda = $this->numero_comanda;
        $mesa->cantidad_clientes = $this->cantidad_clientes;
        $mesa->estado = "con cliente esperando pedido";
        if(Mesa::set($mesa) === false)
        {
            return ["alta_comanda_error"=>"No se pudo completar. Hubo un problema al modificar la mesa"];
        }

        // Seteo la fecha de registro de la comanda
        $this->set_fecha_registro();

        // Realizo el vinculo entre pedido con la comanda, tambien actualizo mi stock, e inserto todos los pedidos en la base de datos de pedidos
        foreach($this->lista_pedidos as $elemento)
        {
            $elemento->numero_comanda = $this->numero_comanda;
            $elemento->fecha_registro = $this->fecha_registro;
            $elemento->fecha_terminado = $this->fecha_registro;
            $elemento->estado = "pendiente";
            Producto::set_pedido($elemento);
            Pedido::add($elemento);
        }

        // Seteo el precio total de la comanda
        $this->set_precio_total();

        // Inserto la comanda a la base datos comandas
        $db_tabla = self::DB_TABLA;
        $accesoDatos = AccesoDatos::GetPdo();
        $consulta = $accesoDatos->GetConsulta("INSERT INTO $db_tabla
                                               (numero_comanda,
                                                precio_total,
                                                numero_mesa,
                                                tipo_mesa,
                                                numero_cliente,
                                                nombre_cliente,
                                                cantidad_clientes,
                                                fecha_registro)
                                               VALUES
                                               (:numero_comanda,
                                                :precio_total,
                                                :numero_mesa,
                                                :tipo_mesa,
                                                :numero_cliente,
                                                :nombre_cliente,
                                                :cantidad_clientes,
                                                :fecha_registro)");
        $consulta->bindParam(':numero_comanda', $this->numero_comanda);
        $consulta->bindParam(':precio_total', $this->precio_total);
        $consulta->bindParam(':numero_mesa', $this->numero_mesa);
        $consulta->bindParam(':tipo_mesa', $this->tipo_mesa);
        $consulta->bindParam(':numero_cliente', $this->numero_cliente);
        $consulta->bindParam(':nombre_cliente', $this->nombre_cliente);
        $consulta->bindParam(':cantidad_clientes', $this->cantidad_clientes);
        $fecha_registro_formato = $this->fecha_registro->format('Y-m-d H:i:s');
        $consulta->bindParam(':fecha_registro', $fecha_registro_formato);
        $consulta->execute();

        // Verifico que se haya insertado
        if(self::existe_numerico_por_igualdad("numero_comanda", $this->numero_comanda) === false)
        {
            ["alta_comanda_error"=>"No se pudo hacer"];
        }

        Movimiento::add($p_dni_empleado, "Realizo el alta de la comanda '$this->numero_comanda'");
        return ["alta_comanda"=>"Realizado"];
    }
    public function traer_todos()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT * FROM $db_tabla");
        $consulta->execute();

        if($consulta->rowCount() > 0)
        {
            $lista_comandas = $consulta->fetchAll(PDO::FETCH_CLASS, "Comanda");
            if(count($lista_comandas) > 0)
            {
                foreach($lista_comandas as $elemento)
                {
                    $lista_pedidos = Pedido::get_por_numero_comanda($elemento->numero_comanda);
                    if(count($lista_pedidos) > 0)
                    {
                        $elemento->lista_pedidos = $lista_pedidos;
                    }
                }
            }
        }

        return ["lista_comandas"=>$lista_comandas];
    }
    public function traer_todos_alta()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE baja = '0'");
        $consulta->execute();

        if($consulta->rowCount() > 0)
        {
            $lista_comandas = $consulta->fetchAll(PDO::FETCH_CLASS, "Comanda");
            if(count($lista_comandas) > 0)
            {
                foreach($lista_comandas as $elemento)
                {
                    $lista_pedidos = Pedido::get_por_numero_comanda($elemento->numero_comanda);
                    if(count($lista_pedidos) > 0)
                    {
                        $elemento->lista_pedidos = $lista_pedidos;
                    }
                }
            }
        }

        return ["lista_comandas"=>$lista_comandas];
    }
    public function traer_uno()
    {
        if(self::existe_numerico_por_igualdad("numero_comanda", $this->numero_comanda) === false)
        {
            return ["traer_una_comanda_error"=>"No se pudo hacer porque no existe el numero de comanda '$this->numero_comanda'"];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE numero_comanda = :numero_comanda");
        $consulta->bindParam(":numero_comanda", $this->numero_comanda);
        $consulta->execute();

        $comanda = $consulta->fetchObject("Comanda");
        $lista_pedidos = Pedido::get_alta_por_numero_comanda($comanda->numero_comanda);
        if(count($lista_pedidos) > 0)
        {
            $comanda->lista_pedidos = $lista_pedidos;
        }
        $comanda->set_precio_total();
        
        return ["comanda"=>$comanda];
    }
    // #endregion Funcionalidades

    public function verificar_si_todos_los_pedidos_estan_servidos()
    {
        foreach($this->lista_pedidos as $elemento)
        {
            if(strcmp($elemento->estado, "servido") != 0)
            {
                return false;
            }
        }
        
        return true;
    }
}

?>