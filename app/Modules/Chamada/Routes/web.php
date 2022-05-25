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
    'prefix' => 'chamada',
], function () {
    Route::get('/', 'ChamadaController@index')->name('chamada.index');

    Route::get('/turma/{turmaId}', 'ChamadaController@turma')->name('chamada.turma');

    Route::get('/realizar/{turmaId}', 'ChamadaController@chamada')->name('chamada.register');
    Route::post('/realizar/{turmaId}', 'ChamadaController@chamada')->name('chamada.register');

    Route::get('/relatorio/{turmaId}/{conteudoId?}', 'ChamadaController@report')->name('chamada.report');

    Route::get('/revisar/{turmaId}/{conteudoId?}', 'ChamadaController@review')->name('chamada.review');
    Route::delete('/revisar/{turmaId}/{conteudoId?}/{destroy}', 'ChamadaController@reviewDestroy')->name('chamada.reviewDestroy');
    Route::put('/revisar/{turmaId}/{conteudoId?}/{update}', 'ChamadaController@reviewUpdate')->name('chamada.reviewUpdate');
});
