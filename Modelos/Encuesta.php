<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "Modelos/Input.php";
require_once "DB/AccesoDatos.php";

class Encuesta
{
    public $numero_encuesta; // Numerico Unico positivo
    public $puntuacion_mesa; // 1 al 10 (x < 5 MALA; x = 5 REGULAR; x > 5 && x < 8 BUENA; x > 7 EXCELENTE)
    public $restaurante_mesa; // 1 al 10 (x < 5 MALA; x = 5 REGULAR; x > 5 && x < 8 BUENA; x > 7 EXCELENTE)
    public $mozo_mesa; // 1 al 10 (x < 5 MALA; x = 5 REGULAR; x > 5 && x < 8 BUENA; x > 7 EXCELENTE)
    public $cocinero_mesa; // 1 al 10 (x < 5 MALA; x = 5 REGULAR; x > 5 && x < 8 BUENA; x > 7 EXCELENTE)
    public $resenia_es_buena; // true | false
    public $resenia_es_mala; // true | false
    public $resenia; // Maximo 66 caracteres
}

?>