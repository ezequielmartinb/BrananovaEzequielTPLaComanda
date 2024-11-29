<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';

// MW
require_once './middlewares/Logger.php';
require_once './middlewares/ValidarDatos.php';


// CONTROLLER
require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/ProductoPedidoController.php';
// require_once './controllers/EncuestaController.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

//SPRINTS

//SPRINT 1
// $app->group('/usuarios', function (RouteCollectorProxy $group) 
// {
//     $group->get('[/]', \UsuarioController::class . ':TraerTodos');
//     $group->get('/traer', \UsuarioController::class . ':TraerUno')->add(new ValidarDatos(array("id")));
//     $group->post('/crear', \UsuarioController::class . ':CargarUno')->add(new ValidarDatos(array("nombre", "apellido", "mail", "puesto", "estado")));    
// });
// $app->group('/producto', function (RouteCollectorProxy $group) 
// {
//     $group->get('[/]', \ProductoController::class . ':TraerTodos');
//     $group->get('/traer', \ProductoController::class . ':TraerUno')->add(new ValidarDatos(array("id")));
//     $group->post('/crear', \ProductoController::class . ':CargarUno')->add(new ValidarDatos(array("descripcion", "tipo", "precio", "tiempoPreparacion")));    
// });
// $app->group('/mesa', function (RouteCollectorProxy $group) 
// {
//     $group->get('[/]', \MesaController::class . ':TraerTodos');
//     $group->get('/traer', \MesaController::class . ':TraerUno')->add(new ValidarDatos(array("id")));
//     $group->post('/crear', \MesaController::class . ':CargarUno')->add(new ValidarDatos(array("estado", "idMozoAsignado")));
// });
// $app->group('/pedido', function (RouteCollectorProxy $group) 
// {
//     $group->get('[/]', \PedidoController::class . ':TraerTodos');
//     $group->get('/traer', \PedidoController::class . ':TraerUno')->add(new ValidarDatos(array("id")));
//     $group->post('/crear', \PedidoController::class . ':CargarUno')->add(new ValidarDatos(array("nombreCliente", "codigoMesa")));
// });
// $app->group('/productoPedido', function (RouteCollectorProxy $group) 
// {
//     $group->get('[/]', \ProductoPedidoController::class . ':TraerTodos');
//     $group->get('/traer', \ProductoPedidoController::class . ':TraerUno');
//     $group->post('/crear', \ProductoPedidoController::class . ':CargarUno')->add(new ValidarDatos(array("idProducto", "idPedido", "idUsuario")));
// });

//SPRINT 2
//SPRINT 3
//SPRINT 4


// RUTAS FUNCIONALIDADES
$app->post("/login", \Logger::class . ':Loguear');

$app->group('/pedido', function (RouteCollectorProxy $group) 
{
    $group->get('[/]', \PedidoController::class . ':TraerTodos');
    $group->get('/traer', \PedidoController::class . ':TraerUno')->add(new ValidarDatos(array("id")));
    $group->post('/crear', \PedidoController::class . ':CargarUno')->add(new ValidarDatos(array("nombreCliente", "codigoMesa")))->add(\Logger::class.':ValidarPermisosMozo');
});
$app->group('/productoPedido', function (RouteCollectorProxy $group) 
{
    $group->get('[/]', \ProductoPedidoController::class . ':TraerTodos');
    $group->get('/traer', \ProductoPedidoController::class . ':TraerUno');
    $group->post('/crear', \ProductoPedidoController::class . ':CargarUno')->add(new ValidarDatos(array("idProducto", "idPedido", "idUsuario")))->add(\Logger::class.':ValidarPermisosMozo');
});



$app->run();
