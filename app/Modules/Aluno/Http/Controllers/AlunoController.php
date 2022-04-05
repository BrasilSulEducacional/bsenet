<?php

namespace App\Modules\Aluno\Http\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;

use App\Modules\Aluno\Models\Aluno;
use App\Modules\Turma\Models\Turma;

use Encore\Admin\Layout\Content;
use Encore\Admin\Grid;
use Encore\Admin\Form;
use Encore\Admin\Show;

class AlunoController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->title('Alunos')
            ->description('controle')
            ->body($this->grid());
    }

    protected function grid()
    {
        $grid = new Grid(new Aluno);

        $grid->quickSearch();

        $grid->id('ID');
        $grid->column('codigo');
        $grid->column('nome');
        $grid->column('data_nasc')->date('Y');
        $grid->column('turma_id', 'Turma')->display(function ($turmaId) {
            return Turma::find($turmaId)->turma;
        });

        $grid->disableRowSelector();

        return $grid;
    }

    public function show($id, Content $content)
    {
        return $content
            ->header('Aluno')
            ->description('detalhes do aluno')
            ->body($this->detail($id));
    }

    public function edit(int $id, Content $content)
    {
        return $content
            ->header('Aluno')
            ->description('editar aluno')
            ->body($this->form()->edit($id));
    }

    public function create(Content $content)
    {
        return $content
            ->title('Aluno')
            ->description('novo aluno')
            ->body($this->form());
    }

    protected function detail($id)
    {
        $show = new Show(Aluno::findOrFail($id));

        $show->id('ID');
        $show->codigo('Codigo');
        $show->nome('Nome');
        $show->data_nasc('Data de nascimento');
        $show->turma_id('Turma');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    protected function form()
    {
        $form = new Form(new Aluno);

        $form->display('id');
        $form->number('codigo', 'Código');

        $form->text('nome')
            ->placeholder('Digite o nome do aluno');

        $form->date('data_nasc', 'Data de Nascimento')
            ->placeholder('Digite a data de nascimento do aluno');

        $form->select('turma_id', 'Turma')->options(function ($id) use ($form) {
            $turma = Turma::find($id);

            if ($turma) {
                return [$turma->id => $turma->turma];
            }
        })->ajax(route('sis.turma.all'));

        $form->display('created_at');
        $form->display('updated_at');

        return $form;
    }
}
