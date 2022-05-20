<?php

namespace App\Modules\Chamada\Models;

use App\Modules\Conteudo\Models\Conteudo;
use App\Modules\Nota\Models\Nota;
use App\Modules\Turma\Models\Turma;
use Illuminate\Database\Eloquent\Model;

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
}
