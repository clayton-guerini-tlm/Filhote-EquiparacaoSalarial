function getAjax(){
    var Ajax = null;
    try{
        // Tenta criar objeto ajax para os browsers mais recentes como Firefox, e Opera
        ajax = new XMLHttpRequest(); // ajax p firefox opera e navegadores recentes
    }catch(ee){
        try{
            // Tenta criar ajax para algumas versões do Microsoft Internet Explorer
            ajax = new ActiveXObject("Msxml2.XMLHTTP"); // IE
        }catch(e){
            try{
                // Tenta criar ajax para algumas versões do Microsoft Internet Explorer
                ajax = new ActiveXObject("Microsoft.XMLHTTP"); // IE
            }catch(E){
                // Browser utilizado não aceita ajax, o objeto não é criado
                ajax = false;
            }
        }
    }
    return ajax;
}

function txtBoxFormat2(objeto, sMask, evtKeyPress) {
    var i, nCount, sValue, fldLen, mskLen,bolMask, sCod, nTecla;

    if(document.all) { // Internet Explorer
        nTecla = evtKeyPress.keyCode;
    } else if(document.layers) { // Nestcape
        nTecla = evtKeyPress.which;
    } else {
        nTecla = evtKeyPress.which;
        //alert(nTecla);
        if (nTecla == 8 || nTecla == 0) {
            return true;
        }
    }

    sValue = objeto.value;

    // Limpa todos os caracteres de formatação que
    // já estiverem no campo.
    sValue = sValue.toString().replace( "-", "" );
    sValue = sValue.toString().replace( "-", "" );
    sValue = sValue.toString().replace( ".", "" );
    sValue = sValue.toString().replace( ",", "" );
    sValue = sValue.toString().replace( ".", "" );
    sValue = sValue.toString().replace( "/", "" );
    sValue = sValue.toString().replace( "/", "" );
    sValue = sValue.toString().replace( ":", "" );
    sValue = sValue.toString().replace( ":", "" );
    sValue = sValue.toString().replace( "(", "" );
    sValue = sValue.toString().replace( "(", "" );
    sValue = sValue.toString().replace( ")", "" );
    sValue = sValue.toString().replace( ")", "" );
    sValue = sValue.toString().replace( " ", "" );
    sValue = sValue.toString().replace( " ", "" );
    fldLen = sValue.length;
    mskLen = sMask.length;

    i = 0;
    nCount = 0;
    sCod = "";
    mskLen = fldLen;

    while (i <= mskLen) {
        bolMask = ((sMask.charAt(i) == "-") || (sMask.charAt(i) == ".") || (sMask.charAt(i) == ",") || (sMask.charAt(i) == "/") || (sMask.charAt(i) == ":"));
        bolMask = bolMask || ((sMask.charAt(i) == "(") || (sMask.charAt(i) == ")") || (sMask.charAt(i) == " "));

        if (bolMask) {
            sCod += sMask.charAt(i);
            mskLen++;
        }
        else {
            sCod += sValue.charAt(nCount);
            nCount++;
        }
        i++;
    }

    objeto.value = sCod;

    if (nTecla != 8) { // backspace
        if (sMask.charAt(i-1) == "9") { // apenas números...
            return ((nTecla > 47) && (nTecla < 58));
        }
        else { // qualquer caracter...
            return true;
        }
    }
    else {
        return true;
    }
}


function InserirItem(prefixo, focus){

    document.getElementById('div_sel_menu').style.display = "none";

    var inputs = document.getElementsByTagName("input");
    for(var i = 0; i< inputs.length; i++){
        if(inputs[i].id.substr(0,prefixo.length) == prefixo){
            switch (inputs[i].type){
                case "hidden" :
                    inputs[i].value = 0;
                    break;
                case "text" :
                    inputs[i].value = "";
                    break;
            }
        }
    }

    var selects = document.getElementsByTagName("select");
    for(var i = 0; i< selects.length; i++){
        if(selects[i].id.substr(0,prefixo.length) == prefixo){
            if(selects[i].id != "smu_men_id"){
                selects[i].value = "";
            }
        }
    }

    document.getElementById('div_busca_resultado').style.display = "none";
    document.getElementById('div_editar').style.display = "block";
    document.getElementById(focus).focus();
}

function SalvarItem(prefixo, tabela, titulo, focus){
    var retorno =  true;
    var msg = "Erros encontrados:\n\n";
    var campos_post = "";

    var c;
    var v;
    var objj;
    var achou_opt = false;

    var inputs = document.getElementsByTagName("input");
    for(var i = 0; i< inputs.length; i++){
        if(inputs[i].id.substr(0,prefixo.length) == prefixo){
            c = inputs[i].name;
            v = inputs[i].value;
            v = v.replace(/&/gi, "|ecom|");

            if (c == 'EMAIL'){
                if(!v.match(/\b(^(\S+@).+((\.com)|(\.net)|(\.edu)|(\.mil)|(\.gov)|(\.org)|(\..{2,2}))$)\b/gi)) {
                    msg+="- Dados inválidos no campo EMAIL.\n";
                    retorno = false;
                }
            }else if(v == "" && c != "VAZIO"){
                msg+="- Campo " + c + " não pode ser vazio.\n";
                retorno = false;
            }
            campos_post+="&"+inputs[i].id+"="+v;
        }
    }

    var textareas = document.getElementsByTagName("textarea");
    for(var i = 0; i< textareas.length; i++){
        if(textareas[i].id.substr(0,prefixo.length) == prefixo){
            c = textareas[i].name;
            v = textareas[i].value;
            v = v.replace(/&/gi, "|ecom|");
            campos_post+="&"+textareas[i].id+"="+v;
        }
    }

    if (tabela == "contato"){

        campos_post+="&gre_id="+document.getElementById('gre_id').value;

    }

    var selects = document.getElementsByTagName("select");
    for(var i = 0; i< selects.length; i++){
        if(selects[i].id.substr(0,prefixo.length) == prefixo){

            objj = selects[i];
            c = selects[i].name;
            v = "";
            if (objj.multiple){
                for (var jj=0; jj< objj.length;jj++){
                    if(objj.options[jj].selected == true){
                        v = v + objj.options[jj].value + "|";
                        achou_opt = true;
                    }
                }

                if(achou_opt){
                    v = v.substr(0,v.length-1);
                    campos_post+="&"+selects[i].id+"="+v;
                }else{
                    msg+="- Selecione ao menos uma opção no campo " + c + ".\n";
                    retorno = false;
                }
            }else{
                v = selects[i].value;
                v = v.replace(/&/gi, "|ecom|");
                if(v == "" && c != "VAZIO"){
                    msg+="- Campo " + c + " não pode ser vazio.\n";
                    retorno = false;
                }
                campos_post+="&"+selects[i].id+"="+v;
            }


        }
    }

    if(!retorno){
        alert(msg);
        return false;
    }

    while(campos_post.indexOf("'") >= 0) {
        campos_post = campos_post.replace("'","");
    }

    document.getElementById('span_carregando_relatorio').innerHTML = "CADASTRANDO "+ titulo.toUpperCase() +"<br /> AGUARDE...";
    document.getElementById('div_editar').style.display = "none";
    document.getElementById('div_carregando').style.display = "block";

    var campos = "funcao_ajax=AjaxSalvarItem&prefixo="+prefixo+"&tabela="+tabela+"&titulo="+titulo+"&focus="+focus+campos_post;


    var AjaxSalvarItem = getAjax();
    if (AjaxSalvarItem != null) {
        AjaxSalvarItem.open("POST", "ajax/ajax_funcoes.php", true);
        AjaxSalvarItem.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        AjaxSalvarItem.setRequestHeader("Content-length", campos.length);
        AjaxSalvarItem.setRequestHeader("Connection", "close");
        AjaxSalvarItem.send(campos);
        AjaxSalvarItem.onreadystatechange = function(){
            if (AjaxSalvarItem.readyState == 4 ){
                if(AjaxSalvarItem.responseText == "inseriu"){
                    document.getElementById('div_carregando').style.display = "none";
                    document.getElementById('div_editar').style.display = "none";
                    alert(titulo +' cadastrado com êxito!');
                    //return false;
                    //document.getElementById('div_editar').style.display = "block";
                    window.location.href = location.search;
                }else if(AjaxSalvarItem.responseText == "alterou"){
                    document.getElementById('div_carregando').style.display = "none";
                    document.getElementById('div_editar').style.display = "none";
                    alert(titulo + ' alterado com êxito!');
                    window.location.href = location.search;
                }else{
                    document.getElementById('div_carregando').style.display = "none";
                    document.getElementById('div_editar').style.display = "block";
                    alert(AjaxSalvarItem.responseText);
                }
            }
        }
    }

    return false;
}

