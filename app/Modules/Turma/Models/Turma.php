<?php

namespace App\Modules\Turma\Models;

use App\Modules\Aluno\Models\Aluno;
use App\Modules\Chamada\Models\Chamada;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Model;

class Turma extends Model
{
    protected $table = 'turmas';

    public function professor()
    {
        return $this->belongsTo(Administrator::class);
    }

    public function alunos()
    {
        return $this->hasMany(Aluno::class);
    }

    public function chamadas()
    {
        return $this->hasMany(Chamada::class);
    }
}
