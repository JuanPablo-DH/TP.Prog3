<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "Modelos/Input.php";
require_once "DB/AccesoDatos.php";

class Pedido
{
    public $numero_pedido; // Numerico mayor a 1000
    public $numero_comanda; // Numerico positivo
    public $tipo; // Bebida Con/Sin Alcohool | Comida
    public $nombre; // Nombre de la comida | bebida
    public $cantidad_unidades; // Cantidad de la comida | bebida
    public $precio_unidades; // Precio de la comida | bebida
    public $fecha_registro; //Sale de la Comanda
    public $fecha_terminado; //Lo determina el Socio agregando los minutos
    public $estado; //(Inicial)-"Pendiente" (Intermedio 1)-"En Preparacion" (Intermedio 2)-"Listo Para Servir" (Final)-"Servido" (Excepcion)-"Cancelado" 
    public $baja;

    
    private const DB_TABLA = "pedidos";


    private const NUMERO_PEDIDO_MSJ_ERROR = "Numero de pedido no valido. Debe ser mayor a 1000 (mil).";
    private const NUMERO_COMANDA_MSJ_ERROR = "Numero de comanda no valido. Debe ser mayor a cero.";
    private const TIPO_MSJ_ERROR = "Tipo no valido. Debe estar 'bebida' o 'comida'.";
    private const NOMBRE_MSJ_ERROR = "Nombre no valido. Debe ser solo letras y/o con signo '-' (guion), y puede tener hasta 30 caracteres como maximo";
    private const CANTIDAD_UNIDADES_MSJ_ERROR = "Cantidad de unidades no valido. Debe ser mayor a cero";
    private const PRECIO_UNIDADES_MSJ_ERROR = "Precio de unidades no valido. Debe ser mayor a cero";
    private const FECHA_TERMINADO_MSJ_ERROR = "Fecha de terminado no valida. Los minutos que tarde el pedido en realizarse deben estar entre 1min y 90min inclusive.";
    private const BAJA_MSJ_ERROR = "Baja de pedido no valido. Debe ser 'true' o 'false'";




