<?php

namespace App\Modules\Pagamentos\Models;

use App\Modules\Aluno\Models\Aluno;
use Illuminate\Database\Eloquent\Model;

class Parcelas extends Model
{

    protected $table = 'parcelas';

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }
}
