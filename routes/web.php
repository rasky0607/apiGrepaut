<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

//Rutas para acceder API desde las URL del n avegador o donde se llamen
$router->post('/registro', ['uses'=> 'UsuariosController@add']);
$router->post('/login', ['uses'=> 'UsuariosController@login']);

//Esta ruta externa comrprueba que el usuario tiene un token antes de realizar una peticion [delete,update,list,listid] con la clase Authenticate.php y AuthServiceProvider.php
$router->group(['middleware' => ['auth']], function () use ($router){
    
    //Tabla Usuarios
        $router->group(['prefix' => 'usuarios'], function () use ($router) {
        $router->get('/', ['uses'=> 'UsuariosController@list']);
        $router->post('/buscar', ['uses'=> 'UsuariosController@buscarUsuario']);
        $router->get('/logout/{id}', ['uses'=> 'UsuariosController@logout']);
        $router->delete('/{id}', ['uses'=> 'UsuariosController@delete']);
        $router->put('/{id}', ['uses'=> 'UsuariosController@update']);
    });

    //Tabla Empresas
    $router->group(['prefix' => 'empresas'], function () use ($router) {
        $router->post('/', ['uses'=> 'EmpresasController@add']);
        $router->get('/', ['uses'=> 'EmpresasController@list']);
        $router->post('/buscar', ['uses'=> 'EmpresasController@buscarEmpresa']);
        $router->delete('/{id}', ['uses'=> 'EmpresasController@delete']);
        $router->put('/{id}', ['uses'=> 'EmpresasController@update']);
        
    });

      //Tabla UsuariosEmpresas 
      //[PENDIENTE]-> comprobar que el usuario que accede ha esta tabla sea de tipo Admin (puede ser desde la api o con un trigger en la BD cuadno haga cambios)
      /*$router->group(['prefix' => 'usuariosempresas'], function () use ($router) {
        $router->post('/', ['uses'=> 'UsuariosEmpresasController@add']);
        $router->get('/', ['uses'=> 'UsuariosEmpresasController@list']);
                     
        $router->get('/enlaempresa/{idUsuario}/{idEmpresa}', ['uses'=> 'UsuariosEmpresasController@buscarUnUsuarioDeUnaEmpresa']);
                     
        $router->get('/misempresas/{idUsuario}', ['uses'=> 'UsuariosEmpresasController@empresasDelUsuario']);
                     
        $router->get('/empresa/{idEmpresa}', ['uses'=> 'UsuariosEmpresasController@buscarUsuariosDeEmpresa']);
                     
        $router->delete('/{idUsuario}/{idEmpresa}', ['uses'=> 'UsuariosEmpresasController@delete']);
        $router->put('/{idUsuario}/{idEmpresa}', ['uses'=> 'UsuariosEmpresasController@update']);
        

    });*/
               
    //Tabla Clientes
    $router->group(['prefix' => 'clientes'], function () use ($router) {
        $router->post('/', ['uses'=> 'ClientesController@add']);
        $router->get('/', ['uses'=> 'ClientesController@list']);
        $router->get('/empresa/{idEmpresa}', ['uses'=> 'ClientesController@clientesEmpresa']);
        $router->get('/buscar/{idEmpresa}/{nombre}[/{apellido}]', ['uses'=> 'ClientesController@buscarCliente']);
        $router->delete('/{id}', ['uses'=> 'ClientesController@delete']);
        $router->put('/{id}', ['uses'=> 'ClientesController@update']);
        

    });
    //Tabla Coches
    $router->group(['prefix' => 'coches'], function () use ($router) {
        $router->post('/', ['uses'=> 'CochesController@add']);
        $router->get('/', ['uses'=> 'CochesController@list']);
        $router->get('/cliente/{idCliente}', ['uses'=> 'CochesController@cochesDeUnCliente']);
        $router->get('/empresa/{idEmpresa}', ['uses'=> 'CochesController@cochesDeClientesDeUnaEmpresa']);
        $router->get('/coche/{id}', ['uses'=> 'CochesController@unCoche']);
        $router->delete('/{id}', ['uses'=> 'CochesController@delete']);
        $router->put('/{id}', ['uses'=> 'CochesController@update']);
        

    });

    //Tabla Servicios
    $router->group(['prefix' => 'servicios'], function () use ($router) {
        $router->post('/', ['uses'=> 'ServiciosController@add']);
        $router->get('/', ['uses'=> 'ServiciosController@list']);
        $router->get('/empresa/{idEmpresa}', ['uses'=> 'ServiciosController@buscarServiciosDeEmpresa']);
        $router->get('/buscar/{idEmpresa}/{nombre}', ['uses'=> 'ServiciosController@buscarUnServicio']);
        $router->delete('/{id}', ['uses'=> 'ServiciosController@delete']);
        $router->put('/{id}', ['uses'=> 'ServiciosController@update']);   
    });
    //Tabla Reparaciones
    $router->group(['prefix' => 'reparaciones'], function () use ($router) {
        $router->post('/', ['uses'=> 'ReparacionesController@add']);
        $router->get('/', ['uses'=> 'ReparacionesController@list']);
        $router->get('/usuario/{usuario}', ['uses'=> 'ReparacionesController@listReparacionesUsuario']);
        $router->get('/coche/{idcoche}', ['uses'=> 'ReparacionesController@reparacionesDeUnChoche']);
        $router->get('/empresa/{idusuario}', ['uses'=> 'ReparacionesController@listReparacionesEmpresa']);
        $router->delete('/{id}', ['uses'=> 'ReparacionesController@delete']);
        $router->put('/{id}', ['uses'=> 'ReparacionesController@update']);
        

    });
    //Tabla ServiciosReparaciones - de esta se obtienen las LineaFacturas al mostrar una factura en detalle
    $router->group(['prefix' => 'serviciosreparaciones'], function () use ($router) {
        $router->post('/', ['uses'=> 'ServiciosReparacionesController@add']);
        $router->get('/', ['uses'=> 'ServiciosReparacionesController@list']);
        $router->get('/{idreparacion}', ['uses'=> 'ServiciosReparacionesController@listServiciosDeUnaReparacion']);
        $router->get('/detalles/{idreparacion}', ['uses'=> 'ServiciosReparacionesController@vistaListServiciosDeUnaReparacion']);
        $router->delete('/{idreparacion}/{numerotrabajo}', ['uses'=> 'ServiciosReparacionesController@delete']);
        $router->put('/{idreparacion}/{numerotrabajo}', ['uses'=> 'ServiciosReparacionesController@update']);
        

    });
    //Tabla Facturas
    $router->group(['prefix' => 'facturas'], function () use ($router) {
        $router->post('/{idreparacion}', ['uses'=> 'FacturasController@nuevaFactura']);
        $router->get('/', ['uses'=> 'FacturasController@list']);
        $router->get('/lineasfactura/{idreparacion}', ['uses'=> 'FacturasController@lineasFactura']);
        $router->get('/empresa/{idusuario}', ['uses'=> 'FacturasController@listFacturasEmpresa']);
        $router->get('/vigentes/empresa/{idusuario}', ['uses'=> 'FacturasController@listFacturasEmpresaVigentes']);
        $router->put('/anularlareparacion/{idreparacionParaAnular}/por/{idreparacionNueva}', ['uses'=> 'FacturasController@anularFactura']);
         
    });

    //Emails

    
});

