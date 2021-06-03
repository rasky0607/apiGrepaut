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

            $table->id();// El primer parametro indica si es autoincremental y el segundo si es clave o no
            $table->string('nombre');
            $table->bigInteger('empresa')->unsigned();//Creamos columna para la clave ajena
            $table->double('precio',10,2);
            $table->string('descripcion')->nullable();//Este campo puede ser null
            $table->foreign('empresa')->references('id')->on('empresas')->onUpdate('cascade');
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
        Schema::dropIfExists('servicios');
    }
}
