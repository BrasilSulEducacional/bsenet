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

class PagamentosController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->title('Pagamentos')
            ->description('Detalhes Pagamentos')
            ->body($this->grid());
    }

    public function grid()
    {
        $grid = new Grid(new Pagamentos);
        $grid->model()->orderby('Id', 'asc');
        $grid->id('Id');
        $grid->column('data_pgto', "Data de Pagamento")->date('Y')->filter('range', 'date');
        $grid->column('valor', "Valor Pago");
        $grid->column('parcela_id', "parcela_id");
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
        $show = new Show(Pagamentos::findOrFail($id));

        $show->id('ID');
        $show->data_pgto('Data Pagamento');
        $show->valor('Valor Pago');
        $show->parcela_id('Parcela id');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    protected function form()
    {
        $form = new Form(new Pagamentos);

        $form->display("id");
        $form->date('data_pgto')->format('YYYY-MM-DD')->required();
        $form->decimal('valor')->required();
        return $form;
    }
   
}
