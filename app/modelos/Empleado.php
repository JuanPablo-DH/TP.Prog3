<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "bd/AccesoDatos.php";
require_once "utils/Input.php";
require_once "utils/TipoRol.php";
require_once "utils/Movimiento.php";

class Empleado
{
    public $id;
    public $mail;
    public $contrasenia;
    public $nombre;
    public $apellido;
    public $dni;
    public $rol;
    public $activo;
    public $fecha_alta;
    public $fecha_modificado;
    public $baja;

    private const DB_TABLA = "empleados";




    // #region Validadores
    public static function validar_id($p_id)
    {
        $p_id = Input::numerico_es_mayor_igual($p_id, 1);

        if($p_id === null)
        {
            throw new Exception(json_encode(["error_input_empleado"=>"Id no valido - Debe ser positivo"]));
        }

        return (int)$p_id;
    }
    public static function validar_mail($p_mail)
    {
        $p_mail = Input::es_mail($p_mail);

        if($p_mail === null)
        {
            throw new Exception(json_encode(["error_input_empleado"=>"Mail de empleado no valido - Debe cumplir con este formato '[alfanumerico/guion/guiones]@[alfanumerico/guion/guiones].[alfanumerico/guion/guiones]'"]));
        }

        return $p_mail;
    }
    public static function validar_contrasenia($p_contrasenia)
    {
        $p_contrasenia = Input::cadena_longitud($p_contrasenia, 4, 100);

        if($p_contrasenia === null)
        {
            throw new Exception(json_encode(["error_input_empleado"=>"Contraseña de empleado no valida - Debe tener por lo menos 4 caracteres como minimo y 100 caracteres como maximo"]));
        }

        return $p_contrasenia;
    }
    public static function validar_nombre($p_nombre)
    {
        $p_nombre = Input::es_alias_con_espacios($p_nombre, 1, 30);

        if($p_nombre === null)
        {
            throw new Exception(json_encode(["error_input_empleado"=>"Nombre de empleado no valido - Debe ser solo letras, puede haber espacios y tener menos de 30 caracteres"]));
        }

        return $p_nombre;
    }
    public static function validar_apellido($p_apellido)
    {
        $p_apellido = Input::es_alias_con_espacios($p_apellido, 1, 30);

        if($p_apellido === null)
        {
            throw new Exception(json_encode(["error_input_empleado"=>"Apellido de empleado no valido - Debe ser solo letras, puede haber espacios y tener menos de 30 caracteres"]));
        }

        return $p_apellido;
    }
    public static function validar_dni($p_dni)
    {
        $p_dni = Input::numerico_esta_entre($p_dni, 30000000, 99999999);
        if($p_dni === null)
        {
            throw new Exception(json_encode(["error_input_empleado"=>"Dni de empleado no valido - Debe ser de 8 digitos y estar entre 30.000.000 y 99.999.999 inclusive"]));
        }

        return (int)$p_dni;
    }
    public static function validar_rol($p_rol)
    {
        $p_rol = strtoupper(Input::limpiar($p_rol));

        $tipo_rol = TipoRol::get_por_nombre($p_rol);
        if($tipo_rol === null)
        {
            throw new Exception(json_encode(["error_input_empleado"=>"Rol de empleado no valido - No existe el tipo de rol '$p_rol'"]));
        }

        return $tipo_rol->nombre;
    }
    public static function validar_activo($p_activo)
    {
        $p_activo = Input::convertir_a_booleano($p_activo);

        if($p_activo === null)
        {
            throw new Exception(json_encode(["error_input_empleado"=>"Estado activo de empleado no valido - Debe ser '1' para [true] o '0' para [false]"]));
        }

        return $p_activo;
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
    public function set_mail($p_mail, $p_validar)
    {
        if($p_validar)
        {
            $this->mail = self::validar_mail($p_mail);
        }
        else
        {
            $this->mail = strtolower(Input::limpiar($p_mail));
        }
    }
    public function set_contrasenia($p_contrasenia, $p_validar)
    {
        if($p_validar)
        {
            $this->contrasenia = self::validar_contrasenia($p_contrasenia);
        }
        else
        {
            $this->contrasenia = strval($p_contrasenia);
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
            $this->rol = strtoupper(Input::limpiar($p_rol));
        }
    }
    public function set_activo($p_activo, $p_validar)
    {
        if($p_validar)
        {
            $this->activo = self::validar_activo($p_activo);
        }
        else
        {
            $this->activo = boolval(Input::limpiar($p_activo));
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
    private static function add($p_empleado, $p_crear_id, $p_asignar_fecha_alta)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("INSERT INTO $db_tabla
                                               (id,
                                                mail,
                                                contrasenia,
                                                nombre,
                                                apellido,
                                                dni,
                                                id_rol,
                                                activo,
                                                fecha_alta,
                                                baja)
                                               VALUES
                                               (:id,
                                                :mail,
                                                :contrasenia,
                                                :nombre,
                                                :apellido,
                                                :dni,
                                                :id_rol,
                                                :activo,
                                                :fecha_alta,
                                                '0')");
        if($p_crear_id)
        {
            $p_empleado->id = self::crear_id();
        }
        $consulta->bindParam(':id', $p_empleado->id);
        $consulta->bindParam(':mail', $p_empleado->mail);
        $hash_contrasenia = password_hash($p_empleado->contrasenia, PASSWORD_DEFAULT);
        $consulta->bindParam(':contrasenia', $hash_contrasenia);
        $consulta->bindParam(':nombre', $p_empleado->nombre);
        $consulta->bindParam(':apellido', $p_empleado->apellido);
        $consulta->bindParam(':dni', $p_empleado->dni);
        $id_rol = TipoRol::get_por_nombre($p_empleado->rol)->id;
        $consulta->bindParam(':id_rol', $id_rol);
        $consulta->bindParam(':activo', $p_empleado->activo);
        if($p_asignar_fecha_alta)
        {
            $p_empleado->set_fecha_alta();
        }
        $fecha_alta_formato = $p_empleado->fecha_alta->format('Y-m-d H:i:s');
        $consulta->bindParam(':fecha_alta', $fecha_alta_formato);
        $consulta->execute();

        return (self::get($p_empleado->id) !== null);
    }
    private static function set($p_empleado)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET mail = :mail,
                                                      contrasenia = :contrasenia,
                                                      nombre = :nombre,
                                                      apellido = :apellido,
                                                      dni = :dni,
                                                      id_rol = :id_rol,
                                                      activo = :activo,
                                                      fecha_modificado = :fecha_modificado,
                                                      baja = :baja
                                                WHERE id = :id");
        $consulta->bindParam(':id', $p_empleado->id);
        $consulta->bindParam(':mail', $p_empleado->mail);
        $hash_contrasenia = password_hash($p_empleado->contrasenia, PASSWORD_DEFAULT);
        $consulta->bindParam(':contrasenia', $hash_contrasenia);
        $consulta->bindParam(':nombre', $p_empleado->nombre);
        $consulta->bindParam(':apellido', $p_empleado->apellido);
        $consulta->bindParam(':dni', $p_empleado->dni);
        $id_rol = TipoRol::get_por_nombre($p_empleado->rol)->id;
        $consulta->bindParam(':id_rol', $id_rol);
        $consulta->bindParam(':activo', $p_empleado->activo);
        $p_empleado->set_fecha_modificado();
        $fecha_modificado_formato = $p_empleado->fecha_modificado->format('Y-m-d H:i:s');
        $consulta->bindParam(':fecha_modificado', $fecha_modificado_formato);
        $consulta->bindParam(':baja', $p_empleado->baja);

        return $consulta->execute();
    }
    private static function del($p_empleado)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("DELETE FROM $db_tabla
                                                     WHERE id = :id");
        $consulta->bindParam(":id", $p_empleado->id);
        return $consulta->rowCount();
    }
    private static function del_log($p_empleado)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("UPDATE $db_tabla
                                                  SET baja = '1'
                                                WHERE id = :id");
        $consulta->bindParam(":id", $p_empleado->id);

        return $consulta->execute();
    }
    private static function get($p_numero_empleado)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      mail,
                                                      contrasenia,
                                                      nombre,
                                                      apellido,
                                                      dni,
                                                      (SELECT rol_tipos.nombre FROM rol_tipos WHERE rol_tipos.id = empleados.id_rol) AS rol,
                                                      activo,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla
                                                WHERE empleados.id = :id");
        $consulta->bindParam(":id", $p_numero_empleado);
        $consulta->execute();

        $registro = $consulta->fetchObject("Empleado");

        if($registro !== false)
        {
            return $registro;
        }

        return null;
    }
    public static function get_alta($p_numero_empleado)
    {
        $empleado = self::get($p_numero_empleado);

        if($empleado !== null && $empleado->baja === 0)
        {
            return $empleado;
        }

        return null;
    }
    public static function get_all()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      mail,
                                                      contrasenia,
                                                      nombre,
                                                      apellido,
                                                      dni,
                                                      (SELECT rol_tipos.nombre FROM rol_tipos WHERE rol_tipos.id = empleados.id_rol) AS rol,
                                                      activo,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "Empleado");
    }
    public static function get_all_alta()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      mail,
                                                      contrasenia,
                                                      nombre,
                                                      apellido,
                                                      dni,
                                                      (SELECT rol_tipos.nombre FROM rol_tipos WHERE rol_tipos.id = empleados.id_rol) AS rol,
                                                      activo,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla
                                                WHERE baja = '0'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "Empleado");
    }
    private static function crear_id()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                               FROM $db_tabla
                                               ORDER BY id DESC
                                               LIMIT 1");
        $consulta->execute();

        if($consulta->rowCount() > 0)
        {
            $registro = $consulta->fetchObject("Empleado");
            return ($registro->id + 1);
        }
            
        return 1;
    }
    // #endregion Utilidades




    // #region Funcionalidades
    public function alta($p_id_empleado)
    {
        if(self::add($this, true, true) === false)
        {
            return ["error_alta_empleado"=>"No se pudo hacer"];
        }

        Movimiento::add($p_id_empleado, "Realizo el alta del empleado '$this->id'");
        return ["alta_empleado"=>"Realizado"];
    }
    public function baja($p_id_empleado)
    {
        $empleado = self::get_alta($this->id);
        if($empleado === null)
        {
            return ["error_baja_empleado"=>"No existe el empleado '$this->id'"];
        }

        $registros_afectados = self::del($this);
        switch($registros_afectados)
        {
            case 1:
                Movimiento::add($p_id_empleado, "Realizo la baja del empleado '$this->id'");
                return ["error_baja_empleado"=>"Realizado"];
            break;

            case 0:
                return ["error_baja_empleado_error"=>"No se pudo hacer"];
            break;

            default:
                return ["error_baja_empleado_error"=>"Se realizo, pero se eliminaron $registros_afectados registros"];
            break;
        }
    }
    public function baja_logica($p_id_empleado)
    {
        $empleado = self::get_alta($this->id);
        if($empleado === null)
        {
            return ["error_baja_logica_empleado"=>"No existe el empleado '$this->id'"];
        }

        if(self::del_log($this) === false)
        {
            return ["error_baja_logica_empleado"=>"No se pudo hacer"];
        }
        
        Movimiento::add($p_id_empleado, "Realizo la baja logica del empleado '$this->id'");
        return ["baja_logica_empleado"=>"Realizado"];
    }
    public function modificar($p_id_empleado)
    {
        $empleado = self::get_alta($this->id);
        if($empleado === null)
        {
            return ["error_modificar_empleado"=>"No existe el empleado '$this->id'"];
        }

        if(self::set($this) === false)
        {
            return ["error_modificar_empleado"=>"No se pudo hacer"];
        }
        
        Movimiento::add($p_id_empleado, "Realizo la modificacion del empleado '$this->id'");
        return ["modificar_empleado"=>"Realizado", "empleado_antes"=>$empleado, "empleado_despues"=>$this];
    }
    public function traer_todos()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      mail,
                                                      contrasenia,
                                                      nombre,
                                                      apellido,
                                                      dni,
                                                      (SELECT rol_tipos.nombre FROM rol_tipos WHERE rol_tipos.id = empleados.id_rol) AS rol,
                                                      activo,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla");
        $consulta->execute();

        return ["lista_empleados"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Empleado")];
    }
    public function traer_todos_alta()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      mail,
                                                      contrasenia,
                                                      nombre,
                                                      apellido,
                                                      dni,
                                                      (SELECT rol_tipos.nombre FROM rol_tipos WHERE rol_tipos.id = empleados.id_rol) AS rol,
                                                      activo,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla
                                                WHERE baja = '0'");
        $consulta->execute();

        return ["lista_empleados"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Empleado")];
    }
    public function traer_uno()
    {
        $empleado = self::get($this->id);
        if($empleado === null)
        {
            return ["error_traer_un_empleado"=>"No existe el empleado '$this->id'"];
        }
        
        return ["empleado"=>$empleado];
    }
    public function traer_uno_por_rol()
    {
        $empleado = self::get($this->id);
        if($empleado === null)
        {
            return ["error_traer_un_empleado_por_rol"=>"No existe el empleado '$this->id'"];
        }
        if(strcmp($empleado->rol, $this->rol) != 0)
        {
            return ["error_traer_un_empleado_por_rol"=>"No coincide el rol del empleado."];
        }

        return ["empleado"=>$empleado];
    }
    public function traer_todos_por_rol()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      mail,
                                                      contrasenia,
                                                      nombre,
                                                      apellido,
                                                      dni,
                                                      (SELECT rol_tipos.nombre FROM rol_tipos WHERE rol_tipos.id = empleados.id_rol) AS rol,
                                                      activo,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla
                                                WHERE empleados.id_rol = :id_rol");
        $id_rol = TipoRol::get_por_nombre($this->rol)->id;
        $consulta->bindParam(':id_rol', $id_rol);
        $consulta->execute();

        return ["lista_empleados"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Empleado")];
    }
    public function traer_todos_por_rol_alta()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT id,
                                                      mail,
                                                      contrasenia,
                                                      nombre,
                                                      apellido,
                                                      dni,
                                                      (SELECT rol_tipos.nombre FROM rol_tipos WHERE rol_tipos.id = empleados.id_rol) as rol,
                                                      activo,
                                                      fecha_alta,
                                                      fecha_modificado,
                                                      baja
                                                 FROM $db_tabla
                                                WHERE empleados.id_rol = :id_rol
                                                      AND
                                                      empleados.baja = '0'");
        $id_rol = TipoRol::get_por_nombre($this->rol)->id;
        $consulta->bindParam(':id_rol', $id_rol);
        $consulta->execute();

        return ["lista_empleados"=>$consulta->fetchAll(PDO::FETCH_CLASS, "Empleado")];
    }
    // #endregion Funcionalidades
}

?>