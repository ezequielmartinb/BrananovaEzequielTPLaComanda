<?php


class ProductoPedido
{
    public $id;
    public $idProducto;
    public $idPedido;
    public $idUsuarioEncargado;
    public $estado;
    public $tiempoPreparacion;
   
    public function crearProductoPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productosPedidos (idProducto, idUsuarioEncargado, idPedido, estado, tiempoPreparacion) VALUES (:idProducto, :idUsuarioEncargado, :idPedido, :estado, :tiempoPreparacion)");        
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);        
        $consulta->bindValue(':idUsuarioEncargado', $this->idUsuarioEncargado, PDO::PARAM_INT);       
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_INT);      
        $consulta->bindValue(':estado', "pendiente", PDO::PARAM_STR);      
        $consulta->bindValue(':tiempoPreparacion', 0, PDO::PARAM_INT);      
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idProducto, idPedido, idUsuarioEncargado, estado, tiempoPreparacion FROM productosPedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    }    
    public static function obtenerIdPorIdPedido($idPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idProducto, idPedido, idUsuarioEncargado, estado, tiempoPreparacion FROM productosPedidos WHERE idPedido = :idPedido");
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    }   
    public static function obtenerProductoPedidoPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idProducto, idPedido, idUsuarioEncargado, estado, tiempoPreparacion FROM productosPedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('ProductoPedido');
    } 
    public static function obtenerProductoPedidoPoridUsuarioEncargado($idUsuarioEncargado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idProducto, idPedido, idUsuarioEncargado, estado, tiempoPreparacion FROM productosPedidos WHERE idUsuarioEncargado = :idUsuarioEncargado");
        $consulta->bindValue(':idUsuarioEncargado', $idUsuarioEncargado, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    } 
    public static function obtenerProductoPedidoPendientesPoridUsuarioEncargado($idUsuarioEncargado, $estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idProducto, idPedido, idUsuarioEncargado, estado, tiempoPreparacion FROM productosPedidos WHERE idUsuarioEncargado = :idUsuarioEncargado AND estado = :estado");
        $consulta->bindValue(':idUsuarioEncargado', $idUsuarioEncargado, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    } 
    public static function obtenerProductosPedidosPorPuesto($puesto, $estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT productos.id, productos.descripcion, usuarios.nombre, usuarios.apellido, productospedidos.estado FROM `productospedidos`, `usuarios`, `productos` WHERE productospedidos.idProducto = productos.id AND usuarios.id = productospedidos.idUsuarioEncargado AND usuarios.puesto = :puesto AND productospedidos.estado = :estado");
        $consulta->bindValue(':puesto', $puesto, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function modificarProductoPedido($productoPedido)
    {        
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productosPedidos SET idProducto = :idProducto, idPedido = :idPedido, idUsuarioEncargado = :idUsuarioEncargado, estado = :estado, tiempoPreparacion = :tiempoPreparacion WHERE id = :id");
        $consulta->bindValue(':id', $productoPedido->id, PDO::PARAM_INT);
        $consulta->bindValue(':idProducto', $productoPedido->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':idPedido', $productoPedido->idPedido, PDO::PARAM_STR);
        $consulta->bindValue(':idUsuarioEncargado', $productoPedido->idUsuarioEncargado, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $productoPedido->estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoPreparacion', $productoPedido->tiempoPreparacion, PDO::PARAM_INT);
        
        $consulta->execute();        
    }
    public static function borrarProductoPedido($productoPedido) 
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE pedido WHERE id = :id");
        $consulta->bindValue(':id', $productoPedido->idPedido, PDO::PARAM_INT);
        $consulta->execute();
    }
}

?>