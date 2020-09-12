<?php


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

/**
 * [Description ReparacionesController]
 * Clase que realiza peticiones a la BD paramodificar o obtener datos y enviarlos donde se necesitan en formato Json
 */
class ReparacionesController extends Controller
{
    /**
     * @param Request $request
     * Registra una nueva reparacion insertando todos los campos optenidos
     * por el parametro request.
     * Por defecto toda reparacion creada, se crea con el estado 'No facturado'
     * @return [json]
     */
    function add(Request $request) //### PENDIENTE DE TESTEO ##
    {
        //Comprobacion de que el id del usuario introducido se corresponde la empresa a la que pertenece el coche
        $idcoche = $request->idcoche;
        $idusuario = $request->idusuario;
        $idempresa=$request->idempresa;
        $estadoReparacion='No facturado';//Valor por defecto al crear una reparacion
        if (!$this->comprobacionCocheYusuarioYEmpresa($idusuario, $idcoche,$idempresa))
            return response()->json(['Error' => 'El id del coche y el id de usuario [el tecnico] no pertenecen a la empresa indicada en el campo idEmpresa', 'id de coche' => $idcoche, 'id de usuario' => $idusuario, 'id de empresa '=>$idempresa], 202);

        $reparacion = Reparaciones::create([
            'estadoReparacion' => $estadoReparacion,
            'idusuario' => $request->idusuario,
            'idcoche' => $request->idcoche,
            'idempresa'=>$request->idempresa
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
    function listReparacionesEmpresa($idEmpresa)// ####PENDIENTE REVISION YA QUE SE AÑADIO EL CAMPO EMPRESA A REPARACIONES#######
    {      
        $reparacionesEmpresa=Reparaciones::where('idempresa',$idEmpresa)->get();
        if(sizeof($reparacionesEmpresa)<=0)//No encontro el reparaciones
            return response()->json(['Error' => 'No existen reparaciones para ese id de empresa.',  'Id de empresa'=>$idEmpresa], 202);
            
        return response()->json($reparacionesEmpresa);
    }

    /**
     * @param mixed $idusuario
     * Listar de reparaciones de un usuario o tecnico concreto pasando un id de usuario y un id de empresa
     * @return [json]
     */
    function listReparacionesUsuario($usuario,$empresa)// ####PENDIENTE REVISION YA QUE SE AÑADIO EL CAMPO EMPRESA A REPARACIONES####### revisar clases USuariosEmpresas y Reparaciones
    {
        $reparacionesUsuario=Reparaciones::where('idusuario',$usuario)->where('idempresa',$empresa)->get();
        if(sizeof($reparacionesUsuario)<=0)//No encontro reparaciones de ese usuario de esa empresa
            return response()->json(['Error' => 'No existen reparaciones para  ese tecnico o id de usuario asociado a esta empresa.', 'Id de usuario' => $usuario, 'Id de empresa'=>$empresa], 202);

        return response()->json($reparacionesUsuario);
    }

    /**
     * Listar reparaciones asociadas con un id de coche concreto
     * de la tabla coches
     * @param mixed $idcoche
     * Reparaciones de un vehiculo concreto
     * @return [json]
     */
    function reparacionesDeUnChoche($idcoche)
    {
        $coche=Coches::find($idcoche);
        if(is_null($coche))//No encontro el coche
            return response()->json(['Error' => 'No existe ese id de coche.', 'Id de coche' => $idcoche], 202);
        return response()->json($coche->reparaciones);//Devuelve las reparaciones relacionadas con ese coche
    }

    /**
     * @param mixed $id
     * Elimina una reparacion pasandole un id de reparacion por parametro
     * @return [json]
     */
    function delete($id)
    {
        $reparacion = Reparaciones::findOrFail($id);
        if (is_null($reparacion))
            return response()->json(['Error' => 'No existe una reparacion el id indicado.', 'Id de reparacion' => $id], 202);

        //Si la reparacion esta con estado "Facturado" no podra ser eliminada
        if (strcasecmp($reparacion['estadoReparacion'], 'Facturado') == 0) //Si devuelve 0 es que el strin coincide(omitiendo mayusculas y  minusculas)
            return response()->json(['Error' => 'Una reparacion con estadoReparacion [Facturado]  no puede ser eliminada.', 'Reparacion' => $reparacion], 202);

        $reparacion->delete();
        return response()->json(['Message' => 'Reparacion eliminada con exito.', 'Reparacion' => $reparacion], 200);
    }

    /**
     * @param Request $request
     * @param mixed $id
     * Actualiza los campos de el parametro $request que llegan diferentes de null
     * pasando el id de la reparacion por parametro
     * @return [json]
     */
    function update(Request $request, $id) 
    {
        $reparacion = Reparaciones::findOrFail($id);
        $estadoReparacion = $request->estadoReparacion;
        $idusuario = $request->idusuario;
        $idcoche = $request->idcoche;

        //Comrpobaciones ande de realizar cambios de que el coche  y el usuario pertenecea a la misma empresa
        if (!is_null($idcoche) && !is_null($idusuario))//Si ambos valores son nulos, es decir ambos se intentan cambiar en el update 
        {
            if (!$this->comprobacionCocheYusuario($idusuario, $idcoche))
                return response()->json(['Error' => 'El usuario y el coche asignados no pertenecen a la misma empresa.', 'id usuario' => $idusuario, 'id coche' => $idcoche], 202);
        }

        if (is_null($idcoche) && !is_null($idusuario)) //Si el id del coche es nulo y el id de usuario  NO
        {
            if (!$this->comprobacionCocheYusuario($idusuario, $reparacion['idcoche']))
                return response()->json(['Error' => 'El usuario y el coche asignados no pertenecen a la misma empresa.', 'id usuario' => $idusuario, 'id coche' =>$reparacion['idcoche']], 202);
        }

        if (is_null($idusuario) && !is_null($idcoche)) //Si el id del coche no es nulo y el id usuario SI
        {
            if (!$this->comprobacionCocheYusuario($reparacion['idusuario'], $idcoche))
                return response()->json(['Error' => 'El usuario y el coche asignados no pertenecen a la misma empresa.', 'id usuario' => $reparacion['idusuario'], 'id coche' => $idcoche], 202);
        }
        //-------------------------------//

        if (strcasecmp($reparacion['estadoReparacion'], 'Facturado') == 0) //Si devuelve 0 es que el strin coincide(omitiendo mayusculas y  minusculas)
            return response()->json(['Error' => 'Una reparacion con estadoReparacion [Facturado]  no puede ser modificada.', 'Reparacion' => $reparacion], 202);

        $respuesta = array(); //Campos que fueron modificados
        if (!is_null($estadoReparacion)) {
            $reparacion->update([
                'estadoReparacion' => $estadoReparacion
            ]);
            array_push($respuesta, 'estadoReparacion'); //Añadimos al final del array
        }

        if (!is_null($idusuario)) {
            $reparacion->update([
                'idusuario' => $idusuario
            ]);
            array_push($respuesta, 'idusuario');
        }

        if (!is_null($idcoche)) {
            $reparacion->update([
                'idcoche' => $idcoche
            ]);
            array_push($respuesta, 'idcoche');
        }


        return response()->json(['message' => 'Reparacion actualizado con exito', 'Modificaciones' => $respuesta, 'Reparacion' => $reparacion], 200);
    }

    /**
     * @param mixed $idCliente
     * @param mixed $idCoche
     * @param mixed $idEmpresa
     * Comprueba con el id del del coche que pertenece a un id de cliente,
     * corresponda con la misma empresa que el id del usuario.
     * Si encuentra alguna coincidencia devuelve true,
     * en caso contrario false.
     * En caso de pertenecer ambos a la misma empresa, se comprueba que el usuario,
     * pertenezca al misma empresa que el campo idEmpresa introducido para el registro de la nueva reparacion
     * en caso correcto devovlera true, en caso contrario false 
     * @return [Booblean bool]
     */
    function comprobacionCocheYusuarioYEmpresa($idusuario, $idcoche,$idEmpresa)
    {
        //Sacamos el id de la empresa a la que pertenece el coche a traves del cliente con el que se asocia
        //SELECT empresa from clientes where id in( SELECT idcliente FROM `coches` WHERE id=1) 
        $empresaDelCoche = Clientes::select('empresa')->whereIn('id', Coches::select('idcliente')->where('id', $idcoche))->get();
        //Sacamos el id de empresa del usuario que se asocia con la reparacion para comprarar
        //SELECT empresa FROM `usuariosempresas` WHERE usuario=1 
        $empresaDelUsuario = Usuariosempresas::select('empresa')->where('usuario', $idusuario)->get();
        if(sizeof($empresaDelCoche)<=0 || sizeof($empresaDelUsuario)<=0 )//Si no encontro resultado en alguna de las dos consultas
            return false;

        //Recorremos el numero de empresas a las que petence el usuario
        foreach($empresaDelUsuario as $valor)
        {
            //echo  $valor['empresa'];
            if ($empresaDelCoche[0]['empresa'] == $valor['empresa']) //Pertenecen a la misma empresa
            {
                if($valor['empresa']==$idEmpresa)//Comprueba si el idEmpresa introducido para la reparacion es correcto respecto al id Usuario introducido
                    return true;

                return false;//Si el usuario no pertenece al idEmpresa introducido para el registro de la reparacion
            }
        }

        return false;
    }

    /**
     * @param mixed $idusuario
     * @param mixed $idcoche
     * Comprueba con el id del del coche que pertenece a un id de cliente,
     * corresponda con la misma empresa que el id del usuario.
     * Si encuentra alguna coincidencia devuelve true,
     * en caso contrario false.
     * @return [Boolean bool]
     */
    function comprobacionCocheYusuario($idusuario, $idcoche)
    {
        //Sacamos el id de la empresa a la que pertenece el coche a traves del cliente con el que se asocia
        //SELECT empresa from clientes where id in( SELECT idcliente FROM `coches` WHERE id=1) 
        $empresaDelCoche = Clientes::select('empresa')->whereIn('id', Coches::select('idcliente')->where('id', $idcoche))->get();
        //Sacamos el id de empresa del usuario que se asocia con la reparacion para comprarar
        //SELECT empresa FROM `usuariosempresas` WHERE usuario=1 
        $empresaDelUsuario = Usuariosempresas::select('empresa')->where('usuario', $idusuario)->get();
        if(sizeof($empresaDelCoche)<=0 || sizeof($empresaDelUsuario)<=0 )//Si no encontro resultado en alguna de las dos consultas
            return false;

         //Recorremos el numero de empresas a las que pertence el usuario
         foreach($empresaDelUsuario as $valor)
         {
             //echo  $valor['empresa'];
             if ($empresaDelCoche[0]['empresa'] == $valor['empresa']) //Pertenecen a la misma empresa
             {
                return true;
             }
         }    

        return false;
    }

}
