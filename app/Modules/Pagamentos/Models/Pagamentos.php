<?php

namespace App\Modules\Pagamentos\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Pagamentos\Models\Parcelas;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pagamentos extends Model
{
    use HasFactory;

    protected $table = 'pagamentos';

    public function formatarDataPagamento(): string
    {
        $dateFormat = new DateTime($this->data_pgto);
        return $dateFormat->format("d/m/Y");
    }

    public function valorParaBrl(): string
    {
        return "R$ " . number_format($this->valor, 2, ",", ".");
    }
    
    public function parcela()
    {
        return $this->belongsTo(Parcelas::class);
    }
}
