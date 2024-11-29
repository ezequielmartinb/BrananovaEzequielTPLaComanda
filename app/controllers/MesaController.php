<?php
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
    
    $mesa = Mesa::obtenerPorEstadoPedido($estadoPedido);
    if($mesa != null)
    {
      $pedido = Pedido::obtenerPedidoPorId($mesa->idPedido);
      $pedido->estado = $parametros['estadoActualizado'];
      Pedido::modificarPedido($pedido);
      $mesa->estado = 'comiendo';
      Mesa::modificarMesa($mesa);

      $payload = json_encode(array("mensaje" => "Mesa modificado con exito"));
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
    
    $mesa = Mesa::obtenerMesaPorId($parametros['id']);
    
    if($mesa != null && ($mesa->estado == 'pendiente' || $mesa->estado == 'pagando'))
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