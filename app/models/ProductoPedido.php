<?php


class ProductoPedido
{
    public $id;
    public $idProducto;
    public $idPedido;
    public $idUsuario;
    public $estado;
   
    public function crearProductoPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productosPedidos (idProducto, idUsuario, idPedido, estado) VALUES (:idProducto, :idUsuario, :idPedido, :estado)");        
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);        
        $consulta->bindValue(':idUsuario', $this->idUsuario, PDO::PARAM_INT);       
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_INT);      
        $consulta->bindValue(':estado', "pendiente", PDO::PARAM_STR);      
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idProducto, idPedido, idUsuario, estado FROM productosPedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    }    
    public static function obtenerIdPorIdPedido($idPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idProducto, idPedido, idUsuario, estado FROM productosPedidos WHERE idPedido = :idPedido");
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    }   
    public static function obtenerProductoPedidoPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idProducto, idPedido, idUsuario, estado FROM productosPedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    } 
    public static function obtenerProductoPedidoPorIdUsuario($idUsuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idProducto, idPedido, idUsuario, estado FROM productosPedidos WHERE idUsuario = :idUsuario");
        $consulta->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    } 
    public static function obtenerProductosPorPuesto($puesto, $estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT productos.id, productos.descripcion, usuarios.nombre, usuarios.apellido, productospedidos.estado FROM `productospedidos`, `usuarios`, `productos` WHERE productospedidos.idProducto = productos.id AND usuarios.id = productospedidos.idUsuario AND usuarios.puesto = :puesto AND productospedidos.estado = :estado");
        $consulta->bindValue(':puesto', $puesto, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function modificarProductoPedido($productoPedido)
    {        
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productosPedidos SET idProducto = :idProducto, idPedido = :idPedido, idUsuario = :idUsuario, estado = :estado WHERE id = :id");
        $consulta->bindValue(':id', $productoPedido->id, PDO::PARAM_INT);
        $consulta->bindValue(':idProducto', $productoPedido->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':idPedido', $productoPedido->idPedido, PDO::PARAM_STR);
        $consulta->bindValue(':idUsuario', $productoPedido->idUsuario, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $productoPedido->estado, PDO::PARAM_STR);
        
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