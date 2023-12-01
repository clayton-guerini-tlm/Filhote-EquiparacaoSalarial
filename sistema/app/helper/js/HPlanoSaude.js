class HPlanoSaude {
  	static gerarSelectFilial(idElemento) {
   		$.ajax({
	        data: {'funcaoAjax': 'CFilial::buscarFilial'}
	    }).done(function (response) {
	    	var rs = HAjax.prepareReturn(response);
	    	var option = HHtml.montarOption(rs,'idFilial', 'filial');
	    	$(idElemento).html('').append(option);
	    });
	}

	static gerarSelectFilialGrupo(idElemento) {
   		$.ajax({
	        data: {'funcaoAjax': 'CFilial::buscarFilialGrupo'}
	    }).done(function (response) {
	    	var rs = HAjax.prepareReturn(response);
	    	var option = HHtml.montarOption(rs,'id', 'texto');
	    	$(idElemento).html('').append(option);
	    });
	}

	static gerarSelectStatusItem(idElemento, selecionado = null) {
   		$.ajax({
	        data: {'funcaoAjax': 'CGerenciarCoparticipacao::buscarStatusItem'}
	    }).done(function (response) {
	    	var rs = HAjax.prepareReturn(response);
	    	var option = HHtml.montarOption(rs,'idStatusItem', 'statusItem', null, selecionado);
	    	$(idElemento).html('').append(option);
	    });
	}
}