function EditarItem(id, prefixo, tabela, titulo, focus){

    document.getElementById('span_carregando_relatorio').innerHTML = "BUSCANDO "+ titulo.toUpperCase() +"<br /> AGUARDE...";
    document.getElementById('div_editar').style.display = "none";
    document.getElementById('div_busca_resultado').style.display = "none";
    document.getElementById('div_carregando').style.display = "block";

    var campos = "funcao_ajax=AjaxEditarItem&id="+id+"&prefixo="+prefixo+"&tabela="+tabela+"&titulo="+titulo+"&focus="+focus;
    var AjaxEditarItem = getAjax();
    if (AjaxEditarItem != null) {
        AjaxEditarItem.open("POST", "ajax/ajax_funcoes.php", true);
        AjaxEditarItem.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        AjaxEditarItem.setRequestHeader("Content-length", campos.length);
        AjaxEditarItem.setRequestHeader("Connection", "close");
        AjaxEditarItem.send(campos);
        AjaxEditarItem.onreadystatechange = function(){
            if (AjaxEditarItem.readyState == 4 ){
                if(AjaxEditarItem.responseXML){
                    //alert(AjaxEditarItem.responseText);
                    ProcessaXMLItem(AjaxEditarItem.responseXML, prefixo, focus);
                }else{
                    document.getElementById('div_carregando').style.display = "none";
                    document.getElementById('div_busca_resultado').style.display = "block";
                    alert(AjaxEditarItem.responseText);
                }
            }
        }
    }
    return false;
}

function EditarCampo(id, prefixo, tabela, campo, obj){
    var valor;

    if(campo == "smu_mostrar" || campo == "apl_mostrar" || campo == "pen_mostrar"){
        var imgg = obj.src;

        if(imgg.substr(imgg.length-11) == 'mostrar.jpg'){
            valor = 0;
        }else{
            valor = 1;
        }

    }else{
        valor = obj.value;
    }

    var campos = "funcao_ajax=AjaxEditarCampo&id="+id+"&prefixo="+prefixo+"&tabela="+tabela+"&campo="+campo+"&valor="+valor;
    var AjaxEditarCampo = getAjax();
    if (AjaxEditarCampo != null) {
        AjaxEditarCampo.open("POST", "ajax/ajax_funcoes.php", true);
        AjaxEditarCampo.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        AjaxEditarCampo.setRequestHeader("Content-length", campos.length);
        AjaxEditarCampo.setRequestHeader("Connection", "close");
        AjaxEditarCampo.send(campos);
        AjaxEditarCampo.onreadystatechange = function(){
            if (AjaxEditarCampo.readyState == 4 ){
                if (AjaxEditarCampo.status == 200 ){
                    if(AjaxEditarCampo.responseText == "alterou"){
                        if(tabela == "menu_submenu" || tabela == "menu_aplicacao"){
                            if(campo == "smu_mostrar" || campo == "apl_mostrar"){
                                if(valor == 0){
                                    obj.src = "imagens/icon_esconder.jpg";
                                }else{
                                    obj.src = "imagens/icon_mostrar.jpg";
                                }
                            }
                        }
                        if (campo == "pen_mostrar"){
                            window.location.href='principal.php?mainapp=controle&app=pendencia';
                        }

                    }else{
                        alert(AjaxEditarCampo.responseText);
                    }
                }
            }
        }
    }
    return false;
}

function ProcessaXMLItem(obj, prefixo, focus){

    var dataArray  = obj.getElementsByTagName("item");
    var quant = dataArray.length;
    var campo;
    var tmp;
    var objj;

    if(dataArray.length > 0){
        document.getElementById('div_carregando').style.display = "none";
        for(var i = 0 ; i < quant ; i++) {
            campo = dataArray[i];

            var c;
            var v;

            var inputs = document.getElementsByTagName("input");
            for(var i = 0; i< inputs.length; i++){
                if(inputs[i].id.substr(0,prefixo.length) == prefixo){
                    c = inputs[i].id;
                    document.getElementById(c).value = campo.getElementsByTagName(c)[0].firstChild.nodeValue;

                }
            }

            var textareas = document.getElementsByTagName("textarea");
            for(var i = 0; i< textareas.length; i++){
                if(textareas[i].id.substr(0,prefixo.length) == prefixo){
                    c = textareas[i].id;
                    document.getElementById(c).value = campo.getElementsByTagName(c)[0].firstChild.nodeValue;

                }
            }

            var spans = document.getElementsByTagName("span");
            for(var i = 0; i< spans.length; i++){
                if(spans[i].id.substr(0,prefixo.length) == prefixo){
                    c = spans[i].id;
                    document.getElementById(c).innerHTML = campo.getElementsByTagName(c)[0].firstChild.nodeValue;

                }
            }

            var selects = document.getElementsByTagName("select");
            for(var i = 0; i< selects.length; i++){
                if(selects[i].id.substr(0,prefixo.length) == prefixo){
                    objj = selects[i];
                    c = selects[i].id;
                    v = campo.getElementsByTagName(c)[0].firstChild.nodeValue;

                    if(c == "qst_questao_n1" || c == "qst_questao_n2" || c == "qst_questao_n3" || c == "qst_questao_n4"){
                        if (campo.getElementsByTagName(c)[0].firstChild.nodeValue != ""){
                            tmp = campo.getElementsByTagName(c)[0].firstChild.nodeValue.split(',');
                            for (var j=0; j<tmp.length;j++){
                                for (var k=0; k<document.getElementById('select_todas').length;k++){
                                    if(document.getElementById('select_todas').options[k].value == tmp[j]){
                                        document.getElementById('select_todas').options[k].selected = true;
                                    }
                                }
                            }
                            MoverItemsOptions('select_todas', c);

                        }

                    }else if(objj.multiple){

                        var vet_grupo 	= v.split('|');
                        var achou_opt;
                        for (var jj=0; jj< objj.length;jj++){
                            achou_opt = false;
                            for (var opt_id in vet_grupo){
                                if(objj.options[jj].value == vet_grupo[opt_id]){
                                    achou_opt = true;
                                }
                            }
                            objj.options[jj].selected = achou_opt;
                        }

                    }else{
                        document.getElementById(c).value = campo.getElementsByTagName(c)[0].firstChild.nodeValue;
                    }
                }
            }
        }
    }else{
        alert('erro');
    }
    document.getElementById('div_carregando').style.display = "none";
    document.getElementById('div_busca_resultado').style.display = "none";
    document.getElementById('div_editar').style.display = "block";
    document.getElementById(focus).focus();
}

