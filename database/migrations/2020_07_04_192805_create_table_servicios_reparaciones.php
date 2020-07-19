<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableServiciosReparaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('serviciosReparaciones', function (Blueprint $table) {
		$table->integer('numerotrabajo', false, true);//numero del trabajo  asginados a una reparacion concreta (clave primaria)
		$table->integer('idreparacion',false,true);//clave ajena de la tabla reparaciones (clave primaria)
                $table->string('servicio');//clave columna para la clave ajena de la tabla servicios
		$table->string('empresaServicio');//Creamos columna para la clave ajena de la tabla servicios
		$table->foreign('idreparacion')->references('id')->on('reparaciones')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla reparaciones
		$table->foreign('servicio')->references('nombre')->on('servicios')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla servicios
		$table->foreign('empresaServicio')->references('empresa')->on('servicios')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla servicios
		$table->primary(['numerotrabajo','idreparacion']);//Declaracion de la clave conpuesta o primary key de la tabla
		//claves de esta tabla (numerotrabajo,idreparacion)
        });
	//Creamos una clave compuesta con un autoincremental (es la mejor forma que encontre de hacerlo en lumen),ya que increments convierte el campo autamticamente en clave primaria
	// y peta al  indicarle abajo que es compuesta, por lo que debemos borrarla y volver a crearla
	//DB::unprepared('ALTER TABLE `serviciosReparaciones` DROP PRIMARY KEY, ADD PRIMARY KEY ( `numerotrabajo` , `idreparacion`)');//Ejemplo de clave compuesta con u n autincrement
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('serviciosReparaciones');
    }
}
