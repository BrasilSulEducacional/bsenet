<?php

namespace App\Modules\Aluno\Http\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Grid;

class NovoAlunoController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('Dashboard')
            ->description('Informações úteis internas...')
            ->body($this->grid());
    }

    protected function grid()
    {
        $grid = new Grid(new SisUnidadesModel);

        $grid->cod_unidade('Codigo');
        $grid->des_unidade('Descrição');
        $grid->des_fantasia('Abreviação');
        $grid->ind_replicacao('Replicação?')->bool(['1' => true, '0' => false]);
        $grid->num_cnpj('CNPJ');

        return $grid;
    }
}
