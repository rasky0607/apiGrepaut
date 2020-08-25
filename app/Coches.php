<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Coches extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','matricula','idcliente','modelo', 'marca'
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
     * 1:N Clientes/Coche->Un coche tiene asociado un cliente
     * pero un cliente puede tener asociado muchos coches
     * @return [Clientes cliente]
     */
    public function cliente(){
        return $this->belongsTo(Clientes::class,'clientes');
    }

    /**
     * 1:N Coches/reparaciones->Un Coche puede estar asociado a muchas reparaciones
     * pero una reparacion solo puede estar asociada a un coche
     * @return [Reparaciones reparaciones]
     */
    public function reparaciones(){
        return $this->hasMany(Reparaciones::class,'idcoche');
    }
}