<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use App\Empresas;
/**
 * [Description Usuarios]
 * Clase modelo de la tabla Usuarios
 */
class Usuarios extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'email', 'password', 'nombre', 'tipo', 'idempresa', 'token','logousuario','estado'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at', 'password', 'token',
    ];

    //####RELACIONES ENTRE TABLAS#####
    /**
     * N:M Empresas/Usuarios->Una Empresa puede tener muchos usuarios (belongsToMany)
     * y un usuario puede estar en varias empresas a la vez
     * @return [Usuarios usuarios]
     */
    /*public function empresas()
    {
        return $this->belongsToMany(Empresas::class, 'usuariosempresas', 'usuario', 'empresa')->withTimestamps()->withPivot('tipoUsuario', 'permisoEscritura');
    }*/

   
}
