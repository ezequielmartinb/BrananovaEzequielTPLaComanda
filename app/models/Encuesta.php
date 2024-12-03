<?php

class Encuesta
{
    public $id;
    public $idMesa;
    public $idPedido;
    public $puntosMesa;
    public $puntosRestaurante;
    public $puntosMozo;
    public $puntosCocinero;
    public $comentario;
    public $fecha;

    public function crearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas (idMesa, idPedido, puntosMesa, puntosRestaurante, puntosMozo, puntosCocinero, comentario, fecha) 
        VALUES (:idMesa, :idPedido, :puntosMesa, :puntosRestaurante, :puntosMozo, :puntosCocinero, :comentario, :fecha)");
        $fecha = new DateTime(date('Y-m-d'));
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_STR);
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_STR);
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
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idMesa, idPedido, puntosMesa, puntosRestaurante, puntosMozo, puntosCocinero, comentario, fecha FROM encuestas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }  
    public static function obtenerEncuestaPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idMesa, idPedido, puntosMesa, puntosRestaurante, puntosMozo, puntosCocinero, comentario, fecha FROM encuestas 
        WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Encuesta');
    }
    
       
    public static function modificarEncuesta($encuesta)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET idMesa = :idMesa, idPedido = :idPedido, puntosMesa = :puntosMesa, puntosRestaurante = :puntosRestaurante, 
        puntosMozo = :puntosMozo, puntosCocinero = :puntosCocinero, comentario = :comentario   WHERE id = :id");
        $consulta->bindValue(':idMesa', $encuesta->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':idPedido', $encuesta->idPedido, PDO::PARAM_INT);
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