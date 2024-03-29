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
    Route::get('/justificar', 'JustificarController@index')->name('justificar.index');
    Route::post('/justificar/{chamadaId}', 'JustificarController@justificar')->name('justificar.chamada');

    Route::get('/relatorio', 'RelatorioController@index')->name('relatorio.index');

    Route::get('/list/all', 'FaltasController@all')->name('faltas.list');
});
