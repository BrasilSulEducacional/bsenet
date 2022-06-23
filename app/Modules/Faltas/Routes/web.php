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

Route::group([
    'middleware' => config('admin.route.middleware'),
    'prefix' => 'faltas'
], function () {
    Route::get('/', 'FaltasController@index')->name('faltas.index');

    Route::post('/justificar/{chamadaId}', 'FaltasController@justificar')->name('justificar.chamada');
});
