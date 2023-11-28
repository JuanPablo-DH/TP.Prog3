<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

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
        $consulta = $accesoDatos->GetConsulta("SELECT id
                                               FROM $db_tabla
                                               ORDER BY id DESC
                                               LIMIT 1");
        $consulta->execute();

        if($consulta->rowCount() > 0)
        {
            $registro = $consulta->fetchObject("Movimiento");
            return ($registro->id + 1);
        }
        
        return 1;
    }

    public static function add($p_dni_empleado, $p_accion)
    {
        
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("INSERT INTO $db_tabla
                                                           (id,
                                                            fecha,
                                                            id_empleado,
                                                            accion)
                                                    VALUES
                                                           (:id,
                                                            :fecha,
                                                            :id_empleado,
                                                            :accion)");
        $id = self::crear_id();
        $consulta->bindParam(':id', $id);
        $fecha_formato = (new DateTime("now"))->format('Y-m-d H:i:s');
        $consulta->bindParam(':fecha', $fecha_formato);
        $consulta->bindParam(':id_empleado', $p_dni_empleado);
        $consulta->bindParam(':accion', $p_accion);
        $consulta->execute();
    }
}

?>