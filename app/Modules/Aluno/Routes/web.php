<?php

use Illuminate\Routing\Router;

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

// Admin::routes();

Route::group([
    'middleware' => config('admin.route.middleware'),
    'prefix' => 'aluno'
], function (Router $router) {
    // $router->get('/', 'AlunoController@index')->name('sis.aluno');

    Route::resource('/controle', 'AlunoController')->names('sis.aluno');

    // // novo aluno
    // $router->get('/create', 'AlunoController@create')->name('sis.aluno.create');
    // $router->post('/create', 'AlunoController@store')->name('sis.aluno.create.post');
});
