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
use Slim\Psr7\UploadedFile; 

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
require_once './controllers/EncuestaController.php';

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
    // $group->get('[/]', \PedidoController::class . ':TraerTodos');
    // $group->get('/traer', \PedidoController::class . ':TraerUno')->add(new ValidarDatos(array("id")));
    // PUNTO 1
    $group->post('/crear', \PedidoController::class . ':CargarUno')->add(new ValidarDatos(array("nombreCliente", "codigoMesa")))->add(\Logger::class.':ValidarPermisosMozo');
    // PUNTO 2
    $group->post('/tomarFoto', \PedidoController::class . ':AsociarFotoConPedido')->add(new ValidarDatos(array("codigoPedido")));
    // PUNTO 3
    $group->put('/cambiarEstado', \PedidoController::class . ':CambiarEstadoPedido')->add(new ValidarDatos(array("id")));
    // PUNTO 4
    $group->post('/traerPedidoPorCodigoMesaYPedido', \PedidoController::class . ':TraerTiempoEstimadoPorCodigoMesayPedido')->add(new ValidarDatos(array("codigoPedido", "codigoMesa")));
    // PUNTO 5
    $group->get('/socio', \PedidoController::class . ':TraerTodos')->add(\Logger::class.':ValidarPermisosSocio');
});
$app->group('/productoPedido', function (RouteCollectorProxy $group) 
{
    // $group->get('[/]', \ProductoPedidoController::class . ':TraerTodos');
    // $group->get('/traer', \ProductoPedidoController::class . ':TraerUno');
    // PUNTO 1
    $group->post('/crear', \ProductoPedidoController::class . ':CargarUno')->add(new ValidarDatos(array("idProducto", "idPedido", "idUsuario")))->add(\Logger::class.':ValidarPermisosMozo');
    // PUNTO 3
    $group->get('/traerPorPuesto', \ProductoPedidoController::class . ':TraerProductosPedidosPendientesPorPuesto')->add(new ValidarDatos(array("puesto")));
    // PUNTO 6
    $group->put('/modificarEstado', \ProductoPedidoController::class . ':ModificarEstado')->add(new ValidarDatos(array("estado")))->add(\Logger::class.':ValidarPermisosPuestoEmpleados');

});
$app->group('/mesa', function (RouteCollectorProxy $group)
{
    // PUNTO 7
    $group->put('/modificarEstadoMesa', \MesaController::class . ':ModificarEstadoMesa')->add(\Logger::class.':ValidarPermisosMozo');
    // PUNTO 8
    $group->get('/socio', \MesaController::class . ':TraerTodos')->add(\Logger::class.':ValidarPermisosSocio');
    // PUNTO 9
    $group->post('/cobrarMesa', \MesaController::class . ':CobrarMesa');
    // PUNTO 10
    $group->delete('/cerrarMesa', \MesaController::class . ':BorrarUno')->add(\Logger::class.':ValidarPermisosSocio');
    // PUNTO 13
    $group->get('/traerMesaMasUsada', \MesaController::class . ':TraerMesaMasUsada')->add(\Logger::class.':ValidarPermisosSocio');

});
$app->group('/encuesta', function (RouteCollectorProxy $group)
{
    // PUNTO 11
    $group->post('/completarEncuesta', \EncuestaController::class . ':CargarUno');
    // PUNTO 12
    $group->get('/traerMejoresComentarios', \EncuestaController::class . ':TraerEncuestaMejoresComentarios');
});

$app->group('/usuarios', function (RouteCollectorProxy $group)
{
    $group->get('/descargarUsuarios', \UsuarioController::class . ':DescargarUsuariosCsv');
    $group->post('/cargarUsuarios', \UsuarioController::class . ':CargarUsuariosCsv');
});

// PUNTO 16
$app->post('/PDF', function (Request $request, Response $response, array $args) 
{
    $uploadedFiles = $request->getUploadedFiles();
    $logo = $uploadedFiles['logo'];

    if ($logo->getError() === UPLOAD_ERR_OK) 
    {
        $directory = __DIR__ . '/uploads';
        
        if (!is_dir($directory)) 
        {
            mkdir($directory, 0777, true);
        }

        $extension = pathinfo($logo->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $nombreArchivo = sprintf('%s.%0.8s', $basename, $extension);
        $logo->moveTo($directory . DIRECTORY_SEPARATOR . $nombreArchivo);

        $pdf = new FPDF();
        $pdf->AddPage();
        $rutaDelLogo = $directory . DIRECTORY_SEPARATOR . $nombreArchivo;
        list($width, $height) = getimagesize($rutaDelLogo);
        $pageWidth = $pdf->GetPageWidth();
        $pageHeight = $pdf->GetPageHeight();
        $x = ($pageWidth - $width) / 2;
        $y = ($pageHeight - $height) / 2;
        $pdf->Image($rutaDelLogo, $x, $y, $width);

        $pdf->SetY($y + $height + 10);
        $output = $pdf->Output('S'); 
        unlink($rutaDelLogo);

        $response = $response->withHeader('Content-Type', 'application/pdf')
                             ->withHeader('Content-Disposition', 'attachment; filename="logo.pdf"')
                             ->withBody(new Slim\Psr7\Stream(fopen('php://temp', 'r+')));
                             
        $response->getBody()->write($output);

        return $response;
    }

    return $response->withStatus(400);
})->add(new ValidarDatos(array("logo")))->add(\Logger::class.':ValidarPermisosSocio');

$app->run();
