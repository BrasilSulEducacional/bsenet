<?php

namespace App\Modules\Chamada\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Aluno\Models\Aluno;
use App\Modules\Chamada\Models\Chamada;
use App\Modules\Turma\Models\Turma;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Form\Builder;
use Encore\Admin\Layout\Content;
use Encore\Admin\Grid;
use Illuminate\Http\Request;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Form as WidgetsForm;
use Encore\Admin\Widgets\Tab;
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

        $tableHeader = ['Código', 'Aluno', '1º Período (F/FJ)', '2º Período (F/FJ)'];

        $alunosRows = $alunos->map(function ($aluno) {
            return [
                'codigo' => $aluno->codigo,
                'aluno' => $aluno->nome,
                'primeiro_periodo' => '
                    F<input type="radio" data-input-check name="falta__' . $aluno->id . '__first" id="falta__' . $aluno->id . '">
                    FJ<input type="radio" data-input-check name="falta__' . $aluno->id . '__first" id="falta__' . $aluno->id . '">
                ',
                'segundo_periodo' => '
                    F<input type="radio" data-input-check name="falta__' . $aluno->id . '__second" id="falta__' . $aluno->id . '">
                    FJ<input type="radio" data-input-check name="falta__' . $aluno->id . '__second" id="falta__' . $aluno->id . '">
                '
            ];
        });

        $tableStyle = ['table-striped', 'table-hover'];

        $table = new Table($tableHeader, $alunosRows->toArray(), $tableStyle);

        $footer = "<a class=\"btn btn-primary pull-right\" id=\"registerChamada\" href=\"" . route('chamada.register', ['turmaId' => $turma->id]) . "\"> Confirmar </a>";
        $box = new Box('Chamada', $table, $footer);

        return $content
            ->title('Turma ' . $turma->turma)
            ->description('Selecione os alunos que faltaram')
            ->body(view('chamada::turma', ['box' => $box,]));
    }

    public function chamada(Content $content, Request $request)
    {
        dd($request->input());
        // $form = new Form(new Chamada);

        // $alunosModel = Aluno::has('turma')->get();
        // $alunos = $alunosModel->map(function ($aluno) {
        //     $presenca = "";
        //     $justificado = "";

        //     return [
        //         'nome' => $aluno->nome,
        //         'presenca' => $presenca,
        //         'justificado' => $justificado,
        //     ];
        // });

        // // dd($alunos->toArray());

        // $table = new Table(['Alunos', 'Presença', 'Justificado'], $alunos->toArray());
        // $box = new Box('Confirme a presença', $table->render());

        // return $content
        //     ->title('Chamada')
        //     ->description('Chamada')
        //     ->body($box);
    }
}
