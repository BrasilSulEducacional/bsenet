<?php

namespace App\Modules\Aluno\Models;

use App\Modules\Turma\Models\Turma;
use Illuminate\Database\Eloquent\Model;

class Aluno extends Model
{
    protected $table = 'alunos';

    public function turma()
    {
        return $this->hasOne(Turma::class);
    }
}
