<?php
// Abri conexao
$conn = RetornaConexaoMysql('local', 'sigo_integrado');

// setando valor padrao para itens
$item['id'] = '';
$item['descricao'] = '';
$item['centralizado'] = '';

//// Editar item cadastrado
 if(isset($_POST['edit-id'])){

    $params = trataPost($conn);

    $sql_update_item = "UPDATE tbl_area SET
        descricao='".strtoupper($params['area_descricao'])."',
        centralizado=".$params['area_centralizado']."
        WHERE id=".$params['edit-id']."
    ;";
    $executou = mysqli_query($conn, $sql_update_item);
    if(!$executou){
      echo '<script type="text/javascript">';
      echo ' alert("'.  mysqli_error($conn).'")';
      echo '</script>';
    }else{
        GravaLogSentenca($sql_update_item, $_SESSION['SIGO']['ACESSO']['ID_USUARIO']);
    }

}

//// deletar item cadastrado
 if(!isset($_POST['edit-id']) && isset($_GET['delete'])){

    $params = trataGet($conn);

    $sql_delete_item = 'DELETE FROM tbl_area WHERE id='.$params['id'].';';
    $executou = mysqli_query($conn, $sql_delete_item);
    if(!$executou){
      echo '<script type="text/javascript">';
      echo ' alert("'.  mysqli_error($conn).'")';
      echo '</script>';
    }else{
        GravaLogSentenca($sql_delete_item, $_SESSION['SIGO']['ACESSO']['ID_USUARIO']);
    }


}

///// Novo item
if($_REQUEST['inserir'] == "1" && ! isset($_GET['edit'])){
// echo 'espaguetaoooooooooo horrorosooo entrou aki :p';
    $param = trataPost($conn);

    $sql_insercao_de_area = 'INSERT INTO tbl_area (descricao,centralizado)
        VALUES
            (
                \''.strtoupper($param['area_descricao']).'\'
                ,'.$param['area_centralizado'].'
            );
    ';


    $executou = mysqli_query($conn, $sql_insercao_de_area);

    if(!$executou){
      echo '<script type="text/javascript">';
      echo ' alert("'.  mysqli_error($conn).'")';
      echo '</script>';
    }else{
        GravaLogSentenca($sql_insercao_de_area, $_SESSION['SIGO']['ACESSO']['ID_USUARIO']);
    }


}else if(isset($_GET['edit'])){
    $params = trataGet($conn);
    $id = $params['id'];

    $sql_busca_item = 'SELECT * FROM tbl_area WHERE id='.$id.';';
    $executou = mysqli_query($conn, $sql_busca_item);

    if(!$executou){
      echo '<script type="text/javascript">';
      echo ' alert("'.  mysqli_error($conn).'")';
      echo '</script>';
    }else{
        $log_sentenca = mysqli_query($conn, $sql_busca_item);
    }

    $item = mysqli_fetch_array($executou);

}

?>
<script type="text/css">
.cursor-pointer{ cursor:pointer; }
</script>
<link type="text/css" rel="stylesheet" href="<?php echo $caminho_raiz?>/js/jquery-1.4.2.js"/>
<script type="text/javascript">
    $(document).ready(function(){

            //ao clicar na imagem de novo abrir o formulario
            $('#form-novo').click(function(){
                window.location.href = 'principal.php?mainapp=cadastro&app=area&novo=1';
            });

            $('.cancelar-form').click(function(){
                $('#form').hide();
            });

        }
    );


</script>
<table class="box_relatorio" align="center" width="100%">
    <tr class="cabecalho_tr">
        <td style="width:16px">
            <img style="cursor:pointer" src="<?php echo $caminho_raiz ?>imagens/icon_new.gif" border="0" id="form-novo"/>
        </td>
        <td>
            <span class="cabecalho_tr">CADASTRO DE &Aacute;REA</span>
        </td>
    </tr>
