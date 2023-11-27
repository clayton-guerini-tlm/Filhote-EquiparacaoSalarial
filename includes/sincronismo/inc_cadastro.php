<script language="javascript">

function validaform() {
    
        if(document.getElementById('chapa').value == '') {
            alert('Caro usuário, favor inserir a chapa do colaborador para que o sincronismo seja executado');
            return false;
        }
    
    return true;
}

</script>
<?php

/**
 * @author Leandro Ferro
 * @since 09/07/2012
 * @copyright Telemont Engenharia de Telecomunicações S.A.
 * ID Demanda: 14302
 * Solicitação: Implementar página de solicitação de sincronismo urgente.
 * 
 * Manutenções / Implementações
 *
 *  
 */

?>
<form id="form1" name="form1" method="post" onsubmit="return validaform();" action="?mainapp=sincronismo&app=cadastro_valida">
<table class="box_relatorio" width="100%">
    <tr class="cabecalho_tr">
        <td><span class="cabecalho_tr">SOLICITAÇÕES DE SINCRONISMO DO SIGO INTEGRADO</span></td>
    </tr>
</table>
</br>
<table class="box_relatorio" align="center" width="400">
    <tr>
        <td nowrap align="center">
            Chapa do Func.:<input type="text" id="chapa" name="chapa" size="10"/>
        </td>
    </tr>
    <tr>
        <td>
            Atualizar Func.: <input name="chkAtualizar1" type="checkbox" id="chkAtualizar1" value="AtualizarFuncionario" />
            Atualizar Férias: <input name="chkAtualizar2" type="checkbox" id="chkAtualizar2" value="AtualizarFerias" />
            Atualizar Afast.: <input name="chkAtualizar3" type="checkbox" id="chkAtualizar3" value="AtualizarAfastamento" />
            Incluir Func.: <input name="chkAtualizar4" type="checkbox" id="chkAtualizar4" value="IncluirFuncionario" />
        </td>
    </tr>
    <tr class="subcabecalho_tr">
        <td><input type="submit" id="Cadastrar" name="Cadastrar" value="Cadastrar"/></td>
    </tr>    
</table>
</form>