<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
//use App\Http\Controllers\DB;
use Illuminate\Support\Facades\DB as FacadesDB;

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
            $table->bigInteger('numerofactura', false, true)->unsigned();//autoincremental(clave primaria)
            $table->bigInteger('idreparacion', false, false)->unsigned();//Clave ajena de propadaga de la tabla reparaciones(clave primaria) gracias a la cual identificamos las facturas de las diferentes empresas
            //$table->bigInteger('idempresa', false, false)->unsigned();
            $table->bigInteger('idusuario', false, false)->unsigned();
            $table->date('fecha');
            $table->enum('estado',['vigente','anulada']);
            $table->foreign('idreparacion')->references('id')->on('reparaciones')->onUpdate('cascade')->onDelete('cascade');//Referencia de la clave ajena de la tabla reparaciones
            //$table->foreign('idempresa')->references('idempresa')->on('reparaciones')->onUpdate('cascade')->onDelete('cascade');//Referencia de la clave ajena de la tabla reparaciones
            $table->foreign('idusuario')->references('idusuario')->on('reparaciones')->onUpdate('cascade')->onDelete('cascade');//Referencia de la clave ajena de la tabla reparaciones
            //Tabla reflexiva
            $table->bigInteger('numerofacturanulada',false,false)->unsigned()->nullable();//clave ajena con sigo misma  que puede ser nula, ya que referenica a un numero de factura anterior que va ser anulada
            $table->bigInteger('idreparacionanulada',false,false)->unsigned()->nullable();//clave ajena con sigo misma  que puede ser nula, ya que referenica a un id de reparacion asociado a un numero de factura anterior anulada
            //----------//
            $table->timestamps();
            $table->primary(['numerofactura','idreparacion']);//Declaracion de la clave conpuesta o primary key de la tabla
        });

        //Referencia de claves ajenas sobre la misma tabla ya que es una reflexion de la tabla en si misma
        FacadesDB::unprepared('ALTER TABLE `facturas`ADD CONSTRAINT `numerofacturanulada_fk` FOREIGN KEY (numerofacturanulada) REFERENCES `facturas`(numerofactura) ON UPDATE CASCADE ON DELETE RESTRICT ');
        FacadesDB::unprepared('ALTER TABLE `facturas`ADD CONSTRAINT `idreparacionanulada_fk` FOREIGN KEY (idreparacionanulada) REFERENCES `facturas`(idreparacion) ON UPDATE CASCADE ON DELETE RESTRICT ');
        
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
