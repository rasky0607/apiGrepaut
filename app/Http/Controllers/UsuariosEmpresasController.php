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
  /**
   * @param Request $request
   * Registra una nueva relacion entre un usuario, una empresa y
   * que permisos y tipo de usuario tiene en esta obteniendo los campos
   * por el parametro request
   * @return [json]
   */
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

  /**
   * @param mixed $idUsuario
   * Dado un id de usuario, muestra todas las empresas relacionadas con ese usuario y sus permisos y tipo de usuario en estas
   * @return [Json]
   */
  function empresasDelUsuario($idUsuario)
  {
    $usuario = Usuarios::findOrFail($idUsuario);
    return response()->json($usuario->empresas);
  }

  /**
   * @param mixed $idEmpresa
   * Dado un id de empresa, muestra todas los usuarios relacionados con esta empresa y sus permisos,
   * es decir todos sus empleados 
   * @return [Json]
   */
  function buscarUsuariosDeEmpresa($idEmpresa)
  {
    $empresa = Empresas::findOrFail($idEmpresa);
    return response()->json($empresa->usuarios);
  }

  /**
   * Lista todos los registros de la tabla UsuariosEmpresas   
   * @return [Json]
   */
  function list()
  {
    return response()->json(Usuariosempresas::all());
  }
  #endregion 

  /**
   * @param mixed $idEmpresa
   * @param mixed $idUsuario
   * Dado un id de empresa y un id de usuario se elimina un registro de esta tabla,
   * es decir se elimina una relacion entre una empresa y un usuario determinado
   * @return [Json]
   */
  function delete($idEmpresa, $idUsuario)
  {
    $usuariosEmpresas = Usuariosempresas::where('usuario', $idUsuario)->where('empresa', $idEmpresa);
    if (sizeof($usuariosEmpresas->get()) <= 0) //Si NO encontro algun resultado
    {
      return response()->json(['message' => 'No existe una relacion entre el usuario y la empresa con dichos Ids.', 'Id usuario:' => $idUsuario, 'Id empresa' => $idEmpresa], 202);
    }
    //Si encontro un resultado 
    $objetoEliminado= clone $usuariosEmpresas->get();
    
    $usuariosEmpresas->delete();
    
    return response()->json(['message' => 'Se elimino el Usuario de la Empresa  con exito', 'Relacion entre eliminada' => $objetoEliminado], 201);
  }

  /**
   * @param Request $request
   * @param mixed $id
   * Actualiza los campos de el parametro $request que llegan diferentes de null
   * NO se permite actualizar el idUsuario o idEmpresa, para ello,
   * para ello debera borrarse la relaciones entre estos dos id de esta tabla y crear uno nuevo
   * @return [json]
   */
  function update($idEmpresa, $idUsuario, Request $request)
  {
    $usuariosEmpresas = Usuariosempresas::where('usuario', $idUsuario)->where('empresa', $idEmpresa);
    if (sizeof($usuariosEmpresas->get()) > 0) //Si existe un registro con esos ids 
    {
      $tipoUsuario = $request->tipoUsuario;
      $permisoEscritura = $request->permisoEscritura;
      $respuesta = array(); //Campos que fueron modificados

      if (!is_null($tipoUsuario)) {
        $usuariosEmpresas->update([
          'tipoUsuario' => $tipoUsuario
        ]);
        array_push($respuesta, 'tipoUsuario ');
      }

      if (!is_null($permisoEscritura)) {
        $usuariosEmpresas->update([
          'permisoEscritura' => $permisoEscritura
        ]);
        array_push($respuesta, 'permisoEscritura ');
      }
      return response()->json(['message' => 'Empresa actualizada con exito', 'Modificaciones' => $respuesta, ' registro de usuariosEmpresas modificado' => $usuariosEmpresas->get()], 201);
    } else {
      return response()->json(['message' => 'No se encontro el usuario con Id: ' . $idUsuario . ' asociado a la empresa con Id: ' . $idEmpresa], 202);
    }
  }
}
