<?php

namespace App\Modules\Pagamentos\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Aluno\Models\Aluno;
use App\Modules\Pagamentos\Models\Parcelas;
use App\Modules\Turma\Models\Turma;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Widgets\Form;
use Encore\Admin\Layout\Content;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Model;
use Illuminate\Http\Request;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Tab;
use Illuminate\Support\Facades\Storage;
use Encore\Admin\Widgets\Collapse;
use Encore\Admin\Widgets\Table;


use PDF;

class PagamentosController extends Controller
{
    // use HasResourceActions;

    public function index(Content $content)
    {


        return $content
            ->title('Pagamentos')
            ->description('Pagamentos')
            ->body($this->grid());
    }

    public function grid()
    {
        $grid = new Grid(new Aluno);

        $grid->quickSearch(function ($model, $query) {
            $model->where('nome', 'like', "%{$query}%")->orWhere('codigo', 'like', "%{$query}%");
        });

        return $grid;
    }
}
