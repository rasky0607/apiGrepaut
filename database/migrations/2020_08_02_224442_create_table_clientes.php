<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableClientes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            //Modificado
            $table->id();//Clave primaria (El primer parametro indica que es  autoincrement, el segundo es unsing es decir sin signo)
            //$table->bigInteger('id',true,true)->unsigned();

            $table->string('nombre');
            $table->string('apellido');
            $table->bigInteger('empresa',false,false)->unsigned();//Creamos columna para la clave ajena
            $table->string('tlf',13);//Ya que  un numero de tlf suele tener 9 digitos, pero puede que se le añada prefijos como +34(España) o +376(Andorra)
            $table->string('email')->nullable();//Este campo puede ser nullo o vacio
            $table->foreign('empresa')->references('id')->on('empresas')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla empresas
            
            //Nuevo
           //$table->primary(['id','empresa']);//Declaracion de la clave conpuesta o primary key de la tabla
            
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
        Schema::dropIfExists('clientes');
    }
}
