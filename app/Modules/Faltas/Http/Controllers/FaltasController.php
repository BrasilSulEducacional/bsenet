<?php

namespace App\Modules\Faltas\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Faltas\Models\Falta;
use App\Modules\Chamada\Models\Chamada;

class FaltasController extends Controller
{
    public function all(Request $queryRequest)
    {
        if ($q = $queryRequest->get('q')) {
            return Chamada::join('faltas_justificadas', 'chamadas.id', '=', 'faltas_justificadas.chamada_id')
                ->join('alunos', 'chamadas.aluno_id', '=', 'alunos.id')
                ->select('alunos.id', 'alunos.nome')
                ->get()
                ->paginate(null, ['id', 'nome as text']);
        }

        return Falta::all()->take(10)->paginate(null, ['aluno.id', 'aluno.name as text']);
    }
}
