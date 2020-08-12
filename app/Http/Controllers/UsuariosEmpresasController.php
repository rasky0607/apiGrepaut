<?php

//Clase que realiza peticiones a la BD para modificar o obtener datos y enviarlos donde se necesitan 
namespace App\Http\Controllers;

use App\Empresas;
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
  
  #region Los Gets

  //Dado un id de usuario, muestra todas las empresas relacionadas con ese usuario y sus permisos en estas
  function empresasDelUsuario($idUsuario)
  {
    $usuario = Usuarios::findOrFail($idUsuario);
    return response()->json($usuario->empresas);
  }

  //Dado un id de empresa, muestra todas los usuarios relacionados con esta empresa y sus permisos 
  function buscarUsuariosDeEmpresa($idEmpresa)
  {
    $empresa = Empresas::findOrFail($idEmpresa);
    return response()->json($empresa->usuarios);
  }

  //Lista todos los registros de la tabla UsuariosEmpresas   
  function list()
  {
    return response()->json(Usuariosempresas::all());
  }
  #endregion 

  function delete($idEmpresa, $idUsuario)
  {
    $usuariosEmpresas = Usuariosempresas::where('usuario', $idUsuario)->where('empresa', $idEmpresa)->get();
    if (sizeof($usuariosEmpresas) <= 0) //Si NO encontro algun resultado
    {
      return response()->json(['message' => 'No existe una relacion entre el usuario y la empresa con dichos Ids.', 'Id usuario:' => $idUsuario, 'Id empresa' => $idEmpresa], 202);
    }
    //Si encontro un resultado     
    Usuariosempresas::where('usuario', $idUsuario)->where('empresa', $idEmpresa)->delete();
    return response()->json(['message' => 'Se elimino el Usuario de la Empresa  con exito', 'Relacion entre eliminada' => $usuariosEmpresas], 201);
  }


  //Actualiza los campos que llegan diferentes de null
  /*NO se permite actualizar el idUsuario o idEmpresa, para ello,
  para ello debera borrarse la relacion entre estos dos id de esta tabla y crear uno nuevo*/
  function update($idEmpresa, $idUsuario, Request $request)
  {
    $usuariosEmpresas = Usuariosempresas::where('usuario', $idUsuario)->where('empresa', $idEmpresa)->get();
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

