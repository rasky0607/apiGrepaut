<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

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
     //Usuarios->Empresas N:M ->Un Usuario puede estar en muchas empresas
     public function usuarios(){
       return $this->belongsToMany(Usuarios::class,'usuariosempresas','empresa','usuario')->withTimestamps()->withPivot('tipoUsuario','permisoEscritura');
    }

     //Empresas->Clientes 1:N ->Un cliente esta en una y solo una empresas
     public function clientes(){
        return $this->hasMany(Clientes::class,'empresa');
     }
}