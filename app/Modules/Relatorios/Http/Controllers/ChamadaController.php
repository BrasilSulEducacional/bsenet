<?php

namespace App\Modules\Relatorios\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Turma\Models\Turma;
use Illuminate\Http\Request;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Form;
use Illuminate\Support\Facades\Storage;
use PDF;

class ChamadaController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('Relatório')
            ->description('Chamada')
            ->body($this->form());
    }

    public function report(Request $request)
    {
        $turma = Turma::find($request->input('turma_id'));
        $alunos = $turma->alunos;
        $qtdDatas = range(1, 22);

        $pdf = PDF::loadView('chamada', compact('turma', 'alunos', 'qtdDatas'));

        $filename = uniqid() . ".pdf";
        $path = Storage::disk('public')->getAdapter()->getPathPrefix();

        Storage::disk('public')->put($filename, $pdf->output());

        return response()->download($path . $filename)->deleteFileAfterSend();
    }

    protected function form()
    {
        $form = new Form();

        $form->attribute([
            'target' => '_blank',
            'pjax-container'
        ]);

        $form->action('chamada/report');

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

        $box = new Box('Gerar chamada', $form);
        $box->style('info');
        $box->solid();

        return $box;
    }
}
