<?php

namespace App\Modules\Relatorios\Http\Controllers;

use PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Modules\Pagamentos\Models\Parcelas;
use App\Modules\Pagamentos\Models\Pagamentos;

class PagamentoController extends Controller
{
    public function comprovante(Request $request)
    {
        $parcelaId = $request->id;
        $tipo = $request->tipo;
        $pagamento = Pagamentos::where("parcela_id","=",$parcelaId)->first();
        if(!empty($pagamento)){
            $nParcelaFinal = Parcelas::where("aluno_id","=",$pagamento->parcela->aluno_id)->where("tipo", "=", $tipo)->max("num_parcela");
            // return view('relatorios::recibo', compact('pagamento', "nParcelaFinal"));
            $pdf = PDF::loadView('relatorios::recibo', compact('pagamento', "nParcelaFinal"));

            $filename = uniqid() . ".pdf";
            $path = Storage::disk('public')->getAdapter()->getPathPrefix();

            Storage::disk('public')->put($filename, $pdf->output());

            return response()->download($path . $filename)->deleteFileAfterSend();
        }
    }
}
