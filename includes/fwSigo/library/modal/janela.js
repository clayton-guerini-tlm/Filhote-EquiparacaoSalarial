function janela(pagina, titulo, largura = 1024, altura = 600, funcaoFechar = function(){}) {
	
	$('#janela').dialog({
			show : "SHOW",
			modal : true,
			title : titulo,
			width : largura,
			height : altura,
			resizable : true,
			draggable : true,
			close : function(event, ui) { funcaoFechar(); }

		}
	).html('<iframe style="width: 99%; height: 99%;" frameborder="0" src="' + pagina + '"></iframe>');
	
}