<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Usuariosempresas extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'usuario','empresa','tipoUsuario','permisoEscritura'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
    ];

    /* ### Relacion de Muchcos a muchos en laravel/lumen N:M  
    para poder acceder a las tablas con la que se relaciona esta, desde su controler ###*/
    
    //Un Usuario puede estar en muchas empresas
    public function usuarios(){
        $this->belongsToMany(Usuarios::class);
    }
     //Una Empresa puede tener muchos usuarios
    public function empresas(){
        $this->belongsToMany(Empresas::class);
    }
        //------------------------------//
}