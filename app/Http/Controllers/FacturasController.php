<?php

namespace App\Http\Controllers;

use App\Serviciosreparaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
//Añadido
use App\Http\Controllers\DB as DB;
use App\Http\Middleware\Authenticate;
use App\Utils;
use App\Coches;
use App\Clientes;
use App\Usuarios;
use App\Servicios;
use App\Usuariosempresas;
use App\Reparaciones;
use App\Facturas;

/**
 * [Description ServiciosReparacionesController]
 * Clase que realiza peticiones a la BD paramodificar o obtener datos y enviarlos donde se necesitan en formato Json
 * La cual tambien actuara como tabla Linea de facturas ya que tendra todos los datos que comprenden  una factura
 */
class FacturasController extends Controller
{
    //Estados de las reparaciones
    public const reparacionFacturado = 'facturado';
    public const reparacionNoFacturado = 'no facturado';
    //Estado de las facturas
    public const facturaVigente = 'vigente';
    public const facturaAnulada = 'anulada';

    /**
     * @param Request $request
     * Registra una nueva factura indicando el idreparacion de la reparacion a facturar
     * por defecto esta nueva factura se crea cone le stado [VIGENTE] y la fecha del dia actual en el que se crea
     * y n o hara referencia aninguna otra factura, ya que es totalmente nueva
     * @return [json]
     */
    function nuevaFactura($idreparacion) {
        //Comrpobar si la reparacion NO esta facturada aun
        if ($this->comprobacionReparacionNoFacturada($idreparacion)) {
            //Obtener la empresa de la reparacion
            //select idempresa from usuarios where id in (select idusuario from reparaciones where id = 7);
            $empresa = Usuarios::select('idempresa')->whereIn('id', Reparaciones::select('idusuario')->where('id',$idreparacion))->get();
            $idempresa = $empresa[0]['idempresa'];
            //Obtener el siguiente numero de factura para la proxima reparacion de esa empresa
            $idusuario = Reparaciones::select('idusuario')->where('id',$idreparacion)->get();
        
            $numerofactura = $this->proximoNumeroFactura($idusuario[0]['idusuario']);

            //Fecha actual Formato->'Y-m-d'
            $fecha = date("Y-m-d");
 
            //Al crear una nueva factura, por defecto esta tiene stado vigente
            $estado = 'vigente';
            //Al crear una nueva factura no hace referencia a otra anulada, pro defecto estos valroes seran null
            $numerofacturanulada = null;
            $idreparacionanulada = null;

            $factura = Facturas::create([
                'numerofactura' => $numerofactura,
                'idreparacion' => $idreparacion,
                'idusuario' => $idusuario[0]['idusuario'],
                'fecha' => $fecha,
                'estado' => $estado,
                'numerofacturanulada' => $numerofacturanulada,
                'idreparacionanulada' => $idreparacionanulada

            ]);
            $this->actualizarEstadoReparacion($idreparacion); //Cambiamos el estado de la reparacion de 'No facturado' a 'Facturado'
            return response()->json(['Message' => 'Reparacion con id ' . $idreparacion . ' facturada con exito.', 'factura' => $factura], 200);
        } else {
            $factura = Facturas::where('idreparacion', $idreparacion)->first();
            return response()->json(['Error' => 'Reparacion con id ' . $idreparacion . ' ya esta facturada.', 'factura' => $factura], 202);
        }
    }

    /**
     * Lista todas las facturas de todas las empresas
     * @return [json]
     */
    function list() {
        return response()->json(Facturas::all());
    }

