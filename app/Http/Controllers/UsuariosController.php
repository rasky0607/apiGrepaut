<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Usuarios;
use Illuminate\Support\Str;
//Añadido
use App\Http\Controllers\DB as DB;
use App\Http\Middleware\Authenticate;
use App\Utils;

/**
 * [Description UsuariosController]
 * Clase que realiza peticiones a la BD paramodificar o obtener datos y enviarlos donde se necesitan en formato Json
 */
class UsuariosController extends Controller
{
     /**
     * @param Request $request
     * Registra un nuevo Usuario insertando todos los campos optenidos
     * por el parametro request
     * @return [json]
     */
    function add(Request $request)
    {
        //Comprobacion de email
        $email = $request->email;
        if (!Utils::validarcorreo($email))
            return response()->json(['Error' => 'El email usado no es correcto', 'Email' => $email], 202);

        $user = Usuarios::create([
            'email' => $request->email,
            'password' => $request->password,
            'nombre' => $request->nombre,
            'idempresa' => $request->idempresa,
            'token' => Str::random(10)
        ]);
        return response()->json(['message' => 'User registrado con exito', 'usuario' => $user], 200);
    }

    /**
     * @param Request $request
     * Iniciar sesion devolvera el token necesario al usuario para realizar el resto de consultas a la API
     * @return [Json]
     */
    function login(Request $request) {
        $email = $request->email;
        $password =$request->password;
        //$user= Usuarios::select('id')->where('email', $email)->where('password', $password)->get();
        $user= Usuarios::where('email', $email)->where('password', $password)->get();
        if (sizeof($user)== 1) //Logeo correcto
        {   //Obtenemos el id del usuario
            $id = $user[0]['id'];
            $idempresa = $user[0]['idempresa'];
            //Actualizamos/guardamnos el nuevo token en la BD
            $token= $this->actualizarToken($id);
            return response()->json(['message' => 'Credenciales correctas','idempresa'=>$idempresa,'id'=>$id,'token'=>$token],200);
        } else {
            //Email o contraseña incorrectas
            return response()->json(['message' => 'Email o Password incorrecto'],202);
        }
    }

    /**
     * @param mixed $id
     * @param mixed $token
     * Actualiza el token del usuario
     * @return [Json]
     */
    function actualizarToken($id) {
        //Generamos un nuevo token y lo devolvemos al usuario
        $token = Str::random(10);
        $user = Usuarios::findOrFail($id);
        $user->update([
            'token' => $token
        ]);
        return $token;
    }
    
    function logout($id) {
        $token = $this->actualizarToken($id);
        return response()->json(['message' => 'deslogueado','id de usuario deslogueado'=>$id,'token'=>$token],200);
    }

    /**
     * Lista todos los usuarios
     * @return [Json]
     */
    function list() {
        return response()->json(Usuarios::all());
    }

    //Busca un usuario concreto o con correos que empiecen igual al indicado y pertenezca a una empresa determinada
    function buscarUsuario(Request $request) {
        $email = $request->email;
        $idempresa = $request->idempresa;
        //echo $email;
        //return;
        //app('db')->enableQueryLog(); //Activar registro de querys
        $usuario = Usuarios::where('email', 'like', '%'.urldecode($email) . '%')->where('idempresa',$idempresa)->get();
        //$query = dd(app('db')->getQueryLog());
        
        if (sizeof($usuario) <= 0) {
            return response()->json(['message' => 'No existe el usuario', 'Usuario con email:' => urldecode($email)], 202);
        } else {
            return response()->json(['Usuario:' => $usuario], 200);
        }
    }

    /**
     * @param mixed $id
     * Elimina un usuario indicando su id por parametro y lo devuelve
     * @return [Json]
     */
    function delete($id) {
        $user = Usuarios::findOrFail($id);
        //Si no encontro usuarios
        if (is_null($user)) {
            return response()->json(['Error' => 'No existe el usuario'], 202);
        }
        //Si econtro un usuario      
        $user->delete();
        return response()->json(['message' => 'User eliminado con exito', 'usuario' => $user], 200);
    }

    /**
     * @param Request $request
     * @param mixed $id
     * Actualiza los campos de el parametro $request que llegan diferentes de null
     * @return [json]
     */
    function update(Request $request, $id)
    {
        $user = Usuarios::findOrFail($id);
        $email = $request->email;
        $nombre = $request->nombre;
        $password = $request->password;
        $idempresa = $request->idempresa;
        $respuesta=array(); //Campos que fueron modificados
        if (!is_null($email)) {
            if (!Utils::validarcorreo($email))
                return response()->json(['Error' => 'El email usado NO es correcto', 'Email' => $email], 202);
            $user->update([
                'email' => $email
            ]);
            array_push($respuesta,'email');//Añadimos al final del array
        }

        if (!is_null($nombre)) {
            $user->update([
                'nombre' => $nombre
            ]);
            array_push($respuesta,'Nombre');
        }

        if (!is_null($password)) {
            $user->update([
                'password' => $password
            ]);
            array_push($respuesta, 'password');
        }
        
        if (!is_null($idempresa)) {
            $user->update([
                'idempresa' => $idempresa
            ]);
            array_push($respuesta, 'idempresa');
        }


        return response()->json(['message' => 'Usuario actualizado con exito', 'Modificaciones' => $respuesta, 'usuario' => $user], 200);
    }

}
