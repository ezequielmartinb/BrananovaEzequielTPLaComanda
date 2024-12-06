<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);
// php -S localhost:666 -t app
use Dotenv\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './middlewares/Logger.php';
require_once './middlewares/ValidarDatos.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/ProductoPedidoController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/EncuestaController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();
$app->post("/login", \Logger::class . ':Loguear');

$app->group('/productoPedido', function (RouteCollectorProxy $group) 
{    
    // PUNTO 1
    $group->post('/crear', \ProductoPedidoController::class . ':CargarUno')
    ->add(new ValidarDatos(array("idProducto", "idPedido", "idUsuarioEncargado")))
    ->add(new Logger(array("Mozo")));
    // PUNTO 3
    $group->get('/traerProductoPedidosPendientesPorPuesto', \ProductoPedidoController::class . ':TraerProductosPedidosPendientesPorIdUsuario')
    ->add(new Logger(array("Cocinero", "Cervecero", "Bartender")));
    $group->put('/modificarEstadoYTiempoPreparacion', \ProductoPedidoController::class . ':ModificarEstadoYTiempoPreparacion')
    ->add(new ValidarDatos(array("estado", "tiempoPreparacion")))
    ->add(new ValidarDatos(array("idProductoPedido")))
    ->add(new Logger(array("Cocinero", "Cervecero", "Bartender")));
    // PUNTO 6
    $group->get('/traerProductoPedidosEnPreparacionPorPuesto', \ProductoPedidoController::class . ':TraerProductosPedidosEnPreparacionPorIdUsuario')
    ->add(new Logger(array("Cocinero", "Cervecero", "Bartender")));
    $group->put('/modificarEstado', \ProductoPedidoController::class . ':ModificarEstado')
    ->add(new ValidarDatos(array("estado")))
    ->add(new ValidarDatos(array("idProductoPedido")))
    ->add(new Logger(array("Cocinero", "Cervecero", "Bartender")));
    

});
$app->group('/pedido', function (RouteCollectorProxy $group) 
{    
    // PUNTO 1
    $group->post('/crear', \PedidoController::class . ':CargarUno')->add(new ValidarDatos(array("nombreCliente", "codigoMesa")))
    ->add(new Logger(array("Mozo")));
    // PUNTO 2
    $group->post('/tomarFoto', \PedidoController::class . ':AsociarFotoConPedido')->add(new ValidarDatos(array("codigoPedido")))
    ->add(new Logger(array("Mozo")));
    // PUNTO 4
    $group->post('/traerPedidoPorCodigoMesaYPedido', \PedidoController::class . ':TraerTiempoEstimadoPorCodigoMesayPedido')
    ->add(new ValidarDatos(array("codigoPedido", "codigoMesa")));
    // PUNTO 5
    $group->get('/socio', \PedidoController::class . ':TraerTodos')
    ->add(new Logger(array("Socio")));
    // PUNTO 14
    $group->get('/traerPedidosNoEntregadosEnElTiempoEstipulado', \PedidoController::class . ':TraerPedidosNoEntregadosEnElTiempoEstipulado')
    ->add(new Logger(array("Socio"))); 
});
$app->group('/mesa', function (RouteCollectorProxy $group)
{
    // PUNTO 7
    $group->put('/modificarEstadoMesa', \MesaController::class . ':ModificarEstadoMesa')
    ->add(new ValidarDatos(array("estadoPedido", "estadoActualizado")))
    ->add(new ValidarDatos(array("idPedido")))
    ->add(new Logger(array("Mozo")));
    // PUNTO 8
    $group->get('/socio', \MesaController::class . ':TraerTodos')
    ->add(new Logger(array("Socio")));
    // PUNTO 9
    $group->post('/cobrarMesa', \MesaController::class . ':CobrarMesa')
    ->add(new ValidarDatos(array("codigoMesa")))
    ->add(new ValidarDatos(array("idPedido")))
    ->add(new Logger(array("Mozo")));
    // PUNTO 10
    $group->delete('/cerrarMesa', \MesaController::class . ':BorrarUno')
    ->add(new ValidarDatos(array("codigoMesa")))
    ->add(new Logger(array("Socio")));
    // PUNTO 13
    $group->get('/traerMesaMasUsada', \MesaController::class . ':TraerMesaMasUsada')
    ->add(new Logger(array("Socio")));

});
$app->group('/producto', function (RouteCollectorProxy $group)
{
    // PUNTO 15
    $group->get('/traerProductosNoEntregadosEnElTiempoEstipulado', \ProductoController::class . ':TraerProductosNoEntregadosEnElTiempoEstipulado')
    ->add(new Logger(array("Socio"))); 
});

$app->group('/encuesta', function (RouteCollectorProxy $group)
{
    // PUNTO 11
    $group->post('/completarEncuesta', \EncuestaController::class . ':CargarUno')    
    ->add(new ValidarDatos(array("codigoMesa", "codigoPedido", "puntosMesa", "puntosRestaurante", "puntosMozo", "puntosCocinero", "comentario")));
    // PUNTO 12
    $group->get('/traerMejoresComentarios', \EncuestaController::class . ':TraerEncuestaMejoresComentarios')
    ->add(new Logger(array("Socio")));    
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
})->add(new Logger(array("Socio")));

$app->run();
