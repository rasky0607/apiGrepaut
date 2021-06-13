<?php

namespace App\Http\Controllers;

use App\ServiciosReparaciones;
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


/**
 * [Description ServiciosReparacionesController]
 * Clase que realiza peticiones a la BD para modificar o obtener datos y enviarlos donde se necesitan en formato Json
 * La cual tambien actuara como tabla Linea de facturas ya que tendra todos los datos que comprenden  una factura
 */
class ServiciosReparacionesController extends Controller
{

    public const reparacionFacturado = 'facturado';
    public const reparacionNoFacturado = 'no facturado';
    /**
     * @param Request $request
     * Registra una nueva reparacion insertando todos los campos optenidos
     * por el parametro request
     * @return [json]
     */
    function add(Request $request) {
        //comprobacion de que el servicio asignado a la reparacion, pertenece a la misma empresa.
        if (!$this->comprobacionEmpresaServicioEmpresaReparacion($request->idreparacion, $request->servicio))
            return response()->json(['Error' => 'El id del servicio asignado no pertenece a la misma empresa que el id de la reparacion indicada, o no existen.', 'id servicio' => $request->servicio, 'id reparacion' => $request->idreparacion,], 202);
        
        //comprobar que si la reparacion esta o no facturada, en caso de devovler false, es decir [facturada], lanza msg error
        if(!$this->comprobacionEstadoDeReparacion($request->idreparacion))
            return response()->json(['Error' => 'Esta reparacion ya esta con estado [facturado] y no se le puede añadir mas serivicios. Nota: cree una reparacion nueva para añadir servicios', 'id reparacion' => $request->idreparacion,], 202);
        
        $precioServicio = $this->obtenerPrecioDelservicio($request->servicio); //Optenemos el precio del servicio i ndicado                
        $numerotrabajo = $this->obtenerNumeroTrabajoDeReparacion($request->idreparacion);
        $servicioReparacion = ServiciosReparaciones::create([
            'idreparacion' => $request->idreparacion,
            'numerotrabajo' => $numerotrabajo,
            'servicio' => $request->servicio,
            'precioServicio' => $precioServicio
        ]);

        return response()->json(['Message' => 'Servicio asignado a la reparacion con exito.', 'servicioReparacion' => $servicioReparacion,], 200);
    }

    /**
     * Lista todos los registros de la tabla serviciosReparaciones
     * @return [json]
     */
    function list() {
        
        return response()->json(ServiciosReparaciones::all());
    }

    /**
     * Lista todos los ids de servicios asignados a un id de reparacion y el numero de trabajo de cada uno
     * @return [json]
     */
    function listServiciosDeUnaReparacion($idreparacion) {
        $serviciosReparacion = ServiciosReparaciones::select('numerotrabajo', 'servicio')->where('idreparacion', $idreparacion)->get();
        return response()->json(['Message' => 'Servicios asignados a la reparacion con id = ' . $idreparacion, 'serviciosReparacion' => $serviciosReparacion], 200);
    }

    /**
     * @param mixed $idreparacion
     * Devuelve una vista estilizada de los serivcios de una reparacion.
     * Mostando la matricula del vehiculo en lugar del id y nombre del servicio ofrecido
     * @return [Json]
     */
    function vistaListServiciosDeUnaReparacion($idreparacion) {
        $reparacion = Reparaciones::select('idcoche', 'estadoReparacion')->where('id', $idreparacion)->first();
        $estado = $reparacion['estadoReparacion'];
        $matriculaCoche = Coches::select('matricula')->where('id', $reparacion['idcoche'])->first();
        $matricula = $matriculaCoche['matricula'];
        $serviciosreparacion = ServiciosReparaciones::select('serviciosreparaciones.numerotrabajo', 'serviciosreparaciones.servicio', 'servicios.nombre', 'serviciosreparaciones.precioServicio')->join('servicios', 'servicios.id', '=', 'serviciosreparaciones.servicio')->where('serviciosreparaciones.idreparacion', $idreparacion)->get();

        //Datos de la reparacion con dichso servicios
        $datosReparacion = array(
            "idreparacion" => $idreparacion,
            "matricula" => $matricula,
            "estado" => $estado
        );

        return response()->json(['Message' => 'Servicios al vehiculo con matricula [' . $matricula . '] y estado [' . $estado . '] y reparacion con id = ' . $idreparacion, 'DatosReparacion' => $datosReparacion, 'serviciosReparacion' => $serviciosreparacion], 200);
    }


