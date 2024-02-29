<?php

namespace App\Modules\Pagamentos\Http\Controllers;

use DateTime;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Layout\Content;
use App\Modules\Aluno\Models\Aluno;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Pagamentos\Models\Devendo;
use App\Modules\Pagamentos\Models\Parcelas;
use App\Modules\Pagamentos\Models\Pagamentos;
use Encore\Admin\Widgets\Form as WidgetsForm;

use Encore\Admin\Controllers\HasResourceActions;


class FinanceiroController extends Controller
{
    const JUROS = 0.00033; //valor juros cobrado de 0,033% ao dia
    const MULTA = 0.02;

    use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->title('Financeiro')
            ->description('Pagamentos')
            ->body($this->tab());
    }

    protected function tab()
    {
        $tab = new Tab();

        $tab->add('Cadastrar Parcelas', $this->formCadastrar());
        $tab->add('Consultar', $this->fromConsulta());

        return $tab->render();
    }

    public function formCadastrar()
    {
        // Cria um novo formulário
        $form = new WidgetsForm(new Parcelas());
        $form->action("financeiro/cadastrar");
        // Adiciona um campo select para escolher um aluno
        $form->select('aluno_id', 'Aluno')->options(function ($id) {
            $aluno = Aluno::find($id);
            if ($aluno) {
                return [$aluno->id => $aluno->nome];
            }
        })->ajax(route('sis.aluno.all'))->required();
        // Adiciona uma tabela de parcelas com campos quantidade e valor
        $form->table('Parcelas', "parcelas", function ($table) {
            $table->number('quantidade', 'Quantidade')->required()->min(0);
            $table->currency("valor", "Valor")->symbol('R$')->required();
        })->required();
        // Adiciona um campo de data para a primeira data de vencimento
        $form->date('vencimento', '1º data de vencimento')->format("DD/MM/YYYY")
            ->placeholder('Digite a data de vencimento da primeira parcela')->style("width", "100%")->required();
        // Adiciona um campo de rádio para escolher se é um acerto ou não
        $form->radio("tipo", "É um acerto?")->options([1 => 'Não', 0 => 'Sim'])->default(1)->required();
        // Cria uma caixa de informações com o formulário
        $box = new Box('Cadastrar parcelas', $form);
        $box->style('info');
        $box->solid();
        return $box; // Retorna a caixa de informações com o formulário
    }

    public function cadastroParcelas(Request $request) //executa quando o formulario de cadastrar parcelas é executado linha 27
    {
        if (!empty($request)) {
            $alunoId = $request->aluno_id; // Obtém o ID do aluno do objeto $request
            $parcelas = $request->Parcelas; // Obtém as parcelas do objeto $request
            $dataVenc = DateTime::createFromFormat("d/m/Y", $request->vencimento); // Cria um objeto DateTime a partir da data de vencimento no formato d/m/Y
            $dataVenc->format("Y-m-d"); // Formata a data de vencimento, mas o resultado não é armazenado
            $dataVenc->modify("-1 month"); // Subtrai um mês da data de vencimento, pois esta pegando um mês a mais após fazer a conversão
            $tipoParcela = $request->tipo; // Obtém o tipo de parcela (pagamento = 1 ou acerto = 0)
            $qtd = 0; // Inicializa a quantidade total de parcelas
            $i = 1; // Inicializa o contador de parcelas
            foreach ($parcelas as $parcela) {
                $qtd = $parcela['quantidade'] + $qtd; // Atualiza a quantidade total de parcelas
                $valor = $parcela['valor']; // Obtém o valor da parcela
                for ($i = $i; $i <= $qtd; $i++) {
                    $p = new Parcelas(); // Cria um novo objeto Parcelas para gravar no tabela do banco de dados parcelas
                    $p->num_parcela = $i; // Define o número da parcela
                    $p->valor = $valor; // Define o valor da parcela
                    $p->vencimento = $dataVenc; // Define a data de vencimento da parcela
                    $dataVenc->modify("+1 month"); // Adiciona um mês à data de vencimento para a próxima parcela
                    $p->aluno_id = $alunoId; // Define o ID do aluno para a parcela
                    $p->tipo = $tipoParcela; // Define o tipo da parcela (pagamento = 1 ou acerto = 0)
                    $p->save(); // Salva o objeto Parcelas no banco de dados
                }
            }
            admin_toastr('Parcelas cadastradas', 'success'); // Exibe uma mensagem de sucesso 
            return redirect('/financeiro/parcelas/aluno/' . $alunoId);
            // Retorna para a página anterior
        }
    }

    public function fromConsulta()
    {
        $form = new WidgetsForm();
        $form->action("financeiro/parcelas/aluno");
        $form->method('POST');
        $form->attribute([
            'pjax-container' => true
        ]);
        Admin::script("
        $('button[type=submit]').click(function (e) {
            var \$form = $(e.currentTarget.form);
            \$form.unbind('submit');
        })");
        // Adiciona um campo select para escolher um aluno
        $form->select('aluno_id', 'Aluno')->options(function ($id) {
            $aluno = Aluno::find($id);
            if ($aluno) {
                return [$aluno->id => $aluno->nome];
            }
        })->ajax(route('sis.aluno.all'))->required();
        // Cria uma caixa de informações com o formulário
        $box = new Box('Consultar parcelas', $form);
        $box->style('info');
        $box->solid();
        return $box;
    }

    public function listarParcelas(Content $content, Request $request)
    {
        $alunoId = $request->input('aluno_id') ?: $request->aluno;
        $aluno = Aluno::find($alunoId);
        return $content
            ->title('Parcelas aluno ' . $aluno->codigo . "/" . $aluno->nome)
            ->description('')
            ->body(view('pagamentos::parcelas', ["parcelas" => $this->consultarParcelas($alunoId)]));
    }

    public function consultarParcelas(int $alunoId)
    {
        $parcelas = Parcelas::where('aluno_id', $alunoId)->get();
        $rows = [];
        foreach ($parcelas as $parcela) {
            $rows[] = [
                "id" => $parcela->id,
                "tipo" => $parcela->tipo,
                "nParcela" => $parcela->num_parcela,
                "valor" => $parcela->valorParaBrl(),
                "dataVencimento" => $parcela->formatarDataVencimento(),
                "situacao" => $parcela->verificarSituacao(),
                "dataPagamento" => $parcela->verificarDataPagamento(),
                "valorPago" => $parcela->verificarValorPago(),
                "aluno_id" => $parcela->aluno_id
            ];
        }
        return $rows;
    }

    public function pagar(Content $content, Request $request)
    {
        $parcelaId = $request->input('parcelaId') ?: $request->parcelaId;

        return $content
            ->title('Pagar')
            ->description('')
            ->body(view('pagamentos::formPagarParcelas', ["parcelaId" => $parcelaId]));

        // ->body($this->fromPagar($parcelaId));
    }

    public function dadosPagamento(Request $request)
    {
        $detalhesPagamento = [];
        $parcela = Parcelas::find($request->id);

        if (!$parcela) {
            echo json_encode(["type" => "error", "message" => "Parcela não encontrada"]);
            return;
        }

        $alunoId = $parcela->aluno_id;
        $nParcela = $parcela->num_parcela;
        $valorOriginal = (float)$parcela->valor;
        $dataAtual = new DateTime();
        $dataVencimentoFormatada = $parcela->formatarDataVencimento();
        $dataVencimento = DateTime::createFromFormat('Y-m-d', $parcela->vencimento);
        $devendo = Devendo::where("aluno_id", $alunoId)->value("devendo") ?? 0;
        $diferenca = $dataAtual->diff($dataVencimento);
        $diasAtraso = $diferenca->days;
        if ($diasAtraso > 0 && $dataVencimento < $dataAtual) {
            $multa = self::MULTA * $valorOriginal;
            $juros = ((self::JUROS) * (int)$diasAtraso) * $valorOriginal;
            $total = $valorOriginal + $multa + $juros + $devendo;
            $percentualJuros = ($juros / $valorOriginal) * 100;
            } else {
            $multa = 0;
            $juros = 0;
            $total = $valorOriginal + $devendo;
            $percentualJuros = 0;
        }

        $detalhesPagamento = [
            "parcela" => $parcela,
            "alunoId" => $alunoId,
            "nParcela" => $nParcela,
            "valor" => $total,
            "dataAtual" => $dataAtual,
            "dataVencimentoFormatada" => $dataVencimentoFormatada,
            "dataVencimento" => $dataVencimento,
            "devendo" => $devendo,
            "multa" => [
                "type" => $dataVencimento < $dataAtual,
                "value" => $multa,
                "percentual" => "2%"
            ],
            "juros" => [
                "type" => $dataVencimento < $dataAtual,
                "value" => $juros,
                "percentual" => $percentualJuros
            ],
        ];

        echo json_encode(["type" => "success", "data" => $detalhesPagamento]);
        return;
    }

    public function confirmarPagamento(Request $request)
    {
        if (!empty($request)) {
            if (Auth::validate(['username' => $request->usuario, 'password' => $request->senha])) {
                if ($request->devendo > 0) {
                    $devendo = Devendo::where("aluno_id", $request->alunoId);
                    $valorDevendo = $devendo->value('devendo');
                    $idDevendo = $devendo->value("id");
                    $novoValorDevendo = $valorDevendo - $request->devendo;
                    $updateDevendo = Devendo::find($idDevendo);
                    $updateDevendo->devendo = $novoValorDevendo;
                    $updateDevendo->save();
                }
                $parcela = Parcelas::find($request->parcelaId);
                if($request->valorPago < $parcela->valor){
                    $devendo = Devendo::where("aluno_id", $request->alunoId);
                    $valorDevendo = $devendo->value('devendo');
                    $idDevendo = $devendo->value("id");
                    if($idDevendo){
                        $novoValorDevendo = $valorDevendo + ($parcela->valor-$request->valorPago);
                        $updateDevendo = Devendo::find($idDevendo);
                        $updateDevendo->devendo = $novoValorDevendo;
                        $updateDevendo->save();
                    }else{
                        $novoDevedor = new Devendo();
                        $novoDevedor->devendo = $parcela->valor-$request->valorPago;
                        $novoDevedor->aluno_id = $request->alunoId;
                        $novoDevedor->save();
                    }
                }
                $novoPagamento = new Pagamentos();
                $novoPagamento->data_pgto = $request->dataPagamento;
                $novoPagamento->valor = $request->valorPago;
                $novoPagamento->parcela_id = $request->parcelaId;
                $novoPagamento->save();
                admin_toastr('Pagamento Cadastrado', 'success');
                return redirect('/financeiro/parcelas/aluno/' . $request->alunoId);
            }
            admin_toastr('Usuario não encontrado', 'error');
            return redirect('/financeiro/parcelas/aluno/' . $request->alunoId);
        }
        admin_toastr('Não foi possivel Cadastrar o Pagamento', 'error');
        return redirect('/financeiro/parcelas/aluno/' . $request->alunoId);
    } 
}
