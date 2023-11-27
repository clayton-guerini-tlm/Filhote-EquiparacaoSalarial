function trim(str){
	try{
		return str.replace(/(^\s+)|(\s+$)/g,"");
	} catch(ex){
		return '';
	}
}

function vazio(variavel){
	return ((trim(variavel) == '')? true: false);
}

function SoNumeros(e){  // Define o campo para aceitar somente números inteiros.
	
	var tecla=new Number();
	
	if(window.event){
		tecla = event.keyCode;
	} else if(e.which){
		tecla = e.which;
	}

	var cola = atalho.getAtalho();
	
	//alert(tecla);
	
	if(((tecla < 48) || (tecla > 57)) && (tecla!=44 && tecla!=46 && tecla!=8 && tecla!=0 && tecla != 13) && cola != 'Ctrl+C' && cola != 'Ctrl+V' && cola != 'Ctrl+X' && cola != 'Ctrl+T'){
		return false;
	} else {
		return true;
	}
}

function SoNumerosFlutuantes(e, valor){   // Define o campo para aceitar somente números Flutuantes com vírgula. 
	var tecla=new Number();
	if(window.event) {
		tecla = event.keyCode;
	} else if(e.which) {
		tecla = e.which;
	}

	var cola = atalho.getAtalho();
	if(((tecla < 48) || (tecla > 57)) && (tecla!=8 && tecla!=0 && tecla != 44) && cola != 'Ctrl+C' && cola != 'Ctrl+V'){
		return false;
	} else {
		if((tecla == 44 && valor.indexOf(',') == -1) || tecla == 8 || tecla == 0 ||(tecla >= 48 && tecla <= 57 && (valor.length - ((valor.indexOf(',') > -1)? valor.indexOf(',') : 100) <= 2)) || cola == 'Ctrl+C' || cola == 'Ctrl+V' ){ // (valor.length > 0 && expre.test(valor)))){ // Verifica se o campo já possui vírgula.
			return true;
		} else {
			return false;
		}
	}
}

/*function SoNumerosFlutuantesSemLimite(e, valor){   // Define o campo para aceitar somente números Flutuantes com vírgula. 
	var tecla=new Number(); alert('teste');
	if(window.event) {
		tecla = event.keyCode;
	} else if(e.which) {
		tecla = e.which;
	}

	var cola = atalho.getAtalho();
	if(((tecla < 44 || tecla == 46 || tecla == 47 || tecla == 48) || (tecla > 57)) && (tecla!=8 && tecla!=0 && tecla != 44) && cola != 'Ctrl+C' && cola != 'Ctrl+V'){
		return false;
	} else {
		if((tecla == 44 && valor.indexOf(',') == -1) || tecla == 8 || tecla == 0 ||(tecla >= 48 && tecla <= 57) || cola == 'Ctrl+C' || cola == 'Ctrl+V' ){ // (valor.length > 0 && expre.test(valor)))){ // Verifica se o campo já possui vírgula.
			return true;
		} else {
			return false;
		}
	}
}*/

function SoNumerosFlutuantesComPonto(e, valor){ // Define o campo para aceitar somente números Flutuantes com ponto.
	var tecla=new Number();
	if(window.event) {
		tecla = event.keyCode;
	} else if(e.which) {
		tecla = e.which;
	}

	var cola = atalho.getAtalho();
	if(((tecla < 48) || (tecla > 57)) && (tecla!=8 && tecla!=0 && tecla != 46) && cola != 'Ctrl+C' && cola != 'Ctrl+V'){
		return false;
	} else {
		return true;
	}
}

function RetornaTecla(e){ // Função para Informar a Tecla que acabou de ser pressionada.
	var tecla 	= false;
	try{
		tecla = (window.event)? event.keyCode : e.which;  
	} catch(ex){}
	return tecla;
}

function SoMaiusculas(campo){ // Faz todas letras do campo se tornarem maiúsculas.
	campo.value = campo.value.toUpperCase();
}

function Arredonda(valor, casas){ // Arredonda os valores de um campo com casas decimais.
	var novo = Math.round(valor * Math.pow(10, casas)) / Math.pow(10, casas);    
	return novo;
}

function destaca(campo){
	try{
		with(campo){
			style.borderColor = '#ff0000';
			style.backgroundColor = '#ffff99';
		}
	} catch(ex){
		window.alert('Versão de Navegador Desatualizado. Favor instalar o Firefox 3, Internet Explorer 7 ou superior ou Opera');
	}
}

function n_destaca(campo){
	try{
		with(campo){
			style.borderColor = '';
			style.backgroundColor = '';
		}
	} catch(ex){
		window.alert('Versão de Navegador Desatualizado. Favor instalar o Firefox 3, Internet Explorer 7 ou superior ou Opera');
	}
}

function destaca_2(campo){
	try{
		campo.style.backgroundColor = '#ff4444';
	} catch(ex){
		window.alert('Versão de Navegador Desatualizado. Favor instalar o Firefox 3, Internet Explorer 7 ou superior ou Opera');
	}
}

