<?php

namespace App\Modules\Conteudo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conteudo extends Model
{
    use SoftDeletes;

    protected $table = 'conteudos';
}
