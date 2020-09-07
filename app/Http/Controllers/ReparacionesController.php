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
        $estadoReparacion='No facturado';//Valor por defecto al crear una reparacion
        if (!$this->comprobacionCocheYusuario($idusuario, $idcoche))
            return response()->json(['Error' => 'El id del coche y el id de usuario[el tecnico] no pertenecen a la mism empresa', 'id de coche' => $idcoche, 'id de usuario' => $idusuario], 202);

        $reparacion = Reparaciones::create([
            'estadoReparacion' => $estadoReparacion,
            'idusuario' => $request->idusuario,
            'idcoche' => $request->idcoche,
            'empresa'=>$request->empresa
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
    function listReparacionesEmpresa($idEmpresa)
    {
        $reparaciones = Reparaciones::select()->whereIn('idusuario', Usuariosempresas::select('usuario')->where('empresa', $idEmpresa)->get())->get();
        if(sizeof($reparaciones)<=0)//No encontro el cliente 
            return response()->json(['Error' => 'No existe reparaciones registradas en la empresa indicada.', 'Id de empresa' => $idEmpresa], 202);
        
        return response()->json($reparaciones);
    }

    /**
     * @param mixed $idusuario
     * Listar de reparaciones de un usuario o tecnico concreto pasando un id de usuario
     * @return [json]
     */
    function listReparacionesUsuario($idusuario)
    {
        $usuario = Usuarios::find($idusuario);
        if(is_null($usuario))//No encontro el usuario
            return response()->json(['Error' => 'No existe ese tecnico o id de usuario.', 'Id de usuario' => $idusuario], 202);

        return response()->json($usuario->reparaciones);
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
    function update(Request $request, $id) //### PENDIENTE DE TESTEO ##
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
     * Comprueba con el id del del coche que pertenece a un id de cliente,
     * corresponda con la misma empresa que el id del usuario.
     * Si encuentra alguna coincidencia devuelve true,
     * en caso contrario false
     * @return [Booblean bool]
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
        if ($empresaDelCoche[0]['empresa'] == $empresaDelUsuario[0]['empresa']) //Pertenecen a la misma empresa
            return true;

        return false;
    }
}
