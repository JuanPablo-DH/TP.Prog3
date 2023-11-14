<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

require_once "modelos/Input.php";
require_once "bd/AccesoDatos.php";

class Encuesta
{
    public $numero_encuesta; // Numerico Unico positivo
    public $puntuacion_restaurante; // 1 al 10 (1,2,3,4 = MALA) (5 REGULAR) (6,7 BUENA) (8,9,10 EXCELENTE)
    public $puntuacion_mozo; // 1 al 10 (1,2,3,4 = MALA) (5 REGULAR) (6,7 BUENA) (8,9,10 EXCELENTE)
    public $puntuacion_bartender; // 1 al 10 (1,2,3,4 = MALA) (5 REGULAR) (6,7 BUENA) (8,9,10 EXCELENTE)
    public $puntuacion_cervezero; // 1 al 10 (1,2,3,4 = MALA) (5 REGULAR) (6,7 BUENA) (8,9,10 EXCELENTE)
    public $puntuacion_cocinero; // 1 al 10 (1,2,3,4 = MALA) (5 REGULAR) (6,7 BUENA) (8,9,10 EXCELENTE)
    public $tipo_resenia; // "buena" | "mala"
    public $resenia; // Maximo 66 caracteres
}

?>