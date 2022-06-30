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
use Encore\Admin\Facades\Admin;
use Encore\Admin\Widgets\Collapse;
use Illuminate\Support\MessageBag;
use Spatie\Browsershot\Browsershot;

class ChamadaController extends Controller
{
    use HasResourceActions;

    protected $datePerPage = 22;

    protected $faltas = [
        0 => 'Presente',
        1 => 'Falta',
    ];

    protected $faltasJustificada = [
        0 => 'Não',
        1 => 'Sim',
        '-'
    ];

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

                $urlFalta = route('faltas.index', ['turma[turma]'=> $turma->turma]);

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
                        <a class=\"btn btn-info btn-sm\" href=\"{$urlFalta}\">
                            <i class=\"fa fa-asterisk\"></i> Faltas
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

        $grid->column('falta', 'Situação')->editable('select', $this->faltas);

        $faltasJustificada = $this->faltasJustificada;

        $grid->column('falta_justificada')->display(function ($item) use ($faltasJustificada) {
            return is_null($item) ? 2 : $item;
        })->editable('select', $this->faltasJustificada);

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();

            $filter->where(function ($query) {
                $query->whereHas('aluno', function ($query) {
                    $query->where('nome', 'like', "%{$this->input}%");
                });
            }, 'Nome');

            $filter->column(1 / 2, function ($filter) {
            });

            $filter->column(1 / 2, function ($filter) {
                $filter->between('feita_em')->date("DD/MM/YYYY");
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

        Chamada::where('id', $chamadaId)->update([
            $request->input('name') => $request->input('value')
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Atualizado com sucesso!',
        ]);
    }

    public function turma(Content $content, Request $request)
    {
        $turma = Turma::find($request->turmaId);
        $alunos = $turma
            ->alunos()
            ->where('status', 1)
            ->orderBy('status', 'desc')
            ->orderBy('nome')
            ->get();

        $excepts = $turma
            ->alunos()
            ->where('status', '!=', 1)
            ->get()
            ->pluck('id')
            ->toJson();

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
            ->body(view('chamada::turma', ['box' => $box, 'excepts' => $excepts, 'turmaId' => $request->turmaId]));
    }

    public function chamada(Content $content, Request $request)
    {
        $chamada = collect($request->input('chamada'));
        $conteudoId = $request->conteudoId;
        $chamadaDate = $request->chamadaDate;
        $turmaId = $request->turmaId;

        if (Chamada::where('turma_id', $turmaId)->where('conteudo_id', $conteudoId)->where('feita_em', $chamadaDate)->first()) {

            return response()->json([
                'type' => 'error',
                'message' => 'Parece que você já fez uma chamada neste dia.'
            ]);
        }

        // alunos com status diferente de 'Cursando'
        $excepts = collect($request->input('excepts'));

        $excepts->each(function ($id) use ($conteudoId, $chamadaDate) {
            $aluno = Aluno::find($id);

            $chamadaFirstPeriod = new Chamada;
            $chamadaFirstPeriod->aluno_id = $aluno->id;
            $chamadaFirstPeriod->turma_id = $aluno->turma_id;
            $chamadaFirstPeriod->conteudo_id = $conteudoId;
            $chamadaFirstPeriod->feita_em = $chamadaDate;
            $chamadaFirstPeriod->periodo = 1;

            $chamadaSecondPeriod = new Chamada;
            $chamadaSecondPeriod->aluno_id = $aluno->id;
            $chamadaSecondPeriod->turma_id = $aluno->turma_id;
            $chamadaSecondPeriod->conteudo_id = $conteudoId;
            $chamadaSecondPeriod->feita_em = $chamadaDate;
            $chamadaSecondPeriod->periodo = 2;

            $chamadaFirstPeriod->save();
            $chamadaSecondPeriod->save();
        });

        $chamada->map(function ($aluno) {

            // dump($aluno);

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

        // admin_success('Chamada', 'Chamada realizada com sucesso!');
        // admin_toastr('Chamada realizada com sucesso!');

        // return redirect(route('chamada.index'));
        return response()->json(['type' => 'success', 'message' => 'Chamada realizada com sucesso!', 'status' => true, 'redirect_url' => route('chamada.index')]);
    }

    public function report(Request $request)
    {

        $turma = Turma::find($request->turmaId);
        $latest = $request->conteudoId ? $turma->chamadas()->where('conteudo_id', $request->conteudoId) : $turma->chamadas()->latest();
        $chamada = Chamada::where('turma_id', $request->turmaId)->where('conteudo_id', $request->conteudoId)->with('aluno');
        $alunos = $chamada->get()->sortBy([
            ['aluno.turma', 'desc'],
            ['aluno.nome', 'asc'],
            ['feita_em', 'asc'],
        ])->groupBy('aluno.nome');

        $chamadaDatas = $chamada->distinct()->get(['feita_em', 'periodo']);
        // $chamadaDatas = $chamada->with('aluno')->get(['feita_em', 'periodo', 'aluno.codigo']);
        // dd($chamada->with('aluno')->distinct()->get(['feita_em', 'periodo']));
        
        $alunos->map(function ($item, $key) use ($chamadaDatas) {
            $diff = $chamadaDatas->pluck('feita_em')->diff($item->pluck('feita_em')->toArray())->all();

            if (!empty($diff)) {
                $diff = collect($diff);
                $diff->each(function ($date, $key) use ($item) {
                    $item->push(collect(["feita_em" => $date, 'codigo' => $item->pluck('aluno.codigo')->first()]));
                });

                $sorted = $item->sortBy(function ($date) {
                    return strtotime(!empty($date->feita_em) ? $date->feita_em : $date->get('feita_em'));
                }, SORT_REGULAR);
            }
        });

        $alunos = $alunos->map(function ($item) {
            return $item->sortBy(function ($date) {
                return strtotime(!empty($date->feita_em) ? $date->feita_em : $date->get('feita_em'));
            }, SORT_REGULAR);
        });

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

        // dd($pagination);

        $conteudo = Conteudo::find($latest->value('conteudo_id'));

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
