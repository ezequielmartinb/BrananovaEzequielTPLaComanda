<?php

class Mesa
{
    public $id;
    public $idPedido;
    public $codigoMesa;
    public $idMozoAsignado;
    public $estado;
    public $recaudacion;

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (codigoMesa, idMozoAsignado, estado, recaudacion) VALUES (:codigoMesa, :idMozoAsignado, :estado, :recaudacion)");
        $codigoMesa = Mesa::obtenerCodigoMesa();
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':idMozoAsignado', $this->idMozoAsignado, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':recaudacion', $this->recaudacion, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idPedido, codigoMesa, idMozoAsignado, estado, recaudacion FROM Mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerMesaPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idPedido, codigoMesa, idMozoAsignado, estado, recaudacion  FROM Mesas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }
    public static function obtenerMesaPorIdPedido($idPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT mesas.id, mesas.idPedido, mesas.codigoMesa, mesas.idMozoAsignado, mesas.estado, mesas.recaudacion  FROM Mesas, Pedidos WHERE pedidos.id = :idPedido AND pedidos.id = mesas.idPedido");
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }
    public static function obtenerMesaPorIdMozoAsignado($idMozoAsignado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idPedido, codigoMesa, idMozoAsignado, estado, recaudacion FROM Mesas WHERE idMozoAsignado = :idMozoAsignado");
        $consulta->bindValue(':idMozoAsignado', $idMozoAsignado, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }
    public static function obtenerMesaPorCodigoMesa($codigoMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idPedido, codigoMesa, idMozoAsignado, estado, recaudacion FROM Mesas WHERE codigoMesa = :codigoMesa");
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }
    public static function obtenerTiempoEstimadoPorCodigoMesa($codigoMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $mesa = Mesa::obtenerMesaPorCodigoMesa($codigoMesa);
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidos.tiempoEstimado FROM `mesas`, `pedidos` WHERE :idPedido = pedidos.id");
        $consulta->bindValue(':idPedido', $mesa->idPedido, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll();
    }
    public static function obtenerPorEstadoPedido($estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT mesas.id, mesas.idPedido, mesas.codigoMesa, mesas.idMozoAsignado, mesas.estado FROM mesas, pedidos WHERE pedidos.estado = :estado");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }
    public static function obtenerMesaMasUsada()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT mesas.codigoMesa, COUNT(*) AS Cantidad_De_Usos FROM mesas, pedidos WHERE mesas.codigoMesa = pedidos.codigoMesa GROUP BY codigoMesa ORDER BY Cantidad_De_Usos DESC LIMIT 1");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS);
    }


    public function modificarMesa()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE Mesas SET idPedido = :idPedido, codigoMesa = :codigoMesa, idMozoAsignado = :idMozoAsignado, estado = :estado, recaudacion = :recaudacion WHERE id = :id");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':idMozoAsignado', $this->idMozoAsignado, PDO::PARAM_INT);
        $consulta->bindValue(':recaudacion', $this->recaudacion, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();
    }    

    public static function borrarMesa($mesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE Mesas SET estado = :estado WHERE id = :id");
        $consulta->bindValue(':id', $mesa->id, PDO::PARAM_INT);
        $consulta->bindValue(':estado', 'cerrada', PDO::PARAM_STR);
        $consulta->execute();
    }
    public static function obtenerCodigoMesa() 
    {        
        $letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numeros = '0123456789';

        $codigoAlfanumerico = $letras[rand(0, strlen($letras) - 1)];
        $codigoAlfanumerico .= $letras[rand(0, strlen($letras) - 1)];
        for ($i = 2; $i < 4; $i++) 
        {
            $codigoAlfanumerico .= $numeros[rand(0, strlen($numeros) - 1)];
        }
    
        $codigoAlfanumerico = str_shuffle($codigoAlfanumerico);
    
        return $codigoAlfanumerico;
    }
}



















?>