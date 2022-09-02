<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcelas', function (Blueprint $table) {
            $table->id();
            $table->integer("num_parcela");
            $table->decimal("valor",$precision = 18, $scale = 2);
            $table->date("vencimento");
            $table->foreignId('aluno_id')->constrained('alunos');
            $table->decimal("devendo",$precision = 18, $scale = 2)->nullable();
            $table->boolean('tipo')->comment('1 = parcela, 0 = acerto');
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
        Schema::dropIfExists('parcelas');
    }
}
