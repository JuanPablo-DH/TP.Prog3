<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "bd/AccesoDatos.php";

class TipoRol
{
    public $id;
    public $nombre;

    private const DB_TABLA = "rol_tipos";

    public static function get($p_id)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE id = :id");
        $consulta->bindParam(":id" , $p_id);
        $consulta->execute();

        $tipo_rol = $consulta->fetchObject("TipoRol");

        if($tipo_rol !== false)
        {
            return $tipo_rol;
        }

        return null;
    }
    public static function get_por_nombre($p_nombre)
    {
        $accesoDatos = AccesoDatos::GetPdo();
        $db_tabla = self::DB_TABLA;
        $consulta = $accesoDatos->GetConsulta("SELECT *
                                                 FROM $db_tabla
                                                WHERE nombre = :nombre");
        $consulta->bindParam(":nombre" , $p_nombre);
        $consulta->execute();

        $tipo_rol = $consulta->fetchObject("TipoRol");

        if($tipo_rol !== false)
        {
            return $tipo_rol;
        }

        return null;
    }
}


?>