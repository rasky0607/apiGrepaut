<?php

//Clase que realiza peticiones a la BD paramodificar o obtener datos y enviarlos donde se necesitan 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Usuarios;
use Illuminate\Support\Str;


class UsuariosController extends Controller
{
   function add(Request $request){
    $user = Usuarios::create([
        //'id'=>$request->id,
        'email'=>$request->email,
        'password'=>$request->password,
        'nombre'=>$request->nombre,     
        'token'=>Str::random(10)
    ]);
    return response()->json(['message' =>'User registrado con exito','usuario'=>$user],201);
   }

    function list(){
        return response()->json(Usuarios::all());
   }

   //###Modificar#### ya que la tabla usuarios ya no tiene la empresa
   function listPorEmpresa($empresa,$email=null){
      if(is_null($email))
        return response()->json(Usuarios::all()->where('empresa','like',$empresa)->values());
      else
      return response()->json(Usuarios::all()->where('empresa','like',$empresa)->where('email','like',$email)->values());       
       
    }
    
   function delete(Request $request){
        $id=$request->id;   
        $user = Usuarios::where('id',$id)->first();
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
        //dd($email);
        if($email!=null){
            $user->update([
                'email'=>$email
            ]);
        }

        if($nombre!=null){
            $user->update([
                'nombre'=>$nombre
            ]);
        }

        if($nombre!=null){
            $user->update([
                'nombre'=>$nombre
            ]);
        }

        if($password!=null){
            $user->update([
                'password'=>$password
            ]);
        }

        
        return response()->json(['message' => 'Usuario actualizado con exito','usuario'=>$user], 200);
   }


}
