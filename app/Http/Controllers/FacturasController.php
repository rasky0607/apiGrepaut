<?php

namespace App\Http\Controllers;

use App\Clientes;
use Illuminate\Http\Request;
use App\Facturas;
use Illuminate\Support\Str;
//Añadido
use App\Http\Controllers\DB as DB;
use App\Http\Middleware\Authenticate;
use App\Utils;

/**
 * [Description FacturasController]
 * Clase que realiza peticiones a la BD paramodificar o obtener datos y enviarlos donde se necesitan en formato Json
 */
class FacturasController extends Controller
{
    /**
     * @param Request $request
     * Registra una nueva factura indicando el idreparacion de la reparacion a facturar
     * por defecto esta nueva factura se crea cone le stado [VIGENTE] y la fecha del dia actual en el que se crea
     * @return [json]
     */
    function facturarReparacion($idreparacion)
    {
      //Obtener el siguiente numero de factura para la proxima reparacion de esa empresa
    }

    /**
     * Lista todas las facturas de todas las empresas
     * @return [json]
     */
    function list()
    {
       
    }

    /**
     * @param mixed $idCliente
     * @param mixed $empresa
     * Muestra las facturas de un solo cliente concreto en una empresa
     * @return [json]
     */
    function facturasDeUnCliente($idCliente,$empresa)
    {
       
    }

    /**
     * @param mixed $numerofactura
     * @param mixed $idreparacion
     * Muestra los datos de una factura, es decir la lineas de una factura que son 
     * optenidas de la tabla serviciosReparaciones indicando el idreparacion
     * @return [Json]
     */
    function lineasDeUnaFactura($numerofactura, $idreparacion)
    {
       
    }

    /**
     * @param  $idEmpresa 
     * Facturas que pertenecen a una empresa concreta a través de el usuario que las atendio
     * @return [json]
     */
    function facturasDeUnaEmpresa($idEmpresa)
    {
       
    }


    /**
     * @param mixed $numerofactura
     * @param mixed $idreparcion
     * Anula una factura para crear otra que referencie a esta
     * @return [json]
     */
    function anularFactura($numerofactura,$idreparcion)
    {

    }

    /**
     * @param Request $request
     * Actualiza el campo de la factura [Estado] de VIGENTE a ANULADA o viceversa
     * pasando un id de la reparacion por parametro
     * @return [json]
     */
    function update(Request $request, $idreparacion)
    {
        
    }

    function proximoNumeroFactura(){
        $numeroFactura= Facturas::select()->all();
    }
}
