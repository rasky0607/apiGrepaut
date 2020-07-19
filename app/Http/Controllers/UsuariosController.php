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
    /*function listPorEmpresaEmail($empresa,$email){
        return response()->json(Usuarios::all()->where('empresa','like',$empresa)->where('email','like',$email)->values());       
    }*/

   function delete(Request $request){
       $email=$request->email;
       $empresa=$request->empresa;
       //dd($email);
    //app('db')->enableQueryLog();
    $user = Usuarios::where('empresa','like',$empresa)->where('email','like',$email);
    //dd(app('db')->getQueryLog());
    //app('db')->disableQueryLog();
    //dd($user);
    $user2=$user;
    if(is_null($user)){
        return response()->json(['message' => 'No existe el usuario'], 200);
    }
    $user->delete();
    //**Arreglar que muestre en elmensaje de exito el u suario eliminado**
    return response()->json(['message' => 'User eliminado con exito','deleteUser'=>$user2], 200);
   }

    ////**NO FUNCIONA **
   function update(Request $request,$email,$empresa){
    $user=Usuarios::where('email',$email)->where('empresa',$empresa)->first();
    $user->update([
        'email'=>$request->email,
        'empresa'=>$request->empresa,
        'nombre'=>$request->nombre


    ]);
    return response()->json(['message' => 'User update Successfully','userUpdate'=>$user], 200);
   }


}
