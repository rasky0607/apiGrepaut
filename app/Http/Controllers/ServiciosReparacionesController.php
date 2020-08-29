<?php
namespace App\Http\Controllers;
use App\Serviciosreparaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
//Añadido
use App\Http\Controllers\DB as DB;
use App\Http\Middleware\Authenticate;
use App\Utils;
use App\Coches;
use App\Clientes;
use App\Usuarios;
use App\Servicios;
use App\Usuariosempresas;
use App\Reparaciones;


/**
 * [Description ServiciosReparacionesController]
 * Clase que realiza peticiones a la BD paramodificar o obtener datos y enviarlos donde se necesitan en formato Json
 */
class ServiciosReparacionesController extends Controller
{
    /**
     * @param Request $request
     * Registra una nueva reparacion insertando todos los campos optenidos
     * por el parametro request
     * @return [json]
     */
    function add(Request $request)
    {
        //comprobacion de que el servicio asignado a la reparacion, pertenece a la misma empresa.
        if(!$this->comprobacionEmpresaServicioEmpresaReparacion($request->idreparacion,$request->servicio))
            return response()->json(['Error'=>'El id del servicio asignado no pertenece a la misma empresa que el id de la reparacion indicada, o no existen.','id servicio'=>$request->servicio,'id reparacion'=>$request->idreparacion,],202);

        $numerotrabajo=$this->obtenerNumeroTrabajoDeReparacion($request->idreparacion);
        $servicioReparacion= Serviciosreparaciones::create([
        'idreparacion' => $request->idreparacion,
        'numerotrabajo' => $numerotrabajo,
        'servicio' => $request->servicio,
        'idEmpresaServicio' => $request->idEmpresaServicio//Este campo puede ser eliminado de la tabla en la BD ##PENDIENTE###
       ]);
       return response()->json(['Message'=>'Servicio asignado a la reparacion con exito.','servicioReparacion'=>$servicioReparacion,],200);
    }

     /**
     * Lista todos los registros de la tabla serviciosReparaciones
     * @return [json]
     */
    function list()
    {
        return response()->json(Serviciosreparaciones::all());
    }

    /**
     * Lista todos los ids de servicios asignados a un id de reparacion y el numero de trabajo de cada uno
     * @return [json]
     */
    function listServiciosDeUnaReparacion($idreparacion)
    {
      $serviciosReparacion= Serviciosreparaciones::select('numerotrabajo','servicio')->where('idreparacion',$idreparacion)->get();
      return response()->json(['Message'=>'Servicios asignados a la reparacion con id = '.$idreparacion,'serviciosReparacion'=>$serviciosReparacion],201);
    }


    /**
     * @param mixed $id
     * Elimina un servicio asignado a una reparacion indicando el id de la reparacio y el numero de trabajo pro parametro
     * @return [json]
     */
    function delete($idreparacion,$numerotrabajo)
    {
       $servicioReparacion=Serviciosreparaciones::where('idreparacion',$idreparacion)->where('numerotrabajo',$numerotrabajo);
       if(sizeof($servicioReparacion->get())<=0)
            return response()->json(['Error'=>'No se encontro asociacion entre este id de reparacion y el numero de trabajo indicados ','idreparacion'=>$idreparacion,'numerotrabajo'=>$numerotrabajo],202);
        
        //Si encontro un resultado 
        $objetoEliminado= clone $servicioReparacion->get();//Necesitamos clonarlo, puesto que una vez se elimina no podemos volver a mostrarlo a diferencia de las busquedas por find id
        $servicioReparacion->delete();

        return response()->json(['message' => 'Se elimino asociacion entre este id de reparacion y el numero de trabajo con exito', 'servicioReparacion' => $objetoEliminado], 201);
    }

    /**
     * @param Request $request
     * @param mixed $id
     * Actualiza los campos de el parametro $request que llegan diferentes de null
     * pasando el id de la reparacion por parametro
     * @return [json]
     */
    function update(Request $request, $id) //### PENDIENTE DE TESTEO ##
    {
       
    }

    /**
     * @param mixed $idreparacion
     * @param mixed $idServicio
     * Comprueba que un id servicio pertenezca
     * a la misma empresa que al id de la reparacion a la que se asigna
     * preguntando si el usuario que esta asignado a la reparacion pertenece a la misma empresa (en la tabla usuariosEmpresas)
     * que el servicio (En la tabla servicios) asignado a esta reparacion.
     * @return [Booblean bool]
     */
    function comprobacionEmpresaServicioEmpresaReparacion($idreparacion,$idServicio){
        $servicio=Servicios::where('id',$idServicio)->get();
        if(sizeof($servicio)<=0)//el id del servicio no existe
            return false;
        //Seleciona el usuario de la tabla usuariosEmpresas que coincida con el id de usuario de cuya reparacion tenga como id la indicada "idreparacion"
        $usuarioEmpresa= Usuariosempresas::whereIn('usuario',Reparaciones::select('idusuario')->where('id',$idreparacion)->get())->get();
        if(sizeof($usuarioEmpresa)<=0)//el id del servicio no existe
            return false;
        $idEmpresaServicio=$servicio[0]['empresa'];
        $idEmpresaUsuario=$usuarioEmpresa[0]['empresa'];

        //Si coinciden los id de empresa, es que el servicio asignado a la reparacion  pertenecen a la misma empresa
        if($idEmpresaServicio==$idEmpresaUsuario)
            return true;
        else
            return false;
    }

    /**
     * @param mixed $idreparacion
     * Determina cual es el ultimo numnero de trabajo de la reparacion, le añade uno más al valor y lo devuelve.
     * En caso de ser 0 o no encontrar registros, devuelve 1.
     * @return [Integer numeroReparacionFinal]
     */
    function obtenerNumeroTrabajoDeReparacion($idreparacion){
        $numerotrabajoObtenido=0;
        $servicioReparacion=Serviciosreparaciones::where('idreparacion',$idreparacion)->max('numerotrabajo');
        if($servicioReparacion!=null)//Si no existen registros previos en la tabla serviciosReparaciones con  ese id de reparacion, por lo lo que no hay un  "numerotrabajo" maximo        
            $numerotrabajoObtenido=$servicioReparacion;

        if($numerotrabajoObtenido==0 || is_null($numerotrabajoObtenido))//si es 0 0 null devolvemos 1, si no, devolvemos el valor +1
        {
            $numerotrabajoObtenido=1;
            return $numerotrabajoObtenido;
        }else{
            $numerotrabajoObtenido++;
            return $numerotrabajoObtenido;
        }         
    }

}