$router->get('/', function () use ($router) {
    return $router->app->version();
});

//Emails [EN P RUEBAS]

$router->get('/test_mail', function () {
    $data = ['test data'];
    try {

        \Illuminate\Support\Facades\Mail::send('emails.test',$data, function (\Illuminate\Mail\Message $message) {
            $message->from('devsan.adm@gmail.com', 'Grepaut Team')
            ->to('esviasalpa@gmail.com')->subject('Bienvenido a grepaut!');
         });

        /* \Illuminate\Support\Facades\Mail::send('emails.test', $data, function (\Illuminate\Mail\Message $message) {
            $message
                ->to('esviasalpa@example.com', 'Grepaut')
                ->from('esviasalpa@example.com', 'Pablo Lopez')
                ->replyTo('esviasalpa@bar.com', 'foobar')
                ->subject('Welcome to grepaut!')
                ->embedData([
                    'personalizations' => [
                        [
                            'to' => [
                                'email' => 'user1@example.com',
                                'name'  => 'user1',
                            ],
                            'substitutions' => [
                                '-email-' => 'user1@example.com',
                            ],
                        ],
                        [
                            'to' => [
                                'email' => 'user2@example.com',
                                'name'  => 'user2',
                            ],
                            'substitutions' => [
                                '-email-' => 'user2@example.com',
                            ],
                        ],
                    ],
                    'categories' => ['user_group1'],
                    'custom_args' => [
                        'user_id' => "123" // Make sure this is a string value
                    ]
                ], 'sendgrid/x-smtpapi');
        });*/
    } catch (Exception $exception) {
        dd($exception->getMessage());
    }
    dd('Mail sent');
});
