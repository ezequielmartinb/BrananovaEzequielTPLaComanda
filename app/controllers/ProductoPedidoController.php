<?php
require_once './models/Pedido.php';
require_once './models/Producto.php';
require_once './models/ProductoPedido.php';

require_once './interfaces/IApiUsable.php';
class ProductoPedidoController extends ProductoPedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody(); 
        $idProducto = $parametros['idProducto'];
        $idUsuario = $parametros['idUsuario'];
        $idPedido = $parametros['idPedido'];        
        $productoPedido = new ProductoPedido();
        $productoPedido->idProducto=$idProducto;
        $productoPedido->idUsuario=$idUsuario;
        $productoPedido->idPedido=$idPedido;
        $productoPedido->crearProductoPedido();
        $pedido = Pedido::obtenerPedidoPorId($idPedido);
        $producto = Producto::obtenerProductoPorId($idProducto);
        if($producto->tiempoPreparacion > $pedido->tiempoEstimado)
        {
            $pedido->tiempoEstimado = $pedido->tiempoEstimado + $producto->tiempoPreparacion;        
            Pedido::modificarPedido($pedido);
        }
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
    public function TraerProductosPedidosPendientesPorPuesto($request, $response, $args)
    {
        $parametros = $request->getQueryParams();
        
        $producto = ProductoPedido::obtenerProductosPorPuesto($parametros['puesto'], 'pendiente');
        if($producto != null)
        {
            $payload = json_encode($producto);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "EL PUESTO NO EXISTE"));
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
    public function ModificarEstado($request, $response, $args)
    {
        $parametros = $request->getParsedBody(); 
        $cookies = $request->getCookieParams();
        $token = $cookies['JWT'];
        AutentificadorJWT::VerificarToken($token);
        $datos = AutentificadorJWT::ObtenerData($token);       
        $productosPedidos = ProductoPedido::obtenerProductoPedidoPorIdUsuario($datos->id);
        if($productosPedidos != null && count($productosPedidos) > 0) 
        {            
            foreach($productosPedidos as $productoPedido)
            {
                if($productoPedido->idUsuario == $datos->id)
                {
                    $estado = $parametros['estado'];           
                    $productoPedido->estado = $estado;
                    ProductoPedido::modificarProductoPedido($productoPedido);
                }
            }
            $payload = json_encode(array("mensaje" => "El estado del Producto Pedido fue modificado con exito"));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "EL ID INGRESADO NO EXISTE Y NO SE PUEDE MODIFICAR"));
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