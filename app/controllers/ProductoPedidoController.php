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