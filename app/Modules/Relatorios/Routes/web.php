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
    Route::get('/boletim/export/aluno/{id}', 'BoletimController@export');

    Route::get('/chamada', 'ChamadaController@index')->name('relatorios.chamada.index');
    Route::post('/chamada/report', 'ChamadaController@report')->name('relatorios.chamada.report');

    Route::get('/comprovante/parcela/{id}', 'PagamentoController@comprovante')->name("relatorios.comprovante.pagamento");
});
