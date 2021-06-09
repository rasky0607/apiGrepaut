<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * [Description Serviciosreparaciones]
 * Clase modelo de la tabla Serviciosreparaciones
 * La cual tambien actuara como tabla Linea de facturas ya que tendra todos los datos que comprenden  una factura
 */
class ServiciosReparaciones extends Model
{
    protected $table = 'serviciosreparaciones';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idreparacion','numerotrabajo','servicio','precioServicio'
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

  
}
