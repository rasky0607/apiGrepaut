<?php

//Clase que realiza peticiones a la BD paramodificar o obtener datos y enviarlos donde se necesitan 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Usuarios;
use Illuminate\Support\Str;
//Añadido
use App\Http\Controllers\DB as DB;
use App\Http\Middleware\Authenticate;

class UsuariosController extends Controller
{
    //Registro
    function add(Request $request)
    {
        //Comprobacion de email
        $email = $request->email;
        if (!$this->validarcorreo($email))
            return response()->json(['Error' => 'El email usado no es correcto', 'Email' => $email], 201);

        $user = Usuarios::create([
            'email' => $request->email,
            'password' => $request->password,
            'nombre' => $request->nombre,
            'token' => Str::random(10)
        ]);
        return response()->json(['message' => 'User registrado con exito', 'usuario' => $user], 200);
    }

    //Iniciar sesion devolvera el token necesario al usuario para realizar el resto de consultas a la API
    function login(Request $request)
    {
        $email = $request->email;
        $password =$request->password;
        $user= Usuarios::select('id')->where('email', $email)->where('password', $password)->get();
        if (sizeof($user)== 1) //Logeo correcto
        {
            //Generamos un nuevo token y lo devolvemos al usuario
            $token = Str::random(10);
            //Actualizamos/guardamnos el nuevo token en la BD
            $user= $this->actualizarToken($user[0]['id'],$token);
            return response()->json(['message' => 'Credenciales correctas','token '=>$token],200);
        } else {
            //Email o contraseña incorrectas
            return response()->json(['message' => 'Email o Password incorrecto'],201);
        }
    }

    //Actualiza el token del usuario
    function actualizarToken($id,$token){
        $user = Usuarios::findOrFail($id);
        $user->update([
            'token' => $token
        ]);
    }

    //Lista todos los usuarios
    function list()
    {
        return response()->json(Usuarios::all());
    }

    //Busca un usuario concreto o con correos que empiecen igual al indicado
    function buscarUsuario($email)
    {
        app('db')->enableQueryLog(); //Activar registro de querys
        $usuario = Usuarios::where('email', 'like', urldecode($email) . '%')->get();
        if (sizeof($usuario) <= 0) {
            return response()->json(['message' => 'No existe el usuario', 'Usuario con email:' => urldecode($email)], 200);
        } else {
            return response()->json(['Usuario:' => $usuario], 200);
        }
    }

    function delete($id)
    {
        $user = Usuarios::findOrFail($id);
        //Si no encontro usuarios
        if (is_null($user)) {
            return response()->json(['message' => 'No existe el usuario'], 200);
        }
        //Si econtro un usuario      
        $user->delete();
        return response()->json(['message' => 'User eliminado con exito', 'usuario' => $user], 200);
    }

    //Actualiza los campos que llegan diferentes de null
    function update(Request $request, $id)
    {
        $user = Usuarios::findOrFail($id);
        $email = $request->email;
        dd($request->email);
        $nombre = $request->nombre;
        $password = $request->password;
        $respuesta = ' '; //Campos que fueron modificados
        if ($email != null) {
            if (!$this->validarcorreo($email))
                return response()->json(['Error' => 'El email usado no es correcto', 'Email' => $email], 201);
            $user->update([
                'email' => $email
            ]);
            $respuesta .= 'email ';
        }

        if ($nombre != null) {
            $user->update([
                'nombre' => $nombre
            ]);
            $respuesta .= 'Nombre ';
        }

        if ($password != null) {
            $user->update([
                'password' => $password
            ]);
            $respuesta .= 'password ';
        }


        return response()->json(['message' => 'Usuario actualizado con exito', 'Modificaciones' => $respuesta, 'usuario' => $user], 200);
    }


    //Comprobacion de email
    function validarcorreo($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) == false)
            return false;
        else
            return true;
    }
}
