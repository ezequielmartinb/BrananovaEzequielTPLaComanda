<?php

class Usuario
{
    public $id;
    public $nombre;
    public $apellido;
    public $mail;
    public $clave;
    public $puesto;
    public $estado;
    public $fechaInicio;
    public $fechaBaja;

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (nombre, apellido, mail, clave, puesto, estado, fechaInicio, fechaBaja) VALUES (:nombre, :apellido, :mail, :clave, :puesto, :estado, :fechaInicio, :fechaBaja)");
        $this->clave="";
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $this->clave = substr($claveHash, 0, 8);
        $fecha = new DateTime(date('Y-m-d'));
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':puesto', $this->puesto, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':fechaInicio', date_format($fecha,'Y-m-d'), PDO::PARAM_STR);
        $consulta->bindValue(':fechaBaja', null, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, apellido, mail, clave, puesto, estado, fechaInicio, fechaBaja FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }  

    public static function obtenerUsuarioPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, apellido, mail, clave, puesto, estado, fechaInicio, fechaBaja FROM usuarios WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }
    public static function obtenerUsuarioPorPuesto($puesto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, apellido, mail, clave, puesto, estado, fechaInicio, fechaBaja FROM usuarios WHERE puesto = :puesto");
        $consulta->bindValue(':puesto', $puesto, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }
    public static function obtenerUsuarioPorMail($mail)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, apellido, mail, clave, puesto, estado, fechaInicio, fechaBaja FROM usuarios WHERE mail = :mail");
        $consulta->bindValue(':mail', $mail, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }
    public static function obtenerUsuarioPorMailYClave($mail, $clave)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, apellido, mail, clave, puesto, estado, fechaInicio, fechaBaja FROM usuarios WHERE mail = :mail AND clave = :clave");
        $consulta->bindValue(':mail', $mail, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }
    public static function obtenerUsuarioPorNombreYApellido($nombre, $apellido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, apellido, mail, clave, puesto, estado, fechaInicio, fechaBaja FROM usuarios WHERE nombre = :nombre, apellido = :apellido");
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $apellido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }
    
    public function modificarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET nombre = :nombre, apellido = :apellido, mail = :mail, clave = :clave, puesto = :puesto, estado = :estado  WHERE id = :id");
        $consulta->bindValue(':id', $usuario->id, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $usuario->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $usuario->apellido, PDO::PARAM_STR);
        $consulta->bindValue(':mail', $usuario->mail, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $usuario->clave, PDO::PARAM_STR);
        $consulta->bindValue(':puesto', $usuario->puesto, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $usuario->estado, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function borrarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja, estado = :estado WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));        
        $consulta->bindValue(':id', $usuario->id, PDO::PARAM_INT);
        $consulta->bindValue(':estado', 'inactivo', PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d'));
        $consulta->execute();
    }    
    public static function leerUsuariosCSV($nombreArchivo) 
    { 
        $usuarios = []; 
        $archivo = fopen($nombreArchivo, 'r'); 
        if ($archivo === false) 
        { 
            die('No se pudo abrir el archivo.'); 
        } 
        $encabezado = fgetcsv($archivo);
        while (($datos = fgetcsv($archivo)) != false) 
        { 
            $usuario = new Usuario();
            $usuario->id = $datos[0];
            $usuario->nombre = $datos[1];
            $usuario->apellido = $datos[2];
            $usuario->mail = $datos[3];
            $usuario->clave = $datos[4];
            $usuario->puesto = $datos[5];
            $usuario->estado = $datos[6];
            $usuario->fechaInicio = $datos[7];
            $usuario->fechaBaja = $datos[8];
            $usuarios[] = $usuario; 
        } 
        fclose($archivo); 
        return $usuarios;
    }
}