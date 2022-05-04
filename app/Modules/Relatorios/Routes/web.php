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
    'prefix' => 'relatorios'
], function () {
    Route::get('/boletim', 'BoletimController@index');
    Route::post('/boletim/report/{type}', 'BoletimController@report', 'report.boletim');
    Route::get('/boletim/report/{type}/{aluno}', 'BoletimController@report');

    Route::get('/chamada', 'ChamadaController@index');
    Route::post('/chamada/report', 'ChamadaController@report');
});
