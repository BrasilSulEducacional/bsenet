<?php

namespace App\Modules\Faltas\Http\Controllers;

use Illuminate\Http\Request;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Form;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Modules\Aluno\Models\Aluno;
use App\Http\Controllers\Controller;
use App\Modules\Chamada\Models\Chamada;
use App\Modules\Faltas\Models\Falta;
use App\Modules\Turma\Models\Turma;

class RelatorioController extends Controller
{
    public function index(Content $content)
    {
        return $content->title('Relatório de faltas')
            ->description('Gerar relatório de faltas')
            ->body($this->tab());
    }

    public function tab()
    {
        $tab = new Tab;

        $tab->add('Justificativa individual', $this->formJustificativaIndividual());
        $tab->add('Justificativas da turma', $this->formJustificativaTurma());

        return $tab;
    }

    private function formJustificativaIndividual()
    {
        $form = new Form;
        $form->select('aluno_id', 'Aluno')->options(function ($id) {
            $aluno = Aluno::find($id);

            if ($aluno) {
                return [$aluno->id => $aluno->nome];
            }
        })->ajax(route('sis.aluno.all'))->required();

        $form->multipleSelect('periodo')->options([
            '1' => '1º Período',
            '2' => '2º Período',
        ])->required();

        return $form;
    }

    private function formJustificativaTurma()
    {
        $form = new Form();
        $form->select('turma_id', 'Turma')->options(function ($id) {
            $turma = Turma::find($id);

            if ($turma) {
                return [$turma->id => $turma->turma];
            }
        })->ajax(route('sis.turma.all'))->required();
        return $form;
    }
}
