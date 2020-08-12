<?php

//Clase que realiza peticiones a la BD paramodificar o obtener datos y enviarlos donde se necesitan 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Clientes;
use Illuminate\Support\Str;
//Añadido
use App\Http\Controllers\DB as DB;
use App\Empresas;

class ClientesController extends Controller
{
    //Registro
    function add(Request $request)
    {
       
    }

  
    //Lista todos los clientes
    function list()
    {
        return response()->json(Clientes::all());
    }

    //Dado un id de empresa devuelve todos los clientes que pertenecen a esa empresa
    function clientesEmpresa($idEmpresa){
        $empresa = Empresas::findOrFail($idEmpresa);
        return response()->json($empresa->clientes);
    }
    //Busca un cliente concreto o con nombre que empiecen igual al indicado
    function buscarCliente($nombre,$apellidos=null)
    {
      
    }

    function delete($id)
    {
       
    }

    //Actualiza los campos que llegan diferentes de null
    function update(Request $request, $id)
    {
      
    }


    //Comprobacion de Tlf
    function validarTlf($tlf)
    {
        if (!preg_match("/(^\+)?([0-9]{9,12}$)/", $tlf)) //Comprobacion de numero de Tlf
            return true;
        return false;
    }
}