function n_destaca_2(campo){
	try{
		campo.style.backgroundColor = '';
	} catch(ex){
		window.alert('Versão de Navegador Desatualizado. Favor instalar o Firefox 3, Internet Explorer 7 ou superior ou Opera');
	}
}

function ComparaComMargem(num1, num2){
	if((Math.abs(num1 - num2)) <= 0.03){
		return true;
	} else {
		return false;
	}
}

function MostraTrabalhando(flag,caminho_sigo, mensagem, altura){
	
	DestravaTela(); // Destrava antes de travá-la novamente.
	
	if(caminho_sigo == null){
		caminho_sigo = '../../';
	}
	
	if(mensagem == null){
		mensagem = "Trabalhando... ";
	}
	
	if(altura == null){
		altura = 300;
	}
	
	if(flag == 1){
		var trava 	= document.createElement("div");
		trava.id 	= "trava_janela";
		
		document.body.style.margin = "0";
		document.body.style.padding = "0";
		
		trava.innerHTML = '<img src="'+caminho_sigo+'imagens/loading.gif" alt="'+mensagem+'" title="'+mensagem+'" style="margin-top:'+altura+'px;"/><br /> <span style="font-size: 10pt;color:#3300cc;" >'+mensagem+'</span>';
		with(trava.style){
			height = ((document.body.scrollHeight > document.body.offsetHeight)? document.body.scrollHeight : document.body.offsetHeight) + 'px';
			width = ((document.body.scrollWidth > document.body.offsetWidth) ? document.body.scrollWidth : document.body.offsetWidth) + 'px';
		}
		
		document.body.appendChild(trava);
	}/* else {
		try{
			var trava	= document.getElementById('trava_janela');
			with(trava.style){
				height = ((document.body.scrollHeight > document.body.offsetHeight)? document.body.scrollHeight : document.body.offsetHeight) + 'px';
				width = ((document.body.scrollWidth > document.body.offsetWidth) ? document.body.scrollWidth : document.body.offsetWidth) + 'px';
			}
		} catch(ex){}
	}*/
}

function DestravaTela(){
	try{
		document.body.removeChild(document.getElementById('trava_janela'));
	} catch(ex){
	// ainda não existe.
	}
}

function DetectaVersaoNavegador(){
	var nome_nav = navigator.appName;
	if (nome_nav == "Microsoft Internet Explorer") {
		var navegador = navigator.userAgent;
		var versao = navegador.indexOf('MSIE ');
		return navegador.substring(versao + 5, versao + 6);  // Retorna a versão do IE.
	} else {
		return 999; // Para sinalizar navegador de verdade.
	}	
}

function LimitaTextArea(id, quant){ // Função para limitação de caracteres de um textarea.
	var objeto = document.getElementById(id);
	if(!document.getElementById("caracteres_"+id)){
		var caracteres = document.createElement("span");
		caracteres.id ="caracteres_"+id;
		with(caracteres){
			style.backgroundColor = "#eee";
			style.fontSize = "12px";
			innerHTML = "&nbsp;" + objeto.value.length + "/"+quant+"&nbsp;";
		}
		objeto.parentNode.appendChild(caracteres);		
	}
	
	objeto.onkeypress = function(e){
		var tecla = (window.event)? event.keyCode : e.which;	
		if(this.value.length >= quant && tecla != 0 && tecla != 8){
			this.value = this.value.substr(0,quant);
			return false;
		} else {
			return true;
		}
	}
	
	objeto.onkeyup = function(){
		document.getElementById("caracteres_"+id).innerHTML = this.value.length + "/"+quant;
	}
}

function mascara(campo, mascara, evento){ // Função que define uma máscara nos campos. deve ser usado com o onKeyPress.
/*
  - Z -> Utilizado na máscara para aceitar somente carateres alfa[A-Z][a-z].
  - 9 -> Utilizado na máscara para aceitar somente carateres numéricos[0-9].
  - # -> Utilizado na máscara para aceitar qualquer tipo de caracter.
*/
	var tecla = (window.event)? event.keyCode : evento.which;
	if(tecla == 8 || tecla == 0 || tecla == 13){
		return true;
	} else {
		var repete = true;
		while(repete){
			var caracs= campo.value.length;
			if(caracs >= mascara.length){ // Verifica se o campo já possui a mesma quantidade de caracteres da máscara.
				return false;
			} else {
				switch(mascara.charAt(caracs)){
					case '#':
					repete = false;
					return true;
					break;

					case '9':
					repete = false;
					if(tecla < 48 || tecla > 57){
						return false;
					}
					break;

					case 'Z':
					repete = false;
					if(!((tecla >= 65 && tecla <= 90) || (tecla >= 97 && tecla <= 122))){
						return false;
					}
					break;

					default:
					if(tecla == 0 || tecla == 8){
						repete = false;
					} else {
						campo.value += mascara.charAt(caracs);
						repete = true;
					}
					break;
				}
			}
		}
	}
}

// Inserindo tratativa de eventos no carregamento da página.
if (window.addEventListener) {
	window.addEventListener("load", verificarConteudosNaCarga, false);
} else {
	window.attachEvent("onload", verificarConteudosNaCarga);
}

