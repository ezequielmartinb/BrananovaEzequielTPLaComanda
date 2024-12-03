<?php
    require_once './models/Usuario.php';
    require_once 'AutentificadorJWT.php';

    class Logger
    {
        public static function LogOperacion($request, $response, $next)
        {
            $retorno = $next($request, $response);
            return $retorno;
        }

        public static function LimpiarCookieUsuario($request, $handler)
        {
            setcookie("jwt", '', time() - 3600);
            return $handler->handle($request);
        }

        public static function Loguear($request, $response, $args)
        {
            $parametros = $request->getParsedBody();
            $mail = $parametros['mail'];
            $clave = $parametros['clave'];
            $usuario = Usuario::obtenerUsuarioPorMailYClave($mail, $clave);
            if($usuario != null)
            {              
                $datos = array('id' => $usuario->id, 'nombre' => $usuario->nombre, 'apellido' => $usuario->apellido, 'puesto' => $usuario->puesto, 'estado' => $usuario->estado);
                $token = AutentificadorJWT::CrearToken($datos);
                setcookie('JWT', $token, time()+6000, '/', 'localhost', false, true);
                $payload = json_encode(array('jwt'=> $token));               
            }
            else
            {
                $payload = json_encode(array('mensaje'=>'Datos Invalidos'));
            }
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        public static function CerrarSesion($request, $response, $args)
        {
            $payload = json_encode(array('mensaje'=>'Sesion Cerrada'));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        public static function ValidarSesion($request, $handler)
        {
            $cookie = $request->getCookieParams();
            if(isset($cookie['JWT']))
            {
                $token = $cookie['JWT'];
                $datos = AutentificadorJWT::ObtenerData($token);
                if($datos->estado == 'Activo')
                {
                    return $handler->handle($request);
                }
                else
                {
                    throw new Exception('Error. No es un usuario ACTIVO');
                }
            }
            throw new Exception('Debe haber iniciado sesion');
        }
        public static function ValidarPermisosPuestoEmpleados($request, $handler, $puesto = false)
        {
            $cookies = $request->getCookieParams();
            $token = $cookies['JWT'];
            try
            {
                AutentificadorJWT::VerificarToken($token);
                $datos = AutentificadorJWT::ObtenerData($token);
                if(!$puesto && ($datos->puesto == 'Bartender' || $datos->puesto == 'Cervecero' || $datos->puesto == 'Cocinero'))
                {
                    return $handler->handle($request);
                }
            }
            catch(Exception $e)
            {
                throw new Exception('Acceso denegado');
            }
        }
        public static function ValidarPermisosMozo($request, $handler)
        {
            $cookies = $request->getCookieParams();
            $token = $cookies['JWT'];
            try
            {
                AutentificadorJWT::VerificarToken($token);
                $datos = AutentificadorJWT::ObtenerData($token);
                if($datos->puesto == 'Mozo') 
                {
                    return $handler->handle($request);
                }
            }
            catch(Exception $e)
            {
                throw new Exception('Acceso denegado. No es un Mozo');
            }
        }
        public static function ValidarPermisosSocio($request, $handler, $puesto = false)
        {
            $cookies = $request->getCookieParams();
            $token = $cookies['JWT'];
            try
            {
                AutentificadorJWT::VerificarToken($token);
                $datos = AutentificadorJWT::ObtenerData($token);
                if(!$puesto && $datos->puesto == 'Socio') 
                {
                    echo "El socio " . $datos->apellido . ", " . $datos->nombre . " realizó la accion: ";
                    return $handler->handle($request);
                }
            }
            catch(Exception $e)
            {
                throw new Exception('Acceso denegado. NO es un Socio');
            }
        }
    }

?>