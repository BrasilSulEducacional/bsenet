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
    Route::get('/', 'ChamadaController@index');

    Route::get('/turma/{turmaId}', 'ChamadaController@turma')->name('chamada.turma');

    Route::post('/realizar/{turmaId}', 'ChamadaController@chamada')->name('chamada.register');
});
