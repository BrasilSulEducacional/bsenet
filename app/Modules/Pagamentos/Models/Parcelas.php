<?php

namespace App\Modules\Pagamentos\Models;

use App\Modules\Aluno\Models\Aluno;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Parcelas extends Model
{
    protected $table = 'parcelas';

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }
    public function pagamentos()
    {
        return $this->belongsTo(Pagamentos::class);
    }

    public function valorParaBrl(): string
    {
        return "R$ " . number_format($this->valor, 2, ",", ".");
    }

    public function formatarDataVencimento(): string
    {
        $dateFormat = new DateTime($this->vencimento);
        return $dateFormat->format("d/m/Y");
    }

    public function verificarSituacao(): string
    {
        $dataAtual = (new DateTime("now", (new DateTimeZone("America/Sao_Paulo"))))->format("Y-m-d");
        $pagamento = Pagamentos::where('parcela_id', "=", $this->id)->get();
        $parcelaPaga = $pagamento->pluck("parcela_id");
        if (!empty($parcelaPaga[0])) {
            $s = "Paga";
        } else {
            if ($this->vencimento >= $dataAtual) {
                $s = "Em aberto";
            } else {
                $s = "Vencida";
            }
        }
        return $s;
    }

    public function verificarDataPagamento(): string
    {
        $dataPagamento = Pagamentos::where('parcela_id', $this->id)->value('data_pgto');
        if ($dataPagamento) {
            $formata = (new DateTime($dataPagamento))->format("d/m/Y");
            return $formata;
        }
        return "--";
    }

    public function verificarValorPago(): string
    {
        $valorPagamento = Pagamentos::where('parcela_id', $this->id)->value('valor');
        if ($valorPagamento) {
            return "R$ " . number_format($valorPagamento, 2, ",", ".");
        }
        return "--";
    }
}
