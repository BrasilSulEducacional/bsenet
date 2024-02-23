<?php

namespace App\Modules\Pagamentos\Models;

use App\Modules\Aluno\Models\Aluno;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Devendo extends Model
{
    use HasFactory;

    protected $table = 'devendo';

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }
}
