class HHtml {
   static montarOption(itens, id, texto, atributo = null, selecionado = null, padrao = 'Selecione') {

	    if(padrao) {
    		var option = "<option value=''>{0}</option>".format(padrao);
	    }

	    $.each(itens, function (idx, value) {

	    	var htmlAtributos = '';

	    	if(atributo != null &&  atributo instanceof Array) {
	    		$.each(atributo, function(i, attr) {
	    			htmlAtributos += attr + '=' + value[attr] + ' ';
	    		});
	    	}

	        var selected = selecionado == value[id] ? 'selected' : '';

	        option += "<option value='{0}' {2} {3}>{1}</option>".format(value[id], value[texto], selected, htmlAtributos);

	    });

	    return option;
	}
}