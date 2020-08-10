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

     //Un Usuario puede estar en muchas empresas
     public function usuarios(){
         /*En primer lugar se pone el modelo con el que esta reacionado este es decir Empresas con Usuarios.
         2- El segundo parametro, indica el nombre especifico de la tabla que crea la relacion esta (Usuarios y Empresas) [ya que no seguimos la convencion de laravel].
         3-El tercer parametro, es el nombre de la clave foranea que relaciona esta nueva tabla[usuariosempresas] con la que estamos [Empresas].
         4-El cuarto parametro, es el nombre  de la clave foranea de la tabla[Usuarios] con la que estamos relacionando a esta[Empresas]
         5-El metodo withTimestamps nos permite rellenar los campos created_at y update_at automaticamente
         6-El metodo withPivot nos permite acceder a los campos de la tabla nueva[usuariosempresas] que crea la relacion entre [Usuarios-Empresas] */
       return $this->belongsToMany(Usuarios::class,'usuariosempresas','empresa','usuario')->withTimestamps()->withPivot('tipoUsuario','permisoEscritura');
    }
}