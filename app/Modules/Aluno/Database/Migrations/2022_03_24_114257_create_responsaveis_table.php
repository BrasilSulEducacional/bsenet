<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResponsaveisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('responsaveis', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('tipo');
            $table->string('email')->nullable();
            $table->dateTime('data_nasc');
            $table->string('estado_civil');
            $table->string('rg');
            $table->string('cpf', 11);

            $table->string('celular')->nullable();
            $table->string('telefone')->nullable();

            $table->unsignedBigInteger('endereco_id');
            $table->foreign('endereco_id')->references('id')->on('enderecos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('responsaveis');
    }
}
