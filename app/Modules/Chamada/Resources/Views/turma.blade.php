{!! $box !!}
<script>
    $(function() {

        var excepts = {{ $excepts }};

        var turmaId = {{ $turmaId }};

        $("input[data-input-check]").iCheck({
            handle: 'radio',
            radioClass: 'icheckbox_minimal-blue',
            disabledClass: '',
        });

        $("input[data-input-check]").on('ifClicked', function(e) {
            $(this).iCheck('uncheck');
        });

        // $("#chamadaDate").datetimepicker({
        //     setDate: new Date(),
        //     format: 'DD/MM/YYYY',
        //     locale: 'pt-br',
        //     allowInputToggle: true
        // });

        $(".conteudo").select2({
            allowClear: true,
            placeholder: {
                id: 'conteudo',
                text: 'Selecione o conteúdo',
            },
        })

        $("a#registerChamada").click(function(e) {
            var $a = $(this);

            e.preventDefault();

            $a.attr('disabled', true);

            var $tbody = $("tbody");
            var chamada = [];
            var conteudoId = $(".conteudo").val();
            var chamadaDate = $(".chamadaDate").val();

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

            // console.log($(this).attr('href'));

            var completeData = {
                chamada,
                excepts,
                conteudoId,
                turmaId,
                chamadaDate
            }

            $.ajax({
                url: $(this).attr('href'),
                // container: '#pjax-container',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: JSON.stringify(completeData),
                method: "POST",
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                success: function(response) {
                    console.log(response);
                    var message = response.message;

                    if (response.type == 'error') {
                        $.admin.toastr.error(message);
                        $a.attr('disabled', false);
                    }

                    if (response.type == 'success') {
                        $.admin.toastr.success(message);

                        $.pjax({
                            url: response.redirect_url,
                            container: '#pjax-container',
                        });
                    }
                }
            });

        });
    });
</script>
