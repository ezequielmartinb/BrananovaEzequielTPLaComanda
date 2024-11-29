<?php


class ProductoPedido
{
    public $id;
    public $idProducto;
    public $idPedido;
    public $idUsuario;
   
    public function crearProductoPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productosPedidos (idProducto, idUsuario, idPedido) VALUES (:idProducto, :idUsuario, :idPedido)");        
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);        
        $consulta->bindValue(':idUsuario', $this->idUsuario, PDO::PARAM_INT);       
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_INT);      
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idProducto, idUsuario FROM productosPedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    }    
    public static function obtenerIdPorIdPedido($idPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idProducto, idPedido, idUsuario FROM productosPedidos WHERE idPedido = :idPedido");
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    }   
    public static function obtenerProductoPedidoPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idProducto, idPedido, idUsuario FROM productosPedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ProductoPedido');
    } 

    public static function modificarProductoPedido($productoPedido)
    {        
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productosPedidos SET idProducto = :idProducto, idPedido = :idPedido, idUsuario = :idUsuario WHERE id = :id");
        $consulta->bindValue(':id', $productoPedido->id, PDO::PARAM_INT);
        $consulta->bindValue(':idProducto', $productoPedido->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':idPedido', $productoPedido->idPedido, PDO::PARAM_STR);
        $consulta->bindValue(':idUsuario', $productoPedido->idUsuario, PDO::PARAM_INT);
        
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