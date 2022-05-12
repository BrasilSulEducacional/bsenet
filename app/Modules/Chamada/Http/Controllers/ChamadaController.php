<?php

namespace App\Modules\Chamada\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Aluno\Models\Aluno;
use App\Modules\Chamada\Models\Chamada;
use App\Modules\Conteudo\Models\Conteudo;
use App\Modules\Turma\Models\Turma;
use Carbon\Carbon;
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
                    F<input type="radio" data-input-check name="falta__' . $aluno->codigo . '__first" id="falta__' . $aluno->codigo . '">
                    FJ<input type="radio" data-input-check name="falta__' . $aluno->codigo . '__first" id="falta__' . $aluno->codigo . '">
                ',
                'segundo_periodo' => '
                    F<input type="radio" data-input-check name="falta__' . $aluno->codigo . '__second" id="falta__' . $aluno->codigo . '">
                    FJ<input type="radio" data-input-check name="falta__' . $aluno->codigo . '__second" id="falta__' . $aluno->codigo . '">
                '
            ];
        });

        $tableStyle = ['table-striped', 'table-hover'];

        $table = new Table($tableHeader, $alunosRows->toArray(), $tableStyle);
        $conteudos = Conteudo::all()->pluck('name', 'id');
        $latest = $turma->chamadas()->latest();

        $options = $conteudos->reduce(function ($a, $b, $key) use ($latest) {
            $checked = $latest->value('conteudo_id') ?: null;

            return $a . "<option value=\"{$key}\" " . ($checked == $key ? 'checked' : '') . "> {$b} </option> \n";
        });

        $header = "
        <div class=\"fields-group form-horizontal\">
            <div class=\"col-md-12\">
                <div class=\"form-group\">
                    <label for=\"chamadaDate\" class=\"col-sm-2 control-label\">Dia</label>
                    <div class=\"col-sm-8\">
                        <div class=\"input-group\">
                            <span class=\"input-group-addon\">
                                <i class=\"fa fa-calendar fa-fw\"></i>
                            </span>
                            <input type=\"date\" id=\"chamadaDate\" class=\"form-control chamadaDate\" value=\"" . date("Y-m-d") . "\">
                        </div>
                    </div>
                </div>

                <div class=\"form-group\">
                    <label for=\"conteudo\" class=\"col-sm-2 control-label\">Conteúdo</label>
                    <div class=\"col-sm-8\">
                        <div class=\"input-group\">
                            <span class=\"input-group-addon\">
                                <i class=\"fa fa-pencil fa-fw\"></i>
                            </span>
                            <input type=\"hidden\" name=\"conteudo\">
                            <select class=\"form-control conteudo\" style=\"width: 100%;\" name=\"conteudo\">
                                {$options}
                            </select>
                        </div>
                    </div>
                </div>
            </div>    
        </div>
        ";

        $form = new WidgetsForm();

        $form->date('data')->format("DD/MM/YYYY");
        $form->select('conteudo')->options($conteudos->toArray())->default($turma->chamadas()->latest()->value('conteudo_id'));
        $form->disableReset();
        $form->disableSubmit();

        $footer = "<a class=\"btn btn-primary pull-right\" id=\"registerChamada\" href=\"" . route('chamada.register', ['turmaId' => $turma->id]) . "\"> Confirmar </a>";
        $box = new Box('Chamada', $header . $table, $footer);

        return $content
            ->title('Turma ' . $turma->turma)
            ->description('Selecione os alunos que faltaram')
            ->body(view('chamada::turma', ['box' => $box,]));
    }

    public function chamada(Content $content, Request $request)
    {
        $chamada = collect($request->input());

        // dd($request->input());

        $chamada = $chamada->map(function ($aluno) {
            if ($aluno["firstPeriod"]["falta"] == $aluno["firstPeriod"]["falta_justificada"]) {
                $aluno["firstPeriod"]["falta_justificada"] = null;
            }

            if (!$aluno["firstPeriod"]["falta"] && $aluno["firstPeriod"]["falta_justificada"]) {
                $aluno["firstPeriod"]["falta"] = true;
            }

            if ($aluno["secondPeriod"]["falta"] == $aluno["secondPeriod"]["falta_justificada"]) {
                $aluno["secondPeriod"]["falta_justificada"] = null;
            }

            if (!$aluno["secondPeriod"]["falta"] && $aluno["secondPeriod"]["falta_justificada"]) {
                $aluno["secondPeriod"]["falta"] = true;
            }



            $alunoModel = Aluno::where('codigo', $aluno["codigo"])->first();
            $chamadaFirstPeriod = new Chamada;

            $chamadaFirstPeriod->aluno_id = $alunoModel->id;
            $chamadaFirstPeriod->turma_id = $alunoModel->turma_id;
            $chamadaFirstPeriod->conteudo_id = $aluno["conteudoId"];
            $chamadaFirstPeriod->periodo = 1;
            $chamadaFirstPeriod->falta = $aluno["firstPeriod"]["falta"];
            $chamadaFirstPeriod->falta_justificada = $aluno["firstPeriod"]["falta_justificada"];
            $chamadaFirstPeriod->feita_em = $aluno["chamadaDate"];

            $chamadaSecondPeriod = new Chamada;

            $chamadaSecondPeriod->aluno_id = $alunoModel->id;
            $chamadaSecondPeriod->turma_id = $alunoModel->turma_id;
            $chamadaSecondPeriod->conteudo_id = $aluno["conteudoId"];
            $chamadaSecondPeriod->periodo = 2;
            $chamadaSecondPeriod->falta = $aluno["secondPeriod"]["falta"];
            $chamadaSecondPeriod->falta_justificada = $aluno["secondPeriod"]["falta_justificada"];
            $chamadaSecondPeriod->feita_em = $aluno["chamadaDate"];

            $chamadaFirstPeriod->save();
            $chamadaSecondPeriod->save();
        });

        admin_toastr('Chamada realizada com sucesso!', 'success');
        redirect()->route('chamada.index');
    }
}
