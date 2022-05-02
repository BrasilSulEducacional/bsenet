<?php

namespace App\Modules\Conteudo\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Conteudo\Models\Conteudo;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Encore\Admin\Grid;
use Illuminate\Http\Request;
use Encore\Admin\Show;

class ConteudoController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->title('Conteúdos')
            ->description('Conteúdos')
            ->body($this->grid());
    }

    protected function grid()
    {
        $grid = new Grid(new Conteudo);

        $grid->column('slug', 'Apelido');
        $grid->column('name', 'Nome');

        return $grid;
    }

    public function show($id, Content $content)
    {
        return $content
            ->header('Conteúdo')
            ->description('detalhes do conteúdo')
            ->body($this->detail($id));
    }

    public function edit(int $id, Content $content)
    {
        return $content
            ->header('Conteúdo')
            ->description('editar conteúdo')
            ->body($this->form()->edit($id));
    }

    public function create(Content $content)
    {
        return $content
            ->title('Conteúdo')
            ->description('novo conteúdo')
            ->body($this->form());
    }

    protected function detail($id)
    {
        $show = new Show(Conteudo::findOrFail($id));

        $show->id('ID');
        $show->slug('Apelido');
        $show->name('Nome');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    protected function form()
    {
        $form = new Form(new Conteudo);

        $form->display("ID");

        $form->text('slug', 'Aplido')
            ->placeholder('Digite o apelido do conteúdo, ex: SO, C&M ...');

        $form->text('name', 'Nome')
            ->placeholder('Digite o nome do conteúdo, ex: Sistema Operacional, Comunicação e Marketing');

        $form->display('created_at');
        $form->display('updated_at');

        return $form;
    }

    public function all(Request $request)
    {
        $q = $request->get('q');

        return Conteudo::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
    }
}
