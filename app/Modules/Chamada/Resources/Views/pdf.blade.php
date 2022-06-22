<style>
    @font-face {
        font-family: 'Roboto', sans-serif;
        font-style: normal;
        font-weight: normal;
        src: url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');
    }

    * {
        font-size: 13px;
    }

    @page {
        margin: 20px 20px 20px 50px;
    }

    .tables {
        page-break-after: always;
    }

    .rotate {
        writing-mode: vertical-rl;
        white-space: nowrap;
        transform: scale(-1);
        transform: rotate(-90deg) translateX(-40px) translateY(-23px);
        position: absolute;
    }
</style>
<div class="root" style="font-family: Roboto, sans-serif">
    @foreach ($pagination as $alunos)
        <div class="tables">
            <table
                style="width: 100%; border-collapse: collapse; background-color: #0275c2; color: white; width: 100%; border: 1px solid #0275c2;">
                <tr style="padding: 10px; height: 100%;">
                    <td colspan="3" style="text-align: center; font-weight: bold;">
                        <span style="float: left;">
                            BSI
                        </span>
                        <span>
                            Chamada
                        </span>
                        <span style="float: right;">
                            Turma: {{ $turma->turma }}
                        </span>
                    </td>
                </tr>
                <tr style="height: 100%; border-top: 1px dashed #fff">
                    <td rowspan="3" style="width: 300px; text-align: center; font-weight: bold;">
                        CURSO PROFISSIONALIZANTE
                    </td>
                    <td style="text-align: left;">
                        <span style="font-weight: bold">
                            Período:
                        </span>
                        {{ date('d/m/Y', strtotime($turma->created_at)) }} a
                        {{ date('d/m/Y', strtotime('+3 year', strtotime($turma->created_at))) }}
                    </td>
                    <td style="text-align: left;">
                        <span style="font-weight: bold">
                            Instrutor:
                        </span>
                        {{ $turma->professor->name }}
                    </td>
                </tr>
                <tr style="height: 100%;">
                    <td style="text-align: left;">
                        <span style="font-weight: bold">
                            Horário:
                        </span>
                        {{ $turma->horario }}
                    </td>
                    <td style="text-align: left;">
                        <span style="font-weight: bold">
                            Dias:
                        </span>
                        {{ $turma->dias }}
                    </td>
                </tr>
            </table>
            <table style="width: 100%; border-collapse: collapse">
                <tbody style="border: 1px solid #000; border-top: none;">
                    <tr style="border: 1px solid #000">
                        <td style="text-align: center; width: 200px;">
                            Conteúdo:
                            <br><br>
                            <b> {{ $conteudo->name }}</b>
                            <br><br>
                            Nome do Aluno
                        </td>
                        <td style="width: 35px; border: 1px solid #000; text-align: center; font-weight: bold">
                            MTR
                        </td>
                        @php
                            $nomeAlunos = array_keys($alunos->toArray());
                        @endphp
                        @foreach ($qtdDatas as $key => $data)
                            <td style="border: 1px solid #000; width: 20px !important; height: 100px; padding: 0;">

                                @if (!empty(
                                    $alunos->first()->values()->toArray()[$key]
                                ))
                                    <div class="rotate">
                                        {{ date('d/m/Y',strtotime($alunos->first()->values()->toArray()[$key]['feita_em'])) }}
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @foreach ($alunos as $name => $aluno)
                        <tr style="border-bottom: 1px dashed #000;">
                            <td style="height: 25px; border-right: 1px solid #000;">
                                {{ $name }}
                            </td>
                            <td style="border-right: 1px solid #000; text-align: center;">
                                {{-- {{ $aluno->first()->aluno->codigo }} --}}
                                {{ ($aluno->first()->has('aluno') ? $aluno->first()->aluno->codigo : $aluno->first()->get('codigo')) }}
                            </td>

                            @if ($aluno->has('diff'))
                                @foreach ($aluno->get('diff') as $diff)
                                    <td style="border-right: 1px solid #000; text-align: center; font-size: 1em">
                                        -
                                    </td>
                                @endforeach
                            @endif

                            @foreach ($aluno as $key => $falta)
                                @if ($key !== 'diff')
                                    <td style="border-right: 1px solid #000; text-align: center; font-size: 1em">
                                        @if (!empty($falta->falta_justificada))
                                            FJ
                                        @elseif(!empty($falta->falta))
                                            F
                                        @elseif(!isset($falta->falta))
                                            -
                                        @else
                                            &middot;
                                        @endif
                                    </td>
                                @endif
                            @endforeach

                            {{-- {{ dump($aluno->count() + ($aluno->has('diff') ? $aluno->get('diff')->count() - 1 : 0)) }} --}}
                            {{-- {{ dump($aluno->count()) }} --}}
                            @if ($aluno->count() != count($qtdDatas))
                                @foreach (range(1, count($qtdDatas) - ($aluno->count() + ($aluno->has('diff') ? $aluno->get('diff')->count() - 1 : 0))) as $key => $item)
                                    {{-- @if (isset($aluno->first()->aluno->turma->turma) && $aluno->first()->aluno->turma->turma == 'Nenhuma')
                                        {{ dump($item) }}
                                        <td style="border-right: 1px solid #000; text-align: center; font-size: 1em">
                                            -
                                        </td>
                                    @else
                                    @endif --}}

                                    <td style="border-right: 1px solid #000; text-align: center; font-size: 1em">
                                    </td>
                                @endforeach
                            @endif
                        </tr>
                    @endforeach
                    <tr style="border-bottom: 1px dashed #000;">
                        <td style="height: 25px; border-right: 1px solid #000;"></td>
                        <td style="border-right: 1px solid #000; text-align: center;"></td>
                        @foreach ($qtdDatas as $item)
                            <td style="border-right: 1px solid #000;"></td>
                        @endforeach
                    </tr>
                    <tr style="border-bottom: 1px dashed #000;">
                        <td style="height: 25px; border-right: 1px solid #000;"></td>
                        <td style="border-right: 1px solid #000; text-align: center;"></td>
                        @foreach ($qtdDatas as $item)
                            <td style="border-right: 1px solid #000;"></td>
                        @endforeach
                    </tr>
                    <tr style="border-bottom: 1px dashed #000;">
                        <td style="height: 25px; border-right: 1px solid #000;"></td>
                        <td style="border-right: 1px solid #000; text-align: center;"></td>
                        @foreach ($qtdDatas as $item)
                            <td style="border-right: 1px solid #000;"></td>
                        @endforeach
                    </tr>
                    <tr style="border-bottom: 1px dashed #000;">
                        <td style="height: 25px; border-right: 1px solid #000;"></td>
                        <td style="border-right: 1px solid #000; text-align: center;"></td>
                        @foreach ($qtdDatas as $item)
                            <td style="border-right: 1px solid #000;"></td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach
</div>
{{-- {{ dd() }} --}}
