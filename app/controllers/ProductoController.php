<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $descripcion = $parametros['descripcion'];
    $precio = $parametros['precio'];
    $sector = $parametros['sector'];
    
    $producto = new Producto();
    $producto->descripcion = $descripcion;
    $producto->precio = $precio;
    $producto->sector = $sector;
    $producto->crearProducto();

    $payload = json_encode(array("mensaje" => "Producto creado con exito"));     

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    $parametros = $request->getQueryParams();
    
    $id = $parametros['id'];          
    $producto = Producto::obtenerProductoPorId($id);
    if($producto != null)
    {
      $payload = json_encode($producto);
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
      $lista = Producto::obtenerTodos();
      $payload = json_encode(array("listaProducto" => $lista));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
  }
  public function TraerProductosNoEntregadosEnElTiempoEstipulado($request, $response, $args)
    {
        $pedidos = Pedido::obtenerTodos();
        $productosNoEntregadosEnElTiempoEstipulado = [];
        foreach($pedidos as $pedido)
        {
            if($pedido->horaEstimadaFinal != $pedido->horaFinal)
            {
              $productosPedidos = ProductoPedido::obtenerIdPorIdPedido($pedido->id);
              foreach($productosPedidos as $productoPedido)
              {
                $producto = Producto::obtenerProductoPorId($productoPedido->idProducto);
                array_push($productosNoEntregadosEnElTiempoEstipulado, $producto);
              }
            }
        }
        $payload = json_encode(array("Lista de Productos no entregados en el tiempo estipulado" => $productosNoEntregadosEnElTiempoEstipulado));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
  
  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    
    $producto = Producto::obtenerProductoPorId($parametros['id']);
    if($producto != null)
    {
      
      $producto->descripcion = $parametros['descripcion'];          
      $producto->precio = $parametros['precio'];    
      $producto->sector = $parametros['sector'];          

      Producto::modificarProducto($producto);

      $payload = json_encode(array("mensaje" => "Producto modificado con exito"));
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
    
    $producto = Producto::obtenerProductoPorId($parametros['id']);
    if($producto != null)
    {
      Producto::borrarProducto($producto);
      $payload = json_encode(array("mensaje" => "Producto borrado con exito"));
    }       
    else
    {
      $payload = json_encode(array("mensaje" => "ID INEXISTENTE Y NO SE PUEDE BORRAR"));
    }

    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}
