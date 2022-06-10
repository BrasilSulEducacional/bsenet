<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChamadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chamadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aluno_id')->constrained('alunos');
            $table->foreignId('turma_id')->constrained('turmas');
            $table->foreignId('conteudo_id')->constrained('conteudos');
            $table->boolean('falta');
            $table->integer('periodo');
            $table->boolean('falta_justificada')->nullable();
            $table->dateTime('feita_em');
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
        Schema::dropIfExists('chamadas');
    }
}
