<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUsuarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	Schema::create('usuarios', function (Blueprint $table) {
           $table->string('email');
           $table->string('password');
           $table->string('empresa');//Creamos columna para la clave ajena
           $table->string('nombre');
           $table->enum('tipo',['admin','user']);
           $table->boolean('tienePermiso');
           $table->foreign('empresa')->references('nombre')->on('empresas')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla empresas 
           $table->primary(['email','empresa']);//Declaracion de la clave conpuesta o primary key de la tabla
	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
}
