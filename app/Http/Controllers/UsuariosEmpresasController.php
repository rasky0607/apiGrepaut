<?php

//Clase que realiza peticiones a la BD paramodificar o obtener datos y enviarlos donde se necesitan 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Usuariosempresas;
use App\Usuarios;
use Illuminate\Support\Str;

class UsuariosEmpresasController extends Controller
{

  function add(Request $request)
  {
    $usuariosEmpresas = Usuariosempresas::create([
      'usuario' => $request->usuario,
      'empresa' => $request->empresa,
      'tipoUsuario' => $request->tipoUsuario,
      'permisoEscritura' => $request->permisoEscritura
    ]);
    return response()->json(['message' => 'Usuario asociado a Empresa con exito', 'Relacion entre Usuarios y Empresas' => $usuariosEmpresas], 201);
  }

  //Lista todos los registros de la tabla UsuariosEmpresas   
  function list()
  {
    return response()->json(Usuariosempresas::all());
  }

  /*Busca si el ID de usuario es NULL busca todos los usuarios relacionados con el ID de la empresa pasada por argumentos
   y si se le pasa un ID de usuario, busca el usuario y empresa concretos pasados por argumentos*/
  function buscarUsuariosDeEmpresa($idEmpresa, $idUsuario=null)
  {
    //app('db')->enableQueryLog();//Activar registro de querys   
    if (is_null($idUsuario))//Si no se pasa un ID de usuario 
    {
      $usuariosEmpresas = Usuariosempresas::select('usuarios.id as idUsuario', 'usuarios.email', 'usuariosempresas.empresa', 'usuariosempresas.tipoUsuario', 'usuariosempresas.permisoEscritura')
        ->join('usuarios', 'usuarios.id', '=', 'usuariosempresas.usuario')
        ->where('usuariosempresas.empresa', $idEmpresa)->get();
      //dd(app('db')->getQueryLog());        
      if (sizeof($usuariosEmpresas) <= 0) //Si NO encontro algun resultado
      {
        return response()->json(['message' => 'No existen usuarios dados de alta en la empresa con id:'.$idEmpresa], 202);
      }
      return response()->json(['message' => $usuariosEmpresas], 200);
    } else {
      $usuariosEmpresas = Usuariosempresas::select('usuarios.id as idUsuario', 'usuarios.email', 'usuariosempresas.empresa', 'usuariosempresas.tipoUsuario', 'usuariosempresas.permisoEscritura')
        ->join('usuarios', 'usuarios.id', '=', 'usuariosempresas.usuario')
        ->where('usuariosempresas.empresa', $idEmpresa)->where('usuariosempresas.usuario', $idUsuario)->get();
      //dd(app('db')->getQueryLog());        
      if (sizeof($usuariosEmpresas) <= 0) //Si NO encontro algun resultado
      {
        return response()->json(['message' => 'No existe una relacion entre el usuario y la empresa con dichos Ids.', 'Id usuario:' => $idUsuario, 'Id empresa' => $idEmpresa], 202);
      }
      return response()->json(['message' => $usuariosEmpresas], 200);
    }
  }

  function delete($idEmpresa, $idUsuario)
  {
    $usuariosEmpresas = Usuariosempresas::where('usuario', $idUsuario)->where('empresa', $idEmpresa)->get();
    //dd($usuariosEmpresas[0]["empresa"]);//Accede a la clave empresa del array de valores que devuelve la consulta
    if (sizeof($usuariosEmpresas) <= 0) //Si NO encontro algun resultado
    {
      return response()->json(['message' => 'No existe una relacion entre el usuario y la empresa con dichos Ids.', 'Id usuario:' => $idUsuario, 'Id empresa' => $idEmpresa], 202);
    }
    //Si encontro un resultado    
    //$usuariosEmpresas->delete();  
    Usuariosempresas::where('usuario', $idUsuario)->where('empresa', $idEmpresa)->delete();
    return response()->json(['message' => 'Se elimino el Usuario de la Empresa  con exito', 'Relacion entre eliminada' => $usuariosEmpresas], 201);
  }


  //Actualiza los campos que llegan diferentes de null
  /*NO se permite actualizar el idUsuario o idEmpresa, para ello,
  para ello debera borrarse la relacion entre estos dos id de esta tabla y crear uno nuevo*/
  function update($idEmpresa, $idUsuario, Request $request)
  {
    $usuariosEmpresas = Usuariosempresas::where('usuario', $idUsuario)->where('empresa', $idEmpresa)->get();
    //$usuariosEmpresas = Usuariosempresas::where('usuario', $idUsuario)->where('empresa', $idEmpresa)->get();
    if (sizeof($usuariosEmpresas) > 0) {
      $tipoUsuario = $request->tipoUsuario;
      $permisoEscritura = $request->permisoEscritura;
      $respuesta = ' '; //Campos que fueron modificados

      if (!is_null($tipoUsuario)) {
        Usuariosempresas::where('usuario', $idUsuario)->where('empresa', $idEmpresa)->update([
          'tipoUsuario' => $tipoUsuario
        ]);
        $respuesta .= 'tipoUsuario ';
      }

      if (!is_null($permisoEscritura)) {
        Usuariosempresas::where('usuario', $idUsuario)->where('empresa', $idEmpresa)->update([
          'permisoEscritura' => $permisoEscritura
        ]);
        $respuesta .= 'permisoEscritura ';
      }
      return response()->json(['message' => 'Empresa actualizada con exito', 'Modificaciones' => $respuesta, ' registro de usuariosEmpresas modificado' => $usuariosEmpresas], 201);
    } else {
      return response()->json(['message' => 'No se encontro el usuario con Id: ' . $idUsuario . ' asociado a la empresa con Id: ' . $idEmpresa], 202);
    }
  }
}
