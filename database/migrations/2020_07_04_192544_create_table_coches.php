<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCoches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coches', function (Blueprint $table) {
            	$table->string('matricula');//Clave primaria
		$table->integer('idcliente',false,true);//Clave primaria
                $table->string('modelo');
                $table->string('marca');//Creamos columna para la clave ajena
		$table->foreign('idcliente')->references('id')->on('clientes')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla clientes
		$table->primary('matricula');//Declaracion de la clave primaria o primary key de la tabla temporal, para luego asignar la clave compuesta con un autoincremental

        });
        //cambiamos la clave primaria anterior de matricula a una clave compuesta formada por un autoincremental que viene de la tabla clientes y la matricula
        DB::unprepared('ALTER TABLE `coches` DROP PRIMARY KEY, ADD PRIMARY KEY ( `matricula` , `idcliente`)');//Ejemplo de clave compuesta con un autincrement

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coches');
    }
}