    /**
     * @param mixed $idempresa
     * Lista facturas de una empresa determinada
     * @return [Json]
     */
    function listFacturasEmpresa($idusuario) {
        $idEmpresa = Usuarios::select('idempresa')->where('id', $idusuario)->get();
        //select * from facturas where idusuario in (select id from usuarios where idempresa = 1);
        $facturasEmpresa=Facturas::whereIn('idusuario',Usuarios::select('id')->where('idempresa',$idEmpresa[0]['idempresa']))->get();
        if(sizeof($facturasEmpresa)<=0)
            return response()->json(['Error' => 'No hay facturas resgistradas aun en la empresa con id '.$idEmpresa[0]['idempresa']], 202);
        return response()->json($facturasEmpresa, 200);
    }

    /** ****PENDIENTE******
     * @param mixed $idempresa
     * Lista facturas de unae mpresa determinada con estado Vigente
     * @return [Json]
     */
    function listFacturasEmpresaVigentes($idempresa) {
        $factura =Facturas::where('idempresa',$idempresa)->where('estado',FacturasController::facturaVigente)->get();
        if(sizeof($factura)<=0)
            return response()->json(['Error' => 'No hay facturas resgistradas con estado [Vigente] aun en la empresa con id '.$idempresa], 202);
        return response()->json(['Message' => 'Facturas de la empresa', 'Facturas' => $factura], 200);
    }

    
    /** ****PENDIENTE******
     * @param mixed $idreparacion
     * Dado un id de reparacion mostramos los servicios realizados en esta
     * y calculamos su coste total y la devolvemos.
     * @return [Json]
     */
    function lineasFactura($idreparacion){
       
        //Datos Factura
        $factura=Facturas::select('numerofactura','estado')->where('idreparacion',$idreparacion)->first();
        $numeroFactura=$factura['numerofactura'];
        $estadoFactura = $factura['estado'];

        //Datos Coche
        $reparacion = Reparaciones::select('idcoche','idusuario')->where('id', $idreparacion)->first();
        $matriculaCoche = Coches::select('matricula')->where('id', $reparacion['idcoche'])->first();
        $matricula = $matriculaCoche['matricula'];

        //Datos Cliente
        $cliente=Clientes::select('nombre','apellido')->whereIn('id',Coches::select('idcliente')->where('id',$reparacion['idcoche'])->first())->first();
        $nombreCliente=$cliente['nombre'].' '.$cliente['apellido'];

        //Datos Usuario
        $usuario=Usuarios::select('nombre')->where('id',$reparacion['idusuario'])->first();
        $nombreUsuario=$usuario['nombre'];

        //Datos Servicios reparacion [Lineas factura]
        $lineasFactura = Serviciosreparaciones::select('serviciosreparaciones.numerotrabajo', 'serviciosreparaciones.servicio', 'servicios.nombre', 'serviciosreparaciones.precioServicio')->join('servicios', 'servicios.id', '=', 'serviciosreparaciones.servicio')->where('serviciosreparaciones.idreparacion', $idreparacion)->get();
        
        //Calculo de precio total
        $precioBruto=0;
        foreach($lineasFactura as $item)
        {
            $precioBruto+=$item['precioServicio'];        
        }

        //Datos de la Factura con dichso servicios
        $datosFactura = array(
            "numeroFactura" => $numeroFactura,
            "estado" => $estadoFactura,
            "matricula" => $matricula,
            "cliente"=>$nombreCliente,
            "Tecnico"=>$nombreUsuario,
            "Precio bruto total"=>$precioBruto
        );

        return response()->json(['Message' =>   'Lineas de factura numero '.$numeroFactura.' de reparacion '.$idreparacion, 'Encabezado Factura' => $datosFactura, 'Lineas Factura' => $lineasFactura], 200);
    }

