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
            $table->id();//primer true para indicar que es autincremental y segundo para indicar que es clave             
            //En su lugar, el usuario iniciara sesion cone l correo y password 
            //y luego en otra ventana se mostrara una serie de cartas de las empresas en las que esta registrado
            //como los workspace, selecionando una u otra empresa, para mostrar los datos de esta,
            //de esta forma un usuario con email puede estar en varias empresas
            $table->string('email')->unique();
            $table->string('password');
            $table->string('nombre');
            $table->timestamps();
            $table->string('token');
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
