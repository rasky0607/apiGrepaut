<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUsuariosEmpresas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuariosEmpresas', function (Blueprint $table) {         
            $table->bigInteger('usuario')->unsigned();//[Id del usuario]primer true para indicar que es autincremental y segundo para indicar que es clave
            $table->bigInteger('empresa')->unsigned();
            $table->enum('tipoUsuario',['admin','user']);//Tipo de usuario en esta empresa, si el primero es Admin por defecto [trigger]
            $table->boolean('permisoEscritura');//El usuario tiene permisos para leer y escribir[true] o solo leer[false],(Si es admin sera true por default, si no, por defecto sera false,[es decir solo permiso de lectura])        
            $table->foreign('empresa')->references('id')->on('empresas')->onUpdate('cascade');
            $table->foreign('usuario')->references('id')->on('usuarios')->onUpdate('cascade');
            $table->primary(['usuario','empresa']);//Declaracion de la clave conpuesta o primary key de la tabla
            $table->timestamps();
        });
   
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuariosEmpresas');
    }
}
