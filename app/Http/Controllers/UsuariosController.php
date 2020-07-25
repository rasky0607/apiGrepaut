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
        'id'=>$request->id,
        'email'=>$request->email,
        'password'=>$request->password,
        'empresa'=>$request->empresa,
        'nombre'=>$request->nombre,
        'tipo'=>$request->tipo,
        'tienePermiso'=>$request->tienePermiso
    ]);
    return response()->json($user,201);
   }

    function list(){
        return response()->json(Usuarios::all());

        //**FUNCIONA **
        //$result=app('db')->select("SELECT * FROM usuarios");
        //return response()->json($result);
   }

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

    //Funciona pero tenemos que poner otro update en el caso de que queramos actualizar solo uno de los campos no todos
   function update(Request $request,$id){
    $user= Usuarios::findOrFail($id);
    $user->update([
        'email'=>$request->email,
        'nombre'=>$request->nombre,
        'tipo'=>$request->tipo,
        'tienePermiso'=>$request->tienePermiso


    ]);
    return response()->json(['message' => 'Usuario actualizado con exito','usuario'=>$user], 200);
   }


}
