<?php

namespace App\Modules\Relatorios\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Aluno\Models\Aluno;
use App\Modules\Nota\Models\Nota;
use App\Modules\Turma\Models\Turma;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Widgets\Form;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Tab;
use Illuminate\Support\Facades\Storage;
use PDF;

class BoletimController extends Controller
{
    // use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->title('Relatório')
            ->description('Boletins')
            ->body($this->tab());
    }

    protected function tab()
    {
        $tab = new Tab();

        $tab->add('Boletim individual', $this->formIndividual());
        $tab->add('Boletim da Turma', $this->formClassroom());

        return $tab->render();
    }

    public function report(Request $request)
    {
        if ($request->type == "aluno") {
            $alunoId = $request->input('aluno_id') ?: $request->aluno;
            $aluno = Aluno::find($alunoId);
            $notas = Nota::where('aluno_id', $alunoId)->get();
            $somaNotas = Nota::where('aluno_id', $alunoId)->sum('nota');
            $countNotas = Nota::where('aluno_id', $alunoId)->count();
            $totalFaltas = Nota::where('aluno_id', $alunoId)->sum('faltas');

            $mediaGeral = $somaNotas / $countNotas;
            $headersColor = $request->input('boletim_headers_color') ?: "#0275c2";
            $pdf = PDF::loadView('boletim', compact('aluno', 'notas', 'mediaGeral', 'totalFaltas', 'headersColor'));

            $filename = uniqid() . ".pdf";
            $path = Storage::disk('public')->getAdapter()->getPathPrefix();

            Storage::disk('public')->put($filename, $pdf->output());

            return response()->download($path . $filename)->deleteFileAfterSend();
        }

        $turma = Turma::find($request->input('turma_id'));
        $alunos = $turma->alunos;

        $collection = collect([]);

        foreach ($alunos as $aluno) {
            $notas = Nota::where('aluno_id', $aluno->id)->get();
            $somaNotas = Nota::where('aluno_id', $aluno->id)->sum('nota');
            $countNotas = Nota::where('aluno_id', $aluno->id)->count();
            $totalFaltas = Nota::where('aluno_id', $aluno->id)->sum('faltas');
            $mediaGeral = $somaNotas / $countNotas;

            $headersColor = $request->input('boletim_headers_color');

            $collection->push(compact('aluno', 'notas', 'mediaGeral', 'totalFaltas', 'headersColor'));
        }

        $pdf = PDF::loadView('boletim_turmas', ['alunos' => $collection->toArray()]);
        $filename = uniqid() . ".pdf";
        $path = Storage::disk('public')->getAdapter()->getPathPrefix();

        Storage::disk('public')->put($filename, $pdf->output());

        return response()->download($path . $filename)->deleteFileAfterSend();
    }

    protected function formIndividual()
    {
        $form = new Form();

        $form->attribute([
            'target' => '_blank',
            'pjax-container'
        ]);

        $form->action('boletim/report/aluno');
        // $form->method('GET');

        $form->disablePjax();

        Admin::script("
        $('button[type=submit]').click(function (e) {
            var \$form = $(e.currentTarget.form);
            \$form.unbind('submit');
        })");


        $form->select('aluno_id', 'Aluno')->options(function ($id) {
            $aluno = Aluno::find($id);

            if ($aluno) {
                return [$aluno->id => $aluno->nome];
            }
        })->ajax(route('sis.aluno.all'))->required();

        $form->color('boletim_headers_color', 'Cores da linha do boletim')->default('#0275c2');

        $box = new Box('Selecione o aluno para gerar o boletim', $form);
        $box->style('info');
        $box->solid();

        return $box;
    }

    protected function formClassroom()
    {
        $form = new Form();

        $form->attribute([
            'target' => '_blank',
            'pjax-container'
        ]);

        $form->action('boletim/report/turma');

        $form->disablePjax();

        Admin::script("
        $('button[type=submit]').click(function (e) {
            var \$form = $(e.currentTarget.form);
            \$form.unbind('submit');
        })");


        $form->select('turma_id', 'Turma')->options(function ($id) {
            $turma = Turma::find($id);

            if ($turma) {
                return [$turma->id => $turma->turma];
            }
        })->ajax(route('sis.turma.all'))->required();

        $form->color('boletim_headers_color', 'Cores da linha do boletim')->default('#0275c2');

        $box = new Box('Selecione uma turma para gerar o boletim', $form);
        $box->style('info');
        $box->solid();

        return $box;
    }

    public function export(Request $request)
    {
        if (!empty($request)) {
            $array_content = [];
            $idAlunos = $request["id"];
            $notas = Nota::where('aluno_id', $idAlunos)->get();

            $array_header = array(
                'Conteudo',
                'Carga Horaria',
                'Frequencia',
                'Notas',
                "CHTotal"

            );
            $chTotal = 0;
            foreach ($notas as $nota) {
                $conteudo = $nota->conteudo->name;
                $ch = round(1.5 * $nota->aulas);
                $chTotal += $ch;
                $presenca = $nota->aulas - $nota->faltas;
                $freq = round(((100 * $presenca) / $nota->aulas));
                $notaConteudo = $nota->nota;

                $array_content[] = [
                    'col_1' => $conteudo,
                    'col_2' => $ch . " Horas",
                    'col_3' => $freq . " %",
                    'col_4' => $notaConteudo,
                    'col_5' => $chTotal

                ];
            }
            $generate = new FileCSV();
            $generate->setHeader($array_header);
            $generate->setContent($array_content);
            $generate->generateAndDownloadFileCSV();
            return;
        }
    }
}
