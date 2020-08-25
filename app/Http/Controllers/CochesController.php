<?php

//Clase que realiza peticiones a la BD paramodificar o obtener datos y enviarlos donde se necesitan 
namespace App\Http\Controllers;

use App\Clientes;
use Illuminate\Http\Request;
use App\Coches;
use Illuminate\Support\Str;
//Añadido
use App\Http\Controllers\DB as DB;
use App\Http\Middleware\Authenticate;
use App\Utils;

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
     * @param mixed $id
     * Pasados un id de cliente, devolvemos todos los coches relacionados con dicho cliente
     * @return [json]
     */
    function cochesDeUnCliente($id)
    {
        $cliente = Clientes::find($id);
        return response()->json($cliente->coches);
    }

    //
    /**
     * @param  $idEmpresa 
     * Coches que pertenecen a clientes de una empresa determinada pasando por la url el id de la empresa
     * @return [json]
     */
    function cochesDeClientesDeUnaEmpresa($idEmpresa)
    {
        //Ejemplo sql= select * from coches where idCliente in(select id from clientes where empresa=1); 
        return response()->json(Coches::select()->whereIn('idcliente', Clientes::select('id')->where('empresa', $idEmpresa)->get())->get());
    }


    /**
     * @param mixed $id
     * Elimina un coche pasando  el id de este por parametro
     * @return [json]
     */
    function delete($id)
    {
        $coche = Coches::findOrFail($id);
        if (is_null($coche)) {
            return response()->json(['Error' => 'No existe el Coche el id indicado','id'=>$id], 202);
        }
        $coche->delete();
        return response()->json(["message" => "Coche eliminado con exito.", "Coche eliminado:" => $coche], 200);
    }

    /**
     * @param Request $request
     * @param mixed $id
     * Actualiza los campos de el parametro $request que llegan diferentes de null
     * @return [json]
     */
    function update(Request $request, $id)
    {
        $coche = Coches::findOrFail($id);
        $matricula = $request->matricula;
        $idcliente = $request->idcliente;
        $modelo = $request->modelo;
        $marca = $request->marca;

        $respuesta=array(); //Campos que fueron modificados
        if (!is_null($matricula)) {
            $coche->update([
                'matricula' => $matricula
            ]);
            array_push($respuesta, 'matricula');
        }

        if (!is_null($idcliente)) {
            $coche->update([
                'idcliente' => $idcliente
            ]);
            array_push($respuesta, 'idcliente');
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
