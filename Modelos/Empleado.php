<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "Modelos/Input.php";
require_once "DB/AccesoDatos.php";

class Empleado
{
    public $numero_empleado; // Int - Positivo
    public $nombre; // String - 30 caracteres
    public $apellido; // String - 30 caracteres
    public $dni; // Int - 8 digitos
    public $rol; // bartender | cervezero | cocinero | mozo | socio
    public $baja;

    private const DB_TABLA = "empleados";


    private const NUMERO_EMPLEADO_MSJ_ERROR = "Numero de empleado no valido. Debe ser positivo";
    private const NOMBRE_MSJ_ERROR = "Nombre de empleado no valido. Debe ser solo letras, puede haber espacios y tener menos de 30 caracteres";
    private const APELLIDO_MSJ_ERROR = "Apellido de empleado no valido. Debe ser solo letras, puede haber espacios y tener menos de 30 caracteres";
    private const DNI_MSJ_ERROR = "Dni de empleado no valido. Debe ser de 8 digitos y estar entre 30.000.000 y 99.999.999 inclusive";
    private const ROL_MSJ_ERROR = "Rol de empleado no valido. Debe ser 'bartender', 'cervezero', 'cocinero', 'mozo' o 'socio'";
    private const BAJA_MSJ_ERROR = "Baja de empleado no valido. Debe ser 'true' o 'false'";




    // #region Setters
    public function set_numero_empleado($p_numero_empleado)
    {
        $p_numero_empleado = Input::numerico_es_mayor_igual($p_numero_empleado, 1);

        if($p_numero_empleado === null)
        {
            throw new Exception(self::NUMERO_EMPLEADO_MSJ_ERROR);
        }

        $this->numero_empleado = (int)$p_numero_empleado;
    }
    public function set_nombre($p_nombre)
    {
        $p_nombre = Input::es_alias_con_espacios($p_nombre, 1, 30);

        if($p_nombre === null)
        {
            throw new Exception(self::NOMBRE_MSJ_ERROR);
        }

        $this->nombre = $p_nombre;
    }
    public function set_apellido($p_apellido)
    {
        $p_apellido = Input::es_alias_con_espacios($p_apellido, 1, 30);

        if($p_apellido === null)
        {
            throw new Exception(self::APELLIDO_MSJ_ERROR);
            
        }

        $this->apellido = $p_apellido;
    }
    public function set_dni($p_dni)
    {
        $p_dni = Input::numerico_esta_entre($p_dni, 30000000, 99999999);

        if($p_dni === null)
        {
            throw new Exception(self::DNI_MSJ_ERROR);
        }

        $this->dni = (int)$p_dni;
    }
    public function set_rol($p_rol)
    {
        $p_rol = Input::limpiar($p_rol);
        $p_rol = strtolower($p_rol);
        
        if (strcmp($p_rol, "bartender") != 0 &&
            strcmp($p_rol, "cervezero") != 0 &&
            strcmp($p_rol, "cocinero") != 0 &&
            strcmp($p_rol, "mozo") != 0 &&
            strcmp($p_rol, "socio") != 0)
        {
            throw new Exception(self::ROL_MSJ_ERROR);
        }

        $this->rol = $p_rol;
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
    private static function get($p_numero_empleado)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                               FROM $db_tabla
                                               WHERE numero_empleado = :numero_empleado");
        $consulta->bindParam(":numero_empleado", $p_numero_empleado);
        $consulta->execute();
        return $consulta->fetchObject("Empleado");
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
    public static function crear_id()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT numero_empleado
                                               FROM $db_tabla
                                               ORDER BY numero_empleado DESC
                                               LIMIT 1");
        $consulta->execute();
        $registro = $consulta->fetchObject("Empleado");
        if($registro != false)
        {
            return ($registro->numero_empleado + 1);
        }
            
        return 1;
    }
    // #endregion Utilidades




