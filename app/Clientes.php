<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use DB;
use App\Coches;

/**
 * [Description Clientes]
 * Clase modelo de la tabla Clientes
 */
class Clientes extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','nombre','empresa','apellido', 'tlf','email'
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
     //
     /**
      * 1:N Clientes/Empresas->Un cliente esta asociado a una empresa (belongsTo)
      *pero una empresa puede tener asociados muchos clientes
      * @return [Empresas empresa]
      */
     public function empresa(){
        return $this->belongsTo(Empresas::class,'empresas')->withTimestamps();
    }

    /**
     * 1:N Cliente/Coches->Un Cliente puede tener muchos coches (hasMany)
     * pero un coche solo tiene asociado un unicocliente
     * @return [Coches coches]
     */
    public function coches(){
        return $this->hasMany(Coches::class,'idcliente');
    }

}