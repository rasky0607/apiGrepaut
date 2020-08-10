<?php

//Clase que realiza peticiones a la BD paramodificar o obtener datos y enviarlos donde se necesitan 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Usuarios;
use Illuminate\Support\Str;
//Añadido
use App\Http\Controllers\DB as DB;

class UsuariosController extends Controller
{
    //Registro
   function add(Request $request){
       //Comprobacion de email
       $email = $request->email;
       if(!$this->validarcorreo($email))
        return response()->json(['Error' =>'El email usado no es correcto','Email'=>$email],201);

        $user = Usuarios::create([
            'email'=>$request->email,
            'password'=>$request->password,
            'nombre'=>$request->nombre,     
            'token'=>Str::random(10)
    ]);
    return response()->json(['message' =>'User registrado con exito','usuario'=>$user],200);
   }

   //Iniciar sesion
   function inicioSesion(Request $request){
       $email = $request->email;
       $password=$request->password;
       if(sizeof(Usuarios::select()->where('email',$email)->where('password',$password)>0))//Encontro algun resultado
       {
           //Logeo correcto
       }else{
           //Email o contraseña incorrectas
       }

   }

   //Lista todos los usuarios
    function list(){          
        return response()->json(Usuarios::all());  
   }

   //Dado un id de usuario, muestra todas las empresas relacionadas con ese usuario y sus permisos en estas
   function ususarioEmpresas($id){
        $usuario=Usuarios::findOrFail($id); 
        return response()->json($usuario->empresas);
   }

   //Busca un usuario concreto o con correos que empiecen igual al indicado
   function buscarUsuario($email){
    app('db')->enableQueryLog();//Activar registro de querys
    $usuario=Usuarios::where('email','like',urldecode($email).'%')->get();
    if(sizeof($usuario)<=0){     
        return response()->json(['message' => 'No existe el usuario','Usuario con email:'=>urldecode($email)], 200);
    }
    else
    {      
        return response()->json(['Usuario:'=>$usuario], 200);
    }
   }

   //###Modificar#### ya que la tabla usuarios ya no tiene la empresa
   /*function listPorEmpresa($empresa,$email=null){
      if(is_null($email))
        return response()->json(Usuarios::all()->where('empresa','like',$empresa)->values());
      else
      return response()->json(Usuarios::all()->where('empresa','like',$empresa)->where('email','like',$email)->values());       
       
    }*/
    
   function delete($id){  
        $user= Usuarios::findOrFail($id);
        //Si no encontro usuarios
        if(is_null($user)){
            return response()->json(['message' => 'No existe el usuario'], 200);
        }
        //Si econtro un usuario      
            $user->delete();
            return response()->json(['message' => 'User eliminado con exito','usuario'=>$user], 200);
          
   }

    //Actualiza los campos que llegan diferentes de null
   function update(Request $request,$id){    
        $user= Usuarios::findOrFail($id);
        $email=$request->email;
        $nombre=$request->nombre;
        $password=$request->password;
        $respuesta=' ';//Campos que fueron modificados
        //dd($email);
        if($email!=null){
            if(!$this->validarcorreo($email))
                return response()->json(['Error' =>'El email usado no es correcto','Email'=>$email],201);
            $user->update([
                'email'=>$email
            ]);
            $respuesta .='email ';          
        }

        if($nombre!=null){
            $user->update([
                'nombre'=>$nombre
            ]);
            $respuesta .='Nombre ';          
        }

        if($password!=null){
            $user->update([
                'password'=>$password
            ]);
            $respuesta .='password ';          
        }

        
        return response()->json(['message' => 'Usuario actualizado con exito','Modificaciones'=>$respuesta,'usuario'=>$user], 200);
   }


     //Comprobacion de email
     function validarcorreo($email){
        if(filter_var($email,FILTER_VALIDATE_EMAIL)== false)
            return false;
        else
            return true;
        }


}