function ExcluirItem(id, nome, tabela, campo, label){

    var confirmar = confirm("Deseja realmente excluir '" + nome + "' do SIGO?");

    if(confirmar){
        if(tabela == "usuario"){
            TrocaStatusBotoesUsuario(true);
        }

        document.getElementById('span_carregando_relatorio').innerHTML = "EXCLUINDO REGISTRO<br /> AGUARDE...";
        document.getElementById('div_busca_resultado').style.display = "none";
        document.getElementById('div_carregando').style.display = "block";

        var campos = "funcao_ajax=AjaxExcluirItem&id="+id+"&tabela="+tabela+"&campo="+campo+"&label="+label;
        var AjaxExcluirItem = getAjax();
        if (AjaxExcluirItem != null) {
            AjaxExcluirItem.open("POST", "ajax/ajax_funcoes.php", true);
            AjaxExcluirItem.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            AjaxExcluirItem.setRequestHeader("Content-length", campos.length);
            AjaxExcluirItem.setRequestHeader("Connection", "close");
            AjaxExcluirItem.send(campos);
            AjaxExcluirItem.onreadystatechange = function(){
                if (AjaxExcluirItem.readyState == 4 ){
                    if(AjaxExcluirItem.responseText == "excluiu"){
                        document.getElementById('div_carregando').style.display = "none";
                        alert(label + ' excluído(a) com êxito!');
                        if(tabela == "usuario"){
                            BuscaUsuario();
                        }else if (tabela == "grupo"){
                            window.location.href='?mainapp=cadastro&app=grupo';
                        }else{
                            window.location.href = location.search;
                        }
                    }else{
                        document.getElementById('div_carregando').style.display = "none";
                        document.getElementById('div_busca_resultado').style.display = "block";
                        if(tabela == "tbl_usuario"){
                            TrocaStatusBotoesUsuario(true);
                        }
                        alert(AjaxExcluirItem.responseText);
                    }
                }
            }
        }
    }else{
        return false;
    }
}

function SalvarPermissao(tipo_permissao, id_tipo_permissao, tipo_menu, id_tipo_menu, valor, nivel,componente){

    if(componente == "select" && document.getElementById(nivel).value > 0){
        document.getElementById(valor).checked = true;
    }else if(componente == "select" && document.getElementById(nivel).value == 0){
        document.getElementById(valor).checked = false;
    }else if(componente == "check" && ! document.getElementById(valor).checked){
        document.getElementById(nivel).value = 0;
    }

    valor = document.getElementById(valor).checked;
    nivel = document.getElementById(nivel).value;

    var campos = "funcao_ajax=AjaxSalvarPermissao&tipo_permissao="+tipo_permissao+"&id_tipo_permissao="+id_tipo_permissao+"&tipo_menu="+tipo_menu+"&id_tipo_menu="+id_tipo_menu+"&valor="+valor+"&nivel="+nivel;

    var AjaxSalvarPermissao = getAjax();
    if (AjaxSalvarPermissao != null) {
        AjaxSalvarPermissao.open("POST", "ajax/ajax_funcoes.php", true);
        AjaxSalvarPermissao.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        AjaxSalvarPermissao.setRequestHeader("Content-length", campos.length);
        AjaxSalvarPermissao.setRequestHeader("Connection", "close");
        AjaxSalvarPermissao.send(campos);
        AjaxSalvarPermissao.onreadystatechange = function(){
            if (AjaxSalvarPermissao.readyState == 4 ){
                if(AjaxSalvarPermissao.responseText != "ok"){
                    document.getElementById('div_carregando').style.display = "none";
                    document.getElementById('div_busca_resultado').style.display = "block";
                    alert(AjaxSalvarPermissao.responseText);
                }
            }
        }
    }

    return false;
}

function MoverItemsOptions(id_origem, id_destino){
    var origem = document.getElementById(id_origem);
    var destino = document.getElementById(id_destino);
    for (i=0; i< origem.length; i++){
        if(origem.options[i].selected == true){
            AdicionaOption(destino, origem.options[i].value, origem.options[i].text);
        }
    }
    for (i= origem.length-1; i>= 0; i--){
        if(origem.options[i].selected == true){
            origem.remove(i);
        }
    }
}

function LimpaSelect(sel){
    for (i= sel.length; i>= 0; i--){
        sel.remove(i);
    }
}

function AdicionaOption(sel, id, texto,selecionado){
    var opt = document.createElement('option');

    if (selecionado == null){
        selecionado = "";
    }
    opt.value = id;
    opt.text = texto;

    if (selecionado == id){
        opt.selected = true;
    }

    try {
        sel.add(opt, null); // standards compliant; doesn't work in IE
    }
    catch(ex) {
        sel.add(opt); // IE only
    }
}

function ProcessaXMLMacroArea(obj, sel, selecionado){

    var dataArray  = obj.getElementsByTagName("area");
    var quant = dataArray.length;
    var campo;
    var nome;
    var valor;
    LimpaSelect(sel);
    AdicionaOption(sel, "", "Selecione");
    if(dataArray.length > 0){
        for(var i = 0 ; i < quant ; i++) {
            campo = dataArray[i];
            nome  = campo.getElementsByTagName('nome')[0].firstChild.nodeValue;
            valor  = campo.getElementsByTagName('valor')[0].firstChild.nodeValue;
            AdicionaOption(sel, valor, nome, selecionado);
        }
    }
}

function MostraCarregandoRelatorio(){
    document.getElementById('div_carregando').style.display='block';
    document.getElementById('div_relatorio').style.display='none';
}

