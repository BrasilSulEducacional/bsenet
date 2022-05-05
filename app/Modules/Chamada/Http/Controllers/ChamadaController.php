<?php

namespace App\Modules\Chamada\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Aluno\Models\Aluno;
use App\Modules\Chamada\Models\Chamada;
use App\Modules\Turma\Models\Turma;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Encore\Admin\Grid;
use Illuminate\Http\Request;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Table;

class ChamadaController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        $user = auth()->user();

        return $content
            ->title('Turmas')
            ->description('Turmas')
            ->body(view("chamada::chamada", ['turmas' => Turma::where('professor_id', $user->id)->get()]));
    }

    public function turma(Content $content, Request $request)
    {
        $turma = Turma::find($request->turmaId);
        $alunos = $turma->alunos;

        return $content
            ->title('Turma')
            ->description('Selecione os alunos que faltaram')
            ->body(view("chamada::turma", compact('turma', 'alunos')));
    }

    public function chamada(Content $content, Request $request)
    {
        $form = new Form(new Chamada);

        $alunosModel = Aluno::has('turma')->get();
        $alunos = $alunosModel->map(function ($aluno) {
            $presenca = "";
            $justificado = "";

            return [
                'nome' => $aluno->nome,
                'presenca' => $presenca,
                'justificado' => $justificado,
            ];
        });

        // dd($alunos->toArray());

        $table = new Table(['Alunos', 'Presença', 'Justificado'], $alunos->toArray());
        $box = new Box('Confirme a presença', $table->render());

        return $content
            ->title('Chamada')
            ->description('Chamada')
            ->body($box);
    }
}