    /**
     * @param mixed $id
     * Elimina un servicio asignado a una reparacion indicando el id de la reparacio y el numero de trabajo pro parametro.
     * los servicios que esten asociados a un id de reparacion con estado FACTURADO, NO podran ser eliminados
     * @return [json]
     */
    function delete($idreparacion, $numerotrabajo) {
        $servicioReparacion = Serviciosreparaciones::where('idreparacion', $idreparacion)->where('numerotrabajo', $numerotrabajo);
        if (sizeof($servicioReparacion->get()) <= 0)
            return response()->json(['Error' => 'No se encontro asociacion entre este id de reparacion y el numero de trabajo indicados ', 'idreparacion' => $idreparacion, 'numerotrabajo' => $numerotrabajo], 202);
        
        //Si esta con estado NO FACTURADO se elimina, si no, no se podra eliminar
        if($this->comprobacionEstadoDeReparacion($idreparacion)){
            //Si encontro un resultado
            $objetoEliminado = clone $servicioReparacion->get(); //Necesitamos clonarlo, puesto que una vez se elimina no podemos volver a mostrarlo a diferencia de las busquedas por find id
            $servicioReparacion->delete();

            return response()->json(['message' => 'Se elimino el trabajo ' . $numerotrabajo . ' de la reparacion con id ' . $idreparacion . ' con exito', 'servicioReparacion' => $objetoEliminado], 201);
        }
        else {
            return response()->json(['Error' => 'No se puede eliminar los servicios de una reparacion con estado FACTURADO ', 'idreparacion' => $idreparacion, 'numerotrabajo' => $numerotrabajo], 202);
        }
    }

    /**
     * @param Request $request
     * @param mixed $id
     * Actualiza los campos de el parametro $request que llegan diferentes de null
     * pasando el id de la reparacion por parametro
     * Nota: Si no le pasamos un "precio" por el valor request,
     * este le asignara el precio automaticamente de la tabla Servicios asociados al id del servicio pasado por el request.
     * En caso contrario se le asignara el precio indicado.
     * NO se puede actulizar o modificar servicios de reparaciones ya facturadas
     * @return [json]
     */
    function update(Request $request, $idreparacion, $numerotrabajo) {
        $reparacion = Reparaciones::select('estadoReparacion')->where('id', $idreparacion)->first();
      
        if($this->comprobacionEstadoDeReparacion($idreparacion)) {
            $servicioReparacion = Serviciosreparaciones::where('idreparacion', $idreparacion)->where('numerotrabajo', $numerotrabajo);
            $servicio = $request->servicio;
            $precioServicio = $request->precio; //Se le asigna un precio manualmente si se le indica, en caso contrario coge el de la tabla servicios automaticamente
            $respuesta = array();
            if (!is_null($servicio)) {
                if (is_null($precioServicio)) { //Si no le indicamos un precio manualmente cogera autoamticamente el del servicio
                    $precioServicio = $this->obtenerPrecioDelservicio($servicio);
                    //Actualizamos
                    $servicioReparacion->update([
                        'servicio' => $servicio,
                        'precioServicio' => $precioServicio
                    ]);
                    array_push($respuesta, 'servicio'); //Añadimos al final del array
                    array_push($respuesta, 'precio cogido de la tabla servicios automaticamente'); //Añadimos al final del array
                } else { //Si el precio NO es null, es decir esta escrito manualmente
                    //Actualizamos
                    $servicioReparacion->update([
                        'servicio' => $servicio,
                        'precioServicio' => $precioServicio
                    ]);
                    array_push($respuesta, 'servicio'); //Añadimos al final del array
                    array_push($respuesta, 'precio Manual escrito por el usuario'); //Añadimos al final del array
                }
            } else //Si el Servicio es null
            {
                if (!is_null($precioServicio)) //Si el precio NO es null
                {
                    $servicioReparacion->update([
                        'precioServicio' => $precioServicio
                    ]);
                    array_push($respuesta, 'precio Manual escrito por el usuario'); //Añadimos al final del array
                }
            }

            return response()->json(['message' => 'Servicios de la reparacion con Id ' . $idreparacion . ' y numero de trabajo ' . $numerotrabajo . ' actualizado con exito.', 'Modificaciones' => $respuesta, 'servicioreparacion' => $servicioReparacion->get()], 200);
        }else{
            //No se puede modificar por que la reparacion ya esta facturada
            return response()->json(['Error' => 'Esta reparacion ya esta con estado [facturado] y no se puede modificar. Nota: cree una reparacion nueva para añadir servicios', 'id reparacion' => $idreparacion,], 202);
        }
    }

