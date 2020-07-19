<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableFacturas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facturas', function (Blueprint $table) {
		$table->integer('numero', false, true);//autoincremental(clave primaria)
		$table->integer('idreparacion', false, true);//Clave ajena de propadaga de la tabla reparaciones(clave primaria) gracias a la cual identificamos las facturas de las diferentes empresas
		$table->date('fecha');
		$table->enum('tipo',['vigente','anulada']);
		$table->foreign('idreparacion')->references('id')->on('reparaciones')->onUpdate('cascade');//Referencia de la clave ajena de la tabla reparaciones
		$table->integer('numeroanulada',false,true)->nullable();//clave ajena con sigo misma  que puede ser nula, ya que referenica a una factura anterior anulada
		//$table->foreign('numeroanulada')->references('numero')->on('facturas')->onUpdate('cascade');//Referencia de la clave ajena reflexiva de la propia tabla facturas
		$table->primary(['numero','idreparacion']);//Declaracion de la clave conpuesta o primary key de la tabla
		

        });
	//Creamos una clave compuesta con un autoincremental (es la mejor forma que encontre de hacerlo en lumen),ya que increments convierte el campo autamticamente en clave primaria
	// y peta al  indicarle abajo que es compuesta, por lo que debemos borrarla y volver a crearla
	//DB::unprepared('ALTER TABLE `facturas` DROP PRIMARY KEY, ADD PRIMARY KEY ( `numero` , `idreparacion`)');//Ejemplo de clave compuesta con u n autincrement
	DB::unprepared('ALTER TABLE `facturas`ADD CONSTRAINT `numeroanulada_fk` FOREIGN KEY (numeroanulada) REFERENCES `facturas`(numero) ON UPDATE CASCADE ON DELETE RESTRICT ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facturas');
    }
}
