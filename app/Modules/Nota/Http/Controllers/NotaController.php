<?php

namespace App\Modules\Nota\Http\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Layout\Content;
use App\Modules\Nota\Models\Nota;
use App\Modules\Aluno\Models\Aluno;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Conteudo\Models\Conteudo;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Widgets\Box;

class NotaController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->title('Notas')
            ->description('Notas')
            ->body($this->grid());
    }

    protected function grid()
    {
        $grid = new Grid(new Aluno);

        $grid->model()->has('notas');

        $grid->quickSearch(function ($model, $query) {
            $model->where('nome', 'like', "%{$query}%")->orWhere('codigo', 'like', "%{$query}%");
        });

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
        });

        // $grid->model()->notas();

        $grid->column('codigo');

        $grid->column('nome', 'Aluno')->expand(function (Model $model) {
            $notas = $model->notas->map(function ($nota) {
                $edit = "<a title=\"Editar\" href=\"controle/" . $nota->id . "/edit\"> <i class=\"fa fa-edit\"></i> </a>";
                $ch = round(1.5 * $nota->aulas);
                $presenca = $nota->aulas - $nota->faltas;
                $freq = round(((100 * $presenca) / $nota->aulas));
                return [
                    'conteudo' => $nota->conteudo->name, // Nome do conteúdo
                    'nota' => $nota->nota,               // Nota do conteúdo
                    'Aulas' => $nota->aulas,             // Quantidade de aulas
                    'faltas' => $nota->faltas,           // Quantidade de faltas
                    'ch' => $ch . " horas",              // Quandiade carga horária
                    'Frequencia' => $freq . "%",         // Frequência da quele conteudo
                    'edit' => $edit
                ];
            });

            $table = new Table(['Conteúdo', 'Nota', 'Aulas', 'Faltas', 'CH', 'Frequência', 'Ação'], $notas->toArray(), ['table-striped', 'table-hover']);

            $footer = "<a href=\"" . url("/relatorios/boletim/report/aluno", ['aluno_id' => $model->id]) . "\" target=\"_blank\"> Imprimir Boletim </a> | <a href=\"" . url("/relatorios/boletim/export/aluno", ['aluno_id' => $model->id]) . "\" target=\"_blank\"> Exportar CSV </a>";

            $box = new Box('Notas', $table->render(), $footer);
            $box->addTools("<a href='#'> boletim </a>");
            return $box;
        });

        $grid->column('turma.turma', 'Turma');

        // $grid->column('conteudo_id', 'Conteúdo')->display(function ($conteudoId) {
        //     return Conteudo::find($conteudoId)->name;
        // });

        // $grid->column('nota', 'Nota');
        // $grid->column('faltas', 'Faltas');
        // $grid->column('aulas', 'Aulas dadas');

        // $grid->quickCreate(function (Grid\Tools\QuickCreate $create) {
        //     $create->select('aluno_id', 'Aluno')->options(function ($id) {
        //         $aluno = Aluno::find($id);

        //         if ($aluno) {
        //             return [$aluno->id => $aluno->nome];
        //         }
        //     })->ajax(route('sis.aluno.all'))->required();

        //     $create->select('conteudo_id', 'Conteúdo')->options(function ($id) {
        //         $conteudo = Conteudo::find($id);

        //         if ($conteudo) {
        //             return [$conteudo->id => $conteudo->name];
        //         }
        //     })->ajax(route('sis.conteudo.all'))->required();

        //     $create->integer('nota', 'Notas');
        //     $create->integer('faltas');
        //     $create->integer('aulas', 'Aulas dadas');
        // });

        return $grid;
    }

    public function show($id, Content $content)
    {
        return $content
            ->header('Nota')
            ->description('detalhes das nota')
            ->body($this->detail($id));
    }

    public function edit(int $id, Content $content)
    {
        return $content
            ->header('Nota')
            ->description('editar nota')
            ->body($this->form()->edit($id));
    }

    public function create(Content $content)
    {
        return $content
            ->title('Nota')
            ->description('nova nota')
            ->body($this->form());
    }

    protected function detail($id)
    {
        $show = new Show(Nota::findOrFail($id));

        $show->id('ID');
        $show->nota('Nota');
        $show->faltas('Faltas');
        $show->aulas('Aulas dadas');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    protected function form()
    {
        $form = new Form(new Nota);

        $form->display("id");

        $form->select('aluno_id', 'Aluno')->options(function ($id) {
            $aluno = Aluno::find($id);

            if ($aluno) {
                return [$aluno->id => $aluno->nome];
            }
        })->ajax(route('sis.aluno.all'))->required();

        $form->select('conteudo_id', 'Conteúdo')->options(function ($id) {
            $conteudo = Conteudo::find($id);

            if ($conteudo) {
                return [$conteudo->id => $conteudo->name];
            }
        })->ajax(route('sis.conteudo.all'))->required();

        $form->decimal('nota')->required();
        $form->decimal('faltas')->required();
        $form->decimal('aulas', 'Aulas dadas')->required();

        $form->display('created_at');
        $form->display('updated_at');

        return $form;
    }

    public function all(Request $request)
    {
        // $q = $request->get('q');

        // return Turma::where('turma', 'like', "%$q%")->paginate(null, ['id', 'turma as text']);
    }
}
