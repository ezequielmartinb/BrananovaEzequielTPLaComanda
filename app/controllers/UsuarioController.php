<?php
require_once './interfaces/IApiUsable.php';
require_once './models/Usuario.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {      
      $parametros = $request->getParsedBody();    
      
      $nombre = $parametros['nombre'];
      $apellido = $parametros['apellido'];
      $mail = $parametros['mail'];
      $estado = $parametros['estado'];
      $puesto = $parametros['puesto'];   
        
      $usr = new Usuario();
      $usr->nombre = $nombre;
      $usr->apellido = $apellido;
      $usr->mail = $mail;
      $usr->estado =  $estado;
      $usr->puesto =  $puesto;
      $usr->crearUsuario();
      
      $payload = json_encode(array("mensaje" => "Usuario creado con exito"));  

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
      $parametros = $request->getQueryParams();
      
      $id = $parametros['id'];          
      $usuario = Usuario::obtenerUsuarioPorId($id);
      if($usuario != null)
      {
        $payload = json_encode($usuario);
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
      $lista = Usuario::obtenerTodos();
      $payload = json_encode(array("listaUsuario" => $lista));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $usuario = Usuario::obtenerUsuarioPorId($parametros['id']);
        if($usuario != null)
        {          
          $usuario->nombre = $parametros['nombre'];        
          $usuario->apellido = $parametros['apellido'];       
          $usuario->mail = $parametros['mail'];       
          $usuario->clave = $parametros['clave'];       
          $usuario->puesto = $parametros['puesto'];       
          $usuario->estado = $parametros['estado'];
          Usuario::modificarUsuario($usuario);

          $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
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
      
      $usuario = Usuario::obtenerUsuarioPorId($parametros['id']);
      if($usuario != null)
      {
        Usuario::borrarUsuario($usuario);
        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
      }       
      else
      {
        $payload = json_encode(array("mensaje" => "ID INEXISTENTE Y NO SE PUEDE BORRAR"));
      } 
      
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    public function DescargarUsuariosCsv($request, $response, $args)
    {
      $usuarios = Usuario::obtenerTodos();

      $archivo = fopen('php://temp', 'r+');

      fputcsv($archivo, ['id', 'nombre', 'apellido', 'mail', 'clave', 'puesto', 'estado', 'fechaInicio', 'fechaBaja']);

      foreach ($usuarios as $usuario) 
      {
        fputcsv($archivo, 
        [
          $usuario->id,
          $usuario->nombre,
          $usuario->apellido,
          $usuario->mail,
          $usuario->clave,
          $usuario->puesto,
          $usuario->estado,
          $usuario->fechaInicio,
          $usuario->fechaBaja
        ]);
      }

      rewind($archivo);

      $csvContent = stream_get_contents($archivo);

      fclose($archivo);

      $response->getBody()->write($csvContent);
      return $response->withHeader('Content-Type', 'application/csv')->withHeader('Content-Disposition', 'attachment; filename="usuarios.csv"');         
    }

    
    public function CargarUsuariosCsv($request, $response, $args)
    {
      if(isset($_FILES['usuarios']))
      {
        $archivo = $_FILES['usuarios'];
        $usuarios = Usuario::leerUsuariosCSV($archivo['tmp_name']);
        if($usuarios != null)
        {
          $payload = json_encode(array("mensaje" => "Los usuarios fueron cargados con exito"));
          foreach($usuarios as $usuario)
          {
            $usuario->crearUsuario();
          }
        }        
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    
}
