// Dúvidas? Pergunte ao Leonardo Araújo (Desenvolvimento de Sistemas TI - BH - MG)

// Aqui ficam os códigos JQUERY da página index.php
// Obs: Os códigos das outras páginas podem ser declarados dentro do arquivo referente
	  
$().ready(function(){ // Estrutura padrão para executar o JQUERY (Verifica quado o DOM está liberado para ser executado).

 	// Funções de avisos e erros
	// Chame estas funções com o método $('#Id_do_elemento').html(aviso("Atenção!","Aviso..."));
	function ok(str){
		$('#ok').html("<div class='ui-state-default ui-corner-all' style='margin-top: 20px; padding: 0 .7em; font:12px Verdana, Geneva, sans-serif;' align='left' ><p><span class='ui-icon ui-icon-check' style='float: left; margin-right: .3em;' ></span>"+str+"</p>/div>");
	}
	function aviso(str){
		// Retorna a string da janela de aviso.
		$('#avisos').html("<div class='ui-state-highlight ui-corner-all' style='margin-top:20px; padding:0 .7em; font:12px Verdana, Geneva, sans-serif;' align='left' ><p align='left' ><span class='ui-icon ui-icon-info' style='float: left; margin-right: .3em;' ></span>"+str+"</p></div>");
	}
	function erro(str){
		// Retorna a string da janela de erro.
		$('#erros').html("<div class='ui-state-error ui-corner-all' style='margin-top: 20px; padding: 0 .7em; font:12px Verdana, Geneva, sans-serif;' align='left'><p><span class='ui-icon ui-icon-alert' style='float: left; margin-right: .3em;' ></span>"+str+"</p></div>");
	}
	
	function validar_especifico(c){
		
		if( $(c).hasClass("cpf") ){ 
			/* Valida os campos do formulário que tiverem a class='cpf'
			 * 
			 */
			var field=$(c);var numCPF=$(c).val();var campo="";for(i=0; i<numCPF.length;i++){if((numCPF.charAt(i)!=".")&&(numCPF.charAt(i)!="-")&&(numCPF.charAt(i)!="/")){campo=campo+numCPF.charAt(i)}}if(campo.length>0){if((campo=="00000000000")||(campo=="11111111111")||(campo=="22222222222")||(campo=="33333333333")||(campo=="44444444444")||(campo=="55555555555")||(campo=="66666666666")||(campo=="77777777777")||(campo=="88888888888")||(campo=="99999999999")||(campo=="12345678901")){return false;}tam=campo.length;val1=tam-1;soma=0;for(i=0;i<=tam-3;i++){val2=campo.substring(i,i+1);soma=soma+(val2*val1);val1=val1-1;}dig1=11-(soma%11);if(dig1==10)dig1=0;if(dig1==11)dig1=0;val1=11;soma=0;for(i=0;i<=tam-2;i++){soma=soma+(campo.substring(i,i+1)*val1);val1=val1-1;}dig2=11-(soma%11);if(dig2==10)dig2=0;if(dig2==11)dig2=0;if((dig1+""+dig2)==campo.substring(tam,tam-2)){return true;}}else{return true;}return false;
		}else{
			return true;
		}	
	}
	
	// Mostra a mensagem de erro dos campos que tiverem o atributo erro='' dentro da tag
	function mostra_erro(obj){	
		if(!$(obj).attr("erro")){
			aviso("FALTA o atributo 'erro' com a mensagem para a validação no INPUT -> "+$(obj).attr("name")+".");
		}else{
			aviso($(obj).attr("erro"));
		}
	}
	/**
	 *  VALIDAÇÃO DE FORMULÁRIOS
	 *	Modo de utilização:
	 *	Atribua a class validar no formulário para que ele seja validado no evento submit
	 *	Quando todos os campos do formulário forem obrigatórios, coloque o atributo na tag <form obri="mensagem de aviso" ></form>
	 *	OBS: Quando for obrigatório preencher pelo menos um campo, utilize o atributo na tag <form obri="mensagem de aviso" unico="true" ></form>
	 *	
	 *	Quando campos específicos do formulário forem obrigatórios, coloque o atributo na tag <input obri="mensagem de aviso" />
	 *	Após a tag de fechamento do form cole a seguinte div: <div id="avisos"></div>
	 *	
	 *	Para validar campos como CPF e CNPJ coloque a respectiva class e o atributo erro="mensagem de erro" no imput.
	 *	
	 */

	$("form.validar").submit(function(){
		$('#avisos').html("");
		var ret = true;
		var um = false;
		var i = 0, j = 0;
		var focar = new Array(), especifico = new Array();
		focar.length = 0;
		especifico.length = 0;
		
		if($(this).attr("obri")){ // Verifica se todos os campos do formulário são para serem validados
			if($(this).attr("unico")){ // Se apenas um campo for obrigatório (qualquer campo estando preenchido o form é submetido. Exceto campos hidden)
				
				ret_1 = check_obj($(this), false);			
				if(ret_1[0] && !ret_1[2]){  // Quando campos não estão preencidos
					aviso($(this).attr("obri"));
					$(ret_1[0]).focus();
					ret = false;
				}else if(ret_1[1]){  // Quando existe erro nos campos específicos
					mostra_erro($(ret_1[1]));
					$(ret_1[1]).select();
					ret = false;
				}
				
			}else{ // Se todos os campos forem obrigatórios
							
				ret_1 = check_obj($(this),false);
				
				if(ret_1[0]){  // Quando campos não estão preencidos
					aviso($(this).attr("obri"));
					$(ret_1[0]).focus();
					ret = false;
				}else if(ret_1[1]){  // Quando existe erro nos campos específicos
					mostra_erro($(ret_1[1]));
					$(ret_1[1]).select();
					ret = false;
				}			
			}		
		}else{ // Se for para validar campos expecíficos
			
			ret_1 = check_obj($(this), true);
			
			if(ret_1[0] ){  // Quando campos não estão preencidos
				aviso($(ret_1[0]).attr("obri"));
				$(ret_1[0]).focus();
				ret = false;
			}else if(ret_1[1]){  // Quando existe erro nos campos específicos
				mostra_erro($(ret_1[1]));
				$(ret_1[1]).select();
				ret = false;
			}
		}
		return ret;		
	});
	
	function check_obj(form,especificos){
		var r = new Array(false, false, false);
		var i = 0;
		var obri = "";
		if(especificos == true) obri = "[obri]";
			
		$(form).find("input[esc!=true]"+obri+",textarea[esc!=true]"+obri).each(function(){					
			var tipo = $(this).attr("type");
			if((tipo!="hidden")&&(tipo!="submit")&&(tipo!="button")&&(tipo!="image")&&(tipo!="reset")){
				if($(this).val()==""){
					if(i == 0) r[0] = $(this);
					if(!$(form).attr("unico")){
						if(!$(this).hasClass("input_obrigatorio")){
							$(this).addClass("input_obrigatorio");
						}
					}
					i++;
				}else{
					$(this).removeClass("input_obrigatorio");
					r[2] = true;
				}				
				if(!validar_especifico($(this))){
					if(!$(this).hasClass("input_obrigatorio")){
						$(this).addClass("input_obrigatorio");
					}
					if(i == 0) r[1] = $(this);
					i++;
				}
			}
		});
		$(form).find("select[esc!=true]"+obri).each(function(){
			if(  $(this,"option:selected").val() == ""  ){
				if(i == 0) r[0] = $(this);				
				if(!$(this).hasClass("input_obrigatorio")){
					$(this).addClass("input_obrigatorio");
				}
				i++;
			}else{
				$(this).removeClass("input_obrigatorio");
				r[2] = true;
			}	
		});		
		return r;
	}
	// FIM - VALIDAÇÃO DE FORMULÁRIOS
});