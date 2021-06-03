<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Servicios;
use Illuminate\Support\Str;
//Añadido
use App\Http\Controllers\DB as DB;
use App\Http\Middleware\Authenticate;
use App\Utils;
use App\Empresas;

/**
 * [Description ServiciosController]
 * Clase que realiza peticiones a la BD paramodificar o obtener datos y enviarlos donde se necesitan en formato Json
 */
class ServiciosController extends Controller
{
    /**
     * @param Request $request
     * Registra un nuevo servicio indicando un request con los nombre de los campso y valores de cada uno de los atributos del servicio
     * y devuelve un json con el servicio creado
     * @return [Json]
     */
    function add(Request $request)
    {     
        $servicio = Servicios::create([
            'nombre' => $request->nombre,
            'empresa' => $request->empresa,
            'precio' => $request->precio,
            'descripcion'=> $request->descripcion
        ]);
        return response()->json(['message' => 'Servicio registrado con exito', 'Servicio' => $servicio], 200);
    }

    /**
     * Lista todos los servicios
     * @return [json]
     */
    function list()
    {
        return response()->json(Servicios::all());
    }

    /**
     * @param mixed $idEmpresa
     * Busca servicios que pertenecen a una empresa
     * @return [json]
     */
    function buscarServiciosDeEmpresa($idEmpresa)
    {
        $empresa=Empresas::find($idEmpresa);
        if(is_null($empresa))//No encontro la empresa
            return response()->json(['Error' => 'No existe ese id de empresa.', 'Id de empresa' => $empresa], 202);

        return response()->json($empresa->servicios);//Devuelve todos los servicios relacionados con esa empresa
    }

    /**
     * @param mixed $idEmpresa
     * @param mixed $nombre
     * Busca un servicios con nombre similares al indicado de una empresa
     * @return [json]
     */
    function buscarUnServicio($idEmpresa,$nombre)
    {
        $servicio=Servicios::where('empresa',$idEmpresa)->where('nombre','like','%'.urldecode($nombre).'%')->get();
        if(sizeof($servicio)>0)
            return response()->json(['message'=>'Servicios similares encontrados:','Servicios'=>$servicio],200);

        return response()->json(['Error'=>'No se encontraron Servicios similares:'],202);
    }

    /**
     * @param mixed $id
     * Elimina un servicio indicando su id por parametro y lo devuelve
     * @return [json]
     */
    function delete($id)
    {
        $servicio = Servicios::findOrFail($id);
        //Si no encontro servicio
        if (is_null($servicio)) {
            return response()->json(['Error' => 'No existe el Servicio con dicho id.','Id'=>$id], 202);
        }
        //Si econtro un servicio      
        $servicio->delete();
        return response()->json(['message' => 'Servicio eliminado con exito', 'Servicio' => $servicio], 200);
    }

    /**
     * @param Request $request
     * @param mixed $id
     * Actualiza los campos de el parametro $request que llegan diferentes de null
     * @return [json]
     */
    function update(Request $request, $id)
    {
        $servicio = Servicios::findOrFail($id);
        $nombre = $request->nombre;
        $empresa = $request->empresa;
        $precio = $request->precio;
        $descripcion=$request->descripcion;
        $respuesta=array(); //Campos que fueron modificados
        if (!is_null($nombre)) {
            $servicio->update([
                'nombre' => $nombre
            ]);
            array_push($respuesta,'nombre');//Añadimos al final del array
        }

        if (!is_null($empresa)) {
            $servicio->update([
                'empresa' => $empresa
            ]);
            array_push($respuesta,'empresa');
        }

        if (!is_null($precio)) {
            $servicio->update([
                'precio' => $precio
            ]);
            array_push($respuesta, 'precio');
        }

        if (!is_null($descripcion)) {
            $servicio->update([
                'descripcion' => $descripcion
            ]);
            array_push($respuesta, 'descripcion');
        }


        return response()->json(['message' => 'Servicio actualizado con exito', 'Modificaciones' => $respuesta, 'Servicio' => $servicio], 200);
    }

}
