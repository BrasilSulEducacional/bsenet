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

class JustificarController extends Controller
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
            ->whereDoesntHave('justificativa')
            ->where('feita_em', ">=","2024-03-01") // filtar as faltas apartir desta data que começamos a utilzar
            ->orderBy('feita_em', 'desc');

        $grid->filter(function ($filter) use ($turmas) {
            $filter->disableIdFilter();

            $filter->scope('falta_justificada', 'Buscar por faltas justificadas na chamada')
                ->where('falta_justificada', '!=', 0)
                ->orWhere('falta_justificada', 1)
                ->whereDoesntHave('justificativa');

            $filter->equal('turma.turma', 'Turma')
                ->select($turmas->pluck('turma', 'turma'));

            $filter->equal('periodo', 'Período')
                ->radio([
                    '' => 'Todos',
                    1 => '1º Período',
                    2 => '2º Período',
                ]);
            $filter->date('feita_em');;
        });

        $grid->column('aluno.nome', 'Aluno');

        $grid->column('conteudo.name', 'Conteudo');
        $grid->column('turma.turma', 'Turma')->label();
        $grid->column('periodo')->display(function ($value) {
            $color = $value == 1 ? 'bg-dark' : 'bg-gray';
            return "<span class='badge {$color}'> {$value} </span>";
        });

        $grid->column('feita_em')->display(function ($date) {
            return date('d/m/Y', strtotime($date));
        })->filter("date");

        return $grid;
    }

    public function justificar(Request $request)
    {
        $validated = $request->validate([
            'observacao' => 'required|min:10'
        ]);
    }
}
