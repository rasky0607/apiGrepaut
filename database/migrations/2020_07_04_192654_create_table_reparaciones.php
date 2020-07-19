<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableReparaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reparaciones', function (Blueprint $table){
		$table->integer('id', true, true);//clave primaria
                $table->enum('estadoReparacion',['facturado','no facturado']);
		$table->string('usuario');//Creamos columna para clave ajena de la tabla usuarios
		$table->string('empresa');//Creamos columna para la clave ajena de la tabla usuarios
		$table->integer('idcliente',false,true);//Creamos columna para la clave ajena de la tabla coches
		$table->string('matricula');//Creamos columna para clave ajena de la tabla coches
		$table->foreign('usuario')->references('email')->on('usuarios')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla usuarios
        	$table->foreign('empresa')->references('empresa')->on('usuarios')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla usuarios
	    	$table->foreign('idcliente')->references('idcliente')->on('coches')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla usuarios
	    	$table->foreign('matricula')->references('matricula')->on('coches')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla usuarios
        });
	//Creamos una clave compuesta con un autoincremental (es la mejor forma que encontre de hacerlo en lumen),ya que increments convierte el campo autamticamente en clave primaria
	// y peta al  indicarle abajo que es compuesta, por lo que debemos borrarla y volver a crearla
	//DB::unprepared('ALTER TABLE `reparaciones` DROP PRIMARY KEY, ADD PRIMARY KEY ( `id` , `usuario` ,`empresa`)');//Ejemplo de clave compuesta con u n autincrement

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reparaciones');
    }
}