</table>
<?php if(isset($executou)&& (isset($_GET['edit-id'])) || isset($_POST['inserir'])):?>
    <div>
        <?php if($executou):?>
        <script type="text/javascript">
            alert("Informação salva com sucesso !");
        </script>
        <?php else:?>
        <script type="text/javascript">
            alert("Informação não foi salva !");
        </script>
        <?php endelseif;?>
        <?php endif;?>
    </div>
<?php endif;?>


<!-- Formulario para insercao de novo usuario -->
<span id="form" style="<?php echo (isset($_GET['edit']) || isset($_REQUEST['inserir']) || isset($_REQUEST['novo']))? '':'display: none;'?>">
<form url="principal.php?mainapp=cadastro&app=area" method="POST">
    <?php if(!isset($_GET['edit'])):?>
        <input type="hidden" value="1" name="inserir"/>
        <?php elseif(isset($_GET['edit'])):?>
        <input type="hidden" value="<?php echo $item['id'];?>" name="edit-id"/>
        <?php endelseif;?>
    <?php endif;?>
    <table class="box_relatorio" width="350" align="center" border="1">
        <tr class="subcabecalho_tr">
            <td colspan="2">DADOS DA &Aacute;REA</td>
        </tr>
        <tr class="tr_cor_cinza">
            <td style="text-align:left"p>&Aacute;REA</td>
            <td>
                <div align="left">
                    <input name="area_descricao" type="text" value="<?php echo $item['descricao'];?>" id="area_descricao" size="60" maxlength="150" />
                </div>
            </td>
        </tr>
        <tr class="tr_cor_branco">
            <td>CENTRALIZADO</td>
            <td>
                <div align="left">
                    <select name="area_centralizado" id="area_centralizado">
                        <option value="1" <?php echo ($item['centralizado'] == '1')? 'selected="true"':'""';?>>Sim</option>
                        <option value="0" <?php echo ($item['centralizado'] == '0')? 'selected="true"':'""';?>>N&atilde;o</option>
                    </select>
                </div>
            </td>
        </tr>
        <tr class="subcabecalho_tr">
            <td colspan="2" class="subcabecalho_tr">
                <input type="submit" value="SALVAR" class="cursor-pointer" />&nbsp;
                <input type="reset" value="LIMPAR" />&nbsp;
                <input class="cancelar-form" type="button" value="CANCELAR" class="cursor-pointer"/>
            </td>
        </tr>
    </table>

</form>
</span>



<?php
    ///////
    //LISTAGEM
    //////
    $seleciona_areas = 'SELECT * FROM tbl_area';
    $resultado = mysqli_query($conn, $seleciona_areas);

    if(!$resultado){
        die("Erro ao executar query: ".mysqli_error($conn));
    }

?>

<table  width="1024" align="center" class="box_relatorio">

    <tr class="cabecalho_tr">
        <td>&Aacute;REA</td>
        <td>CENTRALIZADO</td>
        <td>A&Ccedil;&Atilde;O</td>
    </tr>

  <tr >
    <?php while($row = mysqli_fetch_array($resultado)):?>
    <?php $estilo = ($estilo == 'tr_cor_branco')? 'tr_cor_cinza':'tr_cor_branco'; ?>
    <tr class="<?php echo $estilo ?>">
        <td><?php echo $row['descricao']?></td>
        <td><?php  echo ($row['centralizado'] == "1")? 'SIM':'N&Aacute;O';?></td>
        <td>
            <a href="<?php echo 'principal.php?mainapp=cadastro&app=area&edit=true&id='.$row['id']?>">
            <img style="cursor:pointer; border:none" title="Editar" src="<?php echo $caminho_raiz?>imagens/editar.png" />
            </a>
            <a href="<?php echo 'principal.php?mainapp=cadastro&app=area&delete=true&id='.$row['id']?>">
            <img style="cursor:pointer; border:none" title="Excluir" src="<?php echo $caminho_raiz?>imagens/delete.png" />
            </a>
        </td>
    </tr>
    <?php endwhile;?>


</table>


<?php  mysqli_close($conn); ?>


