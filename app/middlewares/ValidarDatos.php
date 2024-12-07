<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseClass;

class ValidarDatos 
{    
    private $camposAValidar = array();

    public function __construct($camposAValidar)
    {
        $this->camposAValidar = $camposAValidar;
    }

    public function __invoke(Request $request, RequestHandler $requestHandler)
    {
        $response = new ResponseClass();
        $params = $request->getQueryParams();
        $paramsPost = $request->getParsedBody();
        
        if($params != null)
        {
            foreach($this->camposAValidar as $key => $value)
            {            
                if(!isset($params[$value]))
                {
                    $response->getBody()->write(json_encode(array("error" => "Error. Faltan datos $value")));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }           
            }
        }
        else if($paramsPost != null)
        {
            foreach($this->camposAValidar as $key => $value)
            {            
                if(!isset($paramsPost[$value]))
                {
                    $response->getBody()->write(json_encode(array("error" => "Error. Faltan datos $value")));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }           
            }
        }   
        switch($this->camposAValidar)
        {
            case array("nombre", "apellido", "mail", "puesto", "estado"):
                return $this->ValidarDatosAltaUsuario($request, $requestHandler, $response);
                break;            
            case array("descripcion", "tipo", "precio", "tiempoPreparacion"):
                return $this->ValidarDatosAltaProducto($request, $requestHandler, $response);
                break;   
            case array("descripcion", "tipo", "precio", "tiempoPreparacion"):
                return $this->ValidarDatosAltaProducto($request, $requestHandler, $response);
                break;      
            case array("estado", "idMozoAsignado"):
                return $this->ValidarDatosAltaMesa($request, $requestHandler, $response);
                break;  
            case array("idProducto", "idPedido", "idUsuarioEncargado"):
                return $this->ValidarDatosAltaProductoPedido($request, $requestHandler, $response);
                break; 
            case array("nombreCliente", "codigoMesa"):
                return $this->ValidarDatosAltaPedido($request, $requestHandler, $response);
                break;
            case array("id"):
                return $this->ValidarId($request, $requestHandler, $response, "id");
                break;                  
            case array("idPedido"):
                return $this->ValidarId($request, $requestHandler, $response, "idPedido");
                break;        
            case array("idProductoPedido"):
                return $this->ValidarId($request, $requestHandler, $response, "idProductoPedido");
                break;
            case array("codigoPedido"):
                return $this->ValidarDatosCodigoPedidoYFoto($request, $requestHandler, $response);
                break;
            case array("codigoPedido", "codigoMesa"):
                return $this->ValidarDatosCodigoMesaYPedido($request, $requestHandler, $response);
                break; 
            case array("puesto"):
                return $this->ValidarPuesto($request, $requestHandler, $response);
                break;
            case array("estado", "tiempoPreparacion"):
                return $this->ValidarEstadoYTiempoPreparacion($request, $requestHandler, $response);
                break;
            case array("estado"):
                return $this->ValidarEstado($request, $requestHandler, $response);
                break;  
            case array("estadoPedido", "estadoActualizado"):
                return $this->ValidarModificarMesa($request, $requestHandler, $response);
                break;  
            case array("codigoMesa"):
                return $this->ValidarCodigoMesaMesa($request, $requestHandler, $response);
                break;    
            case array("codigoMesa", "codigoPedido","puntosMesa", "puntosRestaurante", "puntosMozo", "puntosCocinero", "comentario"):
                return $this->ValidarDatosEncuesta($request, $requestHandler, $response);
                break;     
            case array("logo"):
                return $this->ValidarDatosLogo($request, $requestHandler, $response);
                break;                     
        }   
    }    
    public function ValidarDatosAltaUsuario(Request $request, RequestHandler $requestHandler, $response)
    {
        $params = $request->getParsedBody();        
        
        if(isset($params["nombre"]) && isset($params["apellido"]) && isset($params["mail"]) && isset($params["puesto"]) && isset($params["estado"]))
        {            
            $nombreIngresado = $params["nombre"]; 
            $apellidoIngresado = $params["apellido"]; 
            $mailIngresado = $params["mail"]; 
            $puestoIngresado = $params["puesto"]; 
            $estadoIngresado = $params["estado"];
            if(filter_var($mailIngresado, FILTER_VALIDATE_EMAIL) && is_string($nombreIngresado) && is_string($apellidoIngresado) && is_string($puestoIngresado) && 
            ($puestoIngresado == "Mozo" || $puestoIngresado == "Cocinero" || $puestoIngresado == "Bartender" || $puestoIngresado == "Cervecero" || $puestoIngresado == "Socio") 
            && is_string($estadoIngresado) && ($estadoIngresado == "Activo" || $estadoIngresado == "Inactivo")) 
            {
                return $requestHandler->handle($request);
            }            
        }           
        else
        {
            $response->getBody()->write(json_encode(array("error" => "Error. Datos ingresados invalidos")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        return $response;   
    }    
    public function ValidarDatosAltaProducto(Request $request, RequestHandler $requestHandler, $response)
    {
        $params = $request->getParsedBody();        
        
        if(isset($params["descripcion"]) && isset($params["precio"]) && isset($params["tipo"]) && isset($params["tiempoPreparacion"]))
        {            
            $descripcionIngresado = $params["descripcion"]; 
            $precioIngresado = $params["precio"]; 
            $tipoIngresado = $params["tipo"]; 
            $tiempoPreparacionIngresado = $params["tiempoPreparacion"]; 
            if(is_string($tipoIngresado) && ($tipoIngresado == "Comida" || $tipoIngresado == "Cerveza" || $tipoIngresado == "Trago") 
            && is_string($descripcionIngresado) && intval($precioIngresado) && intval($tiempoPreparacionIngresado))
            {
                return $requestHandler->handle($request);
            }
            else
            {
                $response->getBody()->write(json_encode(array("error" => "Datos incorrectos")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        }           
        else
        {
            $response->getBody()->write(json_encode(array("error" => "Error. Datos ingresados invalidos")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        return $response;   
    }   
    public function ValidarDatosAltaMesa(Request $request, RequestHandler $requestHandler, $response)
    {
        $params = $request->getParsedBody();        
        
        if(isset($params["estado"]) && isset($params["idMozoAsignado"]))
        {            
            $estadoIngresado = $params["estado"]; 
            $idMozoAsignadoIngresado = $params["idMozoAsignado"]; 
            
            if(is_string($estadoIngresado) && ($estadoIngresado == "Esperando" || $estadoIngresado == "Comiendo" || $estadoIngresado == "Pagando") && intval($idMozoAsignadoIngresado) )
            {
                $usuario = Usuario::obtenerUsuarioPorId($idMozoAsignadoIngresado);
                if($usuario != null && $usuario->puesto == "Mozo")
                {
                    return $requestHandler->handle($request);
                }
                else
                {
                    $response->getBody()->write(json_encode(array("error" => "Mozo asignado INEXISTENTE")));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            }
        }           
        else
        {
            $response->getBody()->write(json_encode(array("error" => "Error. Datos ingresados invalidos")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        return $response;   
    }     
    public function ValidarDatosAltaPedido(Request $request, RequestHandler $requestHandler, $response)
    {
        $params = $request->getParsedBody();        
        
        if(isset($params["nombreCliente"]) && isset($params["codigoMesa"]))
        {            
            $nombreClienteIngresado = $params["nombreCliente"]; 
            $codigoMesaIngresado = $params["codigoMesa"];             
            
            if(is_string($nombreClienteIngresado) && is_string($codigoMesaIngresado)) 
            {
                $mesa = Mesa::obtenerMesaPorCodigoMesa($codigoMesaIngresado);
                if($mesa != null && $mesa->estado == 'disponible')
                {
                    return $requestHandler->handle($request);
                }
                else
                {
                    $response->getBody()->write(json_encode(array("error" => "La mesa $mesa->codigoMesa no está disponible")));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            }
        }           
        else
        {
            $response->getBody()->write(json_encode(array("error" => "Error. Datos ingresados invalidos")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        return $response;   
    }
    public function ValidarDatosAltaProductoPedido(Request $request, RequestHandler $requestHandler, $response)
    {
        $params = $request->getParsedBody();        
        
        if(isset($params["idProducto"]) && isset($params["idUsuarioEncargado"]) && isset($params["idPedido"]))
        {          
            $idProductoIngresado = $params["idProducto"];             
            $idUsuarioEncargadoIngresado = $params["idUsuarioEncargado"];             
            $idPedidoIngresado = $params["idPedido"];             
            if(intval($idPedidoIngresado) && intval($idProductoIngresado) && intval($idUsuarioEncargadoIngresado)
            && Usuario::obtenerUsuarioPorId($idUsuarioEncargadoIngresado) != null && Producto::obtenerProductoPorId($idProductoIngresado) != null 
            && Pedido::obtenerPedidoPorId($idPedidoIngresado) != null) 
            {                
                $pedido = Pedido::obtenerPedidoPorId($idPedidoIngresado);
                if($pedido->estado == 'pendiente')
                {
                    return $requestHandler->handle($request);                
                }
                else
                {
                    $response->getBody()->write(json_encode(array("error" => "El pedido ingresado ya fue consumido o está siendolo")));
                }
            }
            else
            {
                $response->getBody()->write(json_encode(array("error" => "Datos incorrectos")));
            }
        }           
        else
        {
            $response->getBody()->write(json_encode(array("error" => "Error. Datos ingresados invalidos")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        return $response;   
    }    
    public function ValidarDatosModificarMesa(Request $request, RequestHandler $requestHandler, $response)
    {
        $params = $request->getParsedBody();        
        
        if(isset($params["idPedido"]) && isset($params["codigoMesa"]))
        {            
            $idPedidoIngresado = $params["idPedido"]; 
            $codigoMesaIngresado = $params["codigoMesa"]; 
            if(intval($idPedidoIngresado) && is_string($codigoMesaIngresado))
            {
                return $requestHandler->handle($request);
            }
        }           
        else
        {
            $response->getBody()->write(json_encode(array("error" => "Error. Datos ingresados invalidos")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        return $response;   
    }
    public function ValidarDatosCodigoPedidoYFoto(Request $request, RequestHandler $requestHandler, $response)
    {
        $params = $request->getParsedBody(); 
        $uploadedFiles = $request->getUploadedFiles();       
        
        if(isset($params["codigoPedido"]) && isset($uploadedFiles["fotoCliente"]))
        {            
            $codigoPedidoIngresado = $params["codigoPedido"];             
            $fotoClienteIngresado = $uploadedFiles['fotoCliente'];
            $nombreArchivo = $fotoClienteIngresado->getClientFilename();
            $extensionDeLaFoto = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));                          
            if(($extensionDeLaFoto == 'png' || $extensionDeLaFoto == 'jpg' || $extensionDeLaFoto == 'jpeg') 
            & is_string($codigoPedidoIngresado) && Pedido::obtenerPedidoPorCodigoPedido($codigoPedidoIngresado) != null)
            {
                return $requestHandler->handle($request);     
            }
            else
            {
                $response->getBody()->write(json_encode(array("error" => "Datos incorrecto")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            
        }           
        else
        {
            $response->getBody()->write(json_encode(array("error" => "Error. Datos ingresados invalidos")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        return $response;   
    }  
    public function ValidarDatosCodigoMesaYPedido(Request $request, RequestHandler $requestHandler, $response)
    {
        $params = $request->getParsedBody(); 
        
        if(isset($params["codigoPedido"]) && isset($params["codigoMesa"]))
        {            
            $codigoPedidoIngresado = $params["codigoPedido"];             
            $codigoMesaIngresado = $params["codigoMesa"];             
            if(is_string($codigoPedidoIngresado) && is_string($codigoMesaIngresado) 
            && Pedido::obtenerPedidoPorCodigoMesa($codigoMesaIngresado) != null &&  Pedido::obtenerPedidoPorCodigoPedido($codigoPedidoIngresado) != null)
            {
                return $requestHandler->handle($request);     
            }
            else
            {
                $response->getBody()->write(json_encode(array("error" => "Datos incorrecto")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            
        }           
        else
        {
            $response->getBody()->write(json_encode(array("error" => "Error. Datos ingresados invalidos")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        return $response;   
    } 
    public function ValidarPuesto(Request $request, RequestHandler $requestHandler, $response)
    {
        $params = $request->getQueryParams(); 
        
        if(isset($params["puesto"]))
        {            
            $puestoIngresado = $params["puesto"];             
            if(is_string($puestoIngresado) && ($puestoIngresado == 'Cocinero' || $puestoIngresado == 'Bartender' || $puestoIngresado == 'Cervecero'))
            {
                return $requestHandler->handle($request);     
            }
            else
            {
                $response->getBody()->write(json_encode(array("error" => "Puesto ingresado incorrecto")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            
        }           
        else
        {
            $response->getBody()->write(json_encode(array("error" => "Error. Datos ingresados invalidos")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        return $response;   
    }   
    public function ValidarEstadoYTiempoPreparacion(Request $request, RequestHandler $requestHandler, $response)
    {
        $params = $request->getParsedBody(); 
        
        if(isset($params["estado"]) && isset($params["tiempoPreparacion"]))
        {            
            $estadoIngresado = $params["estado"];
            $tiempoPreparacionIngresado = $params["tiempoPreparacion"];             
            if(is_string($estadoIngresado) && intval($tiempoPreparacionIngresado) && intval($tiempoPreparacionIngresado) > 0
            && ($estadoIngresado == 'pendiente' || $estadoIngresado == 'en preparacion' || $estadoIngresado == 'listo para servir'))
            {
                $idProductoPedidoIngresado = $params["idProductoPedido"];
                $productoPedidoIngresado = ProductoPedido::obtenerProductoPedidoPorId($idProductoPedidoIngresado);
                if($productoPedidoIngresado->estado != 'entregado')
                {
                    return $requestHandler->handle($request);     
                }
                else
                    {
                        $response->getBody()->write(json_encode(array("error" => "Id Producto Pedido ingresado incorrecto")));
                        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                    }
            }
            else
            {
                $response->getBody()->write(json_encode(array("error" => "Estado o Tiempo Preparacion ingresado incorrecto")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            
        }           
        else
        {
            $response->getBody()->write(json_encode(array("error" => "Error. Datos ingresados invalidos")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        return $response;   
    }     
    public function ValidarEstado(Request $request, RequestHandler $requestHandler, $response)
    {
        $params = $request->getParsedBody(); 
        
        if(isset($params["estado"]))
        {            
            $estadoIngresado = $params["estado"];
            if(is_string($estadoIngresado) && ($estadoIngresado == 'pendiente' || $estadoIngresado == 'en preparacion' || $estadoIngresado == 'listo para servir'))
            {
                return $requestHandler->handle($request);     
            }
            else
            {
                $response->getBody()->write(json_encode(array("error" => "Estado ingresado incorrecto")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }            
        }           
        else
        {
            $response->getBody()->write(json_encode(array("error" => "Error. Datos ingresados invalidos")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        return $response;   
    }  
    public function ValidarId(Request $request, RequestHandler $requestHandler, $response, $id)
    {
        $paramsGet = $request->getQueryParams();
        $paramsPost = $request->getParsedBody();

        if($paramsGet != null)
        {
            $idIngresado = $paramsGet[$id]; 
            if(!(intval($idIngresado)))
            {
                $response->getBody()->write(json_encode(array("error" => "Error. Datos ingresados invalidos")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        }
        else if($paramsPost != null)
        {
            $idIngresado = $paramsPost[$id]; 
            if(!(intval($idIngresado)))
            {
                $response->getBody()->write(json_encode(array("error" => "Error. Datos ingresados invalidos")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        }
        return $requestHandler->handle($request);
    }
    public function ValidarModificarMesa(Request $request, RequestHandler $requestHandler, $response)
    {
        $params = $request->getParsedBody();        
        if(isset($params["estadoPedido"]) && isset($params["estadoActualizado"]))
        {            
            $estadoPedidoIngresado = $params["estadoPedido"];
            $estadoActualizadoIngresado = $params["estadoActualizado"];
            if(is_string($estadoPedidoIngresado) && $estadoPedidoIngresado == 'listo para servir' 
            && is_string($estadoActualizadoIngresado) && $estadoActualizadoIngresado == 'comiendo')
            {
                return $requestHandler->handle($request);     
            }
            else
            {
                $response->getBody()->write(json_encode(array("error" => "Estados ingresados incorrecto")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }            
        }           
        else
        {
            echo "entre al else";
            $response->getBody()->write(json_encode(array("error" => "Error. Datos ingresados invalidos")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        return $response;   
    }  
    public function ValidarCodigoMesaMesa(Request $request, RequestHandler $requestHandler, $response)
    {
        $params = $request->getParsedBody(); 
        $codigoMesaIngresado = $params['codigoMesa']; 

        if(isset($codigoMesaIngresado) && !preg_match('/^[a-zA-Z0-9]+$/', $codigoMesaIngresado)) 
        { 
            $payload = json_encode(array("error" => "El código de mesa solo puede contener números y letras."));
            $response->getBody()->write($payload); 
            return $response->withHeader('Content-Type', 'application/json'); 
        } 

        return $requestHandler->handle($request);
    }
    public function ValidarDatosEncuesta(Request $request, RequestHandler $requestHandler, $response)
{
    $params = $request->getParsedBody(); 
    $codigoMesaIngresado = $params['codigoMesa']; 
    $codigoPedidoIngresado = $params['codigoPedido']; 
    $puntosMesaIngresado = $params['puntosMesa']; 
    $puntosRestauranteIngresado = $params['puntosRestaurante']; 
    $puntosMozoIngresado = $params['puntosMozo']; 
    $puntosCocineroIngresado = $params['puntosCocinero']; 
    $comentarioIngresado = $params['comentario']; 

    if (!isset($codigoMesaIngresado) || !preg_match('/^[a-zA-Z0-9]+$/', $codigoMesaIngresado) ||
        !isset($codigoPedidoIngresado) || !preg_match('/^[a-zA-Z0-9]+$/', $codigoPedidoIngresado) ||
        !isset($puntosMesaIngresado) || !intval($puntosMesaIngresado) ||
        !isset($puntosRestauranteIngresado) || !intval($puntosRestauranteIngresado) ||
        !isset($puntosMozoIngresado) || !intval($puntosMozoIngresado) ||
        !isset($puntosCocineroIngresado) || !intval($puntosCocineroIngresado) ||
        !isset($comentarioIngresado) || !is_string($comentarioIngresado) || strlen($comentarioIngresado) > 66 ||
        Pedido::obtenerPedidoPorCodigoPedido($codigoPedidoIngresado) == null || Encuesta::obtenerEncuestaPorCodigoPedido($codigoPedidoIngresado) != null ||
        Mesa::obtenerMesaPorCodigoMesa($codigoMesaIngresado) == null || Encuesta::obtenerEncuestaPorCodigoMesa($codigoMesaIngresado) != null) 
    {
        $payload = json_encode(array("error" => "Los datos ingresados no son válidos. Asegúrese de que el código de mesa solo contenga números y letras, todos los puntos sean enteros, y el comentario no exceda los 66 caracteres."));
        $response->getBody()->write($payload); 
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400); 
    }

    return $requestHandler->handle($request);
}


    public function ValidarDatosLogo(Request $request, RequestHandler $requestHandler, $response)
    {
        $uploadedFiles = $request->getUploadedFiles();       
        
        if(isset($uploadedFiles["logo"]))
        {            
            $logoIngresado = $uploadedFiles['logo'];
            $nombreArchivo = $logoIngresado->getClientFilename();
            $extensionDeLaFoto = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));                          
            if(($extensionDeLaFoto == 'png' || $extensionDeLaFoto == 'jpg' || $extensionDeLaFoto == 'jpeg'))
            {
                return $requestHandler->handle($request);     
            }
            else
            {
                $response->getBody()->write(json_encode(array("error" => "Foto subida en un formato invalido incorrecto")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            
        }           
        else
        {
            $response->getBody()->write(json_encode(array("error" => "Error. Datos ingresados invalidos")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        return $response;   
    }  
}
?>