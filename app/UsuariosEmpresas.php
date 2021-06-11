<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use App\Empresas;
use App\Usuarios;
/** *******EN DESUSO*******
 * [Description Usuariosempresas]
 * Clase modelo de la tabla Usuariosempresas.
 * Esta clase/tabla al ser generada por uan relacion de N:M entre Usuarios y Empresas
 * No tiene ninguna funcion que defina u na relacion entre tablas, ya que las relaciones
 * que defines esta tabla  generada por la cardinalidad N:M estan definidas en sus respectivas clases/tablas padre
 * Usuarios y Empresas.php
 */
class UsuariosEmpresas extends Model implements AuthenticatableContract, AuthorizableContract
{
    protected $table = "usuariosempresas";
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

    /*Esta clase/tabla al ser generada por uan relacion de N:M entre Usuarios y Empresas
    No tiene ninguna funcion que defina u na relacion entre tablas, ya que las relaciones
     que defines esta tabla  generada por la cardinalidad N:M estan definidas en sus respectivas clases/tablas padre
     Usuarios y Empresas.php*/

     
    /**
     * 1:N UsuariosEmpresas/Reparaciones-> Una reparacion tiene asociado un unico usuario de una empresa pero
     * un usuario de una empresa puede estar asociado a muchas reparaciones
     * @return [Reparaciones reparaciones]
     */
    /* public function reparaciones()
     {
         return $this->HasManyThrough(Reparaciones::class,'usuario','idusuario')->andWhere('empresa','=',$this->empresa);
     }*/
}