function BuscaArea(pc,banco, tabela, campo_busca, campo_where, where_valor, sel_destino, caminho_raiz, proximos_select, selecionado, campo_valor){

    // pc
    // banco
    // tabela
    // campo_busca 		= indice do option (valor visual)
    // campo_where 		= campo de comparação WHERE
    // where_valor 		= valor de comparação
    // sel_destino 		= select de destino
    // caminho_raiz		= caminho raiz para referenciar o ajax q se chama
    // proximos_select  = select q recebe o resultado
    // selecionado 		= recebe selected
    // campo_valor 		= value option

    var sel = document.getElementById(sel_destino);
    var sel_tmp = proximos_select.split("|");
    var tamanho = sel_tmp.length;

    if (selecionado == null){
        selecionado = '';
    }
    if (campo_valor == null){
        campo_valor = '';
    }

    for (var i=0;i<tamanho;i++){
        LimpaSelect(document.getElementById(sel_tmp[i]));
        AdicionaOption(document.getElementById(sel_tmp[i]), "", "Selecione");
        document.getElementById(sel_tmp[i]).disabled = true;
    }

    if (where_valor != "" && where_valor != "TODOS" && where_valor != "Selecione"){

        sel.disabled = true;
        LimpaSelect(sel);
        AdicionaOption(sel, "", "Carregando");
        var campos = "funcao_ajax=AjaxBuscaArea&tabela="+tabela+"&campo_busca="+campo_busca+"&campo_where="+campo_where+"&pc="+pc+"&banco="+banco+"&where_valor="+where_valor+"&campo_valor="+campo_valor;


        var AjaxBuscaArea = getAjax();
        if (AjaxBuscaArea != null) {
            AjaxBuscaArea.open("POST", caminho_raiz+"ajax/ajax_funcoes.php", true);
            AjaxBuscaArea.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            AjaxBuscaArea.setRequestHeader("Content-length", campos.length);
            AjaxBuscaArea.setRequestHeader("Connection", "close");
            AjaxBuscaArea.send(campos);
            AjaxBuscaArea.onreadystatechange = function(){
                if (AjaxBuscaArea.readyState == 4 ){
                    if (AjaxBuscaArea.status == 200)
                    {
                        if(AjaxBuscaArea.responseXML){
                            ProcessaXMLMacroArea(AjaxBuscaArea.responseXML, sel, selecionado);
                            sel.disabled = false;
                        }else{
                            alert(AjaxBuscaArea.responseText);
                        }
                    }
                }
            }
        }
    }else{

}
}

function BuscaSupervisor(pc,banco, tabela, campo_busca, campo_where, where_valor, sel_destino, caminho_raiz, proximos_select, selecionado, campo_valor){

    // pc
    // banco
    // tabela
    // campo_busca 		= indice do option (valor visual)
    // campo_where 		= campo de comparação WHERE
    // where_valor 		= valor de comparação
    // sel_destino 		= select de destino
    // caminho_raiz		= caminho raiz para referenciar o ajax q se chama
    // proximos_select  = select q recebe o resultado
    // selecionado 		= recebe selected
    // campo_valor 		= value option
  
    var sel = document.getElementById(sel_destino);
    var sel_tmp = proximos_select.split("|");
    var tamanho = sel_tmp.length;

    if (selecionado == null){
        selecionado = '';
    }
    if (campo_valor == null){
        campo_valor = '';
    }
  
    for (var i=0;i<tamanho;i++){
        LimpaSelect(document.getElementById(sel_tmp[i]));
        AdicionaOption(document.getElementById(sel_tmp[i]), "", "Selecione");
        document.getElementById(sel_tmp[i]).disabled = true;
        
    }

    if (where_valor != "" && where_valor != "TODOS" && where_valor != "Selecione"){

        sel.disabled = true;
        LimpaSelect(sel);
        AdicionaOption(sel, "", "Carregando");

        var campos = "funcao_ajax=AjaxBuscaSupervisor&tabela="+tabela+"&campo_busca="+campo_busca+"&campo_where="+campo_where+"&pc="+pc+"&banco="+banco+"&where_valor="+where_valor+"&campo_valor="+campo_valor;
        

        var AjaxBuscaArea = getAjax();
        if (AjaxBuscaArea != null) {
            AjaxBuscaArea.open("POST", caminho_raiz+"ajax/ajax_funcoes.php", true);
            AjaxBuscaArea.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            AjaxBuscaArea.setRequestHeader("Content-length", campos.length);
            AjaxBuscaArea.setRequestHeader("Connection", "close");
            AjaxBuscaArea.send(campos);
            AjaxBuscaArea.onreadystatechange = function(){
                if (AjaxBuscaArea.readyState == 4 ){
                    if (AjaxBuscaArea.status == 200)
                    {
                        if(AjaxBuscaArea.responseXML){
                            ProcessaXMLMacroArea(AjaxBuscaArea.responseXML, sel, selecionado);
                            sel.disabled = false;
                        }else{
                            alert(AjaxBuscaArea.responseText);
                        }
                    }
                }
            }
        }
    }else{

}
}

/*
* @flag_mostra 	-> pode receber tipo boleano(true/false), neste caso será necessário inserir os eventos onmouseover e onmouseout.
*				-> pode receber o objeto em questão Ex. onmouseover="ToolTip('Mensagem', event, this);", não é necessário onmouseout.
*/
function ToolTip(mensagem, evento, flag_mostra){ // Função para Criar ToolTips(Dicas na Tela) para visualização dos Usuários.
    evento = (window.event)? window.event : evento;
    var campo;

    if (typeof(flag_mostra) != "boolean" || flag_mostra == true) { // Verifica se o ToolTip que está sendo invocado é da versão 1 ou 2. boolean = versão 1, object = versão 2.
        if(typeof(flag_mostra) != "boolean"){ // Versão 2 incorporada a versão 1.
            campo	= flag_mostra;

            campo.onmouseout = function(){
                try {
                    document.body.removeChild(document.getElementById('tooltip'));
                } catch(ex){
                // ToolTip inexistente.
                }
            }

            campo.onmousemove = function(e){ // Para Acompanhar os movimentos do mouse.
                try{
                    var tool = document.getElementById('tooltip'); // Captura o objeto na tela.
                    var evento = (window.event)? event : e;

                    var posx = evento.clientX + document.body.scrollLeft + 10;
                    var posy = evento.clientY + document.body.scrollTop + 10;

                    if((document.body.clientHeight + document.body.scrollTop - 30) < posy){
                        posy -= 30;
                        posx += 5;
                    }
                    tool.style.top = posy+"px";  // Definindo as próximas posições.
                    tool.style.left = posx+"px";
                } catch (ex){
                // Não conseguiu achar o objeto.
                }
            } // Fim do acompanhamento dos movimentos do mouse.
            campo.setAttribute('title', ''); // Limpa a label Title do campo.
            campo.setAttribute('alt', ''); // Limpa a label alt do campo.
        }

        var sugest = document.createElement('div');
        sugest.id = 'tooltip';
        with (sugest.style) {
            width 		= '180px';
            backgroundColor = "#ffffaa";
            position 	= "absolute";
            border 		= "1px solid #aaaa00";
            fontSize 	= "12px";
            fontFamily 	= "Verdana";
            zIndex 		= "5000";
            }
        sugest.innerHTML = mensagem;

        var posx = evento.clientX + document.body.scrollLeft + 10;
        var posy = evento.clientY + document.body.scrollTop + 10;
        if ((document.body.clientHeight + document.body.scrollTop - 30) < posy) {
            posy -= 30;
            posx += 5;
        }

        sugest.style.top = posy + "px";
        sugest.style.left = posx + "px";
        document.body.appendChild(sugest);
    } else {
        try {
            document.body.removeChild(document.getElementById('tooltip'));
        }
        catch (ex) {
        // O ToolTip ainda não foi criado.
        }
    }
}

function ToolTip2(msg, evento, campo){
    ToolTip(msg, evento, campo);
}

