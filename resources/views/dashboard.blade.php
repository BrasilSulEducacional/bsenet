<style>
    .title {
        font-size: 50px;
        color: #636b6f;
        font-family: 'Raleway', sans-serif;
        font-weight: 100;
        display: block;
        text-align: center;
        margin: 20px 0 10px 0px;
    }

    .links {
        text-align: center;
        margin-bottom: 20px;
    }

    .links>a {
        color: #636b6f;
        padding: 0 25px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .1rem;
        text-decoration: none;
        text-transform: uppercase;
    }
</style>

<div class="title">
    {!! config('admin.name') !!}
</div>
<div class="links">
    <a href="{{ route('sis.aluno.index') }}">Alunos</a>
    <a href="{{ route('sis.nota.index') }}">Notas</a>
    <a href="{{ route('sis.turma.index') }}">Turmas</a>
</div>