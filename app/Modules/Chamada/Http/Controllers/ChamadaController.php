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

    protected $datePerPage = 22;

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


            $chamada = $chamadasConteudo->map(function ($item, $key) use ($turma) {
                $lastRegister = $item
                    ->sortBy('feita_em')
                    ->pluck('feita_em')
                    ->last();

                $routeReport = route('chamada.report', [
                    'turmaId'    => $item->last()->turma->id,
                    'conteudoId' => $item->last()->conteudo->id
                ]);

                $routeRegister = route('chamada.turma', [
                    'turmaId' => $item->last()->turma->id,
                    'c' => $item->last()->conteudo->id
                ]);

                $routeReview = route('chamada.review', [
                    'turmaId'    => $item->last()->turma->id,
                    'conteudoId' => $item->last()->conteudo->id
                ]);

                return [
                    $item->last()->conteudo->name,
                    $item->count() / 2,
                    date("d/m/Y", strtotime($lastRegister)),
                    "
                        <a class=\"btn btn-success btn-sm\" href=\"{$routeRegister}\">
                            <i class=\"fa fa-calendar-plus-o\"></i> Registrar
                        </a>
                        <a class=\"btn btn-danger btn-sm\" href=\"{$routeReport}\" id=\"report\" target=\"_blank\">
                            <i class=\"fa fa-file-pdf-o\"></i> Relatório
                        </a>
                        <a class=\"btn btn-warning btn-sm\" href=\"{$routeReview}\">
                            <i class=\"fa fa-edit\"></i> Editar
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

    public function review(Content $content, Request $request)
    {
        $turma = Turma::find($request->turmaId);
        $conteudo = Conteudo::find($request->conteudoId);

        $grid = new Grid(new Chamada());
        $grid->model()
            ->where('turma_id', $turma->id)
            ->where('conteudo_id', $conteudo->id);

        $grid->model()
            ->orderBy('feita_em', 'desc')
            ->orderBy('aluno_id')
            ->orderBy('periodo');

        // $grid->column('nome')->expand(function ($aluno) use ($conteudo) {
        //     $chamada = Chamada::where('turma_id', $aluno->turma->id)
        //         ->where('aluno_id', $aluno->id)
        //         ->where('conteudo_id', $conteudo->id)->get();

        //     $display = $chamada->map(function ($item) {
        //         $color = $item->periodo == 1 ? 'primary' : 'info';
        //         $falta = $item->falta ? 'F' : '<span style="font-size: 1.5rem;">&middot;</span>';
        //         $falta_justificada = is_null($item->falta_justificada) ? '--' : ($item->falta_justificada ? 'Sim' : 'Não');
        //         return [
        //             'feita_em' => date("d/m/Y", strtotime($item->feita_em)),
        //             'periodo' => "<span class=\"label label-{$color}\"> {$item->periodo} </span>",
        //             'falta' => $falta,
        //             'falta_justificada' => $falta_justificada,
        //             'acao' => "
        //                 <a href=\"#\">
        //                     <i class=\"fa fa-trash\"></i>
        //                 </a>
        //             "
        //         ];
        //     });

        //     $table = (new Table(['Feita em', 'Período', 'Falta', 'Falta Justificada'], $display->toArray(), ['table-hover', 'table-striped']));
        //     return new Box('Chamada', $table);
        // });

        $grid->column('aluno.nome');

        $grid->column('feita_em')->display(function () {
            return date('Y-m-d', strtotime($this->feita_em));
        })->date('Y')->sortable();

        $grid->column('periodo')->display(function ($item) {
            $color = $item == 1 ? 'danger' : 'success';
            return "<span class=\"label label-{$color}\"> $item </span>";
        });

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();

            $filter->column(1 / 2, function ($filter) {
                $filter->between('feita_em')->date();
            });
        });

        $grid->disableCreateButton();
        $grid->disableExport();

        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableView();
        });

        return $content
            ->title('Turmas')
            ->description('Turmas')
            ->body($grid->render());
    }

    public function reviewDestroy(Request $request)
    {
        $registers = $request->destroy;

        Chamada::destroy(explode(",", $registers));
    }

    public function reviewUpdate(Request $request)
    {
        $chamadaId = $request->update;
        $newDate = $request->input('feita_em');

        Chamada::where('id', $chamadaId)->update([
            'feita_em' => $newDate
        ]);
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
        $latest = $request->conteudoId ? $turma->chamadas()->where('conteudo_id', $request->conteudoId) : $turma->chamadas()->latest();
        $chamada = Chamada::where('turma_id', $request->turmaId)->where('conteudo_id', $request->conteudoId);
        $alunos = $chamada->get()->sortBy([
            ['aluno.nome', 'asc'],
            ['feita_em', 'asc'],
        ])->groupBy('aluno.nome');

        $pagination = collect();
        $qtdDatas = range(1, $this->datePerPage);
        $qtdDatasFaltas = range(1, $this->datePerPage / 2);

        $paginate = $alunos->first()->paginate($this->datePerPage);

        $pages = collect(range($paginate->currentPage(), $paginate->lastPage()));

        $pages->each(function ($item, $key) use ($pagination, $alunos) {
            $pagination->put($key, $alunos->map(function ($item) use ($key) {
                return $item->forPage($key + 1, $this->datePerPage);
            }));
        });

        $conteudo = Conteudo::find($latest->value('conteudo_id'));

        $chamadaDatas = $chamada->distinct()->get(['feita_em', 'periodo']);

        $pdf = PDF::loadView('chamada::pdf', compact(
            'turma',
            // 'alunos',
            'qtdDatas',
            'qtdDatasFaltas',
            'chamada',
            'chamadaDatas',
            'conteudo',
            'pagination'
        ));

        $filename = uniqid() . ".pdf";
        $path = Storage::disk('public')->getAdapter()->getPathPrefix();

        Storage::disk('public')->put($filename, $pdf->output());

        return response()->download($path . $filename)->deleteFileAfterSend();
    }
}
