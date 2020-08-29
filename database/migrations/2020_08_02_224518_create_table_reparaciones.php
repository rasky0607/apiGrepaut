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
        Schema::create('reparaciones', function (Blueprint $table) {
            $table->integer('id', true, true);//clave primaria
            $table->enum('estadoReparacion',['facturado','no facturado']);
            $table->bigInteger('idusuario',false,true)->unsigned();
            $table->bigInteger('idcoche')->unsigned();//Creamos columna para clave ajena de la tabla coches    
            $table->foreign('idusuario')->references('id')->on('usuarios')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla usuarios
            $table->foreign('idcoche')->references('id')->on('coches')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla usuarios
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
        Schema::dropIfExists('reparaciones');
    }
}
