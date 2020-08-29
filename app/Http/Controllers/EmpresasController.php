<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Empresas;
use Illuminate\Support\Str;
use App\Utils;

/**
 * [Description EmpresasController]
 * Clase que realiza peticiones a la BD paramodificar o obtener datos y enviarlos donde se necesitan en formato Json
 */
class EmpresasController extends Controller
{
    /**
     * @param Request $request
     * Registra una nueva empresa insertando todos los campos optenidos
     * por el parametro request
     * @return [json]
     */
    function add(Request $request)
    {
        $tlf = $request->tlf;
        if (!Utils::validarTlf($tlf)) //Comprobacion de numero de Tlf
            return response()->json(['Error' => 'El telefono de la empresa NO es correcto', 'Telefono' => $tlf], 202);
        $empresa = Empresas::create([
            'nombre' => $request->nombre,
            'direccion' => $request->direccion,
            'tlf' => $request->tlf
        ]);
        return response()->json(['message' => 'Empresa registrada con exito', 'Empresa' => $empresa], 200);
    }

     /**
     * Lista todas las empresas
     * @return [json]
     */   
    function list()
    {
        return response()->json(Empresas::all());
    }
 
    /**
     * @param mixed $nombre
     * Busca una empresa concreta que empieze igual, pasando un nombre por parametro
     * @return [json]
     */
    function buscarEmpresa($nombre)
    {
        $empresa = Empresas::where('nombre', 'like',  '%'.urldecode($nombre) . '%')->get();
        if (sizeof($empresa) <= 0)
            return response()->json(['Error' => 'No se encontro la empresa: ', 'Empresa' => urldecode($nombre)], 202);
        else
            return response()->json(['Empresa' => $empresa], 200);
    }

    /**
     * @param mixed $id
     * Elimina un empresa pasando su id por parametro y la devuelve
     * @return [json]
     */
    function delete($id)
    {
        $empresa = Empresas::findOrFail($id);
        if (is_null($empresa)) {
            return response()->json(['message' => 'No existe esta empresa'], 202);
        }
        //Si encontro una empresa      
        $empresa->delete();
        return response()->json(['message' => 'Empresa se ha eliminado con exito', 'Empresa' => $empresa], 200);
    }

    /**
     * @param Request $request
     * @param mixed $id
     * Actualiza los campos de el parametro $request que llegan diferentes de null
     * @return [json]
     */
    function update($id, Request $request)
    {
        $empresa = Empresas::findOrFail($id);
        $nombre = $request->nombre;
        $direccion = $request->direccion;
        $tlf = $request->tlf;

        if (!is_null($tlf)) {
            if (!Utils::validarTlf($tlf)) //Comprobacion de numero de Tlf
                return response()->json(['Error' => 'El telefono de la empresa NO es correcto', 'Telefono' => $tlf], 202);
        }

        $respuesta = array(); //Campos que fueron modificados
        if (!is_null($nombre)) {
            $empresa->update([
                'nombre' => $nombre
            ]);
            array_push($respuesta, 'Nombre ');
        }

        if (!is_null($direccion)) {
            $empresa->update([
                'direccion' => $direccion
            ]);
            array_push($respuesta, 'Direcion ');
        }

        if (!is_null($tlf)) {
            $empresa->update([
                'tlf' => $tlf
            ]);
            array_push($respuesta, 'Tlf ');
        }
        return response()->json(['message' => 'Empresa actualizada con exito', 'Modificaciones' => $respuesta, 'Empresa' => $empresa], 200);
    }

    
}
