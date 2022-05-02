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
</style>
<div class="root" style="font-family: Roboto, sans-serif">
    @foreach ($alunos as $aluno)
        <div class="boletim">
            <div class="boletim-header">
                <div class="boletim-header-line"
                    style="border-radius: 5px; 
                        padding: 5px; 
                        color: white; 
                        background-color: {{$aluno['headersColor']}}; 
                        display: flex;
                        justify-content: space-between;
                        font-size: 1em;"
                    >
                    <span style="margin-right: 20px;">
                        BRASIL SUL EDUCACIONAL - Cursos Profissionais
                    </span>
                    <span style="margin-right: 50px;">BOLETIM</span>
                    <span>MTR: {{ $aluno['aluno']->codigo }}</span>
                </div>
                <div style="padding: 0px 10px;">
                    <div style="display: block; margin: 5px 0px;">
                        <span>
                            <b>Aluno(a):</b>
                            {{ $aluno['aluno']->nome }}
                        </span>
                        <span style="margin: 5px 0px 5px 20px;">
                            <b>Turma:</b>
                            {{ $aluno['aluno']->turma->turma }}
                        </span>
                    </div>
                    <div style="display: block; margin: 5px 0px;">
                        <span>
                            <b>Curso:</b>
                            Curso Integrado de processamento de dados
                        </span>
                    </div>
                    <div style="display: block; margin: 5px 0px;">
                        <span>
                            <b>Professor: </b>
                            {{ $aluno['aluno']->turma->professor->name }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="boletim-body">
                <div class="boletim-body-line" 
                    style="border-radius: 5px; 
                        padding: 5px 10px; 
                        color: white; 
                        background-color: {{$aluno['headersColor']}};
                        ">
                    <span style="width: 300px; border-right: 2px solid #fff; text-align: center; display: inline-block;">
                        Conteúdo
                    </span>
                    <span style="width: 96px; border-right: 2px solid #fff; text-align: center; display: inline-block;">
                        Nota
                    </span>
                    <span style="width: 96px; border-right: 2px solid #fff; text-align: center; display: inline-block;">
                        Faltas
                    </span>
                    <span style="width: 96px; border-right: 2px solid #fff; text-align: center; display: inline-block;">
                        Frequência
                    </span>
                </div>
                <div style="padding: 0px 10px;">
                    <table style="border-collapse: collapse;">
                        {{-- <thead style="border-radius: 5px; 
                        padding: 5px 10px; 
                        color: white; 
                        background-color: #0275c2;
                        padding: 5px 10px;
                        border: 3px solid #0275c2;
                        border-radius: 5px;
                        ">
                            <tr>
                                <th style="width: 300px; padding: 5px 10px;">Conteúdo</th>
                                <th style="width: 100px; padding: 5px 10px;">Nota</th>
                                <th style="width: 100px; padding: 5px 10px;">Faltas</th>
                                <th style="width: 100px; padding: 5px 10px;">Frequências</th>
                                <th style=""></th>
                            </tr>
                        </thead> --}}
                        <tbody>
                            @foreach ($aluno['notas'] as $nota)
                                <tr>
                                    <td style="width: 300px; height: 22px; border-right: 2px solid #000; padding: 0;">
                                        {{ $nota->conteudo->name }}
                                    </td>
                                    <td style="width: 100px; height: 22px; text-align: center; border-right: 2px solid #000; padding: 0;">
                                        {{ number_format($nota->nota, 1, ',', '.') }}
                                    </td>
                                    <td style="width: 100px; height: 22px; text-align: center; border-right: 2px solid #000; padding: 0;">
                                        {{ $nota->faltas }}
                                    </td>
                                    <td style="width: 100px; height: 22px; text-align: center; border-right: 2px solid #000; padding: 0;">
                                        {{ round((100 - (($nota->faltas / $nota->aulas) * 100)), 0) }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                        {{-- <div style="">
                            <span style="width: 300px; border-right: 2px solid #000; display: inline-block;">
                                {{ $nota->conteudo->name }}
                            </span>
                            <span style="width: 100px; border-right: 2px solid #000; text-align: center; display: inline-block;">
                                {{ number_format($nota->nota, 1, ',', '.') }}
                            </span>
                            <span style="width: 100px; border-right: 2px solid #000; text-align: center; display: inline-block;">
                                {{ $nota->faltas }}
                            </span>
                            <span style="width: 100px; border-right: 2px solid #000; text-align: center; display: inline-block;">
                                {{ round((100 - (($nota->faltas / $nota->aulas) * 100)), 0) }}%
                            </span>
                        </div> --}}
                </div>
            </div>
            <div class="boletim-footer">
                <div class="boletim-footer-line" 
                    style="border-radius: 5px; 
                        padding: 5px 10px; 
                        color: white; 
                        background-color: {{$aluno['headersColor']}};">
                    <span style="width: 200px; display: inline-block;">
                        <b>Média Geral:</b> {{ number_format($aluno['mediaGeral'], 1, ',', '.') }}
                    </span>
                    <span style="width: 200px; display: inline-block;">
                        <b>Total Faltas:</b> {{ $aluno['totalFaltas'] }}
                    </span>
                </div>
            </div>
        </div>
        <div class="signature" style="margin: 30px 0px 30px 0px; text-align: center; font-size: 1.2em;">
            ___________________________________________________ <br>
            Responsável
        </div>
    @endforeach
</div>