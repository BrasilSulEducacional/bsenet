<?php

namespace App\Modules\Pagamentos\Http\Controllers;

use Encore\Admin\Layout\Content;
use Encore\Admin\Grid;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Encore\Admin\Widgets\Table;
use App\Modules\Aluno\Models\Aluno;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Pagamentos\Models\Parcelas;
use App\Modules\Pagamentos\Models\Pagamentos;
use Encore\Admin\Controllers\HasResourceActions;

class ParcelasController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->title('Parcelas')
            ->description('Parcelas')
            ->body($this->grid());
    }

    public function grid()
    {
        $grid = new Grid(new Parcelas);
        $grid->quickSearch(function ($model, $query) {
            $model->where('id', $query)->orWhere('aluno_id', 'like', "%{$query}%");
        });
        $grid->model()->orderby('Id', 'asc');
        $grid->id('Id');
        $grid->column('num_parcela', "Nº Parcela");
        $grid->column('valor', "Valor")->editable();
        $grid->column('vencimento', "Data de vencimento")->date('Y')->filter('range', 'date');
        $grid->aluno()->nome('Aluno');
        $grid->column("Situacao")->display(function ($title, $column) {
            $pagamento = Parcelas::find($this->id);
            return $pagamento->verificarSituacao();
        })->expand(function (Model $model) {
            $pagamentos = Pagamentos::where("parcela_id", $this->id)->get()->map(function ($pagamento) {
                $edit = "<a title=\"Editar\" href=\"pagamentos/" . $pagamento->id . "/edit\"> <i class=\"fa fa-edit\"></i> </a>";
                return [
                    'id' => $pagamento->id,
                    'DataPagamento' => $pagamento->formatarDataPagamento(),
                    'ValorPago' => $pagamento->valor,
                    'edit' => $edit
                ];
            });
            return new Table(['ID', "Data Pagamento", "Valor Pago", "Ação"], $pagamentos->toArray());
        });
        $grid->column('tipo', 'Tipo')->replace([0 => 'Acerto', 1 => 'Parcela'])->filter([
            0 => 'Acerto',
            1 => 'Parcela',
        ]);
        return $grid;
    }
    public function show($id, Content $content)
    {
        return $content
            ->header('Parcela')
            ->description('detalhes das Parcela')
            ->body($this->detail($id));
    }

    public function edit(int $id, Content $content)
    {
        return $content
            ->header('Parcela')
            ->description('editar Parcela')
            ->body($this->form()->edit($id));
    }

    public function create(Content $content)
    {
        return $content
            ->title('Parcela')
            ->description('nova nota')
            ->body($this->form());
    }

    protected function detail($id)
    {
        $show = new Show(Parcelas::findOrFail($id));

        $show->id('ID');
        $show->num_parcela('Nº Parcela');
        $show->valor('Valor');
        $show->vencimento('Vencimento');
        $show->aluno_id('Aluno ID');
        $show->tipo('Tipo');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    protected function form()
    {
        $form = new Form(new Parcelas);

        $form->display("id");
        $form->text("num_parcela", "Nº Parcela")->required();
        $form->decimal('valor')->required();
        $form->date('vencimento')->format('YYYY-MM-DD')->required();
        $form->select('aluno_id', 'Aluno')->options(function ($id) {
            $aluno = Aluno::find($id);

            if ($aluno) {
                return [$aluno->id => $aluno->nome];
            }
        })->ajax(route('sis.aluno.all'))->required();

        $form->text('tipo')->required();


        return $form;
    }
    public function all(Request $request)
    {
        $q = $request->get('q');

        return Parcelas::where('id', 'like', "%$q%")->paginate(null, ['id', 'id as text']);
    }
}
