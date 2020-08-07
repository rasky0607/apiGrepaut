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

/*
$router->get('/usuario', ['uses'=> 'UsuariosController@list']);
$router->delete('/delete/{email}/{empresa}', ['uses'=> 'UsuariosController@delete']);
*/
//Rutas para acceder API desde las URL del n avegador o donde se llamen
$router->post('/registro', ['uses'=> 'UsuariosController@add']);

//Esta ruta externa comrprueba que el usuario tiene un token antes de realizar una peticion [delete,update,list,listid] con la clase Authenticate.php y AuthServiceProvider.php
$router->group(['middleware' => ['auth']], function () use ($router){
    
    //Tabla Usuario
    $router->group(['prefix' => 'usuario'], function () use ($router) {
        $router->get('/', ['uses'=> 'UsuariosController@list']);
        $router->get('/{email}', ['uses'=> 'UsuariosController@buscarUsuario']);
        $router->delete('/{id}', ['uses'=> 'UsuariosController@delete']);
        $router->put('/{id}', ['uses'=> 'UsuariosController@update']);
        //Mis notas de ejemplo:
        //Lo que esta etre corchetes es opcional es decir puede ser nulo
        //$router->get('/{empresa}[/{email}]', ['uses'=> 'UsuariosController@listPorEmpresa']);
        //pasando mas de un campo por la URL
        //$router->delete('/{empresa}/{email}', ['uses'=> 'UsuariosController@midelete']);
        

    });

    //Tabla Empresa
    $router->group(['prefix' => 'empresa'], function () use ($router) {
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
        $router->get('/{idEmpresa}[/{idUsuario}]', ['uses'=> 'UsuariosEmpresasController@buscarUsuariosDeEmpresa']);
        $router->delete('/{idEmpresa}/{idUsuario}', ['uses'=> 'UsuariosEmpresasController@delete']);
        $router->put('/{idEmpresa}/{idUsuario}', ['uses'=> 'UsuariosEmpresasController@update']);
        

    });
    
});

$router->get('/', function () use ($router) {
    return $router->app->version();
});