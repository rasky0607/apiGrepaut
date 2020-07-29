<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUsuariosempresas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuariosempresas', function (Blueprint $table) {
            $table->integer('usuario',false,false);//[Id del usuario]primer true para indicar que es autincremental y segundo para indicar que es clave
            $table->string('empresa');
            $table->enum('tipoUsuario',['admin','user']);//Tipo de u suario en esta empresa, si el primero es Admin por defecto [trigger]
            $table->boolean('tienePermiso');//El usuario tiene permisos para leer y escribir o no,(Si es admin sera true por default)
            $table->timestamps();
            $table->primary(['usuario','empresa']);//Declaracion de la clave conpuesta o primary key de la tabla
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuariosempresas');
    }
}
