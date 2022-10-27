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
use Encore\Admin\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlunoController extends Controller
{
    use HasResourceActions;


    protected $situacao = [
        0 => 'Trancado',
        1 => 'Cursando',
        2 => 'Finalizado'
    ];

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

        $grid->quickSearch(function ($model, $query) {
            $model->where('nome', 'like', "%{$query}%")->orWhere('codigo', 'like', "%{$query}%");
        });

        $grid->model()->orderby('codigo', 'desc');

        $turmas = Turma::all()->pluck('turma', 'id')->toArray();


        $grid->id('ID')->sortable()->hide();
        $grid->column('codigo')->sortable();
        $grid->column('nome')->editable()->sortable();
        $grid->column('data_nasc')->date('Y')->sortable();
        $grid->column('turma_id', 'Turma')->editable('select', $turmas);
        $grid->column('status', 'Situação')->editable('select', $this->situacao);

        $grid->filter(function ($filter) {
            $filter->where(function ($query) {
                $query->whereHas('turma', function ($query) {
                    $query->where('turma', 'like', "%{$this->input}%");
                });
            }, 'Turma');
        });


        $grid->quickCreate(function (Grid\Tools\QuickCreate $create) {
            $create->integer('codigo', 'Codigo')->required();
            $create->text('nome', 'Nome')->required();
            $create->date('data_nasc', 'Data de nascimento')->required();

            $create->select('turma_id', 'Turma')->options(function ($id) {
                $turma = Turma::find($id);

                if ($turma) {
                    return [$turma->id => $turma->turma];
                }
            })->ajax(route('sis.turma.all'));
        });


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

    public function create(Request $request, Content $content)
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
        $proxCod = DB::table("alunos")->select("codigo")->orderBy("codigo", "desc")->first();
        $proxCod = $proxCod->codigo + 1;


        $form = new Form(new Aluno);


        $form->display('id');



        $form->number('codigo', 'Código')->min($proxCod)->value($proxCod)->help("Proximo Código: " . $proxCod);

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

        $form->display('status', 'Situação')->options($this->situacao)->default(1);

        $form->display('created_at');
        $form->display('updated_at');

        return $form;
    }

    public function all(Request $request)
    {
        if ($q = $request->get('q')) {
            return Aluno::where('nome', 'like', "%$q%")->paginate(null, ['id', 'nome as text']);
        }

        return Aluno::all()->take(10)->paginate(null, ['id', 'nome as text']);
    }
}
