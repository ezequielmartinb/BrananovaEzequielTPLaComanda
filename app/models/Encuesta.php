<?php

class Encuesta
{
    public $id;
    public $codigoMesa;
    public $codigoPedido;
    public $puntosMesa;
    public $puntosRestaurante;
    public $puntosMozo;
    public $puntosCocinero;
    public $comentario;
    public $fecha;

    public function crearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas (codigoMesa, codigoPedido, puntosMesa, puntosRestaurante, puntosMozo, puntosCocinero, comentario, fecha) 
        VALUES (:codigoMesa, :codigoPedido, :puntosMesa, :puntosRestaurante, :puntosMozo, :puntosCocinero, :comentario, :fecha)");
        $fecha = new DateTime(date('Y-m-d'));
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':puntosMesa', $this->puntosMesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntosRestaurante', $this->puntosRestaurante, PDO::PARAM_INT);
        $consulta->bindValue(':puntosMozo', $this->puntosMozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntosCocinero', $this->puntosCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':comentario', $this->comentario, PDO::PARAM_STR);
        $consulta->bindValue(':fecha', date_format($fecha,'Y-m-d'), PDO::PARAM_STR);   
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigoMesa, codigoPedido, puntosMesa, puntosRestaurante, puntosMozo, puntosCocinero, comentario, fecha FROM encuestas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }  
    public static function obtenerEncuestaPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigoMesa, codigoPedido, puntosMesa, puntosRestaurante, puntosMozo, puntosCocinero, comentario, fecha FROM encuestas 
        WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Encuesta');
    }
    public static function obtenerEncuestaPorCodigoPedido($codigoPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigoMesa, codigoPedido, puntosMesa, puntosRestaurante, puntosMozo, puntosCocinero, comentario, fecha FROM encuestas 
        WHERE codigoPedido = :codigoPedido");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Encuesta');
    }
    public static function obtenerEncuestaPorCodigoMesa($codigoMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigoMesa, codigoPedido, puntosMesa, puntosRestaurante, puntosMozo, puntosCocinero, comentario, fecha FROM encuestas 
        WHERE codigoMesa = :codigoMesa");
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Encuesta');
    }
    
    
       
    public static function modificarEncuesta($encuesta)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET codigoMesa = :codigoMesa, codigoPedido = :codigoPedido, puntosMesa = :puntosMesa, puntosRestaurante = :puntosRestaurante, 
        puntosMozo = :puntosMozo, puntosCocinero = :puntosCocinero, comentario = :comentario   WHERE id = :id");
        $consulta->bindValue(':codigoMesa', $encuesta->codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':codigoPedido', $encuesta->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':puntosMesa', $encuesta->puntosMesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntosRestaurante', $encuesta->puntosRestaurante, PDO::PARAM_INT);
        $consulta->bindValue(':puntosMozo', $encuesta->puntosMozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntosCocinero', $encuesta->puntosCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':comentario', $encuesta->comentario, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function borrarEncuesta($encuesta)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE encuestas WHERE id = :id");
        $consulta->bindValue(':id', $encuesta->id, PDO::PARAM_INT);
        
        $consulta->execute();
    }
    
}