<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

/**
 * [Description Usuariosempresas]
 * Clase modelo de la tabla Usuariosempresas.
 * Esta clase/tabla al ser generada por uan relacion de N:M entre Usuarios y Empresas
 * No tiene ninguna funcion que defina u na relacion entre tablas, ya que las relaciones
 * que defines esta tabla  generada por la cardinalidad N:M estan definidas en sus respectivas clases/tablas padre
 * Usuarios y Empresas.php
 */
class Facturas extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'numerofactura','idreparacion','fecha','estado','numerofacturanulada'
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
}