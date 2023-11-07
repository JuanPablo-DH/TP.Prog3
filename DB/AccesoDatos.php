<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

class AccesoDatos
{
    private static $pdoStatic;
    private $pdo;

    private function __construct()
    {
        try
        {
            $this->pdo = new PDO("mysql:host=" . $_ENV["MYSQL_HOST"] . ";dbname=" . $_ENV["MYSQL_DB"] . ";charset=utf8;port=".$_ENV['MYSQL_PORT'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASS'], array(PDO::ATTR_EMULATE_PREPARES => false,PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $this->pdo->exec("SET CHARACTER SET utf8");
        } 
        catch (PDOException $e)
        { 
            echo "Error" . $e->getMessage();
            die();
        }
    }

    public function GetConsulta($sql)
    { 
        return $this->pdo->prepare($sql); 
    }

    public function GetUltimoIdInsertado()
    { 
        return $this->pdo->lastInsertId(); 
    }

    public static function GetPdo()
    { 
        if (!isset(self::$pdoStatic))
        {          
            self::$pdoStatic = new AccesoDatos();
        }

        return self::$pdoStatic;
    }

    public function __clone()
    { 
        trigger_error('La clonación de este objeto no está permitida', E_USER_ERROR); 
    }
}

?>