function Janela(larg, alt, url, titulo, raiz, mostrar_fechar, acao_apos_fechar, funcao_acao_apos_fechar){
    raiz = (raiz == undefined)? '' : raiz; // Define o caminho raiz.
    
    var altura_janela = (document.documentElement && document.documentElement.scrollTop) || 
              document.body.scrollTop;

    

    if (mostrar_fechar == null) {
        mostrar_fechar = "sim";
    }

    var trava = document.createElement("div");
    trava.id = "trava_janela";
    //document.body.style.margin = "0";
    //document.body.style.padding = "0";
    with(trava.style){
        height = (document.body.scrollHeight > document.body.offsetHeight)? document.body.scrollHeight : document.body.offsetHeight;
        width = (document.body.scrollWidth > document.body.offsetWidth) ? document.body.scrollWidth : document.body.offsetWidth;
        }

    var janela = document.createElement("div");
    janela.id = "janela_frame";
    with(janela.style){
        width = larg + "px";
        height = alt + "px";
        border = "1px solid #000";
        backgroundColor = "#ffffff";
        position = "absolute";
        zIndex = '501';
        top = (80 + altura_janela) + "px";
        left = ((parseInt(document.body.offsetWidth / 2, 10) - parseInt(larg / 2, 10)) + document.body.scrollLeft) + "px";
        }
    if(document.all){
        var tit = document.createElement("<div style='float:left;' >");
    } else {
        var tit = document.createElement("div");
        tit.setAttribute("style", "float:left;");
    }
    with(tit.style){
        width = (larg - 50)+"px";
        height = '25px';
        fontWeight = "normal";
        fontSize = "13px";
        padding = "5px 0 0 7px";
        }
    tit.innerHTML = titulo;

    var cima = document.createElement("div");
    cima.id = 'cabecalho_janela';
    with(cima.style){
        backgroundColor = "#bdf";
        width = larg + "px";
        height = '25px';
        fontFamily = "Verdana";
        fontWeight = "bold";
        borderBottom = "1px solid #777777";
        }

    if(document.all){

        var botao = document.createElement("<div style='float:right;' >");
    } else {
        var botao = document.createElement("div");
        botao.setAttribute("style", "float:right;");
    }
    with(botao.style){
        width = "25px";
        height = "25px";
        }
    botao.id = 'botao_fechar';
    botao.onclick = function(){
        try{
            document.body.removeChild(document.getElementById('trava_janela'));
        } catch(ex){
            alert(ex)
            }
        try{
            document.body.removeChild(document.getElementById('janela_frame'));
        } catch(ex){
            alert(ex.description);
        }
        if(acao_apos_fechar == 'reload'){
            top.location.replace(top.location.href);
        }else{
            if(acao_apos_fechar == 'reload2'){
                window.location.href = window.location.href;
            }else{
                if(acao_apos_fechar == 'funcaoJS'){
                    eval(funcao_acao_apos_fechar);
                }
            }
        }
    }

    if (document.all) {
        var iframe = document.createElement('<iframe frameborder="0" name="iframe_janela" >');
    } else {
        var iframe = document.createElement("iframe");
        iframe.setAttribute("frameborder", '0');
        iframe.name = 'iframe_janela';
    }
    iframe.id = "iframe_janela";

    with(iframe.style){
        width = (larg - 20) + "px";
        height = (alt - 35) + "px";
        marginLeft = "10px";
        marginRight = "10px";
        }
    iframe.src = url;

    document.body.appendChild(trava);
    cima.appendChild(tit);

    if (mostrar_fechar == "nao"){
        botao.style.display = "none";
    }
    cima.appendChild(botao);
    janela.appendChild(cima);
    janela.appendChild(iframe);
    document.body.appendChild(janela);
    trava.style.display = 'block';
    /*	trava.style.height	= '19146px';
	trava.style.width	= '1693px';*/
    trava.setAttribute("background-color","#ffffff");

    this.close = function(){ // Função para fechar a janela.
        try{
            document.body.removeChild(trava);
            document.body.removeChild(janela);
        } catch(ex){
        // Janela Inexistente.
        }
    }

    this.fechar = function(){ // Alias da Função para fechar a janela.
        this.close();
    }

    this.resize = function(largura, altura){
        janela.style.width = largura+"px";
        janela.style.height = altura+"px";
    }

    this.redimensionar = function(largura, altura){ // Alias da Função Resize.
        this.resize(largura, altura);
    }
}

function FecharJanela(){
    this.close = function(){ // Função para fechar a janela.
        try{
            document.body.removeChild(trava);
            document.body.removeChild(janela);
        } catch(ex){
        // Janela Inexistente.
        }
    }

    this.fechar = function(){ // Alias da Função para fechar a janela.
        this.close();
    }
}

function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');

    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        hash[1] = unescape(hash[1]);
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }

    return vars;
}

function AlterarCorTrs(){
    var trs = document.getElementsByTagName("tr");

    for (i=0;i<trs.length;i++){

        if (trs[i].className == "alterar_cor_branco"){
            trs[i].onmouseover = function (){
                this.className = "selecionada_tr";
            }

            trs[i].onmouseout = function (){
                this.className = "alterar_cor_branco";
            }
        }

        if (trs[i].className == "alterar_cor_cinza"){
            trs[i].onmouseover = function (){
                this.className = "selecionada_tr";
            }

            trs[i].onmouseout = function (){
                this.className = "alterar_cor_cinza";
            }
        }
    }

}

/* Função para Requisição Ajax.
 * @param funcao -> função passada por referência onde o ReqAjax disparará o retorno do Ajax com o Objeto.
 * @param campos -> parâmetros POST para serem repassados para o PHP.
 * @param caminho -> caminho de indicação da pasta onde se localiza as funções ajax_funcoes.php
 * @param tentativa -> parâmetro de tentativas de Requisições realizadas pelo Ajax.
 */
function ReqAjax(funcao, campos, tentativa, caminho){
    if (caminho == null){
        caminho = '';
    }
    if (tentativa == null){
        tentativa = 0;
    }
    if (tentativa < 6) { // Número máximo de 6 tentativas.
        var tempo, quebra = Math.random();
        var ObjAjax = getAjax();
        if (ObjAjax != null) {
            ObjAjax.open("POST", caminho + "ajax/ajax_funcoes.php?quebra=" + quebra, true);
            ObjAjax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            ObjAjax.setRequestHeader("Content-length", campos.length);
            ObjAjax.setRequestHeader("Connection", "close");
            ObjAjax.send(campos);
            ObjAjax.onreadystatechange = function(){
                if (ObjAjax.readyState == 1) {
                    tempo = window.setTimeout(function(){
                        ObjAjax.abort();
                        ReqAjax(funcao, campos, tentativa++, caminho);
                    }, 6000);
                } else {
                    try {
                        clearTimeout(tempo);
                    }
                    catch (ex) {
                    // Limpando o delay.
                    }
                    if (ObjAjax.readyState == 4 && ObjAjax.status == 200 && ObjAjax.responseText) { // Verifica se alcançou o status de Pronto.
                        funcao(ObjAjax);  // Chama a Função passada por referência(1º Parâmetro).
                    } else if (ObjAjax.readyState == 4 && ObjAjax.status != 200) {
                        window.setTimeout(function(){
                            ObjAjax.abort();
                            ReqAjax(funcao, campos, tentativa++, caminho);
                        }, 600);
                    }
                }
            }
        }
    } else { // Se já tentou 6 vezes informa o Erro e cancela a operação.
        ObjAjax.abort();
        try {
            clearTimeout(tempo);
        }
        catch (ex) {
        // Limpando o delay.
        }
        window.alert("Aconteceu um erro inesperado.\r\nTente novamente em alguns minutos se caso o erro persistir contacte a DGE.");
        return false;
    }
}

