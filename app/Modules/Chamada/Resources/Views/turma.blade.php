{!! $box !!}
<script>
    $(function() {
        $("input[data-input-check]").iCheck({
            handle: 'radio',
            radioClass: 'icheckbox_minimal-blue',
            disabledClass: '',
        });

        $("input[data-input-check]").on('ifClicked', function(e) {
            $(this).iCheck('uncheck');
        });

        $("#chamadaDate").datetimepicker({
            format: 'DD/MM/YYYY',
            locale: 'pt-br',
            allowInputToggle: true
        });

        $(".conteudo").select2({
            allowClear: true,
            placeholder: {
                id: 'conteudo',
                text: 'Selecione o conteúdo',
            },
        })

        $("a#registerChamada").click(function(e) {
            e.preventDefault();

            var $tbody = $("tbody");
            var chamada = [];
            var conteudoId = $(".conteudo").val();
            var chamadaDate = $(".chamadaDate").val();
            chamadaDate = chamadaDate.split("/").reverse().join("-");
            console.log(chamadaDate);

            $tbody.children("tr").each(function(i, el) {
                var $tr = $(this);
                var codigo = $(el).children('td').eq(0).text();


                var $firstPeriod = $(`[name=falta__${codigo}__first]`);
                var $secondPeriod = $(`[name=falta__${codigo}__second]`);

                var firstPeriod = {
                    falta: $firstPeriod.eq(0).iCheck('update')[0].checked,
                    falta_justificada: $firstPeriod.eq(1).iCheck('update')[0].checked,
                }

                var secondPeriod = {
                    falta: $secondPeriod.eq(0).iCheck('update')[0].checked,
                    falta_justificada: $secondPeriod.eq(1).iCheck('update')[0].checked,
                }

                chamada.push({
                    codigo,
                    conteudoId,
                    chamadaDate,
                    firstPeriod,
                    secondPeriod,
                });

            });


            $.pjax({
                url: $(this).attr('href'),
                container: '#pjax-container',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: JSON.stringify(chamada),

                method: "POST",
                dataType: 'application/json',
                contentType: 'application/json; charset=utf-8',
            });

        });
    });
</script>
