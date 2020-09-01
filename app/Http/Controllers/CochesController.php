<?php

namespace App\Http\Controllers;

use App\Clientes;
use Illuminate\Http\Request;
use App\Coches;
use Illuminate\Support\Str;
//Añadido
use App\Http\Controllers\DB as DB;
use App\Http\Middleware\Authenticate;
use App\Utils;

/**
 * [Description CochesController]
 * Clase que realiza peticiones a la BD paramodificar o obtener datos y enviarlos donde se necesitan en formato Json
 */
class CochesController extends Controller
{
    /**
     * @param Request $request
     * Registra un nuevo coche insertando todos los campos optenidos
     * por el parametro request
     * @return [json]
     */
    function add(Request $request)
    {
        $coche = Coches::create([
            'matricula' => $request->matricula,
            'idcliente' => $request->idcliente,
            'modelo' => $request->modelo,
            'marca' => $request->marca
        ]);
        return response()->json(['message' => 'Coche registrado con exito', 'Coche' => $coche], 200);
    }

    /**
     * Lista todos los coches
     * @return [json]
     */
    function list()
    {
        return response()->json(Coches::all());
    }

    /**
     * @param mixed $idCliente
     * Pasados un id de cliente, devolvemos todos los coches relacionados con dicho cliente
     * @return [json]
     */
    function cochesDeUnCliente($idCliente)
    {
        $cliente = Clientes::findOrFail($idCliente);
        if(is_null($cliente))//No encontro el cliente 
            return response()->json(['Error' => 'No existe ese id de cliente.', 'Id de cliente' => $cliente], 202);
        return response()->json($cliente->coches, 200);//Devuelve todos los coches relacionados con ese cliente
    }

    /**
     * @param mixed $id
     * Muestra los dato de un coche determinado
     * @return [Json]
     */
    function unCoche($id)
    {
        return response()->json(Coches::select()->where('id', $id)->get());
    }

    /**
     * @param  $idEmpresa 
     * Coches que pertenecen a clientes de una empresa determinada pasando por la url el id de la empresa
     * @return [json]
     */
    function cochesDeClientesDeUnaEmpresa($idEmpresa)
    {
        //Ejemplo sql= select * from coches where idCliente in(select id from clientes where empresa=1); 
        $coches=Coches::select()->whereIn('idcliente', Clientes::select('id')->where('empresa', $idEmpresa)->get())->get();
        if(sizeof($coches)<=0)//No encontro el cliente 
            return response()->json(['Error' => 'No existe coches registrados para los clientes de la empresa indicada.', 'Id de empresa' => $idEmpresa], 202);
        
        return response()->json($coches);
        //return response()->json(Coches::select()->whereIn('idcliente', Clientes::select('id')->where('empresa', $idEmpresa)->get())->get());
    }


    /**
     * @param mixed $idCliente
     * @param mixed $matricula
     * Elimina un coche pasando  el id de cliente y una matricula por parametro
     * @return [json]
     */
    function delete($id)
    {
        $coche = Coches::find($id);
        if (is_null($coche)) {
            return response()->json(['Error' => 'No existe el Coche con el id indicado', 'id coche' => $id], 202);
        }
        //Si encontro una empresa      
        $coche->delete();
        return response()->json(["message" => "Coche eliminado con exito.", "Coche eliminado:" => $coche], 200);
    }

    /**
     * @param Request $request
     * @param mixed $idClienteVij
     * @param mixed $matriculaCoche
     * Actualiza los campos de el parametro $request que llegan diferentes de null
     * pasando un id de cliente y una matricula por parametro
     * @return [json]
     */
    function update(Request $request, $id)
    {
        $coche = Coches::findOrFail($id);
        $matriculaNueva = $request->matricula;
        $idclienteNuevo = $request->idcliente;
        $modelo = $request->modelo;
        $marca = $request->marca;
        $respuesta = array(); //Campos que fueron modificados

        if (!is_null($idclienteNuevo)) {
            $coche->update([
                'idcliente' => $idclienteNuevo
            ]);
            array_push($respuesta, 'idcliente');
        }

        if (!is_null($matriculaNueva)) {
            $coche->update([
                'matricula' => $matriculaNueva
            ]);
            array_push($respuesta, 'matricula');
        }

        if (!is_null($modelo)) {
            $coche->update([
                'modelo' => $modelo
            ]);
            array_push($respuesta, 'modelo');
        }

        if (!is_null($marca)) {
            $coche->update([
                'marca' => $marca
            ]);
            array_push($respuesta, 'marca');
        }
        return response()->json(['message' => 'Cliente actualizado con exito', 'Modificaciones' => $respuesta, 'Coche' => $coche], 200);
    }
}
