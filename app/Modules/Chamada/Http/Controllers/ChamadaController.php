<?php

namespace App\Modules\Chamada\Http\Controllers;

use PDF;
use Carbon\Carbon;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Form\Builder;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Layout\Content;
use App\Modules\Aluno\Models\Aluno;
use App\Modules\Turma\Models\Turma;
use App\Http\Controllers\Controller;
use App\Modules\Chamada\Models\Chamada;
use Illuminate\Support\Facades\Storage;
use App\Modules\Conteudo\Models\Conteudo;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Widgets\Form as WidgetsForm;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Widgets\Collapse;
use Spatie\Browsershot\Browsershot;

class ChamadaController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        $user = auth()->user();

        return $content
            ->title('Turmas')
            ->description('Turmas')
            // ->body(view("chamada::chamada", ['turmas' => Turma::where('professor_id', $user->id)->get()]));
            ->body($this->chamadas());
    }

    public function chamadas()
    {
        $collapse = new Collapse();

        $turmas = Turma::where('professor_id', auth()->user()->id)->get();


        foreach ($turmas as $turma) {
            $table = new Table(['Conteúdo', 'Registros', 'Último registro', 'Ação']);
            // $table = new Table(['Keys', 'Values']);
            $chamadasConteudo = $turma
                ->chamadas()
                ->orderBy('conteudo_id')
                ->get()
                ->groupBy('conteudo.id');

            $chamada = $chamadasConteudo->map(function ($item, $key) {
                $routeReport = route('chamada.report', [
                    'turmaId'    => $item->last()->turma->id,
                    'conteudoId' => $item->last()->conteudo->id
                ]);

                $routeRegister = route('chamada.turma', [
                    'turmaId' => $item->last()->turma->id,
                    'c' => $item->last()->conteudo->id
                ]);

                return [
                    $item->last()->conteudo->name,
                    $item->count() / 2,
                    date("d/m/Y", strtotime($item->last()->feita_em)),
                    "
                        <a class=\"btn btn-success btn-sm\" href=\"{$routeRegister}\">
                            <i class=\"fa fa-calendar-plus-o\"></i> Registrar
                        </a>
                        <a class=\"btn btn-danger btn-sm\" href=\"{$routeReport}\" id=\"report\" target=\"_blank\">
                            <i class=\"fa fa-file-pdf-o\"></i> Relatório
                        </a>
                    "
                ];
            });


            if (!empty($chamada->toArray())) {
                $table->setRows($chamada->values()->toArray());
            }

            $collapse->add(
                'Turma ' . $turma->turma,
                "<div class=\"table-responsive\">" . $table->render() . "</div>"
                    . "<a class=\"btn btn-primary btn-sm\" href=\"" . route('chamada.turma', ['turmaId' => $turma->id]) . "\">Nova chamada</a>"
            );
        }

        $box = new Box('Selecione a turma', $collapse->render());

        return $box;
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
        $latest = $request->c ? $turma->chamadas()->where('conteudo_id', $request->c) : $turma->chamadas()->latest();

        $options = $conteudos->reduce(function ($a, $b, $key) use ($latest) {
            $selected = $latest->value('conteudo_id');
            return $a . "<option value=\"{$key}\" " . ($selected == $key ? 'selected' : '') . "> {$b} </option> \n";
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
                            <select class=\"form-control conteudo\" name=\"conteudo\">
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

        $box = new Box('Chamada realizada', "<a href=\"" . route('chamada.index') . "\"> Clique aqui </a> para voltar");

        return $content
            ->title('Turmas')
            ->description('Turmas')
            ->body(view("chamada::success", ['box' => $box]));
    }

    public function report(Request $request)
    {

        $turma = Turma::find($request->turmaId);
        $alunos = $turma->alunos()->orderBy('nome')->get();
        $latest = $request->conteudoId ? $turma->chamadas()->where('conteudo_id', $request->conteudoId) : $turma->chamadas()->latest();

        $conteudo = Conteudo::find($latest->value('conteudo_id'));
        $chamada = Chamada::where('conteudo_id', $latest->value('conteudo_id'))->where('turma_id', $turma->id);
        $chamadaDatas = $chamada->distinct()->get(['feita_em', 'periodo']);

        // dd($chamada->groupBy('aluno_id'));
        // dd($chamada->get());

        $qtdDatas = range(1, 22);
        $qtdDatasFaltas = range(1, 11);

        $pdf = PDF::loadView('chamada', compact('turma', 'alunos', 'qtdDatas', 'qtdDatasFaltas', 'chamada', 'chamadaDatas', 'conteudo'));

        $filename = uniqid() . ".pdf";
        $path = Storage::disk('public')->getAdapter()->getPathPrefix();

        Storage::disk('public')->put($filename, $pdf->output());

        return response()->download($path . $filename)->deleteFileAfterSend();
    }
}