    // #region Setters
    public function set_numero_pedido($p_numero_pedido)
    {
        $p_numero_pedido = Input::numerico_es_mayor_igual($p_numero_pedido, 1000);

        if($p_numero_pedido === null)
        {
            throw new Exception(self::NUMERO_PEDIDO_MSJ_ERROR);
        }

        $this->numero_pedido = (int)$p_numero_pedido;
    }
    public function set_numero_comanda($p_numero_comanda)
    {
        $p_numero_comanda = Input::numerico_es_mayor_igual($p_numero_comanda, 1);

        if($p_numero_comanda === null)
        {
            throw new Exception(self::NUMERO_COMANDA_MSJ_ERROR);
        }

        $this->numero_comanda = (int)$p_numero_comanda;
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
    public function set_cantidad_unidades($p_cantidad_unidades)
    {
        $p_cantidad_unidades = Input::numerico_es_mayor_igual($p_cantidad_unidades, 1);

        if($p_cantidad_unidades === null)
        {
            throw new Exception(self::CANTIDAD_UNIDADES_MSJ_ERROR);
        }

        $this->cantidad_unidades = (int)$p_cantidad_unidades;
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
    public function set_fecha_registro()
    {
        $this->fecha_registro = new DateTime("now");
    }
    public function set_fecha_terminado($p_minutos)
    {
        $p_minutos = Input::numerico_esta_entre($p_minutos, 1, 90);

        if($p_minutos === null)
        {
            throw new Exception(self::FECHA_TERMINADO_MSJ_ERROR);
        }

        $this->fecha_terminado = $this->fecha_registro;
        $this->fecha_terminado->add(new DateInterval("PT" . (int)$p_minutos . "M"));
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
    public static function get_por_numero_comanda($p_numero_comanda)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE numero_comanda = :numero_comanda");
        $consulta->bindParam(":numero_comanda", $p_numero_comanda);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
    }
    public static function convertir_array_asociativo_a_pedido_sin_numero_comanda($p_array_asociativo)
    {
        $pedido = new Pedido();
        $pedido->tipo = $p_array_asociativo["tipo"];
        $pedido->nombre = $p_array_asociativo["nombre"];
        $pedido->cantidad_unidades = $p_array_asociativo["cantidad_unidades"];
        $pedido->precio_unidades = $p_array_asociativo["precio_unidades"];
        return $pedido;
    }
    private static function existe_numerico_por_igualdad($pAtributo, $pValor)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT numero_pedido
                                               FROM $db_tabla
                                               WHERE $pAtributo=:$pAtributo");
        $consulta->bindParam(":$pAtributo" , $pValor);
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
        $consulta = $accesoDatos->GetConsulta("SELECT numero_pedido
                                               FROM $db_tabla
                                               ORDER BY numero_pedido DESC
                                               LIMIT 1");
        $consulta->execute();
        $registro = $consulta->fetchObject("Pedido");
        if($registro != false)
        {
            return ($registro->numero_pedido + 1);
        }
            
        return 1000;
    }
    // #endregion Utilidades




    // #region Funcionalidades
    public function alta()
    {
        $this->numero_pedido = self::crear_id();

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("INSERT INTO $db_tabla
                                                           (numero_pedido,
                                                            numero_comanda,
                                                            tipo,
                                                            nombre,
                                                            cantidad_unidades,
                                                            precio_unidades,
                                                            fecha_registro,
                                                            fecha_terminado,
                                                            estado,
                                                            baja)
                                                    VALUES
                                                            (:numero_pedido,
                                                             :numero_comanda,
                                                             :tipo,
                                                             :nombre,
                                                             :cantidad_unidades,
                                                             :precio_unidades,
                                                             :fecha_registro,
                                                             :fecha_terminado,
                                                             :estado,
                                                             '0')");
        $consulta->bindParam(':numero_pedido', $this->numero_pedido);
        $consulta->bindParam(':numero_comanda', $this->numero_comanda);
        $consulta->bindParam(':tipo', $this->tipo);
        $consulta->bindParam(':nombre', $this->nombre);
        $consulta->bindParam(':cantidad_unidades', $this->cantidad_unidades);
        $consulta->bindParam(':precio_unidades', $this->precio_unidades);
        $fecha_registro_formato = $this->fecha_registro->format("Y-m-d H:i:s");
        $fecha_terminado_formato = $this->fecha_terminado->format("Y-m-d H:i:s");;
        $consulta->bindParam(':fecha_registro', $fecha_registro_formato);
        $consulta->bindParam(':fecha_terminado', $fecha_terminado_formato);
        $consulta->bindParam(':estado', $this->estado);
        $consulta->execute();

        if(self::existe_numerico_por_igualdad("numero_pedido", $this->numero_pedido) === false)
        {
            ["Alta Pedido Error"=>"No se pudo hacer."];
        }

        return ["Alta Pedido"=>"Realizado."];
    }
    public function traer_todos()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT * FROM $db_tabla");
        $consulta->execute();
        return ["lista_pedidos"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Pedido")];
    }
    public function traer_todos_sin_baja()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE baja = '0'");
        $consulta->execute();
        return ["lista_pedidos"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Pedido")];
    }
    public function traer_uno()
    {
        if(self::existe_numerico_por_igualdad("numero_pedido", $this->numero_pedido) === false)
        {
            return ["Traer Uno Pedido Error"=>"No se pudo hacer, porque no existe el Numero de Pedido."];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE numero_pedido = :numero_pedido");
        $consulta->bindParam(":numero_pedido", $this->numero_pedido);
        $consulta->execute();
        return ["pedido"=>$consulta->fetchObject("Pedido")];
    }
    // #endregion Funcionalidades
}
    

?>