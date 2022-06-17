<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
//añadido
use Illuminate\Support\Facades\DB as FacadesDB;

class CreateTableEmpresas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('direccion');
            $table->string('tlf',13);//Ya que  un numero de tlf suele tener 9 digitos, pero puede que se le añada prefijos como +34(España) o +376(Andorra)
            $table->string('logoempresa')->nullable();
            $table->timestamps();
        });
        /*Creamos la clave ajena de la tabla usuarios aqui, ya que como primero se crea la tabla usuarios por el orden en el que se crearon los ficheros de creacion de tablas, para no cambiarlo todo de nuevo, creamos la clave ajena mas tarde para que no pete a la hora de crear la BD, de esta forma cuando se crea la clave, ya existen las dos tablas y no hay problema.*/
        FacadesDB::unprepared('ALTER TABLE `usuarios`ADD CONSTRAINT `idempresa_fk` FOREIGN KEY (idempresa) REFERENCES `empresas`(id) ON UPDATE CASCADE ON DELETE CASCADE ');
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empresas');
    }
}