//Máscara para hora. Porém aceita qualquer valor até 99:99.
function Mascara_Cronometro(tempo)
{
    if(event.keyCode<48 || event.keyCode>57)
    {
        event.returnValue=false;
    }

    if(tempo.value.length==2)
    {
        tempo.value+=":";
    }
}

if(window.addEventListener){ // Adiciona os eventos de janela pronta.
    window.addEventListener("load", TelaPronta, false); // Navegadores exceto IE.
} else {
    window.attachEvent("onload", TelaPronta); // IE 5+.
}

function TelaPronta(){ // Função que será Disparada quando a janela estiver no status complete.
    validadorFormulario(); // Ativa a Validação de Formulário.
}

/*
 * Função para Validação de Formulários.
 * @author Jorge Luiz
 * Diretoria de Gestão Empresarial(DGE) - Matriz - 16/03/2010
 * Atualização e inclusão da validação de INPUTS tipo RADIO - Jorge Luiz - 14/04/2010
 *
 * ---- Formulário Atributos -----
 * validar  	-> Define se o Formulário será Validado (true / false).
 * msg 			-> Mensagem a ser exibida na validação.
 * nome_campos 	-> Define se Exibirá os nomes dos campos na mensagem (true / false).
 *
 * ------ Campos Atributos -------
 * nome_lab		-> Nome Label ao qual será exibido na mensagem.
 * esc			-> Define se o campo será ignorado pela validação (true / false).
 *
 */
function validadorFormulario(){
    var formus = document.getElementsByTagName('form');
    var radios = new Array(); // Guardará os nomes dos radios;

    for(var i = 0; i < formus.length; i++){
        if(formus[i].getAttribute('validar') == 'true'){
            formus[i].onsubmit = function(){
                var msg = "";
                var msg_form = this.getAttribute('msg');
                var inputs = document.getElementsByTagName('input');
                var selects= document.getElementsByTagName('select');
                var radios = new Array(), a = 0;
                var maior  = (selects.length > inputs.length)? selects.length : inputs.length; // Captura o que possui maior quantidade.
                for(var j = 0; j < maior; j++){
                    try {
                        if((inputs[j].type == 'radio') && (inputs[j].name != inputs[((j == 0)? 0 : (j - 1))].name) && (inputs[j].getAttribute('esc') != 'true')){
                            radios[a] = inputs[j].name;
                            a++;
                        }
                        if (inputs[j].getAttribute('esc') != 'true' && inputs[j].getAttribute('type') == 'text' && inputs[j].value == '') {
                            if (inputs[j].form.getAttribute('nome_campos') == 'true') {
                                msg += "- " + inputs[j].getAttribute('nome_lab')+"<br />";
                            } else {
                                msg = "erro";
                            }
                            inputs[j].style.backgroundColor = '#FFFF99'; // Altera a Cor do Input.
                        } else {
                            inputs[j].style.backgroundColor = ''; // Altera a Cor do Input.
                        }
                    } catch(ex){}
                    try {
                        if (selects[j].getAttribute('esc') != 'true' && selects[j].value == '') {
                            if (selects[j].form.getAttribute('nome_campos') == 'true') {
                                msg += "- " + selects[j].getAttribute('nome_lab')+"<br />";
                            } else {
                                msg = "erro";
                            }
                            selects[j].style.backgroundColor = '#FFFF99'; // Altera a Cor do Select.
                        } else {
                            selects[j].style.backgroundColor = ''; // Altera a Cor do Select.
                        }
                    } catch(ex){}
                }
                var tmp_msg = "";
                for(r in radios){
                    var rads = document.getElementsByName(radios[r]);
                    tmp_msg = "- " + rads[0].getAttribute('nome_lab') + "<br />";
                    for(var i = 0; i < rads.length; i++){
                        if(rads[i].checked){
                            tmp_msg = "";
                            break;
                        }
                    }
                    msg += tmp_msg;
                }
                if (msg.length > 0) { // Verifica se a mensagem está diferente de vazia.
                    msg = msg_form + "<br /><br />" + ((this.getAttribute('nome_campos') == 'true')? msg : '');
                    var altu_jan = ((document.all) ? document.body.offsetHeight : window.innerHeight); // Captura o Tamanho da Janela.
                    var altu_cont = document.getElementById('div_geral').offsetHeight; // Captura o tamanho do conteúdo.
                    if (altu_cont < (altu_jan - 150)) { // Verifica se há espaço para o aviso.
                        try {
                            document.getElementById('div_geral').removeChild(document.getElementById('div_aviso')); // Remove o aviso anterior.
                        }
                        catch (ex) {}
                        var div_aviso = document.createElement('div');
                        div_aviso.id = "div_aviso";
                        with (div_aviso) {
                            className = "ui-state-highlight ui-corner-all";
                            style.marginTop = "20px";
                            style.padding = "0 .7em";
                            style.font = "12px Verdana, Geneva, sans-serif";
                            align = "left";
                            }
                        var span_aviso = document.createElement('span');
                        with (span_aviso) {
                            className = "ui-icon-info";
                            style.marginRight = ".3em";
                            }
                        span_aviso.innerHTML = msg;
                        div_aviso.appendChild(span_aviso);
                        document.getElementById('div_geral').appendChild(div_aviso);
                    }
                    else { // Não possui espaço para o aviso.
                        janela_aviso(msg);
                    }
                    return false;
                } else {
                    return true;
                }
            }
        }
    }
}

function janela_aviso(msg){
    var trava = document.createElement("div");
    trava.id = "trava_janela";
    document.body.style.margin = "0";
    document.body.style.padding = "0";
    with(trava.style){
        height = (document.body.scrollHeight > document.body.offsetHeight)? document.body.scrollHeight : document.body.offsetHeight;
        width = (document.body.scrollWidth > document.body.offsetWidth) ? document.body.scrollWidth : document.body.offsetWidth;
        display = '';
        }

    var janela = document.createElement("div");
    janela.id = "janela_frame";
    with (janela.style) {
        width = "500px";
        font = "13px Verdana";
        border = "1px solid #000";
        backgroundColor = "#dfefff";
        paddingBottom = "10px";
        position = "absolute";
        color = "#660000";
        zIndex = '501';
        top = (250 + document.body.scrollTop) + "px";
        left = (Math.ceil((document.body.offsetWidth / 2)-250) + document.body.scrollLeft) + "px";
        }

    if (document.all) {
        var botao = document.createElement("<div style='float:right;' >");
    } else {
        var botao = document.createElement("div");
        botao.setAttribute("style", "float:right;");
    }
    with(botao.style){
        width = "25px";
        height = "25px";
        }
    botao.id = 'botao_fechar';

    if(document.all){
        var tit = document.createElement("<div style='float:left;' >");
    } else {
        var tit = document.createElement("div");
        tit.setAttribute("style", "float:left;");
    }
    with(tit.style){
        width = "450px";
        height = '25px';
        fontWeight = "normal";
        fontSize = "14px";
        padding = "3px 0 0 7px";
        fontWeight = "bold";
        }
    tit.innerHTML = "Aten&ccedil;&atilde;o !";

    var cima = document.createElement("div");
    with(cima.style){
        backgroundColor = "#bdf";
        width = "500px";
        height = '25px';
        textAlign = "left";
        fontFamily = "Verdana";
        fontWeight = "bold";
        borderBottom = "1px solid #777777";
        marginBottom = "10px";
        }
    var baixo = document.createElement("div");
    baixo.style.width = "500px";
    baixo.style.marginTop = "15px";
    var bot = document.createElement("button");
    bot.innerHTML = "Fechar";
    bot.id = "bot_fechar";
    baixo.appendChild(bot);

    cima.appendChild(tit);
    cima.appendChild(botao);
    janela.appendChild(cima);

    janela.innerHTML += msg;
    janela.appendChild(baixo);

    document.body.appendChild(trava);
    document.body.appendChild(janela);

    trava.style.display = 'block';
    bot.focus();

    document.getElementById('botao_fechar').onclick = function(){
        document.body.removeChild(trava);
        document.body.removeChild(janela);
    }
    document.getElementById('bot_fechar').onclick = function(){
        document.body.removeChild(trava);
        document.body.removeChild(janela);
    }

    this.fechar = function(){
        document.body.removeChild(trava);
        document.body.removeChild(janela);
    }
}

