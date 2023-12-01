<?php
/**
      * @author Bruno Macedo Tertuliano
      * @access 09/12/2010
      * @copyright (c 2010)
*/
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt" lang="pt" >
<head>
<title>:: SIGO - ADMINISTRATIVO - PONTO ::</title>
<meta http-equiv="Content-Type" content="text/html; Charset=iso-8859-1" />
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
<link  href="<?php echo $caminho_raiz; ?>css/menu.css" rel="stylesheet" type="text/css" />
<link  href="<?php echo $caminho_raiz; ?>css/index.css" rel="stylesheet" type="text/css" />

<script src="<?php echo $caminho_raiz; ?>js/funcoes.js" ></script>
<script src="js/funcoes.js" ></script>
<script src="<?php echo $caminho_raiz; ?>js/formatacaixadetexto.js" ></script>
<script src="<?php echo $caminho_raiz; ?>includes/biblioteca/jquery/jquery-1.4.2.min.js" ></script>
<script src="<?php echo $caminho_raiz; ?>js/padrao.js" ></script>
<script>
	function abrirJanela(){
		
		if (window.showModalDialog) {
			window.showModalDialog('?mainapp=relatorio&app=espelho_pdf&desabilita_menu=true&desabilita_css=true&chapa='+<?=$row_supervisor['fun_chapa']?>+'&filial=' + <?=$filial?> +'&gerente=' + <?=$gerente?>+'&filial=' + <?=$filial?>+'&filial=' + <?=$filial?>+'&filial=' + <?=$filial?>,'MALA DIRETA - REIMPRESSÃO','dialogWidth:910px;dialogHeight:600px');
		}
		
		<a target="_blanck" href="?mainapp=relatorio&app=espelho_pdf&chapa='.$row_supervisor['fun_chapa'].'&filial='.$filial.'&='..'&supervisor='.$supervisor.'&encarregado='.$encarregado.'&superior='.$chapa_superior.'&status='.$status.'&desabilita_menu=true&desabilita_css=true">
		
	}
</script>

<table class="box_relatorio" width="1024" border="0">
	<tr class="cabecalho_tr">
		<td colspan="2" align="left" style="color:#FFFFFF;"><u>RELATÓRIOS</u> > <u>ENVIA E-MAIL</u></td>
	</tr>
	<tr class="cabecalho_tr">
		<td nowrap><span class="cabecalho_tr">ENVIA EM-MAIL</span></td>
	</tr>
</table>
<br />

<table class="box_relatorio" width="200" align="center" border="1">
	<tr class="subcabecalho_tr">
		<td colspan="2">ENVIO DE E-MAIL PARA SUPERIOR IMEDIATO</td>
	</tr>	
	<tr class="tr_cor_cinza">
		<td align="left">
			Executar envio
		</td>
		<td align="center">
			<a target="_blanck" href="inc_email.php"><img src="<?php echo $caminho_raiz?>imagens/lupa.gif" border="0"></a>
		</td>
	</tr>
</table>