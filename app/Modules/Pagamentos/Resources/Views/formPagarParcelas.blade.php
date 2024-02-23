<div class="row">
    <div class="col-md-12">
        <div class="box box-info box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">Confirmar Pagamento</h3>
                <div class="box-tools pull-right">
                </div><!-- /.box-tools -->
            </div><!-- /.box-header -->
            <div class="box-body" style="display: block;">
                <form id="widget-form-65a7de638062b" action="<?= route("financeiro.confirmar") ?>" method="POST" class="form-horizontal" accept-charset="UTF-8">
                    <input type="hidden" name="parcelaId" id="parcelaId" value="<?= $parcelaId ?>">
                    <input type="hidden" name="alunoId" id="alunoId" value="">
                    <input type="hidden" name="devendo" id="devendo" value="">
                    <div class="box-body fields-group">
                        <div class="form-group  ">
                            <label for="nParcela" class="col-sm-2  control-label">Nº Parcela</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <input disabled="" type="text" id="nParcela" name="nParcela" value="" class="form-control nParcela" placeholder="Entrada Nº Parcela">
                                </div>
                            </div>
                        </div>
                        <div class="form-group  ">
                            <label for="dataVencimento" class="col-sm-2  control-label">Data de Vencimento</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <input disabled="" type="text" id="dataVencimento" name="dataVencimento" value="" class="form-control dataVencimento" placeholder="Entrada Data de Vencimento">
                                </div>
                            </div>
                        </div>
                        <div class="form-group  1">
                            <label for="multa" class="col-sm-2  control-label">Multa</label>
                            <div class="col-sm-8">
                                <input type="checkbox" id="multa" value="0" class="multa">
                                <input type="text" name="multaValor" id="multaValor" style="border:0;" disabled>
                            </div>
                            </label>
                        </div>
                        <div class="form-group 1">
                            <label for="juros" class="col-sm-2  control-label">Juros</label>
                            <div class="col-sm-8">
                                <input type="checkbox" id="juros" value="0" class="juros">
                                <input type="text" name="jurosValor" id="jurosValor" style="border:0;" disabled>
                            </div>
                            </label>
                        </div>
                    </div>
                    <div class="form-group  ">
                        <label for="valorPago" class="col-sm-2  control-label">Valor Pago</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-dollar fa-fw"></i></span>
                                <input type="text" id="valorPago" name="valorPago" value="" class="form-control valorPago" placeholder="Entrada Valor Pago" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group  ">
                        <label for="dataPagamento" class="col-sm-2  control-label">Data do Pagamento</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                                <input style="width: 130px" type="date" id="dataPagamento" name="dataPagamento" value="<?= date("Y-m-d") ?>" class="form-control dataPagamento" placeholder="Entrada Data do Pagamento" required>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group form2">

                        <label for="usuario" class="col-sm-2  control-label">Usuario</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                <input type="text" id="usuario" name="usuario" value="" class="form-control usuario" placeholder="Usuario" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form2">
                        <label for="senha" class="col-sm-2  control-label">Senha</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-eye-slash"></i></span>
                                <input type="password" id="senha" name="senha" value="" class="form-control senha" placeholder="Entrada Senha" required>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <div class="btn-group pull-left">
                                <button type="reset" class="btn btn-warning pull-right">Resetar</button>
                            </div>
                            <div class="btn-group pull-right">
                                <button class="btn btn-info" id="next">Next</button>
                                <button type="submit" class="btn btn-info pull-right" style="display:none">Submeter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div><!-- /.box-body -->
        </div>
    </div>
</div>
<script>
    $(function() {

        var formata = Intl.NumberFormat("pt-BR", {
            style: "currency",
            currency: "BRL"
        })
        $(document).ready(function() {
            var parcelaId = $("#parcelaId").val()
            consultar(parcelaId)

            $(".form2").css({
                "display": "none"
            })

            $("#next").click(function() {
                $(".form-group").css({
                    "display": "none"
                })
                $(".form2").css({
                    "display": "initial"
                })
                $("#next").css({
                    "display": "none"
                })
                $("button[type='submit']").css({
                    "display": "initial"
                })

            })
        })

        function consultar(parcelaId) {
            $.ajax({
                method: "POST",
                url: "<?= route("financeiro.pagar.dadosPagamento") ?>",
                data: {
                    id: parcelaId,
                    _token: "{{ csrf_token() }}",
                },
                dataType: "json",
                error: function() {
                    alert("algo de errado")
                },
                success: function(response) {
                    if (response.type == "success") {
                        if (response.data.nParcela) {
                            $("#nParcela").val(response.data.nParcela)
                        }
                        if (response.data.alunoId) {
                            $("#alunoId").val(response.data.alunoId)
                        }
                        if (response.data.dataVencimentoFormatada) {
                            $("#dataVencimento").val(response.data.dataVencimentoFormatada)
                        }
                        if (response.data.valor) {
                            if (response.data.multa.value > 0) {
                                $("#multa").prop("checked", true);
                                $("#multaValor").val(response.data.multa.percentual + " - " + formata.format(response.data.multa.value))
                            } else {
                                $("#multaValor").val("0% - R$ 0,00")
                                $("#multa").prop("disabled", true);
                            }
                            if (response.data.juros.value > 0) {
                                $("#juros").prop("checked", true);
                                $("#jurosValor").val(response.data.juros.percentual.toFixed(2) + "% - " + formata.format(response.data.juros.value))
                            } else {
                                $("#jurosValor").val("0% - R$ 0,00")
                                $("#juros").prop("disabled", true);

                            }
                            if (response.data.devendo > 0) {
                                if (confirm("Está devendo: " + formata.format(response.data.devendo) + " da parcela anterior. Deseja cobrar?") == true) {
                                    $("#valorPago").val(response.data.valor.toFixed(2))
                                    $("#devendo").val(response.data.devendo)
                                } else {
                                    var valor = parseFloat(response.data.valor) - parseFloat(response.data.devendo)
                                    $("#valorPago").val(valor.toFixed(2))
                                    $("#devendo").val(0)

                                }
                            }
                            $("#valorPago").val(response.data.valor.toFixed(2))
                        }

                        $("#multa").on('change', function() {
                            if ($(this).is(':checked')) {
                                var valorPagar = $("#valorPago").val();
                                var novoValorPagar = parseFloat(valorPagar) + parseFloat(response.data.multa.value);
                                $("#valorPago").val(novoValorPagar.toFixed(2))
                                $("#multaValor").val(response.data.multa.percentual + " - " + formata.format(response.data.multa.value))

                            } else {
                                var valorPagar = $("#valorPago").val();
                                var novoValorPagar = parseFloat(valorPagar) - parseFloat(response.data.multa.value);
                                $("#valorPago").val(novoValorPagar.toFixed(2))
                                $("#multaValor").val("0% - R$ 0,00")

                            }
                        })
                        $("#juros").on('change', function() {
                            if ($(this).is(':checked')) {
                                var valorPagar = $("#valorPago").val();
                                var novoValorPagar = parseFloat(valorPagar) + parseFloat(response.data.juros.value);
                                $("#valorPago").val(novoValorPagar.toFixed(2))
                                $("#jurosValor").val(response.data.juros.percentual.toFixed(2) + "% - " + formata.format(response.data.juros.value))
                            } else {
                                var valorPagar = $("#valorPago").val();
                                var novoValorPagar = parseFloat(valorPagar) - parseFloat(response.data.juros.value);
                                $("#valorPago").val(novoValorPagar.toFixed(2))
                                $("#jurosValor").val("0% - R$ 0,00")
                            }
                        })

                    }
                }

            })
        }


    })
</script>