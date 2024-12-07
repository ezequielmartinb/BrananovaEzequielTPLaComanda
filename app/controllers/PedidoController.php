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
        $codigoMesa = $parametros['codigoMesa'];
        $pedido = new Pedido();
        $pedido->nombreCliente = $nombreCliente;
        $pedido->codigoMesa = $codigoMesa;
        $pedido->codigoPedido = Pedido::obtenerCodigoPedido();
        $pedido->crearPedido();
        $pedidoCreado = Pedido::obtenerPedidoPorCodigoPedido($pedido->codigoPedido);
        $mesa = Mesa::obtenerMesaPorCodigoMesa($codigoMesa);
        $mesa->idPedido = $pedidoCreado->id;
        $mesa->estado = 'esperando';
        $mesa->modificarMesa();
        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));        
        
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
        $lista = Pedido::obtenerPedidoPorEstado("pendiente");
        $payload = json_encode(array("listaPedidos" => $lista));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function TraerTiempoEstimadoPorCodigoMesayPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $pedido = Pedido::obtenerPedidoPorCodigoPedidoYCodigoMesa($parametros['codigoPedido'], $parametros['codigoMesa']);
        $tiempoEstimado = new DateTime($pedido->horaEstimadaFinal);
        $payload = json_encode(array("Horario de entrega" => $tiempoEstimado->format('H:i:s')));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function TraerPedidosNoEntregadosEnElTiempoEstipulado($request, $response, $args)
    {
        $pedidos = Pedido::obtenerTodos();
        $pedidosNoEntregadosEnElTiempoEstipulado = [];
        foreach($pedidos as $pedido)
        {
            if($pedido->horaEstimadaFinal != $pedido->horaFinal)
            {
                array_push($pedidosNoEntregadosEnElTiempoEstipulado, $pedido);
            }
        }
        $payload = json_encode(array("Lista de Pedidos no entregados en el tiempo estipulado" => $pedidosNoEntregadosEnElTiempoEstipulado));

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
    public function CambiarEstadoPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $pedido = Pedido::obtenerPedidoPorId($parametros['id']);
        $estado = $parametros['estado'];
        
        if($pedido != null && $pedido->estado != 'listo para servir') 
        {            
            $productosPedidos = ProductoPedido::obtenerIdPorIdPedido($pedido->id);
            $pedido->estado = $estado;   
            foreach($productosPedidos as $productoPedido)
            {
                $productoPedido->estado = $estado;
                ProductoPedido::modificarProductoPedido($productoPedido);
            }
            Pedido::modificarPedido($pedido);
            $payload = json_encode(array("mensaje" => "Pedido se encuentra en $estado"));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "No hay pedidos pendientes"));
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
    public function AsociarFotoConPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody(); 
        $uploadedFiles = $request->getUploadedFiles();

        $imagen = $uploadedFiles['fotoCliente'];
        $codigoPedido = $parametros['codigoPedido'];

        $pedido = Pedido::obtenerPedidoPorCodigoPedido($codigoPedido);
        $mesa = Mesa::obtenerMesaPorCodigoMesa($pedido->codigoMesa);
        $mesa->idPedido = $pedido->id;
        $mesa->modificarMesa();


        Pedido::guardarYAsociarImagen($imagen, $codigoPedido);

        $payload = json_encode(array("mensaje" => "Imagen asociada correctamente"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>