function verificarConteudo(campo){ // Limpa caracteres diferentes de dígitos dos campos SoNumeros***.
	var expressao 	= /(SoNumeros)/;
	var tirar	 	= /[^\d+((\,|\.)\d+)]|(\,|\.){2,}|(^\,|^\.|\(|\)|\?|\:|\|)*/g;
	try {
		if (campo.value.length > 0) {
			if (tirar.test(campo.value)) { // Verifica se não está válido.
				campo.value = campo.value.replace(tirar, '');
			}
		}
	} catch(ex){}
}

function verificarConteudosNaCarga(){  // Limpa caracteres diferentes de dígitos dos campos SoNumeros*** na carga da página.
	var expressao 	= /(SoNumeros)/;
	var tirar	 	= /[^\d+((\,|\.)\d+)]|(\,|\.){2,}|(^\,|^\.|\(|\)|\?|\:|\|)*/g;
	var inputs 		= document.getElementsByTagName('input');
	var qtd_caracs 	= 0;
	
	for(var i in inputs){
		try {
                    if(inputs[i].type == 'text')
                    {
			if (expressao.test(inputs[i].getAttribute('onkeypress'))) {
				if(window.addEventListener){
					inputs[i].addEventListener("keyup", function(){
						verificarConteudo(this);
						if(this.className == 'chapa'){
							verificaChapa(this);
						}
					}, false);
				} else {
					inputs[i].attachEvent("onkeyup", function(){ // Para o IE.
						var evento	= window.event;
						var campo 	= evento.srcElement; // Capturando o Objeto gerador do evento.
						verificarConteudo(campo);
						if(campo.className == 'chapa'){
							verificaChapa(campo);
						}
					});
				}
				
				qtd_caracs = inputs[i].value.length;
				if (qtd_caracs > 0) {
					if (tirar.test(inputs[i].value)) { // Verifica se não está válido.
						inputs[i].value = inputs[i].value.replace(tirar, '');
					}
				}
			}
                    }
		} catch(ex){
			// Atributo onkeypress inexistente.
		}
	}
}

//RETIRADO DO ARQUIVO ATALHO.JS, SIGO IMPLANTACAO
var atalho = new Atalho();
		
function Atalho(){
	var Ctrl, Shift, Alt;
	var tecla_press, evento;
	
	var rTecla = function(e){
		return (window.event) ? event.keyCode : e.which;
	}
	
	this.setTeclaPresa = function(e){
	
		var tecla = rTecla(e);
		
		switch (tecla) {
			case 16:
				Shift = true;
				break;
				
			case 17:
				Ctrl = true;
				break;
				
			case 18:
				Alt = true;
				break;
				
			default:
				tecla_press = String.fromCharCode(tecla);
				break;
		}
	}
	
	this.setTeclaSolta = function(e){
		var tecla = rTecla(e);
		tecla_press = '';
		switch (tecla) {
			case 16:
				Shift = false;
				break;
				
			case 17:
				Ctrl = false;
				break;
				
			case 18:
				Alt = false;
				break;
		}
	}
	
	this.getCtrl = function(){
		return Ctrl;
	}
	
	this.getAlt = function(){
		return Alt;
	}
	
	this.getShift = function(){
		return Shift;
	}
	
	this.getAtalho = function(){
		if (Ctrl) {
			return "Ctrl+" + tecla_press;
		}
		else if (Alt) {
			return "Alt+" + tecla_press;
		}
		else if (Shift) {
			return "Shift+" + tecla_press;
		}
		else {
			return false;
		}
	}
	
}

document.onkeyup = function(e){
	atalho.setTeclaSolta(e);
}
	
document.onkeydown = function(e){
	atalho.setTeclaPresa(e);
}

function date_format(data, formato_entrada,formato_saida){
	
	if(data == '' || data.length < 10) return '';
	
	var temp = dia = mes = ano = hora = minuto = segundos = '';
	var retorno = '-';
	
	var formata = function(novo_formato,year,month,day,hour,minutes,seconds){
		data_formatada = '-';
		switch(novo_formato){
			case 'dd/mm/YYYY':
				data_formatada = day + '/' + month + '/' + year; 
				break;
			case 'dd/mm/YYYY h:i:s':
				data_formatada = day + '/' + month + '/' + year + ' ' + hour + ':' + minutes + ':' + seconds;
				break;
		}
		return data_formatada;
	}
	
	switch(formato_entrada){
		case 'YYYY-mm-dd':
			temp = data.split('-');
			retorno = formata(formato_saida,temp[0],temp[1],temp[2],'0','0','0');
			break;
		case 'YYYY-mm-dd h:i:s':
			
			temp = data.split(' ');
			
			var date_tmp = temp[0].split('-');
			var time_tmp = temp[1].split(':');
			
			retorno = formata(formato_saida,date_tmp[0],date_tmp[1],date_tmp[2],time_tmp[0],time_tmp[1],time_tmp[2]);
			break;
	}
	
	return retorno;	
}