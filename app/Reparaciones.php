<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

/**
 * [Description Reparaciones]
 * Clase modelo de la tabla Reparaciones
 */
class Reparaciones extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','estadoReparacion', 'idusuario','idempresa','idcoche',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
    ];

    //Relaciones de tablas

    /**
     * 1:N Coches/Reparaciones-> Un coche puede estar asociado a muchas reparaciones
     * pero una reparacion solo se asocia con un coche
     * @return [Reparaciones reparaciones]
     */
    public function coche(){
        return $this->belongsTo(Coches::class,'coches')->withTimestamps();
    }

    /**
     * 1:N usuariosempresas/Reparaciones -> Un usuario de una empresa puede estar asociado a varias reparaciones,
     *  pero una reparacion solo tine un usuario de una empresa
     * @return [Usuariosempresas usuariosempresas]
     */
    /*public function usuariosempresas(){
        return $this->HasManyThrough(Usuariosempresas::class,'usuario','idusuario')->andWhere('empresa','=',$this->empresa);
    }*/
   
}
