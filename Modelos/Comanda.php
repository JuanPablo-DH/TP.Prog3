<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "Modelos/Input.php";
require_once "DB/AccesoDatos.php";
require_once "Modelos/Mesa.php";
require_once "Modelos/Cliente.php";
require_once "Modelos/Pedido.php";
require_once "Modelos/Producto.php";

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


    private const DB_TABLA = "comandas";


    private const NUMERO_COMANDA_MSJ_ERROR = "Numero de comanda no valido, debe ser mayor a cero.";
    private const LISTA_PEDIDOS_MSJ_ERROR = "Lista de pedidos no valida, debe estar seteada.";
    private const NUMERO_MESA_MSJ_ERROR = "Numero de mesa no valido, debe ser de 5 cifras";
    private const TIPO_MESA_MSJ_ERROR = "Tipo de mesa no valido, debe ser chica o grande";
    private const NUMERO_CLIENTE_MSJ_ERROR = "Numero de cliente no valido, debe ser alfanumerico";
    private const NOMBRE_CLIENTE_MSJ_ERROR = "Nombre de cliente no valido, debe ser solo letras y tener menos de 30 caracteres";
    private const CANTIDAD_CLIENTES_MSJ_ERROR = "Cantidad de clientes no valido, deben ser 1 o hasta 4 en total por comanda";




    public function __construct()
    {
        $this->lista_pedidos = array();
    }




    // #region Setters
    public function set_numero_comanda($p_numero_comanda)
    {
        $p_numero_comanda = Input::numerico_es_mayor_igual($p_numero_comanda, 1);

        if($p_numero_comanda === null)
        {
            throw new Exception(self::NUMERO_COMANDA_MSJ_ERROR);
        }

        $this->numero_comanda = (int)$p_numero_comanda;
    }
    public function set_lista_pedidos($p_lista_pedidos)
    {
        $p_lista_pedidos = Input::es_array($p_lista_pedidos, 1, 20);

        if($p_lista_pedidos === null)
        {
            throw new Exception(self::LISTA_PEDIDOS_MSJ_ERROR);
        }

        if($p_lista_pedidos[0] instanceof Pedido)
        {
            foreach($p_lista_pedidos as $elemento)
            {
                array_push($this->lista_pedidos, $elemento);
            }
        }
        else
        {
            foreach($p_lista_pedidos as $elemento)
            {
                array_push($this->lista_pedidos, Pedido::convertir_array_asociativo_a_pedido_sin_numero_comanda($elemento));
            }
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
    public function set_numero_mesa($p_numero_mesa)
    {
        $p_numero_mesa = Input::numerico_es_mayor_igual($p_numero_mesa, 10000);

        if($p_numero_mesa === null)
        {
            throw new Exception(self::NUMERO_MESA_MSJ_ERROR);
        }

        $this->numero_mesa = (int)$p_numero_mesa;
    }
    public function set_tipo_mesa($p_tipo_mesa)
    {
        $p_tipo_mesa = Input::limpiar($p_tipo_mesa);
        $p_tipo_mesa = strtolower($p_tipo_mesa);

        if(strcmp($p_tipo_mesa, "chica") != 0 &&
           strcmp($p_tipo_mesa, "grande") != 0)
        {
            throw new Exception(self::TIPO_MESA_MSJ_ERROR);
        }

        $this->tipo_mesa = $p_tipo_mesa;
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
    public function set_nombre_cliente($p_nombre_cliente)
    {
        $p_nombre_cliente = Input::es_alias_con_espacios($p_nombre_cliente, 1, 30);

        if($p_nombre_cliente === null)
        {
            throw new Exception(self::NOMBRE_CLIENTE_MSJ_ERROR);
        }

        $this->nombre_cliente = $p_nombre_cliente;
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
    public function set_fecha_registro()
    {
        $this->fecha_registro = new DateTime("now");
    }
    // #endregion Setters



    // #region Utilidades
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
    public function alta()
    {
        // Valido los pedidos por lo que yo tengo en los productos
        foreach($this->lista_pedidos as $elemento)
        {
            $ret_validacion_pedido = Producto::validar_pedido($elemento);
            if(!($ret_validacion_pedido === true))
            {
                return ["Alta Comanda Error"=>$ret_validacion_pedido];
            }
        }

        // Busco una mesa que pueda tener la cantidad de clientes solicitada
        $mesa = Mesa::get_por_cantidad_clientes($this->cantidad_clientes);
        if($mesa === false)
        {
            return ["Alta Comanda Error"=>"No se pudo completar. No hay mesas disponibles."];
        }

        // Inserto el cliente en la base de datos clientes
        $cliente = new Cliente();
        $cliente->nombre = $this->nombre_cliente;
        if($cliente->alta() === false)
        {
            return ["Alta Comanda Error"=>"No se pudo completar. Hubo un problema al dar de alta al cliente."];
        }
        
        // Realizo el vinculo entre mesa-cliente con la comanda, y actualizo la base de datos de mesas
        $this->numero_comanda = self::crear_id();
        $this->numero_cliente = $cliente->numero_cliente;
        $this->numero_mesa = $mesa->numero_mesa;
        $this->tipo_mesa = $mesa->tipo;

        $mesa->numero_cliente = $this->numero_cliente;
        $mesa->numero_comanda = $this->numero_comanda;
        $mesa->cantidad_clientes = $this->cantidad_clientes;
        $mesa->estado = "con cliente esperando pedido";
        if(Mesa::set($mesa) === false)
        {
            return ["Alta Comanda Error"=>"No se pudo completar. Hubo un problema al modificar la mesa."];
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
            $elemento->alta();
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
            ["Alta Comanda Error"=>"No se pudo hacer."];
        }

        return ["Alta Comanda"=>"Realizada."];
    }
    public function traer_todos()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT * FROM $db_tabla");
        $consulta->execute();
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
        return ["lista_comandas"=>$lista_comandas];
    }
    public function traer_todos_sin_baja()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE baja = '0'");
        $consulta->execute();
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
        return ["lista_comandas"=>$lista_comandas];
    }
    public function traer_uno()
    {
        if(self::existe_numerico_por_igualdad("numero_comanda", $this->numero_comanda) === false)
        {
            return ["Traer Uno Comanda Error"=>"No se pudo hacer, porque no existe el Numero de Comanda."];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE numero_comanda = :numero_comanda");
        $consulta->bindParam(":numero_comanda", $this->numero_comanda);
        $consulta->execute();
        $comanda = $consulta->fetchObject("Comanda");
        $lista_pedidos = Pedido::get_por_numero_comanda($comanda->numero_comanda);
        if(count($lista_pedidos) > 0)
        {
            $comanda->lista_pedidos = $lista_pedidos;
        }
        return ["comanda"=>$comanda];
    }
    // #endregion Funcionalidades
}

?>