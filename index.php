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

require __DIR__ . "/vendor/autoload.php";

require_once "Controllers/MesaController.php";
require_once "Controllers/ClienteController.php";
require_once "Controllers/EmpleadoController.php";
require_once "Controllers/ProductoController.php";
require_once "Controllers/PedidoController.php";
require_once "Controllers/ComandaController.php";
require_once "Controllers/TestController.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$app = AppFactory::create();
$app->setBasePath("/P3/TP/App");
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

$app->group("/mesa", function(RouteCollectorProxy $group) {
    $group->post("[/]", \MesaController::class . ":alta");
    $group->delete("[/]", \MesaController::class . ":baja");
    $group->put("[/]", \MesaController::class . ":modificar");
    $group->get("/todas[/]", \MesaController::class . ":traer_todos");
    $group->get("/{numero_mesa}", \MesaController::class . ":traer_uno");
});

$app->group("/cliente", function(RouteCollectorProxy $group) {
    $group->post("[/]", \ClienteController::class . ":alta");
    $group->delete("[/]", \ClienteController::class . ":baja");
    $group->put("[/]", \ClienteController::class . ":modificar");
    $group->get("/todos", \ClienteController::class . ":traer_todos");
    $group->get("/{numero_cliente}", \ClienteController::class . ":traer_uno");
});

$app->group("/empleado", function(RouteCollectorProxy $group) {
    $group->post("[/]", \EmpleadoController::class . ":alta");
    $group->delete("[/]", \EmpleadoController::class . ":baja");
    $group->put("[/]", \EmpleadoController::class . ":modificar");
    $group->get("/todos", \EmpleadoController::class . ":traer_todos");
    $group->get("/{numero_empleado}", \EmpleadoController::class . ":traer_uno");
    $group->get("/todos/{rol}", \EmpleadoController::class . ":traer_todos_por_rol");
    $group->get("/{numero_empleado}/{rol}", \EmpleadoController::class . ":traer_uno_por_rol");
});

$app->group("/producto", function(RouteCollectorProxy $group) {
    $group->post("[/]", \ProductoController::class . ":alta");
    $group->delete("[/]", \ProductoController::class . ":baja");
    $group->put("[/]", \ProductoController::class . ":modificar");
    $group->get("/todos", \ProductoController::class . ":traer_todos");
    $group->get("/{numero_producto}", \ProductoController::class . ":traer_uno");
});

$app->group("/pedido", function(RouteCollectorProxy $group) {
    $group->get("/todos", \PedidoController::class . ":traer_todos");
    $group->get("/{numero_pedido}", \PedidoController::class . ":traer_uno");
});

$app->group("/comanda", function(RouteCollectorProxy $group) {
    $group->get("/todos", \ComandaController::class . ":traer_todos");
    $group->get("/{numero_comanda}", \ComandaController::class . ":traer_uno");
});

$app->group("/funcionalidad-mozo/comanda", function(RouteCollectorProxy $group) {
    $group->post("[/]", \ComandaController::class . ":alta");
});

$app->group("/test", function(RouteCollectorProxy $group) {
    $group->get("/{numero_test}", \TestController::class . ":test");
});

/* Ejemplo del link para guardar el movimiento del empleado en 'movimientos'
$app->group("/{numero_empleado}/funcionalidad-mozo/comanda", function(RouteCollectorProxy $group) {
    $group->post("[/]", \ComandaController::class . ":alta");
});*/

$app->run();

?>