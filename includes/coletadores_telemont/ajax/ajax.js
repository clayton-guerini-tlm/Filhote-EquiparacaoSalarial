// AJAX

function getAjax(){
	
	var Ajax = null;
	
	// Trata as exceções até conseguir cria o objeto ajax
	
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


      var mod = getAjax();    
  
 // ######## FUNÇÃO PARA CARREGAR OS VALORES DO FORM PELO AJAX  SEM DAR REFRESH #########
function enviarForm(url, campos, destino) {
		var Ajax = getAjax();

    //Atribuir variavel 'elemento' o elemento que ira receber a pagina postada
    var elemento = document.getElementById(destino);


var arquivo = "imagens/loading.gif";

        //Abre a página que recebe os campos do formulario
    mod.open('POST', url+'?'+campos, true);
	mod.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	mod.onreadystatechange = function(){
		if (mod.readyState == 1){
			document.getElementById('mensagem').innerHTML = '<img src="' + arquivo + '">';
		}
		if(mod.readyState == 4){
			//document.getElementById('debug').value = mod.responseText;
			document.getElementById("mensagem").innerHTML = mod.responseText;
		}
	};

    mod.send(campos);

}

function gerar_area_operacional(area, id){
	var Ajax = getAjax();
		var campos = "funcao_ajax=mostrar_areas_operacionais&area="+area;
		ReqAjax(Pronto, campos);
		function Pronto(obj){
			document.getElementById(id).innerHTML = obj.responseText;
		}
}

function gerar_ip(area, area_op, id){
	var campos = "funcao_ajax=mostrar_ip&area="+area+"&area_operacional="+area_op;
		ReqAjax(Pronto, campos);
		function Pronto(obj){
			document.getElementById(id).innerHTML = obj.responseText;
		}
}
 