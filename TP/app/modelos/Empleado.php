<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "modelos/Input.php";
require_once "modelos/Movimiento.php";
require_once "bd/AccesoDatos.php";

class Empleado
{
    public $numero_empleado; // Int - Positivo
    public $nombre; // String - 30 caracteres
    public $apellido; // String - 30 caracteres
    public $dni; // Int - 8 digitos
    public $rol; // bartender | cervezero | cocinero | mozo | socio
    public $baja;

    private const DB_TABLA = "empleados";


    private const NUMERO_EMPLEADO_MSJ_ERROR = ["input_error_empleado"=>"Numero de empleado no valido - Debe ser positivo"];
    private const NOMBRE_MSJ_ERROR = ["input_error_empleado"=>"Nombre de empleado no valido - Debe ser solo letras, puede haber espacios y tener menos de 30 caracteres"];
    private const APELLIDO_MSJ_ERROR = ["input_error_empleado"=>"Apellido de empleado no valido - Debe ser solo letras, puede haber espacios y tener menos de 30 caracteres"];
    private const DNI_MSJ_ERROR = ["input_error_empleado"=>"Dni de empleado no valido - Debe ser de 8 digitos y estar entre 30.000.000 y 99.999.999 inclusive"];
    private const ROL_MSJ_ERROR = ["input_error_empleado"=>"Rol de empleado no valido - Debe ser 'bartender', 'cervezero', 'cocinero', 'mozo' o 'socio'"];
    private const BAJA_MSJ_ERROR = ["input_error_empleado"=>"Estado baja de empleado no valido - Debe ser '1' para [true] o '0' para [false]"];




    // #region Validadores
    public static function validar_numero_empleado($p_numero_empleado)
    {
        $p_numero_empleado = Input::numerico_es_mayor_igual($p_numero_empleado, 1);

        if($p_numero_empleado === null)
        {
            throw new Exception(json_encode(self::NUMERO_EMPLEADO_MSJ_ERROR));
        }

        return (int)$p_numero_empleado;
    }
    public static function validar_nombre($p_nombre)
    {
        $p_nombre = Input::es_alias_con_espacios($p_nombre, 1, 30);

        if($p_nombre === null)
        {
            throw new Exception(json_encode(self::NOMBRE_MSJ_ERROR));
        }

        return $p_nombre;
    }
    public static function validar_apellido($p_apellido)
    {
        $p_apellido = Input::es_alias_con_espacios($p_apellido, 1, 30);

        if($p_apellido === null)
        {
            throw new Exception(json_encode(self::APELLIDO_MSJ_ERROR));
        }

        return $p_apellido;
    }
    public static function validar_dni($p_dni)
    {
        $p_dni = Input::numerico_esta_entre($p_dni, 30000000, 99999999);

        if($p_dni === null)
        {
            throw new Exception(json_encode(self::DNI_MSJ_ERROR));
        }

        return (int)$p_dni;
    }
    public static function validar_rol($p_rol)
    {
        $p_rol = Input::limpiar($p_rol);
        $p_rol = strtolower($p_rol);
        
        if (strcmp($p_rol, "bartender") != 0 &&
            strcmp($p_rol, "cervezero") != 0 &&
            strcmp($p_rol, "cocinero") != 0 &&
            strcmp($p_rol, "mozo") != 0 &&
            strcmp($p_rol, "socio") != 0)
        {
            throw new Exception(json_encode(self::ROL_MSJ_ERROR));
        }

        return $p_rol;
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
    public function set_numero_empleado($p_numero_empleado, $p_validar)
    {
        if($p_validar)
        {
            $this->numero_empleado = self::validar_numero_empleado($p_numero_empleado);
        }
        else
        {
            $this->numero_empleado = intval(Input::limpiar($p_numero_empleado));
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
    public function set_apellido($p_apellido, $p_validar)
    {
        if($p_validar)
        {
            $this->apellido = self::validar_apellido($p_apellido);
        }
        else
        {
            $this->apellido = strtolower(Input::limpiar($p_apellido));
        }
    }
    public function set_dni($p_dni, $p_validar)
    {
        if($p_validar)
        {
            $this->dni = self::validar_dni($p_dni);
        }
        else
        {
            $this->dni = intval(Input::limpiar($p_dni));
        }
    }
    public function set_rol($p_rol, $p_validar)
    {
        if($p_validar)
        {
            $this->rol = self::validar_rol($p_rol);
        }
        else
        {
            $this->rol = strtolower(Input::limpiar($p_rol));
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
    private static function get($p_numero_empleado)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                               FROM $db_tabla
                                               WHERE numero_empleado = :numero_empleado");
        $consulta->bindParam(":numero_empleado", $p_numero_empleado);
        $consulta->execute();

        if($consulta->rowCount() === 1)
        {
            return $consulta->fetchObject("Empleado");
        }

        return null;
    }
    public static function get_alta($p_numero_empleado)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                               FROM $db_tabla
                                               WHERE numero_empleado = :numero_empleado
                                                 AND baja = '0'");
        $consulta->bindParam(":numero_empleado", $p_numero_empleado);
        $consulta->execute();

        if($consulta->rowCount() === 1)
        {
            return $consulta->fetchObject("Empleado");
        }

        return null;
    }

    public static function get_por_dni($p_dni)
    {
        $p_dni = intval(Input::limpiar($p_dni));

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE dni = :dni");
        $consulta->bindParam(":dni" , $p_dni);
        $consulta->execute();

        if($consulta->rowCount() === 1)
        {
            return $consulta->fetchObject("Empleado");
        }

        return null;
    }
    public static function get_alta_por_dni($p_dni)
    {
        $p_dni = intval(Input::limpiar($p_dni));

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE dni = :dni
                                                  AND baja = '0'");
        $consulta->bindParam(":dni" , $p_dni);
        $consulta->execute();

        if($consulta->rowCount() === 1)
        {
            return $consulta->fetchObject("Empleado");
        }

        return null;
    }

    public static function get_por_dni_y_nombre($p_dni, $p_nombre)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE dni = :dni
                                                  AND BINARY nombre = :nombre");
        $consulta->bindParam(":dni" , $p_dni);
        $consulta->bindParam(":nombre" , $p_nombre);
        $consulta->execute();

        if($consulta->rowCount() === 1)
        {
            return $consulta->fetchObject("Empleado");
        }

        return null;
    }
    public static function get_alta_por_dni_y_nombre($p_dni, $p_nombre)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE dni = :dni
                                                  AND BINARY nombre = :nombre
                                                  AND baja = '0'");
        $consulta->bindParam(":dni" , $p_dni);
        $consulta->bindParam(":nombre" , $p_nombre);
        $consulta->execute();

        if($consulta->rowCount() === 1)
        {
            return $consulta->fetchObject("Empleado");
        }

        return null;
    }

    private static function existe_cadena_por_igualdad($pAtributo, $pValor)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT numero_empleado
                                                 FROM $db_tabla
                                                WHERE BINARY $pAtributo=:$pAtributo");
        $consulta->bindParam(":$pAtributo" , $pValor);
        $consulta->execute();

        if($consulta->rowCount() > 0)
        {
            return true;
        }

        return false;
    }
    private static function existe_numerico_por_igualdad($pAtributo, $pValor)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT numero_empleado
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

