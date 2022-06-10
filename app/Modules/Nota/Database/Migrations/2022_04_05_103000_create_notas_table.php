<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('aluno_id');
            $table->unsignedBigInteger('conteudo_id');
            $table->float('nota', 2, 2);
            $table->integer('faltas');
            $table->integer('aulas');

            $table->foreign('aluno_id')->references('id')->on('alunos');
            $table->foreign('conteudo_id')->references('id')->on('conteudos');

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
        Schema::dropIfExists('notas');
    }
}
