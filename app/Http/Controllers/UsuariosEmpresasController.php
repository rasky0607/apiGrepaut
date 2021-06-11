<?php

namespace App\Http\Controllers;

use App\Empresas;
use Illuminate\Http\Request;
use App\UsuariosEmpresas;
use App\Usuarios;
use Illuminate\Support\Str;

/** CLASE EN DESHUSO
 * [Description UsuariosEmpresasController]
 * Clase que realiza peticiones a la BD paramodificar o obtener datos y enviarlos donde se necesitan en formato Json
 */
class UsuariosEmpresasController extends Controller
{
  /**
   * @param Request $request
   * Registra una nueva relacion entre un usuario, una empresa y
   * que permisos y tipo de usuario tiene en esta obteniendo los campos
   * por el parametro request
   * Por defecto el primer usuario dado de alta en una empresa es Admin
   * con permisos de escritura True.
   * El resto de usuarios por defecto se crearan como User con permisos a False.
   * @return [json]
   */
  function add(Request $request)
  {
    $empleadosEnLaEmpresa=Usuariosempresas::where('empresa',$request->empresa)->count();
    $tipoUsuario=null;
    $permisoEscritura=null;
    //Si es el primer usuario de la empresa, se crea como Admin con permisos de escritura a true
    if($empleadosEnLaEmpresa== 0){
      $tipoUsuario='Admin';
      $permisoEscritura=true;
    }else{//de lo contrario, se crea como User con permisos de escritura a false
      $tipoUsuario='User';
      $permisoEscritura=false;
    }
    $usuariosEmpresas = Usuariosempresas::create([
      'usuario' => $request->usuario,
      'empresa' => $request->empresa,
      'tipoUsuario' => $tipoUsuario,
      'permisoEscritura' => $permisoEscritura
    ]);
    return response()->json(['message' => 'Usuario asociado a Empresa con exito', 'Empleado' => $usuariosEmpresas], 201);
  }

  #region Los Gets

  /**
   * @param mixed $idUsuario
   * Dado un id de usuario, muestra todas las empresas relacionadas con ese usuario y sus permisos y tipo de usuario en estas
   * @return [Json]
   */
  function empresasDelUsuario($idUsuario)
  {
    $usuariosEmpresas = UsuariosEmpresas::where('usuario', $idUsuario)->get();
    return $usuariosEmpresas;
  }

  /**
   * @param mixed $idEmpresa
   * Dado un id de empresa, muestra todas los usuarios relacionados con esta empresa y sus permisos,
   * es decir todos sus empleados 
   * @return [Json]
   */
  function buscarUsuariosDeEmpresa($idEmpresa)
  {
    //$empresa = Empresas::findOrFail($idEmpresa)->get();
      $usuariosEmpresas = Usuariosempresas::select('usuarios.id as idUsuario', 'usuarios.email', 'usuariosempresas.empresa', 'usuariosempresas.tipoUsuario', 'usuariosempresas.permisoEscritura')
          ->join('usuarios', 'usuarios.id', '=', 'usuariosempresas.usuario')
          ->where('usuariosempresas.empresa', $idEmpresa)->get();

    if(is_null($usuariosEmpresas))//No encontro la empresa
      return response()->json(['Error' => 'No existe ese id de empresa.', 'Id de empresa' => $usuariosEmpresas], 202);

    //return response()->json($empresa->usuarios);//Devuelve todos los usuarios relacionados con la empresa

      return response()->json($usuariosEmpresas);
  }

  /**
   * @param mixed $idUsuario
   * @param mixed $idEmpresa
   * Busca una relacion concreta entre un usuario y una empresa
   * @return [Json]
   */
  function buscarUnUsuarioDeUnaEmpresa($idUsuario,$idEmpresa)
  {
      $usuariosEmpresas = Usuariosempresas::select('usuarios.id as idUsuario', 'usuarios.email', 'usuariosempresas.empresa', 'usuariosempresas.tipoUsuario', 'usuariosempresas.permisoEscritura')
          ->join('usuarios', 'usuarios.id', '=', 'usuariosempresas.usuario')
          ->where('usuariosempresas.empresa', $idEmpresa)->where('usuariosempresas.usuario',$idUsuario)->get();
    //$usuariosEmpresas = UsuariosEmpresas::where('usuario', $idUsuario)->where('empresa', $idEmpresa)->get();
    if (sizeof($usuariosEmpresas) <= 0) //Si NO encontro algun resultado
    {
      return response()->json(['message' => 'No existe una relacion entre el usuario y la empresa con dichos Ids.', 'Id usuario:' => $idUsuario, 'Id empresa' => $idEmpresa], 202);
    }
    //Si encontro un resultado 
  
    return response()->json($usuariosEmpresas, 201);
  }

  /**
   * Lista todos los registros de la tabla UsuariosEmpresas   
   * @return [Json]
   */
  function list()
  {
    return response()->json(UsuariosEmpresas::all());
  }
  #endregion 

  /**
   * @param mixed $idUsuario
   * @param mixed $idEmpresa
   * Dado un id de empresa y un id de usuario se elimina un registro de esta tabla,
   * es decir se elimina una relacion entre una empresa y un usuario determinado
   * @return [Json]
   */
  function delete($idUsuario,$idEmpresa)
  {
    $usuariosEmpresas = Usuariosempresas::where('usuario', $idUsuario)->where('empresa', $idEmpresa);
    if (sizeof($usuariosEmpresas->get()) <= 0) //Si NO encontro algun resultado
    {
      return response()->json(['message' => 'No existe una relacion entre el usuario y la empresa con dichos Ids.', 'Id usuario:' => $idUsuario, 'Id empresa' => $idEmpresa], 202);
    }
    //Si encontro un resultado 
    $objetoEliminado= clone $usuariosEmpresas->get();//Necesitamos clonarlo, puesto que una vez se elimina no podemos volver a mostrarlo a diferencia de las busquedas por id
    $usuariosEmpresas->delete();
    
    return response()->json(['message' => 'Se elimino el Usuario de la Empresa  con exito', 'Relacion entre eliminada' => $objetoEliminado], 201);
  }

  /**
   * @param Request $request
   * @param mixed $idUsuario
   * @param mixed $idEmpresa
   * Actualiza los campos de el parametro $request que llegan diferentes de null
   * NO se permite actualizar el idUsuario o idEmpresa, para ello,
   * para ello debera borrarse la relaciones entre estos dos id de esta tabla y crear uno nuevo
   * @return [json]
   */
  function update($idUsuario,$idEmpresa, Request $request)
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
