<?php
    require_once './models/Usuario.php';
    require_once 'AutentificadorJWT.php';
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
    use Slim\Psr7\Response;

    class Logger
    {
        private $camposAValidar;

        public function __construct($camposAValidar)
        {
            $this->camposAValidar = $camposAValidar;
        }
        public function __invoke(Request $request, RequestHandler $handler): Response
        {
            $header = $request->getHeaderLine('Authorization');
            $token = trim(explode("Bearer", $header)[1]);
            try 
            {
                AutentificadorJWT::VerificarToken($token);
                $data = AutentificadorJWT::ObtenerData($token);

                if($request instanceof \Psr\Http\Message\ServerRequestInterface)
                {
                    if(in_array($data->puesto, $this->camposAValidar))
                    {
                        $response = $handler->handle($request);
                    }
                    else
                    {
                        $response = new Response();
                        $payload = json_encode(array('error' => "El usuario $data->apellido, $data->nombre cuyo puesto es $data->puesto no tiene permisos para esta tarea."));
                        $response->getBody()->write($payload);
                        $response = $response->withHeader('Content-Type', 'application/json')->withStatus(403);
                    }
                }     
            } 
            catch (Exception $e) 
            {
                $response = new Response();
                $payload = json_encode(array('error' => 'Token ingresado INVALIDO'));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }
            return $response->withHeader('Content-Type', 'application/json');                          
        }

        public static function Loguear($request, $response, $args)
        {
            $parametros = $request->getParsedBody();
            $mail = $parametros['mail'];
            $clave = $parametros['clave'];
            $usuario = Usuario::obtenerUsuarioPorMailYClave($mail, $clave);
            if($usuario != null && $usuario->estado != 'Inactivo')
            {              
                $datos = array('id' => $usuario->id, 'nombre' => $usuario->nombre, 'apellido' => $usuario->apellido, 'puesto' => $usuario->puesto, 'estado' => $usuario->estado);
                $token = AutentificadorJWT::CrearToken($datos);
                $payload = json_encode(array('jwt'=> $token));               
            }
            else
            {
                $payload = json_encode(array('mensaje'=>'Datos Invalidos'));
            }
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }  
        

?>