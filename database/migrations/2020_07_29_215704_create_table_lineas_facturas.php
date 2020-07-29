<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableLineasFacturas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lineasFacturas', function (Blueprint $table) {
            $table->integer('numerofactura',false,true);//clave ajena de la tabla Facturas (clave primaria)
            $table->integer('idreparacionfacturada',false,true);//clave ajena de la tabla Facturas (clave primaria)
            $table->integer('linea', false, true);//Numero de linea de la factura (clave primaria)
            $table->integer('numerotrabajo',false,true);//clave ajena de  serviciosReparaciones
            $table->integer('idreparacion',false,true);//clave ajena de la tabla Facturas (clave primaria)
            $table->double('precio',10,2);
            $table->timestamps();
            //Este campo (precio) guarda el precio del servicio en el momento que se creo la linea de factura,
            //(por si cambia en un futuro y necesitamos reimprimir la factura, que el coste no cambie) este double tiene precision dos decimales
            $table->foreign('numerotrabajo')->references('numerotrabajo')->on('serviciosReparaciones')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla serviciosReparaciones
            $table->foreign('idreparacion')->references('idreparacion')->on('serviciosReparaciones')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla  serviciosReparaciones
            $table->foreign('idreparacionfacturada')->references('idreparacion')->on('facturas')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla  facturas
            $table->foreign('numerofactura')->references('numero')->on('facturas')->onUpdate('cascade');//Referencia de la clave ajena que se prograga desde la tabla  facturas
            $table->primary(['linea','numerofactura','idreparacionfacturada']);//Declaracion de la clave conpuesta o primary key de la tabla
        });
        //Modificamos la clave a  una clave compuesta con un autoincremental (es la mejor forma que encontre de hacerlo en lumen),ya que increments convierte el campo autamticamente en clave primaria
	    // y peta al  indicarle abajo que es compuesta, por lo que debemos borrarla y volver a crearla
	    //DB::unprepared('ALTER TABLE `lineasfacturas` DROP PRIMARY KEY, ADD PRIMARY KEY ( `linea` , `numerofactura` ,`idreparacionfacturada`)');//Ejemplo de clave compuesta con un autincrement
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lineasFacturas');
    }
}
