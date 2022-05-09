{{-- <div class="box">
    <div class="box-header with-border">
        <div class="pull-left">
            <label for="date">Data da chamada</label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </span>
                <input class="form-control" type="date" name="date" id="date" value="{{ date("Y-m-d") }}" max="{{ date("Y-m-d") }}">
            </div>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-striped table-hover">
            <thead>
                <th>Código</th>
                <th>Nome</th>
                <th>Compareceu?</th>
                <th>Justificada?</th>
            </thead>
            <tbody>
                @foreach ($alunos as $aluno)
                    <tr>
                        <td> {{ $aluno->codigo }} </td>
                        <td> {{ $aluno->nome }} </td>
                        <td>
                            <input 
                                type="radio" 
                                data-input-check 
                                data-input-falta 
                                name="checkFalta__{{ $aluno->id }}" 
                                id="checkFalta__{{ $aluno->id }}" checked> 
                        </td>
                        <td>
                            <input 
                                type="radio" 
                                data-input-check 
                                data-input-justificado
                                name="checkFalta__{{ $aluno->id }}" 
                                id="checkFalta__{{ $aluno->id }}"> 
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div> --}}
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

        $("a#registerChamada").click(function(e) {
            e.preventDefault();

            var $tbody = $("tbody");
            var data = {};

            $tbody.children("tr").each(function(i, el) {
                var $tr = $(this);
                var codigo = $(el).children('td').eq(0).text()
                // var 
                console.log(codigo);
            });

        });
    });
</script>
