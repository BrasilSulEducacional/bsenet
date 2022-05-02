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
</style>
<div class="root" style="font-family: Roboto, sans-serif">
    <table style="width: 100%; border-collapse: collapse; background-color: #0275c2; color: white; width: 100%; border: 1px solid #0275c2;">
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
                {{ date("d/m/Y", strtotime($turma->created_at)) }} a 
                {{ date("d/m/Y", strtotime("+3 year", strtotime($turma->created_at))) }}
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
                <td style="text-align: center; width: 300px;">
                    Conteúdo <br><br><br><br>
                    Nome do Aluno
                </td>
                <td style="width: 35px; border: 1px solid #000; text-align: center; font-weight: bold">
                    MTR
                </td>
                @foreach ($qtdDatas as $data)
                    <td style="border: 1px solid #000">

                    </td>
                @endforeach
            </tr>
            @foreach ($alunos as $aluno)
                <tr style="border-bottom: 1px dashed #000;">
                    <td style="height: 25px; border-right: 1px solid #000;"> {{ $aluno->nome }} </td>
                    <td style="border-right: 1px solid #000; text-align: center;"> {{ $aluno->codigo }} </td>
                    @foreach ($qtdDatas as $item)
                        <td style="border-right: 1px solid #000;"></td>
                    @endforeach
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

{{-- {{dd()}} --}}