    // #region Funcionalidades
    public function alta()
    {
        if(self::existe_numerico_por_igualdad("dni", $this->dni) === true)
        {
            return ["Alta Empleado Error"=>"No se pudo hacer, porque ya existe el dni '$this->dni'"];
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
            return ["Alta Empleado Error"=>"No se pudo hacer."];
        }

        return ["Alta Empleado"=>"Realizado."];
    }
    public function baja()
    {
        if(self::existe_numerico_por_igualdad("numero_empleado", $this->numero_empleado) === false)
        {
            return ["Baja Empleado Error"=>"No se pudo hacer, porque no existe el Numero de Empleado."];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("DELETE FROM $db_tabla
                                               WHERE numero_empleado = :numero_empleado");
        $consulta->bindParam(":numero_empleado", $this->numero_empleado);
        $consulta->execute();
        $registros_afectados = $consulta->rowCount();
        switch($registros_afectados)
        {
            case 1:
                return ["Baja Empleado"=>"Realizado."];
            break;

            case 0:
                return ["Baja Empleado Error"=>"No se pudo hacer."];
            break;

            default:
                return ["Baja Empleado Error"=>"Se realizo, pero se eliminaron $registros_afectados registros."];
            break;
        }
    }
    public function baja_logica()
    {
        if(self::existe_numerico_por_igualdad("numero_empleado", $this->numero_empleado) === false)
        {
            return ["Baja Logica Empleado Error"=>"No se pudo hacer, porque no existe el Numero de Empleado."];
        }

        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET baja = '1'
                                                WHERE numero_empleado = :numero_empleado");
        $consulta->bindParam(":numero_empleado", $this->numero_empleado);
        $consulta->execute();
        $registros_afectados = $consulta->rowCount();
        switch($registros_afectados)
        {
            case 1:
                return ["Baja Logica Empleado"=>"Realizado."];
            break;

            case 0:
                return ["Baja Logica Empleado Error"=>"No se pudo hacer."];
            break;

            default:
                return ["Baja Logica Empleado Error"=>"Se realizo, pero se eliminaron logicamnte $registros_afectados registros."];
            break;
        }
    }
    public function modificar()
    {
        if(self::existe_numerico_por_igualdad("numero_empleado", $this->numero_empleado) === false)
        {
            return ["Modificar Empleado Error"=>"No se pudo hacer. Porque no existe el numero de empleado $this->numero_empleado."];
        }

        $empleado = Empleado::get($this->numero_empleado);
        $empleado->baja_logica();

        if(self::existe_alta_numerico_por_igualdad("dni", $this->dni) === true)
        {
            return ["Modificar Empleado Error"=>"No se pudo hacer. Porque ya existe el dni $this->dni."];
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
        $consulta->execute();
        $registros_afectados = $consulta->rowCount();
        switch($registros_afectados)
        {
            case 1:
                return ["Modificar Empleado"=>"Realizado."];
            break;

            case 0:
                return ["Modificar Empleado Error"=>"No se pudo hacer."];
            break;

            default:
                return ["Modificar Empleado Error"=>"Se realizo, pero se modificaron $registros_afectados registros."];
            break;
        }
    }
    public function traer_todos()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT * FROM $db_tabla");
        $consulta->execute();
        return ["lista_empleados"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Empleado")];
    }
    public function traer_todos_sin_baja()
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
            return ["Traer Uno Empleado Por Rol Error"=>"No se pudo hacer, porque no existe el numero de empleado $this->numero_empleado."];
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
            return ["Traer Uno Empleado Por Rol Error"=>"No se pudo hacer, porque no existe el rol $this->rol."];
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
    public function traer_todos_por_rol_sin_baja()
    {
        if(self::existe_cadena_por_igualdad("rol", $this->rol) === false)
        {
            return ["Traer Uno Empleado Por Rol Error"=>"No se pudo hacer, porque no existe el rol $this->rol."];
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
            return ["Traer Uno Empleado Por Rol Error"=>"No se pudo hacer, porque no existe el numero de empleado $this->numero_empleado."];
        }

        $empleado = self::get($this->numero_empleado);

        if(strcmp($this->rol, $empleado->rol) != 0)
        {
            return ["Traer Uno Empleado Por Rol Error"=>"No se pudo hacer, porque no coincide el rol del empleado."];
        }

        return ["empleado"=>$empleado];
    }
    // #endregion Funcionalidades
}

?>