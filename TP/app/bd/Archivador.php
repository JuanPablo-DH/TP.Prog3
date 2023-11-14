<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "modelos/Input.php";

class Archivador
{
    private static function validar_files($p_archivo_files)
    {
        if(isset($p_archivo_files) === true || strcmp($p_archivo_files["name"], "") != 0)
        {
            return $p_archivo_files;
        }

        return null;
    }
    private static function validar_nombre_archivo($p_nombre_archivo)
    {
        $p_nombre_archivo = Input::limpiar($p_nombre_archivo);
        if(isset($p_nombre_archivo) === true || strlen($p_nombre_archivo) > 0)
        {
            return $p_nombre_archivo;
        }

        return null;
    }
    private static function validar_path_directorio($p_path_directorio)
    {
        $p_path_directorio = Input::limpiar($p_path_directorio);
        if(is_dir($p_path_directorio) === true)
        {
            return $p_path_directorio;
        }

        return null;
    }
    private static function validar_path_archivo($p_path_archivo)
    {
        $p_path_archivo = Input::limpiar($p_path_archivo);
        if(file_exists($p_path_archivo) === true)
        {
            return $p_path_archivo;
        }

        return null;
    }

    /**#### Sube un archivo a una ruta especifica.
     * @param $_FILES $p_archivo_files Archivo que se subira.
     * @param string $p_path_directorio Ruta a donde se subira el archivo
     * @return true Si subio el archivo.
     * @return false No subio el archivo.
     * @detalles
     * * 1. Valida que este seteado el $_FILES.
     * * 2. Valida la existencia del directorio.
     * * 4. Sube el archivo al directorio especificado.
     */
    public static function archivo_subir_a_directorio($p_archivo_files, $p_path_directorio)
    {
        $p_archivo_files = self::validar_files($p_archivo_files);
        $p_path_directorio = self::validar_path_directorio($p_path_directorio);

        if($p_archivo_files === null || $p_path_directorio === null)
        {
            return false;
        }

        $destino = $p_path_directorio . $p_archivo_files["name"];
        return move_uploaded_file($p_archivo_files["tmp_name"], $destino);
    }

    /**#### Sube un archivo a una ruta especifica cambiandole el nombre al archivo.
     * @param $_FILES $p_archivo_files Archivo que se subira.
     * @param string $p_path_directorio Ruta a donde se subira el archivo
     * @param string $p_nombre_archivo Nombre nuevo que tendra el archivo.
     * @return true Si subio el archivo.
     * @return false No subio el archivo.
     * @detalles
     * * 1. Valida que este seteado el $_FILES.
     * * 2. Valida la existencia del directorio.
     * * 3. Valida el nombre que tendra el archivo.
     * * 4. Sube el archivo al directorio especificado y con el nombre especificado.
     */
    public static function archivo_subir_a_directorio_modificando_nombre($p_archivo_files, $p_path_directorio, $p_nombre_archivo)
    {
        $p_archivo_files = self::validar_files($p_archivo_files);
        $p_path_directorio = self::validar_path_directorio($p_path_directorio);
        $p_nombre_archivo = self::validar_nombre_archivo($p_nombre_archivo);

        if($p_archivo_files === null || $p_path_directorio === null || $p_nombre_archivo === null)
        {
            return false;
        }

        $filesInfo = pathInfo($p_archivo_files["name"]);
        $nombre_modificado = $p_nombre_archivo . "." . $filesInfo["extension"];
        $destino = $p_path_directorio . $nombre_modificado;
        return move_uploaded_file($p_archivo_files["tmp_name"], $destino);
    }

    /**#### Elimina un archivo de una ruta espefica.
     * @param string $p_path_archivo Ruta del archivo a eliminar.
     * @return true Si lo elimino.
     * @return false No lo elimino.
     * @detalles
     * * 1. Valida la existencia del archivo especificado.
     * * 2. Elimina el archivo.
     */
    public static function archivo_eliminar($p_path_archivo)
    {
        $p_path_archivo = self::validar_path_archivo($p_path_archivo);

        if($p_path_archivo === null)
        {
            return false;
        }

        return unlink($p_path_archivo);
    }

    /**#### Renombra un archivo de una ruta especifica.
     * @param string $p_path_archivo Ruta del archivo a renombrar.
     * @param string $p_nombre_archivo Nuevo nombre del archivo.
     * @return true Si pudo renombrar el archivo.
     * @return false No pudo renombrar el archivo.
     * @detalles
     * * 1. Valida la existencia del archivo especificado.
     * * 2. Valida el nuevo nombre que tendra el archivo.
     * * 3. Renombra el archivo.
     */
    public static function archivo_renombrar($p_path_archivo, $p_nombre_archivo)
    {
        $p_path_archivo = self::validar_path_archivo($p_path_archivo);
        $p_nombre_archivo = self::validar_nombre_archivo($p_nombre_archivo);

        if($p_path_archivo === null || $p_nombre_archivo === null)
        {
            return false;
        }

        return rename($p_path_archivo, $p_nombre_archivo);
    }

    /**#### Mueve un archivo a una ruta especifica.
     * @param string $p_path_archivo Ruta del archivo a mover.
     * @param string $p_path_directorio Ruta del directorio donde se movera el archivo.
     * @return true Si pudo mover el archivo.
     * @return false No pudo mover el archivo.
     * @detalles
     * * 1. Valida la existencia del archivo especificado.
     * * 2. Valida la existencia del directorio.
     * * 3. Mueve el archivo al directorio especificado.
     */
    public static function archivo_mover_a_directorio($p_path_archivo, $p_path_directorio)
    {
        $p_path_archivo = self::validar_path_archivo($p_path_archivo);
        $p_path_directorio = self::validar_path_directorio($p_path_directorio);

        if($p_path_archivo === null || $p_path_directorio === null)
        {
            return false;
        }

        $info = pathInfo($p_path_archivo);
        $destino = $p_path_directorio . $info["basename"];
        return rename($p_path_archivo, $destino);
    }

    /**#### Copia un archivo a una ruta especifica.
     * @param string $p_path_archivo Ruta del archivo a copiar.
     * @param string $p_path_directorio Ruta del directorio donde se copiara el archivo.
     * @return true Si pudo copiar el archivo.
     * @return false No pudo copiar el archivo.
     * @detalles
     * * 1. Valida la existencia del archivo especificado.
     * * 2. Valida la existencia del directorio.
     * * 3. Copia el archivo al directorio especificado.
     */
    public static function archivo_copiar_a_directorio($p_path_archivo, $p_path_directorio)
    {
        $p_path_archivo = self::validar_path_archivo($p_path_archivo);
        $p_path_directorio = self::validar_path_directorio($p_path_directorio);

        if($p_path_archivo === null || $p_path_directorio === null)
        {
            return false;
        }

        $info = pathInfo($p_path_archivo);
        $destino = $p_path_directorio . $info["basename"];
        return copy($p_path_archivo, $destino);
    }
}

?>