    /** ****PENDIENTE******
     * @param mixed $idreparacionParaAnular
     * @param mixed $idreparacionNueva
     * Busca la factura a anular, cambia su estado de 'vigente' a 'anulada'
     * y cambia el numerofacturanulada de la nueva Factura sustituta de null a el numero de la vija factura que fue anulada
     * y devolvemos la nueva factura final
     * @return [Json]
     */
    function anularFactura($idreparacionParaAnular, $idreparacionNueva)
    {
        //Comprobar que el idreparacionNueva no esta facturada
        if ($this->comprobacionReparacionNoFacturada($idreparacionNueva)) {
            //0-Buscamos el numero de factura de la factura a anular
            $numerofacturaParaAnular = 0;
            $numerofacturaParaAnular = Facturas::select('numerofactura')->where('idreparacion', $idreparacionParaAnular)->first();
            //1-Cambiar estado de factura a Anulada
            $facturaParaAnular = Facturas::where('idreparacion', $idreparacionParaAnular);

            $facturaParaAnular->update([
                'estado' => FacturasController::facturaAnulada
            ]);

            //2-Crear reparacion nueva y Añadir a esta reparacion los servicios reparacion pertinentes [Cosa de la UI de la APP]
            //3-Creamos la nueva factura
            $this->nuevaFactura($idreparacionNueva);
            //4-Referencia a nueva factura de la nueva reparacion a factura anulada
            $facturaSustituta = Facturas::where('idreparacion', $idreparacionNueva);
            $facturaSustituta->update([
                'numerofacturanulada' => $numerofacturaParaAnular['numerofactura'],
                'idreparacionanulada' => $idreparacionParaAnular
            ]);

            return response()->json(['Message' => 'Factura anulada con exito ', 'Factura nueva ' => $facturaSustituta->get(), 'Factura anulada' => $facturaParaAnular->get()], 200);
        }else{
            $factura=Facturas::where('idreparacion',$idreparacionNueva);
            return response()->json(['Error' => 'Esta reparcion ya esta facturada, crea una nueva reparacion para poder anular la factura de la reparacion indicada con id '.$idreparacionParaAnular, 'Factura  ' => $factura->get()], 202);
        }
    }


    /**
     * @param mixed $idempresa
     * Obtenemos el proximo numero de factura para una empresa
     * @return [bigInteger]
     */
    function proximoNumeroFactura($idusuario)
    {
        $numerofacturaActual = 0;
        $siguienteNumerofactura = 0;
        //selecionamos las facturas de una empresa y cogemos el maximo numero factura de estas
        $idEmpresa = Usuarios::select('idempresa')->where('id', $idusuario)->get();
        $numerofacturaActual = Facturas::whereIn('idusuario',Usuarios::select('id')->where('idempresa',$idEmpresa[0]['idempresa']))->max('numerofactura');
        $siguienteNumerofactura = $numerofacturaActual;
        
        //Si es = 0 o null es que no hay ninguna factura dada de alta en esa empresa y sera la primera, es decir la nº1
        if ($numerofacturaActual == 0 || is_null($numerofacturaActual))
        {
            $siguienteNumerofactura = 1;
            return $siguienteNumerofactura;
        }
        return ++$siguienteNumerofactura; //Si encontro alguna factura dada de alta en esa empresa,el numero de factura sera el maximo + 1
    }

    /**
     * @param mixed $idreparacion
     * Comprueba si una reparacion tiene estado Facturado o no.
     * Si no esta facturada, devuelve true, si esta facturada devuelve flase.
     * @return [Booblean]
     */
    function comprobacionReparacionNoFacturada($idreparacion)
    {
        $reparacion = Reparaciones::select('estadoReparacion')->where('id', $idreparacion)->first();
        if ($reparacion['estadoReparacion'] == FacturasController::reparacionNoFacturado) //No esta facturada, ya que la consulta devuelve un valor
            return true;

        return false; //Ya esta facturada, ya que la consulta no devuelve un valor
    }

    /**
     * @param mixed $idreparacion
     * Actualiza el estado de la reparacion 'No facturado' a 'Facturado'
     * @return [type]
     */
    function actualizarEstadoReparacion($idreparacion)
    {
        $reparacion = Reparaciones::findOrFail($idreparacion);
        $reparacion->update([
            'estadoReparacion' => FacturasController::reparacionFacturado
        ]);
    }
}
