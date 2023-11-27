<table class="box_relatorio" width="1024" border="0">
	<tr class="cabecalho_tr">
		<td nowrap><span class="cabecalho_tr">RECADASTRO DE SENHA</span></td>
	</tr>
</table>
<br />
<div id="div_busca" style="display:block">
<form onsubmit="return RecadastraSenha();">
<table class="box_relatorio" width="420" align="center" border="1">
	<tr class="subcabecalho_tr">
		<td colspan="2">PREENCHA OS SEGUINTES DADOS</td>
	</tr>
	<tr class="tr_cor_cinza">
		<td>REGISTRO Oi</td>
		<td><div align="left"><input name="usuario" type="text" id="usuario" size="20" maxlength="8" /></div></td>
	</tr>
	<tr class="tr_cor_branco">
		<td>E-MAIL</td>
		<td><div align="left"><input name="email" type="text" id="email" size="20" maxlength="50" value="" />
		<select id="email_sufixo">
			<option value="@telemont.com.br">@telemont.com.br</option>
			<option value="@telemontrac.com.br">@telemontrac.com.br</option>
			<option value="@telemontrbs.com.br">@telemontrbs.com.br</option>
			<option value="@telemontrgo.com.br">@telemontrgo.com.br</option>
			<option value="@telemontrms.com.br">@telemontrms.com.br</option>
			<option value="@telemontrmt.com.br">@telemontrmt.com.br</option>
			<option value="@telemontrio.com.br">@telemontrio.com.br</option>
			<option value="@telemontrro.com.br">@telemontrro.com.br</option>
		</select></div></td>
	</tr>
	<tr class="subcabecalho_tr">
		<td colspan="2"><input type="submit" value="BUSCAR"><input type="button" value="LIMPAR" onclick="window.location.href='?mainapp=home&app=recadastro_senha';"></td>
	</tr>
</table>
</form>
</div>

<div id="div_carregando" class="div_carregando_relatorio">
	<div align="center"><img src="imagens/loading.gif" /></div>
	<span class="span_carregando_relatorio" id="span_carregando_relatorio">????<br /> AGUARDE...</span>
</div>