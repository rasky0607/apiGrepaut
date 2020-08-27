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
        $router->get('/{email}', ['uses'=> 'UsuariosController@buscarUsuario']);
        $router->delete('/{id}', ['uses'=> 'UsuariosController@delete']);
        $router->put('/{id}', ['uses'=> 'UsuariosController@update']);
    });

    //Tabla Empresas
    $router->group(['prefix' => 'empresas'], function () use ($router) {
        $router->post('/', ['uses'=> 'EmpresasController@add']);
        $router->get('/', ['uses'=> 'EmpresasController@list']);
        $router->get('/{nombre}', ['uses'=> 'EmpresasController@buscarEmpresa']);
        $router->delete('/{id}', ['uses'=> 'EmpresasController@delete']);
        $router->put('/{id}', ['uses'=> 'EmpresasController@update']);
        
    });

      //Tabla UsuariosEmpresas 
      //[PENDIENTE]-> comprobar que el usuario que accede ha esta tabla sea de tipo Admin (puede ser desde la api o con un trigger en la BD cuadno haga cambios)
      $router->group(['prefix' => 'usuariosempresas'], function () use ($router) {
        $router->post('/', ['uses'=> 'UsuariosEmpresasController@add']);
        $router->get('/', ['uses'=> 'UsuariosEmpresasController@list']);
        $router->get('/{idUsuario}/{idEmpresa}', ['uses'=> 'UsuariosEmpresasController@buscarUnUsuarioDeUnaEmpresa']);
        $router->get('/misempresas/{idUsuario}', ['uses'=> 'UsuariosEmpresasController@empresasDelUsuario']);
        $router->get('/empleados/{idEmpresa}', ['uses'=> 'UsuariosEmpresasController@buscarUsuariosDeEmpresa']);
        $router->delete('/{idUsuario}/{idEmpresa}', ['uses'=> 'UsuariosEmpresasController@delete']);
        $router->put('/{idUsuario}/{idEmpresa}', ['uses'=> 'UsuariosEmpresasController@update']);
        

    });
    //Tabla Clientes
    $router->group(['prefix' => 'clientes'], function () use ($router) {
        $router->post('/', ['uses'=> 'ClientesController@add']);
        $router->get('/', ['uses'=> 'ClientesController@list']);
        $router->get('/{idEmpresa}', ['uses'=> 'ClientesController@clientesEmpresa']);
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
        $router->delete('/{idCliente}/{matricula}', ['uses'=> 'CochesController@delete']);
        $router->put('/{idCliente}/{matriculaCoche}', ['uses'=> 'CochesController@update']);
        

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
        $router->get('/{idusuario}', ['uses'=> 'ReparacionesController@listReparacionesUsuario']);
        $router->get('/coche/{matricula}/{empresa}', ['uses'=> 'ReparacionesController@reparacionesDeUnChoche']);
        $router->get('/empresa/{idEmpresa}', ['uses'=> 'ReparacionesController@listReparacionesEmpresa']);
        $router->delete('/{id}', ['uses'=> 'ReparacionesController@delete']);
        $router->put('/{id}', ['uses'=> 'ReparacionesController@update']);
        

    });
    //Tabla ServiciosReparaciones

    //Tabla Facturas

    //Tabla LineaFacturas

    
});

$router->get('/', function () use ($router) {
    return $router->app->version();
});