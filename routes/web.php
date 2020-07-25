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

$router->group(['prefix' => 'usuario'], function () use ($router) {
    $router->get('/', ['uses'=> 'UsuariosController@list']);
    $router->get('/{empresa}[/{email}]', ['uses'=> 'UsuariosController@listPorEmpresa']);//Lo que esta etre corchetes es opcional es decir puede ser nulo
    //$router->delete('/{empresa}/{email}', ['uses'=> 'UsuariosController@midelete']);
    $router->delete('/', ['uses'=> 'UsuariosController@delete']);
    $router->put('/{id}', ['uses'=> 'UsuariosController@update']);
    

});

$router->get('/', function () use ($router) {
    return $router->app->version();
});