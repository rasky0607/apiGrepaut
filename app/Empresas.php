<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

/**
 * [Description Empresas]
 * Clase modelo de la tabla Empresas
 */
class Empresas extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','nombre','direccion', 'tlf'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
    ];

    //####RELACIONES ENTRE TABLAS#####
     /**
      *N:M Usuarios/Empresas->Un Usuario puede estar en muchas empresas,
      *y una empresa puede tener asociados muchos usuarios.
      * @return [Usuarios usuarios]
      */
     public function usuarios(){
       return $this->belongsToMany(Usuarios::class,'usuariosempresas','empresa','usuario')->withTimestamps()->withPivot('tipoUsuario','permisoEscritura');
    }

     /**
      *1:N Clientes/Empresas->Una Empresa tiene asociados muchos Clientes(hasMany),
      *pero un cliente esta asociado con una unica empresa
      * @return [Clientes clientes]
      */
     public function clientes(){
        return $this->hasMany(Clientes::class,'empresa');
     }

     /**
      * 1:N Una Servicios/Empresas->Empresa tiene asociados muchos Servicios(hasMany),
      *pero un servicio esta asociado a una sola empresa
      * @return [Servicios servicios]
      */
     public function servicios(){
        return $this->hasMany(Servicios::class,'empresa');
     }
}