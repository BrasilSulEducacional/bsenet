<?php

namespace App\Modules\Faltas\Actions;

use App\Modules\Faltas\Models\Falta;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Justificar extends RowAction
{
    public $name = 'Justificar Falta';

    public function handle(Model $model, Request $request)
    {
        $falta = new Falta;
        $falta->observacao = $request->get('observacao');
        $falta->chamada_id = $request->get('_key');
        $falta->save();

        return $this->response()->success('Success message.')->refresh();
    }

    public function form()
    {
        $periodos = [
            1 => '1 Período',
            2 => '2 Período',
        ];

        $this->display('id');
        $this->checkbox('periodo')->options($periodos);
        $this->textarea('observacao', 'Observação')->required()->rules('required|min:20');
    }
}