/**
 *
 * Esta função submete um formulário qualquer passado como parametro em um evento qualquer,
 * ou seja, pode ser chamada em qualquer evento.
 *
 * Desenvolvedor Walter Thiago
 * Data 20/09/2010
 *
 * @param String formName - nome do formulário que será submetido em um evento qualquer
 * @return void
 */
function submitForm(formName){
    document.forms[formName].submit();
}


/**
 * Implementações das funções Trim, LTrim e RTrim, replaceAll em javascript.
 * Desenvolvedor Walter Thiago
 * Data 20/09/2010
 */

//-------------------------------------------------
//Remove espacos em branco na direita e na esquerda de uma string
String.prototype.trim = function() {
    return this.replace(/^\s+|\s+$/g, '');
}

//Remove espacos em branco à esquerda de uma string
String.prototype.ltrim = function() {
    return this.replace(/^\s+/, '');
}

//Remove espacos em branco à direita de uma string
String.prototype.rtrim = function() {
    return this.replace(/\s+$/, '');
}

String.prototype.replaceAll = function(de, para){
    var str = this;
    var pos = str.indexOf(de);
    while (pos > -1){
        str = str.replace(de, para);
        pos = str.indexOf(de);
    }
    return (str);
}


//-------------------------------------------------

/**
 * Função para validação de cpf.
 * Recebe por parametro o id do campo.
 * Retorna verdadeiro se o cpf for valido ou falso caso contrario.
 *
 * Desenvolvedor Walter Thiago
 * Data 20/09/2010
 *
 */

function validaCPF(idCampoCPF) {

    // Recebe o valor digitado no campo
    var CPF = document.getElementById(idCampoCPF).value;

    // Verifica se o campo é nulo
    if (CPF.trim() == '') {
        return false;
    }

    // Aqui começa a checagem do CPF
    var POSICAO, I, SOMA, DV, DV_INFORMADO;
    var DIGITO = new Array(10);

    // Retira os dois últimos dígitos do número informado
    DV_INFORMADO = CPF.substr(9, 2);

    // Desemembra o número do CPF na array DIGITO
    for (I=0; I<=8; I++) {
        DIGITO[I] = CPF.substr( I, 1);
    }

    // Calcula o valor do 10º dígito da verificação
    POSICAO = 10;
    SOMA = 0;
    for (I=0; I<=8; I++) {
        SOMA = SOMA + DIGITO[I] * POSICAO;
        POSICAO = POSICAO - 1;
    }

    DIGITO[9] = SOMA % 11;
    if (DIGITO[9] < 2) {
        DIGITO[9] = 0;
    }else{
        DIGITO[9] = 11 - DIGITO[9];
    }

    // Calcula o valor do 11º dígito da verificação
    POSICAO = 11;
    SOMA = 0;
    for (I=0; I<=9; I++) {
        SOMA = SOMA + DIGITO[I] * POSICAO;
        POSICAO = POSICAO - 1;
    }

    DIGITO[10] = SOMA % 11;
    if (DIGITO[10] < 2) {
        DIGITO[10] = 0;
    }else{
        DIGITO[10] = 11 - DIGITO[10];
    }

    // Verifica se os valores dos dígitos verificadores conferem
    DV = DIGITO[9] * 10 + DIGITO[10];
    if (DV != DV_INFORMADO) {
        return false;
    }

    return true;

}

//Walter Thiago A. Reis - 30/11/2010
function refresh(url){
    window.location.href = url;
}

//	Walter Thiago A. Reis - 30/11/2010
function retornaMesExtenso(month){
    switch(month){
        case '1':
        case '01':
            return 'Janeiro';
            break;
        case '2':
        case '02':
            return 'Fevereiro';
            break;
        case '3':
        case '03':
            return 'Março';
            break;
        case '4':
        case '04':
            return 'Abril';
            break;
        case '5':
        case '05':
            return 'Maio';
            break;
        case '6':
        case '06':
            return 'Junho';
            break;
        case '7':
        case '07':
            return 'Julho';
            break;
        case '8':
        case '08':
            return 'Agosto';
            break;
        case '9':
        case '09':
            return 'Setembro';
            break;
        case '10':
            return 'Outubro';
            break;
        case '11':
            return 'Novembro';
            break;
        case '12':
            return 'Dezembro';
            break;
        default:
            return 'Mes desconhecido';
            break;
    }
}

//Bruno Macedo Tertuliano - 15/04/2011
function retornaDiaSemana($diaSemana){
    switch($diaSemana) {
        case 0:
            $diaSemana = "Domingo";
            break;
        case 1:
            $diaSemana = "Segunda-Feira";
            break;
        case 2:
            $diaSemana = "Terça-Feira";
            break;
        case 3:
            $diaSemana = "Quarta-Feira";
            break;
        case 4:
            $diaSemana = "Quinta-Feira";
            break;
        case 5:
            $diaSemana = "Sexta-Feira";
            break;
        case 6:
            $diaSemana = "Sábado";
            break;
    }

    return $diaSemana;
}

