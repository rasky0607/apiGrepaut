<?php

//Clase que realiza peticiones a la BD paramodificar o obtener datos y enviarlos donde se necesitan 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\UsuariosEmpresas;
use Illuminate\Support\Str;

class UsuariosEmpresasController extends Controller{
    
   //###Modificar#### ya que la tabla usuarios ya no tiene la empresa
   /*function listPorEmpresa($empresa,$email=null){
      if(is_null($email))
        return response()->json(Usuarios::all()->where('empresa','like',$empresa)->values());
      else
      return response()->json(Usuarios::all()->where('empresa','like',$empresa)->where('email','like',$email)->values());       
       
    }*/
}