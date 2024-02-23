<style>
    th,
    .table>tbody>tr>td {
        vertical-align: middle;
        text-align: center;
        font-size: 1em;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="box box-success box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">Parcelas</h3>
                @foreach ($parcelas as $parcela)
                @if($parcela['tipo'] == 0)
                <h3 class="box-title"></h3>
                <a href="#boxAcerto"> <button class="btn btn-danger">Aluno Pussui Acerto</button></a>
                @break
                @endif
                @endforeach
                <div class="box-tools pull-right">

                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div><!-- /.box-tools -->
            </div><!-- /.box-header -->
            <div class="box-body" style="display: block;">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nº Parcela</th>
                            <th>Valor</th>
                            <th>Vencimento</th>
                            <th>Situação</th>
                            <th>Data do Pagamento</th>
                            <th>Valor Pago</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($parcelas))

                        <tr>
                            <td colspan=7>Não tem parcelas cadastradas</td>
                        </tr>
                        @else
                        @foreach ($parcelas as $parcela)
                        <?php $alunoId = $parcela["aluno_id"]; ?>
                        <?php if ($parcela["tipo"] == 1) :
                            if ($parcela["situacao"] == "Vencida") {
                                $colorRow = 'red';
                            } elseif ($parcela["situacao"] == "Paga") {
                                $colorRow = '#ddd';
                            } else {
                                $colorRow = 'trasparent';
                            }
                            $parcelaId = $parcela['id'];
                        ?>
                            <tr style="background-color: <?= $colorRow ?>;">
                                <td>{{str_pad($parcela["nParcela"] , 2 , '0' , STR_PAD_LEFT)}}</td>
                                <td>{{$parcela["valor"]}}</td>
                                <td>{{$parcela["dataVencimento"]}}</td>
                                <td>{{$parcela["situacao"]}}</td>
                                <td>{{$parcela["dataPagamento"]}}</td>
                                <td>{{$parcela["valorPago"]}}</td>
                                <td>
                                    <a href='/financeiro/controle/parcelas?&__search__=<?= $parcelaId ?>'>
                                        <button type=" button" class="btn btn-primary" title="Edita Parcela Nº {{$parcela['nParcela']}}">Editar</button>
                                    </a>
                                    <a href="{{ route('financeiro.pagar.parcela', ['type'=>'parcela','parcelaId'=>$parcela['id']]) }}">
                                        <button type="button" class="btn btn-success" title="Pagar Parcela Nº {{$parcela['nParcela']}}" <?php if ($parcela["situacao"] == "Paga") : ?>disabled<?php endif ?>>Pagar</button>
                                    </a>
                                    <a href="/financeiro/controle/parcelas?&__search__=<?= $parcelaId ?>">
                                        <button type="button" class="btn btn-danger" title="Pagar Parcela Nº {{$parcela['nParcela']}}" <?php if ($parcela["situacao"] != "Paga") : ?>disabled<?php endif ?>>Cancelar Pagamento</button>
                                    </a>
                                    <a href="{{ route('relatorios.comprovante.pagamento', ['id'=>$parcela['id'], 'tipo'=>1]) }}" target="_blank">
                                        <button type="button" class="btn btn-warning" title="Pagar Parcela Nº {{$parcela['nParcela']}}" <?php if ($parcela["situacao"] != "Paga") : ?>disabled<?php endif ?>>Recibo</button>
                                    </a>
                                </td>
                            </tr>
                        <?php endif ?>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div><!-- /.box-body -->
            <div class="box-footer">
                <a href='/financeiro/controle/parcelas?&__search__=<?= $alunoId ?>'>Editar Parcelas</a>
            </div><!-- /.box-footer-->
        </div>
    </div>
</div>

@foreach ($parcelas as $parcela)
                @if($parcela['tipo'] == 0)
<!-- Tabela Acertos -->
<div class="row">
    <div class="col-md-12">
        <div class="box box-danger box-solid" id="boxAcerto">
            <div class="box-header with-border">
                <h3 class="box-title">Acerto</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div><!-- /.box-tools -->
            </div><!-- /.box-header -->
            <div class="box-body" style="display: block;">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nº Parcela</th>
                            <th>Valor</th>
                            <th>Vencimento</th>
                            <th>Situação</th>
                            <th>Data do Pagamento</th>
                            <th>Valor Pago</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($parcelas))

                        <tr>
                            <td colspan=7>Não tem parcelas cadastradas</td>
                        </tr>
                        @else
                        @foreach ($parcelas as $parcela)
                        <?php $alunoId = $parcela["aluno_id"]; ?>
                        <?php if ($parcela["tipo"] == 0) :
                            if ($parcela["situacao"] == "Vencida") {
                                $colorRow = 'red';
                            } elseif ($parcela["situacao"] == "Paga") {
                                $colorRow = '#ddd';
                            } else {
                                $colorRow = 'trasparent';
                            }
                            $parcelaId = $parcela['id'];
                        ?>
                            <tr style="background-color: <?= $colorRow ?>;">
                                <td>{{ str_pad($parcela["nParcela"] , 2 , '0' , STR_PAD_LEFT)}}</td>
                                <td>{{$parcela["valor"]}}</td>
                                <td>{{$parcela["dataVencimento"]}}</td>
                                <td>{{$parcela["situacao"]}}</td>
                                <td>{{$parcela["dataPagamento"]}}</td>
                                <td>{{$parcela["valorPago"]}}</td>
                                <td>
                                    <a href='/financeiro/controle/parcelas?&__search__=<?= $parcelaId ?>'>
                                        <button type=" button" class="btn btn-primary" title="Edita Parcela Nº {{$parcela['nParcela']}}">Editar</button>
                                    </a>
                                    <a href="{{ route('financeiro.pagar.parcela', ['type'=>'parcela','parcelaId'=>$parcela['id']]) }}">
                                        <button type="button" class="btn btn-success" title="Pagar Parcela Nº {{$parcela['nParcela']}}" <?php if ($parcela["situacao"] == "Paga") : ?>disabled<?php endif ?>>Pagar</button>
                                    </a>
                                    <a href="/financeiro/controle/parcelas?&__search__=<?= $parcelaId ?>">
                                        <button type="button" class="btn btn-danger" title="Pagar Parcela Nº {{$parcela['nParcela']}}" <?php if ($parcela["situacao"] != "Paga") : ?>disabled<?php endif ?>>Cancelar Pagamento</button>
                                    </a>
                                    <a href="{{ route('relatorios.comprovante.pagamento', ['id'=>$parcela['id'],'tipo'=>0]) }}" target="_blank">
                                        <button type="button" class="btn btn-warning" title="Pagar Parcela Nº {{$parcela['nParcela']}}" <?php if ($parcela["situacao"] != "Paga") : ?>disabled<?php endif ?>>Recibo</button>
                                    </a>
                                </td>
                            </tr>
                        <?php endif ?>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div><!-- /.box-body -->
            <div class="box-footer">
                <a href='/financeiro/controle/parcelas?&__search__=<?= $alunoId ?>'>Editar Parcelas</a>
            </div><!-- /.box-footer-->
        </div>
    </div>
</div>
@break                @endif
                @endforeach