<?php

namespace App\Modules\Nota\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Aluno\Models\Aluno;
use App\Modules\Conteudo\Models\Conteudo;
use App\Modules\Nota\Models\Nota;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Encore\Admin\Grid;
use Illuminate\Http\Request;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;

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
        $grid = new Grid(new Nota);

        $grid->quickSearch();

        $grid->column('aluno_id', 'Aluno')->display(function ($alunoId) {
            return Aluno::find($alunoId)->nome;
        });

        $grid->column('conteudo_id', 'Conteúdo')->display(function ($conteudoId) {
            return Conteudo::find($conteudoId)->name;
        });

        $grid->column('nota', 'Nota');
        $grid->column('faltas', 'Faltas');
        $grid->column('aulas', 'Aulas dadas');

        $grid->quickCreate(function (Grid\Tools\QuickCreate $create) {
            $create->select('aluno_id', 'Aluno')->options(function ($id) {
                $aluno = Aluno::find($id);

                if ($aluno) {
                    return [$aluno->id => $aluno->nome];
                }
            })->ajax(route('sis.aluno.all'))->required();

            $create->select('conteudo_id', 'Conteúdo')->options(function ($id) {
                $conteudo = Conteudo::find($id);

                if ($conteudo) {
                    return [$conteudo->id => $conteudo->name];
                }
            })->ajax(route('sis.conteudo.all'))->required();

            $create->integer('nota');
            $create->integer('faltas');
            $create->integer('aulas', 'Aulas dadas');
        });

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

        // $form->setWidth(2);

        $form->display("id");

        $form->select('aluno_id', 'Aluno')->options(function ($id) {
            $aluno = Aluno::find($id);

            if ($aluno) {
                return [$aluno->id => $aluno->nome];
            }
        })->ajax(route('sis.aluno.all'));

        $form->select('conteudo_id', 'Conteúdo')->options(function ($id) {
            $conteudo = Conteudo::find($id);

            if ($conteudo) {
                return [$conteudo->id => $conteudo->name];
            }
        })->ajax(route('sis.conteudo.all'));

        $form->decimal('nota');
        $form->decimal('faltas');
        $form->decimal('aulas', 'Aulas dadas');

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
