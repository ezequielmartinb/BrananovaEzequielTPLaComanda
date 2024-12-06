<?php
require_once './models/Encuesta.php';
require_once './interfaces/IApiUsable.php';

class EncuestaController extends Encuesta implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {      
    $parametros = $request->getParsedBody();    
      
    $codigoMesa = $parametros['codigoMesa'];
    $codigoPedido = $parametros['codigoPedido'];
    $puntosMesa = $parametros['puntosMesa'];
    $puntosRestaurante = $parametros['puntosRestaurante'];
    $puntosMozo = $parametros['puntosMozo'];   
    $puntosCocinero = $parametros['puntosCocinero'];   
    $comentario = $parametros['comentario'];      
    
    $encuesta = new Encuesta();
    $encuesta->codigoMesa = $codigoMesa;
    $encuesta->codigoPedido = $codigoPedido;
    $encuesta->puntosMesa = $puntosMesa;
    $encuesta->puntosRestaurante =  $puntosRestaurante;
    $encuesta->puntosMozo =  $puntosMozo;
    $encuesta->puntosCocinero =  $puntosCocinero;
    $encuesta->comentario =  $comentario;
    $encuesta->crearEncuesta();
    
    $payload = json_encode(array("mensaje" => "Encuesta creada con exito"));  

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    $parametros = $request->getQueryParams();
      
    $id = $parametros['id'];          
    $encuesta = Encuesta::obtenerEncuestaPorId($id);
    if($encuesta != null)
    {
      $payload = json_encode($encuesta);
    }
    else
    {
      $payload = json_encode(array("mensaje" => "EL ID INGRESADO NO EXISTE"));
    }     

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
  public function TraerEncuestaMejoresComentarios($request, $response, $args)
  {
    $encuestas = Encuesta::obtenerTodos();
    $encuestasConMejoresComentarios = [];
    foreach($encuestas as $encuesta)
    {
      $promedio = ($encuesta->puntosMesa + $encuesta->puntosRestaurante + $encuesta->puntosMozo + $encuesta->puntosCocinero) / 4;
      if($promedio >= 8)
      {
        $encuestasConMejoresComentarios[] = $encuesta; 
      }
    } 
    $payload = json_encode(array("lista encuesta con mejores comentarios" => $encuestasConMejoresComentarios));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Encuesta::obtenerTodos();
    $payload = json_encode(array("listaEncuesta" => $lista));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
  
  
  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
        
    $encuesta = Encuesta::obtenerEncuestaPorId($parametros['id']);
    if($encuesta != null)
    {          
      $encuesta->codigoMesa = $parametros['codigoMesa'];        
      $encuesta->codigoPedido = $parametros['codigoPedido'];       
      $encuesta->puntosMesa = $parametros['puntosMesa'];       
      $encuesta->puntosRestaurante = $parametros['puntosRestaurante'];       
      $encuesta->puntosMozo = $parametros['puntosMozo'];       
      $encuesta->puntosCocinero = $parametros['puntosCocinero'];
      $encuesta->comentario = $parametros['comentario'];
      Encuesta::modificarEncuesta($encuesta);

      $payload = json_encode(array("mensaje" => "Encuesta modificada con exito"));
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
      
      $encuesta = Encuesta::obtenerEncuestaPorId($parametros['id']);
      if($encuesta != null)
      {
        Encuesta::borrarEncuesta($encuesta);
        $payload = json_encode(array("mensaje" => "Encuesta borrado con exito"));
      }       
      else
      {
        $payload = json_encode(array("mensaje" => "ID INEXISTENTE Y NO SE PUEDE BORRAR"));
      } 
      
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
  }
    
}
