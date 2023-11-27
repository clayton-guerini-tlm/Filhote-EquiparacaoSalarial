<script type="text/javascript">
function MostraSelecionado(regiao){
	var span_selecionado = document.getElementById('span_selecionado');

	switch (regiao){
		case 'MG':
			span_selecionado.innerHTML = "ÁREA SELECIONADA: ÁREA 1 OI RMG";
		break;
		case 'SP':
			span_selecionado.innerHTML = "ÁREA SELECIONADA: ÁREA 3 TELEFONICA";
		break;
		case 'MS':
			span_selecionado.innerHTML = "ÁREA SELECIONADA: ÁREA 2 OI";
		break;
		case 'RJ':
			span_selecionado.innerHTML = "ÁREA SELECIONADA: ÁREA 1 OI RRJ";
		break;
		case 'ES':
			span_selecionado.innerHTML = "ÁREA SELECIONADA: ÁREA 1 OI RES";
		break;
		case 'NENHUM':
			span_selecionado.innerHTML = "SELECIONE UMA ÁREA";
		break;
	}
	
}
</script>

<table width="100%" border="1" align="center" class="box_relatorio">
  <tr class="cabecalho_tr">
    <td nowrap><span class="cabecalho_tr">SELECIONE A ÁREA DESEJADA</span></td>
  </tr>
</table>

<br />
<br />

<div align="center">

	<map name="nav1" id="nav1">
		<area onmouseover="MostraSelecionado('MS')" onmouseout="MostraSelecionado('NENHUM')" shape="poly" alt="ÁREA 2 OI" href="<?php echo $href['AREA2'] ?>" coords="43,154,110,177,147,162,163,171,197,171,201,157,213,175,282,180,303,133,322,186,321,224,308,243,307,265,276,271,253,308,245,313,240,323,229,323,221,306,203,304,198,246,179,243,173,218,127,200,124,181,108,183,91,193,73,193,41,161" />	
		<area onmouseover="MostraSelecionado('MG')" onmouseout="MostraSelecionado('NENHUM')" shape="poly" alt="ÁREA 1 OI RMG" href="sistema.php" coords="307,285,269,279,310,266,318,234,343,225,385,244,378,257,363,288,358,297,335,304,331,312,320,310,319,298" />
		<area onmouseover="MostraSelecionado('SP')" onmouseout="MostraSelecionado('NENHUM')" shape="poly" alt="ÁREA 3 TELEFONICA" href="<?php echo $href['AREA3_SP'] ?>" coords="306,288,326,320,319,322,305,337,283,315,256,306,269,282" />
		<area onmouseover="MostraSelecionado('ES')" onmouseout="MostraSelecionado('NENHUM')" shape="poly" alt="ÁREA 1 OI RES" href="<?php echo $href['AREA1_ES'] ?>" coords="385,265,385,280,376,291,367,295,368,289,375,278,375,265,380,263" />				
		<area onmouseover="MostraSelecionado('RJ')" onmouseout="MostraSelecionado('NENHUM')" shape="poly" alt="ÁREA 1 OI RRJ" href="<?php echo $href['AREA1_RJ'] ?>" coords="340,307,360,302,365,295,373,296,375,305,365,315,343,313" />
	</map>
	
	<!--<img border="0" src="imagens/mapa_brasil.gif" usemap="#nav1" />-->
	<img border="0" src="imagens/mapa_brasil_novo.png" usemap="#nav1" />
	
</div>

<div align="center">
	<span id="span_selecionado" class="valor_preto">SELECIONE UMA ÁREA</span>	
</div>