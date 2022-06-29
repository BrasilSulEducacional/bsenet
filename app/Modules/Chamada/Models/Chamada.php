<?php

namespace App\Modules\Chamada\Models;

use App\Modules\Nota\Models\Nota;
use Illuminate\Support\Facades\DB;
use App\Modules\Aluno\Models\Aluno;
use App\Modules\Turma\Models\Turma;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Conteudo\Models\Conteudo;
use App\Modules\Faltas\Models\Falta;

class Chamada extends Model
{
    protected $table = 'chamadas';

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }

    public function conteudo()
    {
        return $this->belongsTo(Conteudo::class);
    }

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }

    public function justificativa()
    {
        return $this->hasOne(Falta::class, 'chamada_id');
    }
}
