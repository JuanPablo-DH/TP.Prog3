<?php

class Movimiento
{
    public $numero_movimiento;
    public $fecha;
    public $dni_empleado;
    public $accion;


    private const DB_TABLA = "movimientos";




    private static function crear_id()
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT numero_movimiento
                                               FROM $db_tabla
                                               ORDER BY numero_movimiento DESC
                                               LIMIT 1");
        $consulta->execute();

        if($consulta->rowCount() > 0)
        {
            $registro = $consulta->fetchObject("Movimiento");
            return ($registro->numero_movimiento + 1);
        }
        
        return 1;
    }




    public static function add($p_dni_empleado, $p_accion)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("INSERT INTO $db_tabla
                                                           (numero_movimiento,
                                                            fecha,
                                                            dni_empleado,
                                                            accion)
                                                    VALUES
                                                           (:numero_movimiento,
                                                            :fecha,
                                                            :dni_empleado,
                                                            :accion)");
        $numero_movimiento = self::crear_id();
        $consulta->bindParam(':numero_movimiento', $numero_movimiento);
        $fecha_formato = (new DateTime("now"))->format('Y-m-d H:i:s');
        $consulta->bindParam(':fecha', $fecha_formato);
        $consulta->bindParam(':dni_empleado', $p_dni_empleado);
        $consulta->bindParam(':accion', $p_accion);
        $consulta->execute();
    }
}

?>