    /**
     * @param mixed $idreparacion
     * @param mixed $idServicio
     * Comprueba que un id servicio pertenezca
     * a la misma empresa que al id de la reparacion a la que se asigna
     * preguntando si el usuario que esta asignado a la reparacion pertenece a la misma empresa (en la tabla Usuarios)
     * que el servicio (En la tabla servicios) asignado a esta reparacion.
     * @return [Booblean bool]
     */
    function comprobacionEmpresaServicioEmpresaReparacion($idreparacion, $idServicio) {
        $servicio = Servicios::where('id', $idServicio)->first();
        if (is_null($servicio)) //el id del servicio no existe
            return false;
        //Seleciona el usuario de la tabla usuariosEmpresas que coincida con el id de usuario de cuya reparacion tenga como id la indicada "idreparacion"
        $idusuario = Usuarios::whereIn('id', Reparaciones::select('idusuario')->where('id', $idreparacion)->get())->first();
        
        if (is_null($idusuario)) //el id del servicio no existe
            return false;
        $idEmpresaServicio = $servicio['empresa'];
        $idEmpresaUsuario = $idusuario['idempresa'];
        //Si coinciden los id de empresa, es que el servicio asignado a la reparacion pertenecen a la misma empresa
        if ($idEmpresaServicio == $idEmpresaUsuario)
            return true;
        else
            return false;
    }

    /**
     * @param mixed $idreparacion
     * Determina cual es el ultimo numnero de trabajo de la reparacion, le añade uno más al valor y lo devuelve.
     * En caso de ser 0 o no encontrar registros, devuelve 1.
     * @return [Integer numeroReparacionFinal]
     */
    public function obtenerNumeroTrabajoDeReparacion($idreparacion) {
        $numerotrabajoObtenido = 0;
        $servicioReparacion = Serviciosreparaciones::where('idreparacion', $idreparacion)->max('numerotrabajo');
        if ($servicioReparacion != null) //Si no existen registros previos en la tabla serviciosReparaciones con  ese id de reparacion, por lo lo que no hay un  "numerotrabajo" maximo        
            $numerotrabajoObtenido = $servicioReparacion;

        if ($numerotrabajoObtenido == 0 || is_null($numerotrabajoObtenido)) //si es 0 0 null devolvemos 1, si no, devolvemos el valor +1
        {
            $numerotrabajoObtenido = 1;
            return $numerotrabajoObtenido;
        } else {
            $numerotrabajoObtenido++;
            return $numerotrabajoObtenido;
        }
    }

    /**
     * @param mixed $idServicio
     * Optiene el precio del servicio indicado por id
     * para trasladarlo automaticamente a la tabla serviciosReparaciones
     * @return [Double]
     */
    function obtenerPrecioDelservicio($idServicio) {
        $precioServicio = Servicios::select('precio')->where('id', $idServicio)->first(); //Optenemos el precio del servicio   
        return $precioServicio['precio'];
    }

    /**
     * @param mixed $idreparacion
     * Comprueba el estado de la reparacion, si es 'facturado', esta no puede añadir
     *  ni modificar servicios en la tabla serviciosreparaciones y devolvera false,
     * en caso contrario si podra, es decir estado 'no facturado' y devolvera true.
     * @return [Boolean]
     */
    function comprobacionEstadoDeReparacion($idreparacion) {
        $reparacion = Reparaciones::select('estadoReparacion')->where('id', $idreparacion)->first();
        //dd($reparacion['estadoReparacion']);
        if($reparacion['estadoReparacion']==ServiciosReparacionesController::reparacionNoFacturado)
            return true;

        return false;
    }
}
