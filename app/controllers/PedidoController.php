<?php
require_once './models/Pedido.php';
require_once './models/Producto.php';
require_once './models/ProductoPedido.php';

require_once './interfaces/IApiUsable.php';
class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody(); 
        
        $nombreCliente = $parametros['nombreCliente'];
        $tiempoEstimado = 0;
        $codigoMesa = $parametros['codigoMesa'];
        $pedido = new Pedido();
        $pedido->nombreCliente = $nombreCliente;
        $pedido->tiempoEstimado = $tiempoEstimado;
        $pedido->codigoMesa = $codigoMesa;
        $pedido->crearPedido();
        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));
        $pedidoDB = Pedido::obtenerPedidoPorCodigoMesa($codigoMesa);
        var_dump($pedidoDB);
        $mesa = Mesa::obtenerMesaPorCodigoMesa($codigoMesa);
        $mesa->idPedido = $pedidoDB[0]->id;
        $mesa->modificarMesa();
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function TraerUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $pedido = Pedido::obtenerPedidoPorId($parametros['id']);
        if($pedido!=null)
        {
            $payload = json_encode($pedido);
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
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedidos" => $lista));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function TraerPedidosPorPuestoYEstadoEnPreparacion($request, $response, $args)
    {
        $parametros = $request->getQueryParams();
        
        $pedido = Pedido::obtenerPedidoPorPuesto($parametros['puesto'], 'en preparacion');
        if($pedido != null)
        {
            $payload = json_encode($pedido);
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
        
        $pedido = Pedido::obtenerPedidoPorId($parametros['id']);
        
        if($pedido != null) 
        {            
            $pedido->codigoPedido = $parametros['codigoPedido'];
            $pedido->nombreCliente = $parametros['nombreCliente'];
            $pedido->estado = $parametros['estado'];            
            $pedido->tiempoEstimado = $parametros['tiempoEstimado'];          
            Pedido::modificarPedido($pedido);  
            $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "EL ID INGRESADO NO EXISTE Y NO SE PUEDE MODIFICAR"));
        }          
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }    
    public function ModificarEstadoPedidoYTiempoPreparacion($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $pedido = Pedido::obtenerPedidoPorId($parametros['id']);
        
        if($pedido != null) 
        {            
            $pedido->estado = "en preparacion";   
            Pedido::modificarPedido($pedido);          
            $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "EL ID INGRESADO NO EXISTE Y NO SE PUEDE MODIFICAR"));
        }          
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }  
    public function ModificarEstadoPedidoListoParaServir($request, $response, $args)
    {
        $pedido = Pedido::obtenerPedidoPorEstado("en preparacion");        
        if($pedido != null) 
        {            
            $pedido->estado = "listo para servir";   
            Pedido::modificarPedido($pedido);          
            $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
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
        $pedido = Pedido::obtenerPedidoPorId($parametros['id']);

        if($pedido != null)
        {
            Pedido::borrarPedido($pedido);
            $payload = json_encode(array("mensaje" => "Pedido fue borrado con exito"));
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