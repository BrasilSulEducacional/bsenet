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
    'prefix' => 'financeiro'
], function (Router $router) {
    Route::get('/', 'FinanceiroController@index')->name('financeiro.index');
    Route::post('/cadastrar', 'FinanceiroController@cadastroParcelas')->name('financeiro.cadastro.parcelas');

    Route::post('/parcelas/{type}', 'FinanceiroController@listarParcelas', 'financeiro.consulta.parcela');
    Route::get('/parcelas/{type}/{aluno}', 'FinanceiroController@listarParcelas');

    Route::get('/pagar/{type}/{parcelaId}', 'FinanceiroController@pagar')->name('financeiro.pagar.parcela');
    Route::post('/confirmarPagamento', 'FinanceiroController@confirmarPagamento')->name('financeiro.confirmar');

    Route::post('/dadosPagamento', 'FinanceiroController@dadosPagamento')->name('financeiro.pagar.dadosPagamento');

    Route::resource('/controle/parcelas', 'ParcelasController');

    Route::resource('/controle/pagamentos', 'PagamentosController');


});
