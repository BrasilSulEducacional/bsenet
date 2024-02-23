<!DOCTYPE html>
	 <html lang="pt-br">
	 <head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <style>
	     body {
	       font-family: Calibri, DejaVu Sans, Arial;
	       margin: 0;
	       padding: 0;
	       border: none;
	       font-size: 13px;
	     }
	 
	     #exemplo {
	       width: 100%;
	       height: auto;
	       overflow: hidden;
	       padding: 5px 0;
	       text-align: center;
	       background-color: #CCC;
	       color: #FFF;
	     }
.linha-vertical {
  height: 200px;/*Altura da linha*/
  border-left: 1px solid;/* Adiciona borda esquerda na div como ser fosse uma linha.*/
}

*{
	font-size:1em;
}
	   </style>
	 </head>
	 <body>
	   <div id='exemplo'>
	     Recibo<br/>
	   </div>
	 <table>
<tr>
<td><img src='https://www.brasilsuleducacional.com.br/img/logo.png' width='120'></td>

<td width=350px><b>BRASIL SUL EDUCACIONAL<b></td>
<td rowspan=5 width=14px><div class='linha-vertical'></div></td>
<td><b>Contrato:</b> {{$pagamento->parcela->aluno->codigo}} </td>
</tr>
<tr>
<td colspan=2><b>Recebemos de:</b> {{$pagamento->parcela->aluno->nome}}</td>
<td><b>Parcela:</b> <?=str_pad($pagamento->parcela->num_parcela , 2, '0' , STR_PAD_LEFT)?>/<?=str_pad($nParcelaFinal , 2, '0' , STR_PAD_LEFT)?></td>
</tr>
<tr>
<td  width='130px'><b>Parcela:</b><?=str_pad($pagamento->parcela->num_parcela , 2, '0' , STR_PAD_LEFT)?>/<?=str_pad($nParcelaFinal , 2, '0' , STR_PAD_LEFT)?></td>
<td><b>Valor:</b> {{$pagamento->parcela->valorParaBrl()}}</td>
<td><b>Valor:</b> {{$pagamento->valorParaBrl()}}</td>
</tr>
<tr>
<td><b>Contrato:</b> {{$pagamento->parcela->aluno->codigo}}</td>
<td><b>Vencimento:</b> {{$pagamento->parcela->formatarDataVencimento()}}</td>
<td><b>Vencimento:</b> {{$pagamento->parcela->formatarDataVencimento()}}</td>
</tr>
<tr>
<td><b>Data:</b> {{$pagamento->formatarDataPagamento()}}</td>
<td><b>Ass:</b> _______________________________&nbsp;&nbsp;&nbsp;</td>
<td><b>Data:</b> {{$pagamento->formatarDataPagamento()}}</td>
</tr>
</table>
</body>
</html>