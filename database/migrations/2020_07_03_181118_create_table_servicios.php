<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableServicios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->string('nombre');
            $table->string('empresa');//Creamos columna para la clave ajena
            $table->double('precio',10,2);//este double tiene precision dos decimales
            $table->string('descripcion')->nullable();//Este campo puede ser nullo o vacio
            $table->foreign('empresa')->references('nombre')->on('empresas')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla empresas 
            $table->primary(['nombre','empresa']);//Declaracion de la clave conpuesta o primary key de la tabla
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servicios');
    }
}
