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

require_once "middlewares/LoginMiddleware.php";

require_once "controllers/JwtController.php";

require_once "controllers/TestController.php";




require_once "utils/AutentificadorJWT.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();




$app = AppFactory::create();
$app->setBasePath("/P3/TP/app");
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();



$app->group("/jwt", function(RouteCollectorProxy $group) {
    $group->post("/crear-token", \JwtController::class . ":crear_token");
    
});




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




$app->group("/test", function(RouteCollectorProxy $group) {

    $group->get("/{numero_test}", \TestController::class . ":test");

});




// JWT test
$app->group('/jwt', function (RouteCollectorProxy $group) {

        $group->post('/crearToken', function (Request $request, Response $response) {    
          $parametros = $request->getParsedBody();
      
          $usuario = $parametros['usuario'];
          $perfil = $parametros['perfil'];
          $alias = $parametros['alias'];
      
          $datos = array('usuario' => $usuario, 'perfil' => $perfil, 'alias' => $alias);
      
          $token = AutentificadorJWT::CrearToken($datos);
          $payload = json_encode(array('jwt' => $token));
      
          $response->getBody()->write($payload);
          return $response
            ->withHeader('Content-Type', 'application/json');
        });
      
        $group->get('/devolverPayLoad', function (Request $request, Response $response) {
          $header = $request->getHeaderLine('Authorization');
          $token = trim(explode("Bearer", $header)[1]);
      
          try {
            $payload = json_encode(array('payload' => AutentificadorJWT::ObtenerPayLoad($token)));
          } catch (Exception $e) {
            $payload = json_encode(array('error' => $e->getMessage()));
          }
      
          $response->getBody()->write($payload);
          return $response
            ->withHeader('Content-Type', 'application/json');
        });
      
        $group->get('/devolverDatos', function (Request $request, Response $response) {
          $header = $request->getHeaderLine('Authorization');
          $token = trim(explode("Bearer", $header)[1]);
      
          try {
            $payload = json_encode(array('datos' => AutentificadorJWT::ObtenerData($token)));
          } catch (Exception $e) {
            $payload = json_encode(array('error' => $e->getMessage()));
          }
      
          $response->getBody()->write($payload);
          return $response
            ->withHeader('Content-Type', 'application/json');
        });
      
        $group->get('/verificarToken', function (Request $request, Response $response) {
          $header = $request->getHeaderLine('Authorization');
          $token = trim(explode("Bearer", $header)[1]);
          $esValido = false;
      
          try {
            AutentificadorJWT::verificarToken($token);
            $esValido = true;
          } catch (Exception $e) {
            $payload = json_encode(array('error' => $e->getMessage()));
          }
      
          if ($esValido) {
            $payload = json_encode(array('valid' => $esValido));
          }
      
          $response->getBody()->write($payload);
          return $response
            ->withHeader('Content-Type', 'application/json');
        });
      });
      
// JWT en login
$app->group('/auth', function (RouteCollectorProxy $group) {
      
        $group->post('/login', function (Request $request, Response $response) {    
          $parametros = $request->getParsedBody();
      
          $usuario = $parametros['usuario'];
          $contraseña = $parametros['contraseña'];
      
          if($usuario == 'prueba' && $contraseña == '1234'){ // EJEMPLO!!! Acá se deberia ir a validar el usuario contra la DB
            $datos = array('usuario' => $usuario);
      
            $token = AutentificadorJWT::CrearToken($datos);
            $payload = json_encode(array('jwt' => $token));
          } else {
            $payload = json_encode(array('error' => 'Usuario o contraseña incorrectos'));
          }
      
          $response->getBody()->write($payload);
          return $response
            ->withHeader('Content-Type', 'application/json');
        });
      
});



$app->run();


?>