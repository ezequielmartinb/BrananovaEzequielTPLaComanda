<?php

use Symfony\Component\Console\Output\NullOutput;

require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();        
    
    $estado = $parametros['estado'];
    $idMozoAsignado = $parametros['idMozoAsignado'];

    $mesa = new Mesa();
    $mesa->idMozoAsignado = $idMozoAsignado;
    $mesa->estado = $estado;
    $mesa->recaudacion = 0;
    $mesa->crearMesa();
    $payload = json_encode(array("mensaje" => "Mesa creado con exito"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    $parametros = $request->getQueryParams();
    
    $id = $parametros['id'];          
    $mesa = mesa::obtenerMesaPorId($id);
    if($mesa!=null)
    {
      $payload = json_encode($mesa);
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
    $lista = Mesa::obtenerTodos();
    $payload = json_encode(array("listaMesa" => $lista));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
  public function TraerMesaMasUsada($request, $response, $args)
  {
    $payload = json_encode(array("Mesa Mas Usada" => Mesa::obtenerMesaMasUsada()));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
  
  public function TraerTiempoEstimadoPorCodigoMesa($request, $response, $args)
  {
    $parametros = $request->getQueryParams();
    $lista = Mesa::obtenerTiempoEstimadoPorCodigoMesa($parametros['codigoMesa']);     
    $payload = json_encode(array("Tiempo de espera" => $lista[0]['tiempoEstimado'] . ' minutos'));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
  
  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    
    $mesa = Mesa::obtenerMesaPorId($parametros['id']);
    if($mesa != null)
    {
      $mesa->idPedido = $parametros['idPedido'];
      $mesa->codigoMesa = $parametros['codigoMesa'];
      $mesa->idMozoAsignado = $parametros['idMozoAsignado'];
      $mesa->estado = $parametros['estado'];
      $mesa->modificarMesa();

      $payload = json_encode(array("mensaje" => "Mesa modificado con exito"));
    }
    else
    {
      $payload = json_encode(array("mensaje" => "EL ID INGRESADO NO EXISTE Y NO SE PUEDE MODIFICAR"));
    }         
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  } 

  public function ModificarEstadoMesa($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $estadoPedido = $parametros['estadoPedido'];
    $estadoActualizado = $parametros['estadoActualizado'];
    
    $mesa = Mesa::obtenerPorEstadoPedido($estadoPedido);
    if($mesa != null)
    {
      $pedido = Pedido::obtenerPedidoPorId($mesa->idPedido);
      $productosPedidos = ProductoPedido::obtenerTodos();
      
      foreach($productosPedidos as $productoPedido)
      {
        if($productoPedido->estado == "listo para servir" && $productoPedido->idPedido == $mesa->idPedido)
        {
          $productoPedido->estado = "entregado";
          ProductoPedido::modificarProductoPedido($productoPedido);
        }
      }
      $pedido->estado = $parametros['estadoActualizado'];
      Pedido::modificarPedido($pedido);
      $mesa->estado = $estadoActualizado;
      $mesa->modificarMesa();

      $payload = json_encode(array("mensaje" => "Mesa se modifico al estado $estadoActualizado"));
    }
    else
    {
      $payload = json_encode(array("mensaje" => "EL ID INGRESADO NO EXISTE Y NO SE PUEDE MODIFICAR"));
    }         
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  } 
  public function CobrarMesa($request, $response, $args)
  {
    $parametros = $request->getParsedBody();        
    
    $codigoMesa = $parametros['codigoMesa'];
    $mesa = Mesa::obtenerMesaPorCodigoMesa($codigoMesa);
    $pedido = Pedido::obtenerPedidoPorCodigoMesa($codigoMesa);
    if($mesa != null && $pedido != null && $mesa->estado == 'comiendo')
    {      
      $productosPedidos = ProductoPedido::obtenerIdPorIdPedido($pedido[0]->id);

      $acumuladorPrecioMesa = 0;
      foreach($productosPedidos as $productoPedido)
      {
        $producto = Producto::obtenerProductoPorId($productoPedido->idProducto);

        $acumuladorPrecioMesa = $acumuladorPrecioMesa + $producto[0]->precio;
      }
      $mesa->recaudacion = $acumuladorPrecioMesa;
      $mesa->estado = "disponible";
      $mesa->idPedido = null;
      $mesa->modificarMesa();
      $pedido[0]->estado = "cobrado";      
      Pedido::modificarPedidoEstado($pedido[0]->estado, $pedido[0]->id);
      $payload = json_encode(array("mensaje" => "La mesa $codigoMesa fue cobrada con exito. La suma fue de $ $acumuladorPrecioMesa"));
    }
    else
    {
      $payload = json_encode(array("mensaje" => "Codigo de Mesa Invalido"));
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    
    $mesa = Mesa::obtenerMesaPorCodigoMesa($parametros['codigoMesa']);
    
    if($mesa != null && ($mesa->idPedido == null || $mesa->estado == 'disponible'))
    {
      Mesa::borrarMesa($mesa);
      $payload = json_encode(array("mensaje" => "Mesa cerrada con exito"));
    }       
    else
    {
      $payload = json_encode(array("mensaje" => "ID INEXISTENTE Y NO SE PUEDE CERRAR"));
    }

    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}
