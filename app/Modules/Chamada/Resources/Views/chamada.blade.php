<div class="box">
    <div class="box-header">
        <h2>Selecione sua turma</h2>
    </div>
    <div class="box-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Turma</th>
                    <th>Quantiade de alunos</th>
                    <th>Última chamada realizada</th>
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
                                $turma->chamadas()->latest()->value('released_at')
                            ))
                                {{ date('d/m/Y',strtotime($turma->chamadas()->latest()->value('released_at'))) }}
                            @else
                                Sem registros*
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('chamada.turma', ['turmaId' => $turma->id]) }}">Realizar chamada</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>
