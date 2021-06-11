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
            $table->bigInteger('id', true, true);//clave primaria
            $table->enum('estadoReparacion',['facturado','no facturado']);
            $table->bigInteger('idusuario',false,true)->unsigned();
            //$table->bigInteger('idempresa',false,true)->unsigned();//necesitamos este campo, ya que un usuario puede pertenece a varias empresa y puede lugar a mezclar reparciones de diferentes empresas
            $table->bigInteger('idcoche')->unsigned();    
            //$table->foreign('idusuario')->references('usuario')->on('usuariosempresas')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla usuariosempresas
            $table->foreign('idusuario')->references('id')->on('usuarios')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla Usuarios
            //$table->foreign('idempresa')->references('empresa')->on('usuariosempresas')->onUpdate('cascade')->onDelete('cascade');//Referencia de la clave ajena que se prograga desde la tabla usuariosempresas
            $table->foreign('idcoche')->references('id')->on('coches')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla coches
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
