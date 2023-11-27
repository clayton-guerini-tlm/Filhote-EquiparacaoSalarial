class HCsv {
   	static downloadArquivo(stringCsv, nomeArquivo) {
   		var exportar = document.createElement('a');
    	var csvFile = new Blob([stringCsv], {type: "text/csv"});
        exportar.download = nomeArquivo + '.csv';
        exportar.href = window.URL.createObjectURL(csvFile);
        exportar.style.display = "none";
        document.body.appendChild(exportar);
        exportar.click();
        document.body.removeChild(exportar);
	}
}