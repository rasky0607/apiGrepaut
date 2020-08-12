<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Usuarios extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','email', 'password','nombre','token'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at', 'password','token',
    ];

    //####RELACIONES ENTRE TABLAS#####
      //N:M ->Una Empresa puede tener muchos usuarios
      public function empresas(){
        return $this->belongsToMany(Empresas::class,'usuariosempresas','usuario','empresa')->withTimestamps()->withPivot('tipoUsuario','permisoEscritura');
    }
}
