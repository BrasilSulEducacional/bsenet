<?php

namespace App\Modules\Faltas\Http\Controllers;

use App\Modules\Chamada\Models\Chamada;
use App\Http\Controllers\Controller;
use App\Modules\Faltas\Http\Requests\JustificarRequest;
use App\Modules\Turma\Models\Turma;
use Encore\Admin\Widgets\Collapse;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Grid;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;

class FaltasController extends Controller
{
    public function index(Content $content)
    {
        return $content->title('Faltas por turma')
            ->description('Realizar um relatório de faltas')
            ->body($this->grid());
    }

    public function grid()
    {
        $grid = new Grid(new Chamada);
        $turmas = Turma::where('professor_id', auth()->user()->id)->get();

        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableActions();

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        $grid->model()
            ->whereIn('turma_id', $turmas->pluck('id'))
            ->where('falta', 1)
            ->where('falta_justificada', 0);

        $grid->column('aluno.nome', 'Aluno');

        $grid->column('conteudo.name', 'Conteudo');
        $grid->column('turma.turma', 'Turma')->badge('default');

        $grid->column('feita_em')->display(function ($date) {
            return date('d/m/Y', strtotime($date));
        });

        $grid->column('Ação')->display(function () {
            return 'Justificar';
        })->modal('Adicionar observação...', function ($chamada) {
            $form =  new Form();

            $form->action(route('justificar.chamada', $chamada->id));
            $form->method('POST');
            $form->attribute('pjax-container');
            $form->textarea('observacao', 'Observação');

            return $form;
        });

        return $grid;
    }

    public function justificar(Request $request)
    {
        $chamadaId = $request->chamadaId;
    }
}
