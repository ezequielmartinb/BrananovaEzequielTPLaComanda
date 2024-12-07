<?php
require_once 'Producto.php';
class Pedido
{
    public $id;
    public $nombreCliente;
    public $fotoCliente;
    public $codigoPedido;
    public $codigoMesa;
    public $estado;
    public $horaInicio;
    public $horaFinal;
    public $horaEstimadaFinal;
    
    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigoPedido, codigoMesa, horaEstimadaFinal, nombreCliente, estado, horaInicio, horaFinal) 
        VALUES (:codigoPedido, :codigoMesa, :horaEstimadaFinal, :nombreCliente, :estado, :horaInicio, :horaFinal)");
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $horaInicio = new DateTime();
        $codigoPedido = Pedido::obtenerCodigoPedido();
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':estado', 'pendiente', PDO::PARAM_STR);                   
        $consulta->bindValue(':horaInicio', $horaInicio->format('Y-m-d H:i:s'), PDO::PARAM_STR);   
        $consulta->bindValue(':horaFinal', null, PDO::PARAM_STR);       
        $consulta->bindValue(':horaEstimadaFinal', null, PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigoPedido, codigoMesa, horaEstimadaFinal, nombreCliente, fotoCliente, estado, horaInicio, horaFinal FROM pedidos");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }   

    public static function obtenerPedidoPorCodigoPedido($codigoPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigoPedido, codigoMesa, horaEstimadaFinal, nombreCliente, fotoCliente, estado, horaInicio, horaFinal FROM pedidos WHERE codigoPedido = :codigoPedido");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject('Pedido');
    }
    public static function obtenerPedidoPorCodigoMesa($codigoMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigoPedido, codigoMesa, horaEstimadaFinal, nombreCliente, fotoCliente, estado, horaInicio, horaFinal FROM pedidos WHERE codigoMesa = :codigoMesa");
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject('Pedido');
    }
    public static function obtenerPedidoPorCodigoPedidoYCodigoMesa($codigoPedido, $codigoMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigoPedido, codigoMesa, horaEstimadaFinal, nombreCliente, fotoCliente, estado, horaInicio, horaFinal FROM pedidos WHERE codigoPedido = :codigoPedido AND codigoMesa = :codigoMesa");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject('Pedido');
    }

    public static function obtenerPedidoPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigoPedido, codigoMesa, horaEstimadaFinal, nombreCliente, fotoCliente, estado, horaInicio, horaFinal FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchObject('Pedido');
    }
    public static function obtenerPedidoPorEstado($estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigoPedido, codigoMesa, horaEstimadaFinal, nombreCliente, fotoCliente, estado, horaInicio, horaFinal FROM pedidos WHERE estado = :estado");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject('Pedido');
    }
    public static function asignarImagenAlPedido($rutaImagen, $codigoPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET fotoCliente = :fotoCliente WHERE codigoPedido = :codigoPedido");

        $consulta->bindValue(':fotoCliente', $rutaImagen, PDO::PARAM_STR);
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_INT);

        $consulta->execute();
    }
    public static function guardarYAsociarImagen($imagen, $codigoPedido)
    {

        $carpetaDestino = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'fotosClientes' . DIRECTORY_SEPARATOR;
        $nombreImagen = $codigoPedido . ".jpg";
        $rutaImagen = $carpetaDestino . $nombreImagen;

        if (!is_dir($carpetaDestino)) 
        {
            mkdir($carpetaDestino, 0777, true);
        }

        $fileStream = $imagen->getStream();

        $fileStream->seek(0);
        file_put_contents($rutaImagen, $fileStream->getContents());

        Pedido::asignarImagenAlPedido($rutaImagen, $codigoPedido);        
    }

    public static function modificarPedido($pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET codigoPedido = :codigoPedido, nombreCliente = :nombreCliente, estado = :estado, horaEstimadaFinal = :horaEstimadaFinal WHERE id = :id");
        $consulta->bindValue(':id', $pedido->id, PDO::PARAM_INT);
        $consulta->bindValue(':codigoPedido', $pedido->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':horaEstimadaFinal', $pedido->horaEstimadaFinal, PDO::PARAM_STR);
        $consulta->bindValue(':nombreCliente', $pedido->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $pedido->estado, PDO::PARAM_STR);
        $consulta->execute();
    }
    public static function modificarPedidoEstado($pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = :estado, horaFinal = :horaFinal WHERE id = :id");
        $consulta->bindValue(':id', $pedido->id, PDO::PARAM_INT);        
        $consulta->bindValue(':estado', $pedido->estado, PDO::PARAM_STR);
        $consulta->execute();
    }
    public static function modificarPedidoTiempoPreparacion($pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET codigoPedido = :codigoPedido, nombreCliente = :nombreCliente, estado = :estado, horaEstimadaFinal = :horaEstimadaFinal WHERE id = :id");
        $consulta->bindValue(':id', $pedido->id, PDO::PARAM_INT);
        $consulta->bindValue(':codigoPedido', $pedido->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':horaEstimadaFinal', $pedido->horaEstimadaFinal, PDO::PARAM_STR);
        $consulta->bindValue(':nombreCliente', $pedido->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $pedido->estado, PDO::PARAM_STR);
        $consulta->execute();
    }
    
    public static function obtenerPedidoPorPuesto($puesto, $estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT productos.id, productos.descripcion, pedidos.codigoPedido, pedidos.estado, usuarios.puesto, usuarios.nombre, usuarios.apellido FROM `pedidos`, `usuarios`, `productospedidos`, `productos` WHERE productospedidos.idProducto = productos.id AND usuarios.id = productospedidos.idUsuario AND pedidos.id = productospedidos.idPedido AND usuarios.puesto = :puesto AND pedidos.estado = :estado");
        $consulta->bindValue(':puesto', $puesto, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function borrarPedido($pedido) 
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = :estado, horaFinal = :horaFinal WHERE id = :id");
        $fecha = new DateTime(date('Y-m-d'));
        $consulta->bindValue(':id', $pedido->id, PDO::PARAM_INT);
        $consulta->bindValue(':estado', 'cancelado', PDO::PARAM_STR);
        $consulta->bindValue(':horaFinal', date_format($fecha,'Y-m-d'), PDO::PARAM_STR);   
        $consulta->execute();
    } 

    public static function obtenerCodigoPedido() 
    {        
        $letras = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numeros = '0123456789';

        $codigoAlfanumerico = $letras[rand(0, strlen($letras) - 1)];
        for ($i = 2; $i < 6; $i++) 
        {
            $caracteres = rand(0, 1) ? $letras : $numeros;
            $codigoAlfanumerico .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }

        $codigoAlfanumerico = str_shuffle($codigoAlfanumerico);

        return $codigoAlfanumerico;
    }
}
?>