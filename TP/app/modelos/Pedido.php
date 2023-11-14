<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "modelos/Input.php";
require_once "modelos/Movimiento.php";
require_once "bd/AccesoDatos.php";

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


    private const NUMERO_PEDIDO_MSJ_ERROR = ["input_error_pedido"=>"Numero de pedido no valido - Debe ser mayor a 1000 (mil)."];
    private const NUMERO_COMANDA_MSJ_ERROR = ["input_error_pedido"=>"Numero de comanda del pedido no valido - Debe ser mayor a cero."];
    private const TIPO_MSJ_ERROR = ["input_error_pedido"=>"Tipo de pedido no valido - Debe estar 'bebida' o 'comida'."];
    private const NOMBRE_MSJ_ERROR = ["input_error_pedido"=>"Nombre de pedido no valido - Debe ser solo letras y/o con signo '-' (guion), y puede tener hasta 30 caracteres como maximo"];
    private const CANTIDAD_UNIDADES_MSJ_ERROR = ["input_error_pedido"=>"Cantidad de unidades del pedido no valido -  Debe ser mayor a cero"];
    private const PRECIO_UNIDADES_MSJ_ERROR = ["input_error_pedido"=>"Precio de unidades del pedido no valido - Debe ser mayor a cero"];
    private const FECHA_TERMINADO_MSJ_ERROR = ["input_error_pedido"=>"Fecha de terminado del pedido no valido - Los minutos que tarde el pedido en realizarse deben estar entre 1min y 90min inclusive."];
    private const BAJA_MSJ_ERROR = ["input_error_pedido"=>"Estado baja de pedido no valido - Debe ser '1' para [true] o '0' para [false]"];



    
    // #region Validadores
    public static function validar_numero_pedido($p_numero_pedido)
    {
        $p_numero_pedido = Input::numerico_es_mayor_igual($p_numero_pedido, 1000);

        if($p_numero_pedido === null)
        {
            throw new Exception(json_encode(self::NUMERO_PEDIDO_MSJ_ERROR));
        }

        return (int)$p_numero_pedido;
    }
    public static function validar_numero_comanda($p_numero_comanda)
    {
        $p_numero_comanda = Input::numerico_es_mayor_igual($p_numero_comanda, 1);

        if($p_numero_comanda === null)
        {
            throw new Exception(json_encode(self::NUMERO_COMANDA_MSJ_ERROR));
        }

        return (int)$p_numero_comanda;
    }
    public static function validar_tipo($p_tipo)
    {
        $p_tipo = Input::limpiar($p_tipo);
        $p_tipo = strtolower($p_tipo);

        if(strcmp($p_tipo, "bebida") != 0 &&
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
    public static function validar_cantidad_unidades($p_cantidad_unidades)
    {
        $p_cantidad_unidades = Input::numerico_es_mayor_igual($p_cantidad_unidades, 1);

        if($p_cantidad_unidades === null)
        {
            throw new Exception(json_encode(self::CANTIDAD_UNIDADES_MSJ_ERROR));
        }

        return (int)$p_cantidad_unidades;
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
    public static function validar_fecha_terminado($p_minutos)
    {
        $p_minutos = Input::numerico_esta_entre($p_minutos, 1, 90);

        if($p_minutos === null)
        {
            throw new Exception(json_encode(self::FECHA_TERMINADO_MSJ_ERROR));
        }

        return (int)$p_minutos;
    }
    public static function validar_estado($p_estado)
    {
        $p_estado = Input::limpiar($p_estado);
        $p_estado = strtolower($p_estado);

        if(strcmp($p_estado, "pendiente") != 0 &&
           strcmp($p_estado, "en preparacion") != 0 &&
           strcmp($p_estado, "listo para servir") != 0 &&
           strcmp($p_estado, "servido") != 0)
        {
            throw new Exception(json_encode(self::TIPO_MSJ_ERROR));
        }

        return $p_estado;
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
    public function set_numero_pedido($p_numero_pedido, $p_validar)
    {
        if($p_validar)
        {
            $this->numero_pedido = self::validar_numero_pedido($p_numero_pedido);
        }
        else
        {
            $this->numero_pedido = intval(Input::limpiar($p_numero_pedido));
        }
    }
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
    public function set_fecha_registro()
    {
        $this->fecha_registro = new DateTime("now");
    }
    public function set_fecha_terminado($p_minutos, $p_validar)
    {
        if($p_validar)
        {
            $p_minutos = self::validar_fecha_terminado($p_minutos);
        }
        else
        {
            $p_minutos = intval(Input::limpiar($p_minutos));
        }

        $this->fecha_terminado = new DateTime("now");
        $this->fecha_terminado->add(new DateInterval("PT" . $p_minutos . "M"));
    }
    public function set_estado($p_estado, $p_validar)
    {
        if($p_validar)
        {
            $this->estado = self::validar_estado($p_estado);
        }
        else
        {
            $this->estado = strtolower(Input::limpiar($p_estado));
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
    public static function add($p_pedido)
    {
        $p_pedido->numero_pedido = self::crear_id();

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
        $consulta->bindParam(':numero_pedido', $p_pedido->numero_pedido);
        $consulta->bindParam(':numero_comanda', $p_pedido->numero_comanda);
        $consulta->bindParam(':tipo', $p_pedido->tipo);
        $consulta->bindParam(':nombre', $p_pedido->nombre);
        $consulta->bindParam(':cantidad_unidades', $p_pedido->cantidad_unidades);
        $consulta->bindParam(':precio_unidades', $p_pedido->precio_unidades);
        $fecha_registro_formato = $p_pedido->fecha_registro->format("Y-m-d H:i:s");
        $fecha_terminado_formato = $p_pedido->fecha_terminado->format("Y-m-d H:i:s");;
        $consulta->bindParam(':fecha_registro', $fecha_registro_formato);
        $consulta->bindParam(':fecha_terminado', $fecha_terminado_formato);
        $consulta->bindParam(':estado', $p_pedido->estado);
        $consulta->execute();

        return self::existe_numerico_por_igualdad("numero_pedido", $p_pedido->numero_pedido);
    }
    public static function set($p_pedido)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET
                                                      numero_comanda = :numero_comanda,
                                                      tipo = :tipo,
                                                      nombre = :nombre,
                                                      cantidad_unidades = :cantidad_unidades,
                                                      precio_unidades = :precio_unidades,
                                                      fecha_registro = :fecha_registro,
                                                      fecha_terminado = :fecha_terminado,
                                                      estado = :estado,
                                                      baja = :baja
                                                WHERE 
                                                      numero_pedido = :numero_pedido");
        $consulta->bindParam(':numero_pedido', $p_pedido->numero_pedido);
        $consulta->bindParam(':numero_comanda', $p_pedido->numero_comanda);
        $consulta->bindParam(':tipo', $p_pedido->tipo);
        $consulta->bindParam(':nombre', $p_pedido->nombre);
        $consulta->bindParam(':cantidad_unidades', $p_pedido->cantidad_unidades);
        $consulta->bindParam(':precio_unidades', $p_pedido->precio_unidades);

        if($p_pedido->fecha_registro instanceof DateTime)
        {
            $fecha_registro_formato = $p_pedido->fecha_registro->format('Y-m-d H:i:s');
        }
        else
        {
            $fecha_registro_formato = $p_pedido->fecha_registro;
        }
        if($p_pedido->fecha_terminado instanceof DateTime)
        {
            $fecha_terminado_formato = $p_pedido->fecha_terminado->format('Y-m-d H:i:s');
        }
        else
        {
            $fecha_terminado_formato = $p_pedido->fecha_terminado;
        }
        
        $consulta->bindParam(':fecha_registro', $fecha_registro_formato);
        $consulta->bindParam(':fecha_terminado', $fecha_terminado_formato);
        $consulta->bindParam(':estado', $p_pedido->estado);
        $consulta->bindParam(':baja', $p_pedido->baja);
        return $consulta->execute();
    }
    public static function get_alta($p_numero_pedido)
    {
        if(self::existe_numerico_por_igualdad("numero_pedido", $p_numero_pedido) === false)
        {
            return ["traer_un_pedido_error"=>"No se pudo hacer porque no existe el numero de pedido '$p_numero_pedido'"];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE numero_pedido = :numero_pedido
                                                      AND
                                                      baja = '0'");
        $consulta->bindParam(":numero_pedido", $p_numero_pedido);
        $consulta->execute();
        return $consulta->fetchObject("Pedido");
    }
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
        $pedido->nombre = $p_array_asociativo["nombre"];
        $pedido->cantidad_unidades = $p_array_asociativo["cantidad_unidades"];
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
    private static function existe_alta_numerico_por_igualdad($pAtributo, $pValor)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT numero_pedido
                                               FROM $db_tabla
                                               WHERE $pAtributo=:$pAtributo
                                                     AND
                                                     baja = '0'");
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
    public function alta($p_dni_empleado)
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
        $fecha_terminado_formato = $this->fecha_registro->format("Y-m-d H:i:s");
        $consulta->bindParam(':fecha_registro', $fecha_registro_formato);
        $consulta->bindParam(':fecha_terminado', $fecha_terminado_formato);
        $consulta->bindParam(':estado', $this->estado);
        $consulta->execute();

        if(self::existe_numerico_por_igualdad("numero_pedido", $this->numero_pedido) === false)
        {
            ["alta_pedido_error"=>"No se pudo hacer"];
        }

        Movimiento::add($p_dni_empleado, "Realizo el alta del pedido '$this->numero_pedido'");
        return ["alta_pedido"=>"Realizado"];
    }
    public function traer_todos()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT * FROM $db_tabla");
        $consulta->execute();

        return ["lista_pedidos"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Pedido")];
    }
    public function traer_todos_alta()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE baja = '0'");
        $consulta->execute();

        return ["lista_pedidos"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Pedido")];
    }
    public function traer_bebidas_sin_alcohol_alta_pendiente()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE baja = '0'
                                                      AND
                                                      tipo = 'bebida'
                                                      AND
                                                      estado = 'pendiente'");
        $consulta->execute();

        return ["lista_pedidos"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Pedido")];
    }
    public function traer_bebidas_con_alcohol_alta_pendiente()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE baja = '0'
                                                      AND
                                                      tipo = 'bebida-alcohol'
                                                      AND
                                                      estado = 'pendiente'");
        $consulta->execute();

        return ["lista_pedidos"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Pedido")];
    }
    public function traer_comidas_alta_pendiente()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE baja = '0'
                                                      AND
                                                      tipo = 'comida'
                                                      AND
                                                      estado = 'pendiente'");
        $consulta->execute();

        return ["lista_pedidos"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Pedido")];
    }
    public function traer_listos_para_servir_alta()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE baja = '0'
                                                      AND
                                                      estado = 'listo para servir'");
        $consulta->execute();

        return ["lista_pedidos"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Pedido")];
    }
    public function traer_uno()
    {
        if(self::existe_numerico_por_igualdad("numero_pedido", $this->numero_pedido) === false)
        {
            return ["traer_un_pedido_error"=>"No se pudo hacer porque no existe el numero de pedido '$this->numero_pedido'"];
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

    public static function cervezero_elaborar($p_numero_pedido, $p_minutos_elaboracion)
    {
        if(self::existe_alta_numerico_por_igualdad("numero_pedido", $p_numero_pedido) === false)
        {
            return ["cervezero_elaborar_error"=>"No existe el numero de pedido '$p_numero_pedido'"];
        }

        $pedido = self::get_alta($p_numero_pedido);
        if(strcmp($pedido->tipo, "bebida-alcohol") != 0)
        {
            return ["cervezero_elaborar_error"=>"El cevezero solo puede elaborar pedidos de tipo 'bebida-alcohol'"];
        }

        $pedido->set_fecha_terminado($p_minutos_elaboracion, false);
        $pedido->estado = "en preparacion";

        if(self::set($pedido) === false)
        {
            return ["cervezero_elaborar_error"=>"No se pudo hacer"];
        }

        return ["cervezero_elaborar"=>"Realizado"];
    }
    public static function bartender_elaborar($p_numero_pedido, $p_minutos_elaboracion)
    {
        if(self::existe_alta_numerico_por_igualdad("numero_pedido", $p_numero_pedido) === false)
        {
            return ["bartender_elaborar_error"=>"No existe el numero de pedido '$p_numero_pedido'"];
        }

        $pedido = self::get_alta($p_numero_pedido);
        if(strcmp($pedido->tipo, "bebida") != 0)
        {
            return ["bartender_elaborar_error"=>"El bartender solo puede elaborar pedidos de tipo 'bebida'"];
        }

        $pedido->set_fecha_terminado($p_minutos_elaboracion, false);
        $pedido->estado = "en preparacion";

        if(self::set($pedido) === false)
        {
            return ["bartender_elaborar_error"=>"No se pudo hacer"];
        }

        return ["bartender_elaborar"=>"Realizado"];
    }
    public static function cocinero_elaborar($p_numero_pedido, $p_minutos_elaboracion)
    {
        if(self::existe_alta_numerico_por_igualdad("numero_pedido", $p_numero_pedido) === false)
        {
            return ["cocinero_elaborar_error"=>"No existe el numero de pedido '$p_numero_pedido'"];
        }

        $pedido = self::get_alta($p_numero_pedido);
        if(strcmp($pedido->tipo, "comida") != 0)
        {
            return ["cocinero_elaborar_error"=>"El cocinero solo puede elaborar pedidos de tipo 'comida'"];
        }

        $pedido->set_fecha_terminado($p_minutos_elaboracion, false);
        $pedido->estado = "en preparacion";

        if(self::set($pedido) === false)
        {
            return ["cocinero_elaborar_error"=>"No se pudo hacer"];
        }

        return ["cocineror_elabora"=>"Realizado"];
    }
    
    public static function cervezero_terminar($p_numero_pedido)
    {
        if(self::existe_alta_numerico_por_igualdad("numero_pedido", $p_numero_pedido) === false)
        {
            return ["cervezero_terminar_error"=>"No existe el numero de pedido '$p_numero_pedido'"];
        }

        $pedido = self::get_alta($p_numero_pedido);
        if(strcmp($pedido->tipo, "bebida-alcohol") != 0)
        {
            return ["cervezero_terminar_error"=>"El cevezero solo puede terminar pedidos de tipo 'bebida-alcohol'"];
        }

        $pedido->estado = "listo para servir";

        if(self::set($pedido) === false)
        {
            return ["cervezero_terminar_error"=>"No se pudo hacer"];
        }

        return ["cervezero_terminar"=>"Realizado"];
    }
    public static function bartender_terminar($p_numero_pedido)
    {
        if(self::existe_alta_numerico_por_igualdad("numero_pedido", $p_numero_pedido) === false)
        {
            return ["bartender_terminar_error"=>"No existe el numero de pedido '$p_numero_pedido'"];
        }

        $pedido = self::get_alta($p_numero_pedido);
        if(strcmp($pedido->tipo, "bebida") != 0)
        {
            return ["bartender_terminar_error"=>"El bartender solo puede terminar pedidos de tipo 'bebida'"];
        }

        $pedido->estado = "listo para servir";

        if(self::set($pedido) === false)
        {
            return ["bartender_terminar_error"=>"No se pudo hacer"];
        }

        return ["bartender_terminar"=>"Realizado"];
    }
    public static function cocinero_terminar($p_numero_pedido)
    {
        if(self::existe_alta_numerico_por_igualdad("numero_pedido", $p_numero_pedido) === false)
        {
            return ["cocinero_terminar_error"=>"No existe el numero de pedido '$p_numero_pedido'"];
        }

        $pedido = self::get_alta($p_numero_pedido);
        if(strcmp($pedido->tipo, "comida") != 0)
        {
            return ["cocinero_terminar_error"=>"El cocinero solo puede terminar pedidos de tipo 'comida'"];
        }

        $pedido->estado = "listo para servir";

        if(self::set($pedido) === false)
        {
            return ["cocinero_terminar_error"=>"No se pudo hacer"];
        }

        return ["cocinero_terminar"=>"Realizado"];
    }

    public static function mozo_servir($p_numero_pedido)
    {
        if(self::existe_alta_numerico_por_igualdad("numero_pedido", $p_numero_pedido) === false)
        {
            return ["mozo_servir_error"=>"No existe el numero de pedido '$p_numero_pedido'"];
        }

        $pedido = self::get_alta($p_numero_pedido);
        if(strcmp($pedido->estado, "listo para servir") != 0)
        {
            return ["mozo_servir_error"=>"El mozo solo puede servir pedidos que esten en el estado 'listo para servir'"];
        }

        $pedido->estado = "servido";

        if(self::set($pedido) === false)
        {
            return ["mozo_servir_error"=>"No se pudo hacer"];
        }

        $comanda = Comanda::get_alta($pedido->numero_comanda);
        $comanda->lista_pedidos = Pedido::get_por_numero_comanda($comanda->numero_comanda);
        $mesa = Mesa::get($comanda->numero_mesa);
        
        if($comanda->verificar_si_todos_los_pedidos_estan_servidos() === true)
        {
            $mesa->estado = "con clientes comiendo";
            Mesa::set($mesa);
        }

        return ["mozo_servir"=>"Realizado"];
    }
}
    

?>