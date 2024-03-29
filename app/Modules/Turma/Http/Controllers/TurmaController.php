<?php

namespace App\Modules\Turma\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Turma\Models\Turma;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Encore\Admin\Grid;
use Illuminate\Http\Request;
use Encore\Admin\Show;

class TurmaController extends Controller
{
    use HasResourceActions;

    protected $dias = [
        '----'              => 'none',
        'Segundas-feiras'   => 'Segunda',
        'Terças-feiras'     => 'Terça',
        'Quartas-feiras'    => 'Quarta',
        'Quintas-feiras'    => 'Quinta',
        'Sextas-feiras'     => 'Sexta',
        'Sábado'            => 'Sábado',
    ];

    public function index(Content $content)
    {
        return $content
            ->title('Turmas')
            ->description('Turmas')
            ->body($this->grid());
    }

    protected function grid()
    {
        $grid = new Grid(new Turma);

        $professores = Administrator::all()->pluck('name', 'id')->toArray();

        $grid->id("ID");
        $grid->column("turma");
        $grid->column("horario")->editable();
        $grid->column("dias")->editable('select', $this->dias);
        $grid->column("professor_id", "Professor")->editable('select', $professores);
        $grid->column('alunos')->display(function ($teste) {
            return count($teste);
        })->label();

        return $grid;
    }

    public function show($id, Content $content)
    {
        return $content
            ->header('Turma')
            ->description('detalhes da turma')
            ->body($this->detail($id));
    }

    public function edit(int $id, Content $content)
    {
        return $content
            ->header('Turma')
            ->description('editar turma')
            ->body($this->form()->edit($id));
    }

    public function create(Content $content)
    {
        return $content
            ->title('Turma')
            ->description('nova turma')
            ->body($this->form());
    }



    protected function detail($id)
    {
        $show = new Show(Turma::findOrFail($id));

        $show->id('ID');
        $show->turma('Turma');
        $show->horario('Horário');
        $show->dias('dias');

        $show->professor('Professor da Turma', function ($prof) {
            $prof->setResource('/auth/users');

            $prof->id();
            $prof->name();
            $prof->avatar()->image();
        });

        $show->alunos("Alunos da Turma", function ($alunos) {

            $alunos->setResource('/aluno/controle');

            $alunos->id()->hide();
            $alunos->codigo();
            $alunos->nome()->editable()->sortable();
            $alunos->data_nasc("Data de Nascimento")->date('Y')->sortable();
        });
        return $show;
    }

    protected function form()
    {
        $form = new Form(new Turma);

        $form->display("ID");

        $form->text('turma', 'Turma')
            ->placeholder('Digite o número da turma');

        $form->text('horario', 'Horário')
            ->placeholder('Digite o horário da turma')
            ->inputmask(['mask' => '99\h99 \a\s 99\h99']);

        $form->select('dias', 'Dias')
            ->options($this->dias)
            ->placeholder('Selecione o dia');

        $form->select('professor_id', 'Professor')->options('/all/professores');

        $form->display('created_at');
        $form->display('updated_at');

        return $form;
    }

    public function all(Request $request)
    {
        $q = $request->get('q');

        return Turma::where('turma', 'like', "%$q%")->paginate(null, ['id', 'turma as text']);
    }
}
