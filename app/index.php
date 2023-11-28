<?php

/*

Programacion III
TP - La Comanda
Juan Pablo Dongo Huaman, Div. 3°C

*/

date_default_timezone_set('America/Argentina/Buenos_Aires');

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . "/../vendor/autoload.php";

require_once "controllers/MesaController.php";
require_once "middlewares/MesaMiddleware.php";

require_once "controllers/ProductoController.php";
require_once "middlewares/ProductoMiddleware.php";

require_once "controllers/PedidoController.php";
require_once "middlewares/PedidoMiddleware.php";

require_once "controllers/ComandaController.php";
require_once "middlewares/ComandaMiddleware.php";

require_once "controllers/ClienteController.php";
require_once "middlewares/ClienteMiddleware.php";

require_once "controllers/EmpleadoController.php";
require_once "middlewares/EmpleadoMiddleware.php";

require_once "controllers/EncuestaController.php";
require_once "middlewares/EncuestaMiddleware.php";

require_once "middlewares/LoginMiddleware.php";

require_once "controllers/TestController.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$app = AppFactory::create();
$app->setBasePath("/P3/TP/app");
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

$app->group("/mesa", function(RouteCollectorProxy $group) {

    $group->post("[/]", \MesaController::class . ":alta")
            ->add(\MesaMiddleware::class . ":validar_input_alta");

    $group->delete("[/]", \MesaController::class . ":baja")
            ->add(\MesaMiddleware::class . ":validar_input_baja");

    $group->put("[/]", \MesaController::class . ":modificar")
            ->add(\MesaMiddleware::class . ":validar_input_modificar");

    $group->get("/todas/", \MesaController::class . ":traer_todos");

    $group->get("/una/", \MesaController::class . ":traer_uno")
            ->add(\MesaMiddleware::class . ":validar_input_traer_uno");

    $group->post("/cobrar/", \MesaController::class . ":cobrar")
            ->add(\MesaMiddleware::class . ":validar_input_cobrar");

    $group->post("/cerrar/", \MesaController::class . ":cerrar")
            ->add(\MesaMiddleware::class . ":validar_input_cerrar");

    $group->get("/mas-usada/", \MesaController::class . ":traer_mas_usada");

})->add(\LoginMiddleware::class . ":acceso_sistema_mesa");

$app->group("/producto", function(RouteCollectorProxy $group) {

    $group->post("[/]", \ProductoController::class . ":alta")
            ->add(\ProductoMiddleware::class . ":validar_input_alta");

    $group->delete("[/]", \ProductoController::class . ":baja")
            ->add(\ProductoMiddleware::class . ":validar_input_baja");

    $group->put("[/]", \ProductoController::class . ":modificar")
            ->add(\ProductoMiddleware::class . ":validar_input_modificar");

    $group->get("/todos/", \ProductoController::class . ":traer_todos");

    $group->get("/uno/", \ProductoController::class . ":traer_uno")
            ->add(\ProductoMiddleware::class . ":validar_input_traer_uno");

    $group->post("/cargar-csv/", \ProductoController::class . ":cargar_csv")
            ->add(\ProductoMiddleware::class . ":validar_input_cargar_csv");

    $group->get("/crear-csv/", \ProductoController::class . ":crear_csv");


})->add(\LoginMiddleware::class . ":acceso_sistema_producto");

$app->group("/pedido", function(RouteCollectorProxy $group) {

    $group->put("/elaborar/", \PedidoController::class . ":elaborar")
            ->add(\PedidoMiddleware::class . ":validar_input_elaborar");

    $group->put("/terminar/", \PedidoController::class . ":terminar")
            ->add(\PedidoMiddleware::class . ":validar_input_terminar");

    $group->put("/servir/", \PedidoController::class . ":servir")
            ->add(\PedidoMiddleware::class . ":validar_input_servir");

    $group->get("/todos/", \PedidoController::class . ":traer_todos");

    $group->get("/uno/", \PedidoController::class . ":traer_uno")
            ->add(\PedidoMiddleware::class . ":validar_input_traer_uno");

})->add(\LoginMiddleware::class . ":acceso_sistema_pedido");

$app->group("/comanda", function(RouteCollectorProxy $group) {

    $group->post("[/]", \ComandaController::class . ":alta")
            ->add(\ComandaMiddleware::class . ":validar_input_alta");

    $group->get("/todas/", \ComandaController::class . ":traer_todos");

    $group->get("/una/", \ComandaController::class . ":traer_uno")
            ->add(\ComandaMiddleware::class . ":validar_input_traer_uno");

})->add(\LoginMiddleware::class . ":acceso_sistema_comanda");

$app->group("/cliente", function(RouteCollectorProxy $group) {

    $group->post("[/]", \ClienteController::class . ":alta")
            ->add(\ClienteMiddleware::class . ":validar_input_alta");

    $group->delete("[/]", \ClienteController::class . ":baja")
            ->add(\ClienteMiddleware::class . ":validar_input_baja");

    $group->put("[/]", \ClienteController::class . ":modificar")
            ->add(\ClienteMiddleware::class . ":validar_input_modificar");

    $group->get("/todos/", \ClienteController::class . ":traer_todos");

    $group->get("/uno/", \ClienteController::class . ":traer_uno")
            ->add(\ClienteMiddleware::class . ":validar_input_traer_uno");

})->add(\LoginMiddleware::class . ":acceso_sistema_cliente");

$app->group("/empleado", function(RouteCollectorProxy $group) {

    $group->post("[/]", \EmpleadoController::class . ":alta")
            ->add(\EmpleadoMiddleware::class . ":validar_input_alta");
    
    $group->delete("[/]", \EmpleadoController::class . ":baja")
            ->add(\EmpleadoMiddleware::class . ":validar_input_baja");

    $group->put("[/]", \EmpleadoController::class . ":modificar")
            ->add(\EmpleadoMiddleware::class . ":validar_input_modificar");

    $group->get("/todos/", \EmpleadoController::class . ":traer_todos");

    $group->get("/uno/", \EmpleadoController::class . ":traer_uno")
            ->add(\EmpleadoMiddleware::class . ":validar_input_traer_uno");

    $group->get("/todos-por-rol/", \EmpleadoController::class . ":traer_todos_por_rol")
            ->add(\EmpleadoMiddleware::class . ":validar_input_traer_todos_por_rol");

    $group->get("/uno-por-rol/", \EmpleadoController::class . ":traer_uno_por_rol")
            ->add(\EmpleadoMiddleware::class . ":validar_input_traer_uno_por_rol");

})->add(\LoginMiddleware::class . ":acceso_sistema_empleado");

$app->post('/crear-token/', \EmpleadoController::class . ":crear_token")->add(\LoginMiddleware::class . ":acceso_sistema_crear_token");

$app->get("/cliente-pedidos/", \ClienteController::class . ":traer_pedidos")->add(\ClienteMiddleware::class . ":validar_input_traer_pedidos");

$app->group("/encuesta", function(RouteCollectorProxy $group) {

     $group->post("[/]", \EncuestaController::class . ":alta")
             ->add(\EncuestaMiddleware::class . ":validar_input_alta");

     $group->get("/mejores-comentarios/", \EncuestaController::class . ":traer_mejores_comentarios");
    
});

$app->group("/test/", function(RouteCollectorProxy $group) {

    $group->get("{numero_test}", \TestController::class . ":test");

});

$app->run();

?>