//Walter Thiago A. Reis - 27/01/2011
function urlencode( str ) {

    var histogram = {}, tmp_arr = [];
    var ret = (str+'').toString();

    var replacer = function(search, replace, str) {
        var tmp_arr = [];
        tmp_arr = str.split(search);
        return tmp_arr.join(replace);
    };

    // The histogram is identical to the one in urldecode.
    histogram["'"]   = '%27';
    histogram['(']   = '%28';
    histogram[')']   = '%29';
    histogram['*']   = '%2A';
    histogram['~']   = '%7E';
    histogram['!']   = '%21';
    histogram['%20'] = '+';
    histogram['\u20AC'] = '%80';
    histogram['\u0081'] = '%81';
    histogram['\u201A'] = '%82';
    histogram['\u0192'] = '%83';
    histogram['\u201E'] = '%84';
    histogram['\u2026'] = '%85';
    histogram['\u2020'] = '%86';
    histogram['\u2021'] = '%87';
    histogram['\u02C6'] = '%88';
    histogram['\u2030'] = '%89';
    histogram['\u0160'] = '%8A';
    histogram['\u2039'] = '%8B';
    histogram['\u0152'] = '%8C';
    histogram['\u008D'] = '%8D';
    histogram['\u017D'] = '%8E';
    histogram['\u008F'] = '%8F';
    histogram['\u0090'] = '%90';
    histogram['\u2018'] = '%91';
    histogram['\u2019'] = '%92';
    histogram['\u201C'] = '%93';
    histogram['\u201D'] = '%94';
    histogram['\u2022'] = '%95';
    histogram['\u2013'] = '%96';
    histogram['\u2014'] = '%97';
    histogram['\u02DC'] = '%98';
    histogram['\u2122'] = '%99';
    histogram['\u0161'] = '%9A';
    histogram['\u203A'] = '%9B';
    histogram['\u0153'] = '%9C';
    histogram['\u009D'] = '%9D';
    histogram['\u017E'] = '%9E';
    histogram['\u0178'] = '%9F';

    // Begin with encodeURIComponent, which most resembles PHP's encoding functions
    ret = encodeURIComponent(ret);

    for (search in histogram) {
        replace = histogram[search];
        ret = replacer(search, replace, ret) // Custom replace. No regexing
    }

    // Uppercase for full PHP compatibility
    return ret.replace(/(\%([a-z0-9]{2}))/g, function(full, m1, m2) {
        return "%"+m2.toUpperCase();
    });

    return ret;
}


function urldecode( str ) {

    var histogram = {};
    var ret = str.toString();

    var replacer = function(search, replace, str) {
        var tmp_arr = [];
        tmp_arr = str.split(search);
        return tmp_arr.join(replace);
    };

    // The histogram is identical to the one in urlencode.
    histogram["'"]   = '%27';
    histogram['(']   = '%28';
    histogram[')']   = '%29';
    histogram['*']   = '%2A';
    histogram['~']   = '%7E';
    histogram['!']   = '%21';
    histogram['%20'] = '+';
    histogram['\u20AC'] = '%80';
    histogram['\u0081'] = '%81';
    histogram['\u201A'] = '%82';
    histogram['\u0192'] = '%83';
    histogram['\u201E'] = '%84';
    histogram['\u2026'] = '%85';
    histogram['\u2020'] = '%86';
    histogram['\u2021'] = '%87';
    histogram['\u02C6'] = '%88';
    histogram['\u2030'] = '%89';
    histogram['\u0160'] = '%8A';
    histogram['\u2039'] = '%8B';
    histogram['\u0152'] = '%8C';
    histogram['\u008D'] = '%8D';
    histogram['\u017D'] = '%8E';
    histogram['\u008F'] = '%8F';
    histogram['\u0090'] = '%90';
    histogram['\u2018'] = '%91';
    histogram['\u2019'] = '%92';
    histogram['\u201C'] = '%93';
    histogram['\u201D'] = '%94';
    histogram['\u2022'] = '%95';
    histogram['\u2013'] = '%96';
    histogram['\u2014'] = '%97';
    histogram['\u02DC'] = '%98';
    histogram['\u2122'] = '%99';
    histogram['\u0161'] = '%9A';
    histogram['\u203A'] = '%9B';
    histogram['\u0153'] = '%9C';
    histogram['\u009D'] = '%9D';
    histogram['\u017E'] = '%9E';
    histogram['\u0178'] = '%9F';

    for (replace in histogram) {
        search = histogram[replace]; // Switch order when decoding
        ret = replacer(search, replace, ret) // Custom replace. No regexing
    }

    // End with decodeURIComponent, which most resembles PHP's encoding functions
    ret = decodeURIComponent(ret);

    return ret;
}

/*
 * Função para Validação de Formulários.
 * @author Lud Akell
 * Diretoria de Gestão Empresarial(DGE) - Matriz - 25/02/2011
 * Menos robusta que a função validadorFormulario, porém mais objetiva
 *
 * ---- Observações -----
 * Não funciona para “textareas” e outras tags mais raras que não inputs e selects. Para essas, procure usar a 'validadorFormulario'.
 * O valor passado no atributo de validação deve ser obrigatoriamente “true”. (Ex input validar=true)
 * O parâmetro passado na chamada da função deve ser obrigatoriamente o nome do atributo escolhido (Ex: ValidaFormSimples('validar'); )
 *
 */

function ValidaFormSimples(tipo)
{
    // Variavel que decide se a div erro vai ser exibida ou não
    var erro = true;

    var inputs = document.getElementsByTagName('input');

    for(var i=0; i < inputs.length; i++)
    {

        if(inputs[i].getAttribute(tipo) == "true" && inputs[i].value == "")
        {
            inputs[i].style.backgroundColor = '#FFFF80';
            inputs[i].style.border = '1px red solid';

            erro = false;
        }
        else
        {
            inputs[i].style.backgroundColor = '';
            inputs[i].style.borderColor = '';
        }
    }

    var selects = document.getElementsByTagName('select');

    for(var i=0; i < selects.length; i++)
    {
        if(selects[i].getAttribute(tipo) == "true" && selects[i].value == "")
        {
            selects[i].style.background = '#FFFF80';
            selects[i].style.border = '1px red solid';

            erro = false;
        }
        else
        {
            selects[i].style.backgroundColor = '';
            selects[i].style.borderColor = '';
        }
    }

    if(erro == false)
    {
        msg = "Favor preencher os campos destacados";
        alert(msg);
    }

    return erro;
}

//Pega a data inserida pelo usuário e a transforma no formato yyyymmdd (sem caractere separando)
function AlteraFormatoData(data)
{
    var partes = data.split("/");
    var dia = partes[0];
    var mes = partes[1];
    var ano = partes[2];

    return ano+mes+dia;
}

function fecha_janela(funcao){
	
    var trava = document.getElementById('trava_janela');
    var janela = document.getElementById('janela_frame');
	
    try{
        document.body.removeChild(trava);
        document.body.removeChild(janela);
    }catch(e){
		
    }
	
    if(funcao != null && funcao != '' && funcao != undefined && typeof(funcao) == 'string'){
        eval(funcao);
    }else if (typeof(funcao) == 'function'){
        funcao();
    }

}

function ajaxEnviarErro(mensagem_erro,caminho_raiz){
	
    var ajax = getAjax();
	
    var campos = "funcao_ajax=AjaxEnviarEmailErro&msg="+escape(mensagem_erro);
	
    if (ajax != null) {
        ajax.open("POST", caminho_raiz+"ajax/ajax_funcoes.php", true);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.setRequestHeader("Content-length", campos.length);
        ajax.setRequestHeader("Connection", "close");
        ajax.send(campos);
        ajax.onreadystatechange = function(){
            if (ajax.readyState == 4 ){
                if (ajax.status == 200){
                    if(ajax.responseText.trim() != ''){
                        alert('Erro: ' + ajax.responseText);
                    }
                }else{
                    alert('Erro: ' + ajax.status);
                }
            }
        }
    }else{
        alert('Erro ao instanciar o objeto ajax. Tente novamente e caso o erro persista entre em contato com a DGE.');
    }
}

function FormataNumDecimalParaBD(numero)
{
    if(numero != "" && numero != 0)
        return parseFloat(numero.replace(",", "." )); 
    else
        return 0;
}