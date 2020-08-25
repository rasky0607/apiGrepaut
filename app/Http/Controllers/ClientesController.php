<?php

//Clase que realiza peticiones a la BD paramodificar o obtener datos y enviarlos donde se necesitan 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Clientes;
use Illuminate\Support\Str;
//Añadido
use App\Http\Controllers\DB as DB;
use App\Empresas;
use App\Utils;
use App\Coches;



class ClientesController extends Controller
{
     /**
     * @param Request $request
     * Registra un nuevo cliente insertando todos los campos optenidos
     * por el parametro request
     * @return [json]
     */
    function add(Request $request)
    {
        $email = $request->email;
        $tlf = $request->tlf;

        if (!Utils::validarTlf($tlf)) //Comprobacion de email  
            return response()->json(['Error' => 'El telefono usado no es correcto', 'tlf' => $tlf], 201);

        if (!is_null($email)) //Con email
        {
            if (!Utils::validarcorreo($email)) //Comprobacion de email  
                return response()->json(['Error' => 'El email usado no es correcto', 'Email' => $email], 201);

            $cliente = Clientes::create([
                'nombre' => $request->nombre,
                'empresa' => $request->empresa,
                'apellido' => $request->apellido,
                'tlf' => $request->tlf,
                'email' => $request->email
            ]);
        } else //Sin email
        {
            $cliente = Clientes::create([
                'nombre' => $request->nombre,
                'empresa' => $request->empresa,
                'apellido' => $request->apellido,
                'tlf' => $request->tlf,
                'email' => null
            ]);
        }
        return response()->json(['message' => 'Cliente registrado con exito', 'Cliente' => $cliente], 200);
    }

    /**
     * Lista todos los clientes
     * @return [json]
     */
    function list()
    {
        return response()->json(Clientes::all());
    }

    /**
     * @param mixed $idEmpresa
     * Dado un id de empresa devuelve todos los clientes que pertenecen a esa empresa
     * @return [json]
     */
    function clientesEmpresa($idEmpresa)
    {
        $empresa = Empresas::find($idEmpresa);
        if(is_null($empresa))
            return response()->json(["Error:"=>"No se encontro ninguna empresa con ese id.","IdEmpresa: "=>$idEmpresa],202);
        return response()->json($empresa->clientes);
    }

    /**
     * @param mixed $idEmpresa
     * @param mixed $nombre
     * @param null $apellido
     * Busca un cliente concreto o con nombre que empiecen igual al indicado,
     * o un apellido y pertenezca a un Id de empresa concreto
     * @return [json]
     */
    function buscarCliente($idEmpresa, $nombre, $apellido = null)
    {
        //dd('idEmpresa',$idEmpresa, ' nombre',$nombre,' apellidos',$apellidos);
        if (is_null($apellido)) {
            $result = Clientes::where('empresa', $idEmpresa)->where('nombre', 'like', urlencode($nombre) . '%')->get();

            if (sizeof($result) <= 0) //Si NO encontro resultados
                return response()->json(['msg', 'No se encontron clientes con el nombre indicado ', 'nombre' => urlencode($nombre)], 202);

            return response()->json(['msg', 'Cliente encontrado' => $result], 201);
        } else {
            //app('db')->enableQueryLog();//Activar registro de querys   
            $result = Clientes::where('empresa', $idEmpresa)->where('nombre', urlencode($nombre))->where('apellido', 'like', urlencode($apellido) . '%')->get();
            //dd(app('db')->getQueryLog());
            if (sizeof($result) <= 0) //Si NO encontro resultados
                return response()->json(['msg', 'No se encontron clientes con el nombre y apellidos indicados ', 'nombre' => urlencode($nombre), 'apellidos' => urlencode($apellido)], 202);

            return response()->json(['msg', 'Cliente encontrado' => $result], 201);
        }
    }

    /**
     * @param mixed $id
     * Elimina un cliente pasando su id por parametro y lo devuelve
     * @return [json]
     */
    function delete($id)
    {
        $cliente = Clientes::find($id);
        if (is_null($cliente)) //Si no encuentra el cliente
            return response()->json(['message' => 'No existe el cliente'], 202);

        $cliente->delete();
        return response()->json(['message' => 'Cliente eliminado con exito', 'Cliente' => $cliente], 200);
    }

    /**
     * @param Request $request
     * @param mixed $id
     * Actualiza los campos de el parametro $request que llegan diferentes de null
     * @return [json]
     */
    function update(Request $request, $id)
    {
        $cliente = Clientes::findOrFail($id);
        $nombre = $request->nombre;
        $empresa = $request->empresa;
        $apellido = $request->apellido;
        $tlf = $request->tlf;
        $email = $request->email;

        //Comprobaciones
        if(!is_null($tlf))
        {
            if (!Utils::validarTlf($tlf)) //Comprobacion de numero de Tlf
                return response()->json(['Error' => 'El telefono de el cliente NO es correcto', 'Telefono' => $tlf], 202);
        }
        if (!is_null($email)) 
        {
            if (!Utils::validarcorreo($email))//Comprobacion de numero de Email
                return response()->json(['Error' => 'El email usado no es correcto', 'Email' => $email], 202);
        }


        $respuesta=array(); //Campos que fueron modificados
        if (!is_null($nombre)) {
            $cliente->update([
                'nombre' => $nombre
            ]);
            array_push($respuesta, 'nombre');
        }

        if (!is_null($empresa)) {
            $cliente->update([
                'empresa' => $empresa
            ]);
            array_push($respuesta, 'empresa');
        }

        if (!is_null($apellido)) {
            $cliente->update([
                'apellido' => $apellido
            ]);
            array_push($respuesta, 'apellido');
        }

        if (!is_null($tlf)) {
            $cliente->update([
                'tlf' => $tlf
            ]);
            array_push($respuesta, 'tlf');
        }

        if (!is_null($email)) {
            $cliente->update([
                'email' => $email
            ]);
            array_push($respuesta ,'email ');
        }
        return response()->json(['message' => 'Cliente actualizado con exito', 'Modificaciones' => $respuesta, 'Cliente' => $cliente], 200);
    }

}
