<?php

//Clase que realiza peticiones a la BD paramodificar o obtener datos y enviarlos donde se necesitan 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reparaciones;
use Illuminate\Support\Str;
//Añadido
use App\Http\Controllers\DB as DB;
use App\Http\Middleware\Authenticate;
use App\Utils;
use App\Coches;
use App\Clientes;
use App\Usuarios;
use App\Usuariosempresas;

class ReparacionesController extends Controller
{
    /**
     * @param Request $request
     * Registra una nueva reparacion insertando todos los campos optenidos
     * por el parametro request
     * @return [json]
     */
    function add(Request $request)
    {
        //Comprobacion de que el id del cliente introducido se corresponde con el dueño del coche en la tabla coches
        $idCliente=$request->idcliente;
        $idCoche=$request->idcoche;
        if(!$this->comprobacionCocheDeCliente($idCliente,$idCoche))
            return response()->json(['Error'=>'El id del coche no se corresponde con el id del cliente','id de coche'=>$idCoche,'id de cliente'=>$idCliente],202);

        $reparacion = Reparaciones::create([
            'estadoReparacion' => $request->estadoReparacion,
            'idusuario' => $request->idusuario,
            'idcliente' => $request->idcliente,
            'idcoche' => $request->idcoche
        ]);
        return response()->json(['message' => 'Reparacion registrada con exito', 'Reparacion' => $reparacion], 200);
    }

    /**
     * Lista todas las reparaciones de todas las empresas
     * @return [json]
     */
    function list()
    {
        return response()->json(Reparaciones::all());
    }

    /**
     * @param mixed $idEmpresa
     * Listar reparaciones de una empresa concreta dado un id de empresa
     * @return [json]
     */
    function listReparacionesEmpresa($idEmpresa){    
        $reparaciones= Reparaciones::select()->whereIn('idusuario',Usuariosempresas::select('usuario')->where('empresa',$idEmpresa)->get())->get();
         return response()->json($reparaciones);
    }
    
    /**
     * @param mixed $idusuario
     * Listar de reparaciones de un usuario o tecnico concreto pasando un id de usuario
     * @return [json]
     */
    function listReparacionesUsuario($idusuario){
        $usuario=Usuarios::find($idusuario);
        return response()->json($usuario->reparaciones);
    }

    /**
     * Listar reparaciones asociadas con un la matricula concreta
     * de la tabla coches
     * @param mixed $matricula
     * Matricula del vehiculos a buscar con reparaciones
     * @return [json]
     */
    function reparacionesDeUnChoche($matricula){
        $reparaciones= Reparaciones::select()->whereIn('idcoche',Coches::select('id')->where('matricula',$matricula)->get())->get();
         return response()->json($reparaciones);
       
    }

    /**
     * @param mixed $id
     * Elimina una reparacion pasandole un id de reparacion por parametro
     * @return [json]
     */
    function delete($id)
    {
        $reparacion=Reparaciones::findOrFail($id);
        if(is_null($reparacion))
            return response()->json(['Error'=>'No existe una reparacion el id indicado.','Id de reparacion'=>$id],202);
                
        $reparacion->delete();   
        return response()->json(['Message'=>'Reparacion eliminada con exito.','Reparacion'=>$reparacion],200);
    }

    /**
     * @param Request $request
     * @param mixed $id
     * Actualiza los campos de el parametro $request que llegan diferentes de null
     * @return [json]
     */
    function update(Request $request, $id)
    {
        $reparacion=Reparaciones::findOrFail($id);
        $estadoReparacion=$request->estadoReparacion;
        $idusuario=$request->idusuario;
        $idcliente=$request->idcliente;
        $idcoche=$request->idcoche;
        
        $respuesta = array(); //Campos que fueron modificados
        if(!is_null($estadoReparacion)){
            $reparacion->update([
                'estadoReparacion'=>$estadoReparacion
            ]);
            array_push($respuesta,'estadoReparacion');//Añadimos al final del array
        }

        if(!is_null($idusuario)){
            $reparacion->update([
                'idusuario'=>$idusuario
            ]);
            array_push($respuesta,'idusuario');
        }

        if(!is_null($idcliente)){
            /*comprobamos si el id del coche tambien se pretende actualizar, y si es asi, comprobamos la correspondencia con el id cliente con los dos nuevos ids
            en caso contrario comprobamos la correspondencia entre el viejo id de coche con el nuevo id del usuario*/
            if(!is_null($idcoche)){
                if($this->comprobacionCocheDeCliente($idcliente,$idcoche))//Si el nuevo cliente se corresponde con el nuevo coche
                {
                    $reparacion->update([
                        'idcliente'=>$idcliente,
                        'idcoche'=>$idcoche
                    ]);
                    array_push($respuesta,'idcliente');
                    array_push($respuesta,'idcoche');
                }else
                    return response()->json(['Error'=>'El id del cliente no se corresponde con el del coche en la tabla coches.','id cliente'=>$idcliente,'id coche'=>$idcoche],202);
            }else{
                if($this->comprobacionCocheDeCliente($idcliente,$reparacion['idcoche']))//Si el nuevo cliente se corresponde con el viejo id del coche
                {
                    $reparacion->update([
                        'idcliente'=>$idcliente
                    ]);
                    array_push($respuesta,'idcliente');
                }
                else
                    return response()->json(['Error'=>'El id del cliente no se corresponde con el del coche en la tabla coches.','id cliente'=>$idcliente,'id coche'=>$reparacion['idCoche']],202);
            }
           
        }
        else if(is_null($idcliente) && !is_null($idcoche)){
           
            if($this->comprobacionCocheDeCliente($reparacion['idcliente'],$idcoche)){                  
                    $reparacion->update([
                        'idcoche'=>$idcoche
                    ]);
                    array_push($respuesta,'idcoche');
                }
                else
                    return response()->json(['Error'=>'El id del cliente no se corresponde con el del coche en la tabla coches.','id coche'=>$idcoche,'id cliente'=>$reparacion['idcliente']],202);
        }

        return response()->json(['message' => 'Reparacion actualizado con exito', 'Modificaciones' => $respuesta, 'Reparacion' => $reparacion], 200);
    }

    /**
     * @param mixed $idCliente
     * @param mixed $idCoche
     * Comprueba que el id del cliente se corresponde con el id del coche en la tabla coches,
     * es decir que el coche pertenece a ese cliente
     * Si encuentra alguna coincidencia devuelve true,
     * en caso contrario false
     * @return [Booblean boobl]
     */
    function comprobacionCocheDeCliente($idCliente,$idCoche){
        $coche=Coches::where('idcliente',$idCliente)->where('id',$idCoche)->get();
        if(sizeof($coche)<=0)//No encontro ninguna correspondencia
            return false;
        
        return true;
    }

}
