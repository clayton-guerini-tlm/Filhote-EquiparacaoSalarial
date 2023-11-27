class HExcel {

	/**
	 * Exporta tabela para o excel no formato
	 * Os parâmetros devem ser colocados como atributos dentro da tag <table>
	 * @param nomeArquivo; nome do arquivo que será baixado
	 * @param removerCabecalho: não exportar o cabeçalho
	 * @param tipo: determina o formato do arquivo, o padrão é "xls", pode ser enviado "csv"
	 * @exemplo de chamada: botão que irá disparar a ação: onClick="HExcel.exportarExcel(\'#tabela2\')";
	 * @exemplo de tag table: <table class="table table-hover" id="tabela2" nomeArquivo="teste2">
	 */
  	static exportarExcel(element) {

	    var nomeArquivo = $(element).attr('nomeArquivo');
	    var nth = "";
	    $(element).clone().css("display", "").attr('id', 'tabelaExportarExcel').insertAfter(element);

	    $('#tabelaExportarExcel thead tr').children().each(function () {

	        if (typeof $(this).attr('removeExcel') !== 'undefined' && $(this).attr('removeExcel') !== false) {

	            nth = $(this).index() + 1;

	            $('#tabelaExportarExcel th:nth-child(' + nth + ')').remove();
	            $('#tabelaExportarExcel td:nth-child(' + nth + ')').remove();
	        }

	        if ($(this).is(':hidden') && (typeof $(this).attr('exibeExcel') === 'undefined' || $(this).attr('exibeExcel') === false)) {

	            nth = $(this).index() + 1;

	            $('#tabelaExportarExcel th:nth-child(' + nth + ')').remove();
	            $('#tabelaExportarExcel td:nth-child(' + nth + ')').remove();

	        }

	    });

	    /**
	     * Remover Cabeçalho
	     */
	    if (typeof $(element).attr('removerCabecalho') !== 'undefined' && $(element).attr('removerCabecalho') !== false) {

	        $('#tabelaExportarExcel thead').empty();

	    }

	    $("#tabelaExportarExcel").attr('border', '1');
	    $("#tabelaExportarExcel tr td img").remove();

	    var tabela = document.getElementById('tabelaExportarExcel');
	    var exportar = document.createElement('a');

	    /**
	     * CSV
	     */
	    if ($(element).attr('tipo') == "csv") {

	        var tabelaCsv = $(tabela);
	        var csv = [];

	        for (var i = 0; i < tabelaCsv.length; i++) {

	            var row = [];
	            var rows = tabelaCsv[i].querySelectorAll("tr");

	            for (var j = 0; j < rows.length; j++) {

	                var cols = rows[j].querySelectorAll("td, th");

	                for (var k = 0; k < cols.length; k++) {

	                    if (k + 1 == cols.length) {
	                        row.push(cols[k].innerText);
	                    } else {
	                        row.push(cols[k].innerText + ";");
	                    }

	                }

	                row.push("\n");

	            }

	            csv.push(row.join(""));

	        }

	        var csvFile = new Blob([csv], {type: "text/csv"});

	        exportar.download = nomeArquivo + '.csv';
	        exportar.href = window.URL.createObjectURL(csvFile);
	        exportar.style.display = "none";

	        document.body.appendChild(exportar);
	        exportar.click();

	        /**
	         * EXCEL
	         */
	    } else {

	        var exportar = document.createElement('a');
	        var blob = new Blob(["\ufeff",tabela.outerHTML], {type: "text/plain;charset=utf-8;"});

			exportar.href 		= URL.createObjectURL(blob);
	        exportar.charset = "UTF-8";
	        exportar.download = nomeArquivo + '.xls';
	        document.body.appendChild(exportar);
	        exportar.click();

	    }

	    document.body.removeChild(exportar);
	    $("#tabelaExportarExcel").remove();

	}
}