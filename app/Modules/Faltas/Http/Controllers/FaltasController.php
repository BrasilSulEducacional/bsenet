<?php

namespace App\Modules\Faltas\Http\Controllers;

use Encore\Admin\Grid;
use Illuminate\Http\Request;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Form;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Collapse;
use App\Modules\Turma\Models\Turma;
use App\Http\Controllers\Controller;
use App\Modules\Faltas\Models\Falta;
use App\Modules\Chamada\Models\Chamada;
use App\Modules\Faltas\Actions\Justificar;
use App\Modules\Faltas\Http\Requests\JustificarRequest;

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

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableView();
            $actions->disableDelete();

            $actions->add(new Justificar);
        });

        $grid->model()
            ->whereIn('turma_id', $turmas->pluck('id'))
            ->where('falta', 1)
            ->where('falta_justificada', 0)
            ->whereDoesntHave('justificativa');

        $grid->filter(function ($filter) {
            $filter->scope('falta_justificada', 'Buscar por faltas justificadas na chamada')
                ->where('falta_justificada', '!=', 0)
                ->orWhere('falta_justificada', 1)
                ->whereDoesntHave('justificativa');
        });

        $grid->column('aluno.nome', 'Aluno');

        $grid->column('conteudo.name', 'Conteudo');
        $grid->column('turma.turma', 'Turma')->badge('default');
        $grid->column('periodo')->badge();

        $grid->column('feita_em')->display(function ($date) {
            return date('d/m/Y', strtotime($date));
        });

        return $grid;
    }

    public function justificar(Request $request)
    {
        $validated = $request->validate([
            'observacao' => 'required|min:10'
        ]);
    }
}
