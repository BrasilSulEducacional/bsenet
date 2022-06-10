<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your module. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

use Illuminate\Routing\Router;

Route::group([
    'middleware' => config('admin.route.middleware'),
    'prefix' => 'conteudo'
], function (Router $router) {
    Route::resource('/controle', 'ConteudoController');
    $router->get('/all', 'ConteudoController@all')->name('sis.conteudo.all');
});
