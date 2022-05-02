<?php

namespace App\Modules\Nota\Models;

use App\Modules\Aluno\Models\Aluno;
use App\Modules\Conteudo\Models\Conteudo;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $table = 'notas';

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }

    public function conteudo()
    {
        return $this->belongsTo(Conteudo::class);
    }
}
