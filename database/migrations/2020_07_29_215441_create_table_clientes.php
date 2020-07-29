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
            $table->integer('id', true, true);//Clave primaria (El primer parametro indica que es  autoincrement, el segundo es unsing es decir sin signo)
            $table->string('nombre');
            $table->string('apellido');
            $table->string('empresa');//Creamos columna para la clave ajena
            $table->string('tlf',13);//Ya que  un numero de tlf suele tener 9 digitos, pero puede que se le añada prefijos como +34(España) o +376(Andorra)
            $table->string('email')->nullable();//Este campo puede ser nullo o vacio
            $table->foreign('empresa')->references('nombre')->on('empresas')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla empresas
            $table->timestamps();
            //Nota: Evitamos usar increments en la definicion de los id y en su lugar usamos integer, para a la hora de propagar la clave que nos nos de error de formacion de clave ajena
        });
         //DB::unprepared('ALTER TABLE `clientes` DROP PRIMARY KEY, ADD PRIMARY KEY ( `id` , `empresa`)');//Ejemplo de clave compuesta con u n autincrement
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
