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
     * @param mixed $idCliente
     * Pasados un id de cliente, devolvemos todos los coches relacionados con dicho cliente
     * @return [json]
     */
    function cochesDeUnCliente($idCliente)
    {
        $cliente = Clientes::findOrFail($idCliente);
        return response()->json($cliente->coches,200);
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
     * @param mixed $idCliente
     * @param mixed $matricula
     * Elimina un coche pasando  el id de cliente y una matricula por parametro
     * @return [json]
     */
    function delete($idCliente,$matricula)
    {
        $coche= Coches::where('idcliente',$idCliente)->where('matricula',$matricula);
        if (is_null($coche)) {
            return response()->json(['Error' => 'No existe el Coche la matricula y el id de cliente indicados','matricula'=>$matricula,'id cliente'=>$idCliente], 202);
        }
        //Si encontro un resultado 
        $objetoEliminado= clone $coche->get();//Necesitamos clonar el objeto ya que una vez es eliminado no podemos vovler a mostrarlo, a diferenica de los que son buscados por id
        $coche->delete();
        return response()->json(["message" => "Coche eliminado con exito.", "Coche eliminado:" => $objetoEliminado], 200);
    }

    /**
     * @param Request $request
     * @param mixed $idClienteVij
     * @param mixed $matriculaCoche
     * Actualiza los campos de el parametro $request que llegan diferentes de null
     * pasando un id de cliente y una matricula por parametro
     * @return [json]
     */
    function update(Request $request, $idClienteVij,$matriculaCoche)
    {
        $coche= Coches::where('idcliente',$idClienteVij)->where('matricula',$matriculaCoche);
        $matriculaNueva = $request->matricula;
        $idclienteNuevo = $request->idcliente;
        $modelo = $request->modelo;
        $marca = $request->marca;
        $respuesta=array(); //Campos que fueron modificados

        if(!is_null($idclienteNuevo) && !is_null($matriculaNueva))//Si  las claves son distintas de null
        {
            $coche->update([
                'matricula' => $matriculaNueva,
                'idcliente' => $idclienteNuevo
            ]);
            array_push($respuesta, 'matricula');//Añadimos al final del array de modificaciones
            array_push($respuesta, 'idcliente');
            $coche= Coches::where('idcliente',$idclienteNuevo)->where('matricula',$matriculaNueva);
        }else if(!is_null($idclienteNuevo) && is_null($matriculaNueva))//Si el ID Cliente NO es null pero la matricula si
        {
            $coche->update([
                'idcliente' => $idclienteNuevo
            ]);
            array_push($respuesta, 'idcliente');
            $coche= Coches::where('idcliente',$idclienteNuevo)->where('matricula',$matriculaCoche);

        }else if(is_null($idclienteNuevo) && !is_null($matriculaNueva))//Si la Matricula NO es null pero el id cliente si
        {
            $coche->update([
                'matricula' => $matriculaNueva
            ]);
            array_push($respuesta, 'matricula');
            $coche= Coches::where('idcliente',$idClienteVij)->where('matricula',$matriculaNueva);
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
        return response()->json(['message' => 'Cliente actualizado con exito', 'Modificaciones' => $respuesta, 'Coche' => $coche->get()], 200);
    }

}
