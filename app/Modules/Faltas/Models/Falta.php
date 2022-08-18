<?php

namespace App\Modules\Faltas\Models;

use App\Modules\Aluno\Models\Aluno;
use App\Modules\Chamada\Models\Chamada;
use Illuminate\Database\Eloquent\Model;

class Falta extends Model
{
    protected $table = 'faltas_justificadas';

    public $timestamps = false;

    public function chamada()
    {
        return $this->belongsTo(Chamada::class);
    }
}
