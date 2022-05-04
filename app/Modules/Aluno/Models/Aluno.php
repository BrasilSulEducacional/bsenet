<?php

namespace App\Modules\Aluno\Models;

use App\Modules\Nota\Models\Nota;
use App\Modules\Turma\Models\Turma;
use Illuminate\Database\Eloquent\Model;

class Aluno extends Model
{
    protected $table = 'alunos';

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }

    public function notas()
    {
        return $this->hasMany(Nota::class);
    }
}