    private static function existe_alta_cadena_por_igualdad($pAtributo, $pValor)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT numero_empleado
                                                 FROM $db_tabla
                                                WHERE baja = '0'
                                                      AND
                                                      BINARY $pAtributo=:$pAtributo");
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
        $consulta = $accesoDatos->GetConsulta("SELECT numero_empleado
                                               FROM $db_tabla
                                               WHERE baja = '0'
                                                     AND
                                                     $pAtributo=:$pAtributo");
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
        $consulta = $accesoDatos->GetConsulta("SELECT numero_empleado
                                               FROM $db_tabla
                                               ORDER BY numero_empleado DESC
                                               LIMIT 1");
        $consulta->execute();

        if($consulta->rowCount() > 0)
        {
            $registro = $consulta->fetchObject("Empleado");
            return ($registro->numero_empleado + 1);
        }
            
        return 1;
    }
    // #endregion Utilidades




    // #region Funcionalidades
    public function alta($p_dni_empleado)
    {
        if(self::existe_numerico_por_igualdad("dni", $this->dni) === true)
        {
            return ["alta_empleado_error"=>"No se pudo hacer porque ya existe el dni '$this->dni'"];
        }

        $this->numero_empleado = self::crear_id();
        
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("INSERT INTO $db_tabla
                                               (numero_empleado,
                                                nombre,
                                                apellido,
                                                dni,
                                                rol,
                                                baja)
                                               VALUES
                                               (:numero_empleado,
                                                :nombre,
                                                :apellido,
                                                :dni,
                                                :rol,
                                                '0')");
        $consulta->bindParam(':numero_empleado', $this->numero_empleado);
        $consulta->bindParam(':nombre', $this->nombre);
        $consulta->bindParam(':apellido', $this->apellido);
        $consulta->bindParam(':dni', $this->dni);
        $consulta->bindParam(':rol', $this->rol);
        $consulta->execute();

        if(self::existe_numerico_por_igualdad("numero_empleado", $this->numero_empleado) === false)
        {
            return ["alta_empleado_error"=>"No se pudo hacer"];
        }

        Movimiento::add($p_dni_empleado, "Realizo el alta del empleado '$this->numero_empleado'");
        return ["alta_empleado"=>"Realizado"];
    }
    public function baja($p_dni_empleado)
    {
        if(self::existe_numerico_por_igualdad("numero_empleado", $this->numero_empleado) === false)
        {
            return ["baja_empleado_error"=>"No se pudo hacer porque no existe el numero de empleado '$this->numero_empleado'"];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("DELETE FROM $db_tabla
                                                     WHERE numero_empleado = :numero_empleado");
        $consulta->bindParam(":numero_empleado", $this->numero_empleado);
        $registros_afectados = $consulta->rowCount();
        switch($registros_afectados)
        {
            case 1:
                Movimiento::add($p_dni_empleado, "Realizo la baja del empleado '$this->numero_empleado'");
                return ["baja_empleado"=>"Realizado"];
            break;

            case 0:
                return ["baja_empleado_error"=>"No se pudo hacer"];
            break;

            default:
                return ["baja_empleado_error"=>"Se realizo, pero se eliminaron $registros_afectados registros"];
            break;
        }
    }
    public function baja_logica($p_dni_empleado)
    {
        if(self::existe_numerico_por_igualdad("numero_empleado", $this->numero_empleado) === false)
        {
            return ["baja_logica_empleado_error"=>"No se pudo hacer porque no existe el numero de empleado '$this->numero_empleado'"];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET baja = '1'
                                                WHERE numero_empleado = :numero_empleado");
        $consulta->bindParam(":numero_empleado", $this->numero_empleado);
        if($consulta->execute() === false)
        {
            return ["baja_logica_empleado_error"=>"No se pudo hacer"];
        }
        
        Movimiento::add($p_dni_empleado, "Realizo la baja logica del empleado '$this->numero_empleado'");
        return ["baja_logica_empleado"=>"Realizado"];
    }
    public function modificar($p_dni_empleado)
    {
        if(self::existe_numerico_por_igualdad("numero_empleado", $this->numero_empleado) === false)
        {
            return ["modificar_empleado_error"=>"No se pudo hacer. Porque no existe el numero de empleado '$this->numero_empleado'"];
        }

        $this->baja_logica($p_dni_empleado);

        if(self::existe_alta_numerico_por_igualdad("dni", $this->dni) === true)
        {
            return ["modificar_empleado_error"=>"No se pudo hacer. Porque ya existe el dni '$this->dni'"];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET nombre = :nombre,
                                                      apellido = :apellido,
                                                      dni = :dni,
                                                      rol = :rol,
                                                      baja = :baja
                                                WHERE numero_empleado = :numero_empleado");
        $consulta->bindParam(':numero_empleado', $this->numero_empleado);
        $consulta->bindParam(':nombre', $this->nombre);
        $consulta->bindParam(':apellido', $this->apellido);
        $consulta->bindParam(':dni', $this->dni);
        $consulta->bindParam(':rol', $this->rol);
        $consulta->bindParam(':baja', $this->baja);
        if($consulta->execute() === false)
        {
            return ["modificar_empleado_error"=>"No se pudo hacer"];
        }
        
        Movimiento::add($p_dni_empleado, "Realizo la modificacion del empleado '$this->numero_empleado'");
        return ["modificar_empleado"=>"Realizado"];
    }

    public function traer_todos()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT * FROM $db_tabla");
        $consulta->execute();

        return ["lista_empleados"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Empleado")];
    }
    public function traer_todos_alta()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE baja = '0'");
        $consulta->execute();

        return ["lista_empleados"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Empleado")];
    }
    public function traer_uno()
    {
        if(self::existe_numerico_por_igualdad("numero_empleado", $this->numero_empleado) === false)
        {
            return ["traer_un_empleado_error"=>"No se pudo hacer porque no existe el numero de empleado '$this->numero_empleado'"];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                               FROM $db_tabla
                                               WHERE numero_empleado = :numero_empleado");
        $consulta->bindParam(":numero_empleado", $this->numero_empleado);
        $consulta->execute();

        return ["empleado"=>$consulta->fetchObject("Empleado")];
    }

    public function traer_todos_por_rol()
    {
        if(self::existe_cadena_por_igualdad("rol", $this->rol) === false)
        {
            return ["traer_todos_empleados_por_rol_error"=>"No se pudo hacer porque no existe el rol '$this->rol'"];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("      SELECT *
                                                       FROM $db_tabla
                                               WHERE BINARY rol = :rol");
        $consulta->bindParam(":rol", $this->rol);
        $consulta->execute();

        return ["lista_empleados"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Empleado")];
    }
    public function traer_todos_por_rol_alta()
    {
        if(self::existe_alta_cadena_por_igualdad("rol", $this->rol) === false)
        {
            return ["traer_un_empleado_por_rol_error"=>"No se pudo hacer porque no existe el rol '$this->rol'"];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE baja = '0'
                                                      AND
                                                      BINARY rol = :rol");
        $consulta->bindParam(":rol", $this->rol);
        $consulta->execute();

        return ["lista_empleados"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Empleado")];
    }
    
    public function traer_uno_por_rol()
    {
        if(self::existe_numerico_por_igualdad("numero_empleado", $this->numero_empleado) === false)
        {
            return ["traer_un_empleado_por_rol_error"=>"No se pudo hace, porque no existe el numero de empleado '$this->numero_empleado'"];
        }

        $empleado = self::get($this->numero_empleado);

        if(strcmp($this->rol, $empleado->rol) != 0)
        {
            return ["traer_un_empleado_por_rol_error"=>"No se pudo hacer porque no coincide el rol del empleado."];
        }

        return ["empleado"=>$empleado];
    }
    // #endregion Funcionalidades
}

?>