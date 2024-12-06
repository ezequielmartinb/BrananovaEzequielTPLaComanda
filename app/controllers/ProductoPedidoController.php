<?php
// require_once './models/Pedido.php';
require_once './models/Producto.php';
require_once './models/ProductoPedido.php';

require_once './interfaces/IApiUsable.php';
class ProductoPedidoController extends ProductoPedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody(); 
        $idProducto = $parametros['idProducto'];
        $idUsuarioEncargado = $parametros['idUsuarioEncargado'];
        $idPedido = $parametros['idPedido'];        
        $productoPedido = new ProductoPedido();
        $productoPedido->idProducto=$idProducto;
        $productoPedido->idUsuarioEncargado=$idUsuarioEncargado;
        $productoPedido->idPedido=$idPedido;
        $productoPedido->crearProductoPedido();        
        $payload = json_encode(array("mensaje" => "Producto Pedido creado con exito"));
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function TraerUno($request, $response, $args)
    {
        $parametros = $request->getQueryParams();
        
        $productoPedido = ProductoPedido::obtenerProductoPedidoPorId($parametros['id']);
        if($productoPedido != null)
        {
            $payload = json_encode($productoPedido);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "EL ID INGRESADO NO EXISTE"));
        }       
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = ProductoPedido::obtenerTodos();
        $payload = json_encode(array("Lista de Productos Pedidos" => $lista));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function TraerProductosPedidosPendientesPorIdUsuario($request, $response, $args)
    {
        $parametros = $request->getQueryParams();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        AutentificadorJWT::VerificarToken($token);
        $data = AutentificadorJWT::ObtenerData($token);
        $producto = ProductoPedido::obtenerProductoPedidoPendientesPorIdUsuarioEncargado($data->id, 'pendiente');
        if($producto != null)
        {
            $payload = json_encode($producto);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay productos pendientes"));
        }       
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function TraerProductosPedidosEnPreparacionPorIdUsuario($request, $response, $args)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        AutentificadorJWT::VerificarToken($token);
        $data = AutentificadorJWT::ObtenerData($token);
        $producto = ProductoPedido::obtenerProductoPedidoPendientesPorIdUsuarioEncargado($data->id, 'en preparacion');
        if($producto != null)
        {
            $payload = json_encode($producto);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay productos en preparacion"));
        }       
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();        
        $productoPedido = ProductoPedido::obtenerProductoPedidoPorId($parametros['id']);
        
        if($productoPedido != null) 
        {            
            $idProducto = $parametros['idProducto'];
            $idUsuario = $parametros['idUsuario'];
            $idPedido = $parametros['idPedido'];  
            $productoPedido->idUsuario = $idUsuario;
            $productoPedido->idProducto = $idProducto;
            $productoPedido->idPedido = $idPedido;
            ProductoPedido::modificarProductoPedido($productoPedido);
            $payload = json_encode(array("mensaje" => "Producto Pedido modificado con exito"));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "EL ID INGRESADO NO EXISTE Y NO SE PUEDE MODIFICAR"));
        }          
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function ModificarEstadoYTiempoPreparacion($request, $response, $args)
    {
        $parametros = $request->getParsedBody(); 
        $cookies = $request->getCookieParams();
        $token = $cookies['JWT'];
        AutentificadorJWT::VerificarToken($token);
        $datos = AutentificadorJWT::ObtenerData($token);       
        $estado = $parametros['estado'];
        $idProductoPedido = $parametros['idProductoPedido'];   
        $tiempoPreparacion = $parametros['tiempoPreparacion'];   
        $productoPedido = ProductoPedido::obtenerProductoPedidoPorId($idProductoPedido);

        if($productoPedido != null  && $productoPedido->idUsuarioEncargado == $datos->id)
        {                        
            $productoPedido->estado = $estado;
            $productoPedido->tiempoPreparacion = $tiempoPreparacion;
            ProductoPedido::modificarProductoPedido($productoPedido);
            $pedido = Pedido::obtenerPedidoPorId($productoPedido->idPedido);
            if($pedido != false)
            {
                if($pedido->horaEstimadaFinal == null)
                {
                    $intervalo = new DateInterval('PT'.$tiempoPreparacion.'M');
                    $horaInicio = new DateTime($pedido->horaInicio);
                    $pedido->horaEstimadaFinal = $horaInicio->add($intervalo);
                    Pedido::modificarPedido($pedido);
                }
                else
                {
                    $horaInicio = new DateTime($pedido->horaInicio); 
                    $horaEstimadaFinal = new DateTime($pedido->horaEstimadaFinal);
                    $diferencia = $horaEstimadaFinal->getTimestamp() - $horaInicio->getTimestamp();
                    if ($tiempoPreparacion > $diferencia / 60)
                    {
                        $intervalo = new DateInterval('PT'.$tiempoPreparacion.'M');
                        $horaEstimadaFinal = new DateTime($pedido->horaEstimadaFinal);
                        $pedido->horaEstimadaFinal = $horaEstimadaFinal->add($intervalo);
                        Pedido::modificarPedido($pedido);
                    }
                }
                $payload = json_encode(array("mensaje" => "El estado y el tiempo de preparacion del Producto Pedido fue modificado con exito"));                    
            }            
            else 
            { 
                $payload = json_encode(array("error" => "No se encontró el pedido correspondiente.")); 
            }
        }
        else if($producto=Producto::obtenerProductoPorId($productoPedido->idProducto) == null)
        {
            $payload = json_encode(array("mensaje" => "ID Producto Pedido ingresado invalido"));
        }
        else
        {
            $producto=Producto::obtenerProductoPorId($productoPedido->idProducto);
            $payload = json_encode(array("mensaje" => "El usuario $datos->apellido, $datos->nombre no es responsable del producto $producto->descripcion"));
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function ModificarEstado($request, $response, $args)
    {
        $parametros = $request->getParsedBody(); 
        $cookies = $request->getCookieParams();
        $token = $cookies['JWT'];
        AutentificadorJWT::VerificarToken($token);
        $datos = AutentificadorJWT::ObtenerData($token);       
        $estado = $parametros['estado'];
        $idProductoPedido = $parametros['idProductoPedido'];        
        $productoPedido = ProductoPedido::obtenerProductoPedidoPorId($idProductoPedido);

        if($productoPedido != null  && $productoPedido->idUsuarioEncargado == $datos->id)
        {                        
            $productoPedido->estado = $estado;
            ProductoPedido::modificarProductoPedido($productoPedido);
            $pedido = Pedido::obtenerPedidoPorId($productoPedido->idPedido);
            if($pedido != false)
            {
                $todosListos = true;
                $productosPedidos = ProductoPedido::obtenerIdPorIdPedido($pedido->id);
                foreach($productosPedidos as $productoPedido) 
                { 
                    if($productoPedido->estado !== 'listo para servir') 
                    { 
                        $todosListos = false; 
                        break; 
                    } 
                } 
                if($todosListos) 
                { 
                    $pedido->estado = 'listo para servir';                     
                    Pedido::modificarPedidoEstadoYHoraFinal($pedido); 
                }
                $payload = json_encode(array("mensaje" => "El estado del Producto Pedido fue modificado con exito"));
            }            
            else 
            { 
                $payload = json_encode(array("error" => "No se encontró el pedido correspondiente.")); 
            }
        }
        else if($producto=Producto::obtenerProductoPorId($productoPedido->idProducto) == null)
        {
            $payload = json_encode(array("mensaje" => "ID Producto Pedido ingresado invalido"));
        }
        else
        {
            $producto=Producto::obtenerProductoPorId($productoPedido->idProducto);
            $payload = json_encode(array("mensaje" => "El usuario $datos->apellido, $datos->nombre no es responsable del producto $producto->descripcion"));
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    
    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();        
        $productoPedido = ProductoPedido::obtenerProductoPedidoPorId($parametros['id']);

        if($productoPedido != null)
        {
            productoPedido::borrarProductoPedido($productoPedido);
            $payload = json_encode(array("mensaje" => "Producto Pedido fue borrado con exito"));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "ID INEXISTENTE Y NO SE PUEDE BORRAR"));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }    
}
?>