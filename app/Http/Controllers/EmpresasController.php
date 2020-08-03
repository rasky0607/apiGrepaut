<?php

//Clase que realiza peticiones a la BD paramodificar o obtener datos y enviarlos donde se necesitan 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Empresas;
use Illuminate\Support\Str;

class EmpresasController extends Controller
{ 
    function add(Request $request){
        $tlf=$request->tlf;
        if(!preg_match("/(^\+)?([0-9]{9,12}$)/", $tlf))//Comprobacion de numero de Tlf
            return response()->json(['Error' =>'El telefono de la empresa NO es correcto','Telefono'=>$tlf],200);      
        $empresa = Empresas::create([
            'nombre'=>$request->nombre,
            'direccion'=>$request->direccion,
            'tlf'=>$request->tlf
        ]);
        return response()->json(['message' =>'Empresa registrada con exito','Empresa'=>$empresa],201);
       }
    
       //Lista todas las empresas   
        function list(){ 
            return response()->json(Empresas::all());   
       }

       //Busca una empresa concretao que empiezen igual, pasando un nombre por la URL
       function buscarEmpresa($nombre){ 
            //Nota: urldecode($nombre) Importante para evitar errores de espacios o caracteres raros al llegar el dato por la url   
            //app('db')->enableQueryLog();//Activar registro de querys   
            $empresa=Empresas::where('nombre','like',urldecode($nombre).'%')->get();
            //dd(app('db')->getQueryLog());
            if(sizeof($empresa)<=0)
                return response()->json(['Error'=>'No se encontro la empresa: ','Empresa'=>urldecode($nombre)],201);
            else
                return response()->json(['Empresa'=>$empresa],201);    
        }
            
       function delete($id){
            $empresa= Empresas::findOrFail($id);
            if(is_null($empresa)){
                return response()->json(['message' => 'No existe esta empresa'], 200);
            }
            //Si encontro una empresa      
                $empresa->delete();
                return response()->json(['message' => 'Empresa se ha eliminado con exito','Empresa'=>$empresa], 200);
              
       }
    
        //Actualiza los campos que llegan diferentes de null
       function update($id,Request $request){                   
                $empresa= Empresas::findOrFail($id);
                $nombre=$request->nombre;
                $direccion=$request->direccion;
                $tlf=$request->tlf;
                $respuesta=' ';//Campos que fueron modificados
                if(!is_null($nombre)){
                    $empresa->update([
                        'nombre'=>$nombre           
                    ]);
                    $respuesta .='Nombre ';                  
                }
        
                if(!is_null($direccion)){
                    $empresa->update([
                        'direccion'=>$direccion               
                    ]);
                    $respuesta .='Direcion ';
                }
        
                if(!is_null($tlf)){
                    if(!preg_match("/(^\+)?([0-9]{9,12}$)/", $tlf))//Comprobacion de numero de Tlf
                        return response()->json(['Error' =>'El telefono de la empresa NO es correcto','Telefono'=>$tlf],200);      
                    $empresa->update([
                        'tlf'=>$tlf                      
                    ]);
                    $respuesta .='Tlf ';
                }
                return response()->json(['message' => 'Empresa actualizada con exito','Modificaciones'=>$respuesta,'Empresa'=>$empresa], 201);
                

       }
}