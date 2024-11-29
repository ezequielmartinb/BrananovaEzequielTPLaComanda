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
    $tipo = $parametros['tipo'];
    $tiempoPreparacion = $parametros['tiempoPreparacion'];    
    
    $producto = new Producto();
    $producto->descripcion = $descripcion;
    $producto->precio = $precio;
    $producto->tipo = $tipo;
    $producto->tiempoPreparacion = $tiempoPreparacion;
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
  
  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    
    $producto = Producto::obtenerProductoPorId($parametros['id']);
    if($producto != null)
    {
      
      $producto->descripcion = $parametros['descripcion'];          
      $producto->precio = $parametros['precio'];    
      $producto->tipo = $parametros['tipo'];          
      $producto->tiempoPreparacion = $parametros['tiempoPreparacion'];               

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
