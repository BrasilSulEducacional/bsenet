<div class="box">
    <div class="box-header">
        <h2>Selecione sua turma</h2>
    </div>
    <div class="box-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Turma</th>
                    <th>Alunos</th>
                    <th>Última chamada</th>
                    <th>Realizar</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($turmas as $turma)
                    <tr>
                        <td>
                            <a href="#">{!! $turma->turma !!}</a>
                        </td>
                        <td>
                            <span class="badge badge-success">{!! count($turma->alunos) !!}</span>
                        </td>
                        <td>
                            @if (!empty(
                                $turma->chamadas()->latest()->value('feita_em')
                            ))
                                {{ date('d/m/Y',strtotime($turma->chamadas()->latest()->value('feita_em'))) }}
                            @else
                                Sem registros*
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('chamada.turma', ['turmaId' => $turma->id]) }}">
                                Registrar
                            </a>
                        </td>
                        <td>
                            <a 
                                href="{{ route('chamada.report', ['turmaId' => $turma->id]) }}" 
                                id="report" 
                                target="_blank">
                                Relatório
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>

<script>
    $(function () {
        var $a = $("a#report");
    });
</script>