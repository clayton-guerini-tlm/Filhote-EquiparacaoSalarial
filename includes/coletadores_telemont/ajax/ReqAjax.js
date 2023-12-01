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
		}catch (ex){
		
			try{
				// Tenta criar ajax para algumas versões do Microsoft Internet Explorer
				ajax = new ActiveXObject("Microsoft.XMLHTTP"); // IE
		
			}catch(ex) {
				// Browser utilizado não aceita ajax, o objeto não é criado
				ajax = false;
			}
		}
	}
	
	return ajax;
}




function ReqAjax(funcao, campos){
	
	var tempo, quebra = Math.random();
	
	var ObjAjax = getAjax();
	if (ObjAjax != null) {
	
		ObjAjax.open("POST", "ajax/inc_ajax_funcoes.php?quebra=" + quebra, true);
		ObjAjax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		ObjAjax.setRequestHeader("Content-length", campos.length);
		ObjAjax.setRequestHeader("Connection", "close");
		ObjAjax.send(campos);
		ObjAjax.onreadystatechange = function(){
			
			if(ObjAjax.readyState != 1){
				clearTimeout(tempo);
			}
		
			if (ObjAjax.readyState == 4 && ObjAjax.status == 200 && ObjAjax.responseText){ // Verifica se alcançou o status de Pronto.
            
				try {
					clearTimeout(tempo);
				} catch(ex){
					// Limpando o delay.
				}
        				
				funcao(ObjAjax);            
			
        	} else if(ObjAjax.readyState == 4 && !tempo && ObjAjax.status != 200){
				window.setTimeout(function(){
					ObjAjax.abort();
					clearTimeout(tempo);
					ReqAjax(funcao, campos);
				}, 150);
			}
		}
		
		if(ObjAjax.readyState == 1){
			tempo = window.setTimeout(function(){
			ObjAjax.abort();
			ReqAjax(funcao, campos);
			}, 2500);
		}
	}
}