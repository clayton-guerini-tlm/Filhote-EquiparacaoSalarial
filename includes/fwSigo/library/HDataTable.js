class HDataTable {

	/**
	 * Gera DataTable
	 * @param {string} idElementoDestino: id html da tabela
	 * @param {objeto} [opcional] dataset: objeto normato {atributo: valor}
	 * @param {array} [opcional] ordemColuna: array com os nome dos atributos, irá estabelecer a ordem que aparece na tela
	 * @param {array} [opcional] removerBotao: array com os nome dos botões que devem ser removidos ['excel', 'pdf', 'copy']
	 * @param {objeto} [opcional] linhaPadrao: objeto {coluna: valorDefault} para quando a coluna no dataset estiver vazia
	 * @param {string} [opcional] nomeArquivo: string "arquivo do zé". Estabelece o nome do arquivo ao exportar para excel e pdf
	 * @param {array} [opcional] colunasExportar: array com os indices das colunas que irão ser exportados para excel/pdf/copy
	 */
	 constructor(idElementoDestino, {dataset, ordemColuna, removerBotao, btnAcao, linhaPadrao, nomeArquivo, colunasExportar} = {}) {
		/**
		 * ATRIBUTOS
		 */
		if(idElementoDestino.charAt(0) != '.' && idElementoDestino.charAt(0) != '#') {
			this.idElemento = '#'+idElementoDestino;
		}else {
			this.idElemento   = idElementoDestino;
		}
		this.dataset      = null;
		this.column       = null;
		this.ordemColuna  = ordemColuna;
		this.removerBotao = removerBotao;
		this.btnAcao      = btnAcao;
		this.linhaPadrao = linhaPadrao;
		this.nomeArquivo = (nomeArquivo) ? nomeArquivo : idElementoDestino;
		this.colunasExportar = colunasExportar;


		 if(dataset instanceof Array) {
		 	this.dataset = this.prepareDataset(dataset);
		 	this.column = this.getColumn(this.dataset);
		 }else if (dataset instanceof Object) {
		 	var tmpArr = [];
		 	var key = '';

		 	for(key in dataset) {
		 		tmpArr[tmpArr.length] = dataset[key]
		 	}
		 	this.dataset = this.prepareDataset(tmpArr);
		 	this.column = this.getColumn(this.dataset);
		 }

		/**
		 * ATRIBUTO PADRÃO
		 */
		 var atributoPadrao = this.getAtributoPadrao();

		/**
		 * GERAR DATATABLE
		 */

		 if ($(this.idElemento).is('div')) {
		 	var htmlTable = "<table><thead><tr>";
		 	this.column.forEach(function(item){

		 		htmlTable += "<th>{0}</th>".format(item.data);
		 	});
		 	htmlTable += "</tr></thead><tbody></tbody></table>";
		 	$(this.idElemento).html(htmlTable).show();
		 	this.idElemento +=  " >table";
		 }

		 if( $.fn.DataTable.isDataTable(this.idElemento) ) {
		 	var htmlAux = $(this.idElemento).html();
		 	$(this.idElemento).DataTable().clear().destroy();
		 	$(this.idElemento).html(htmlAux);
		 	htmlAux = '';
		 }

		 $(this.idElemento).DataTable(atributoPadrao);


		}

		prepareDataset(dataset) {


			var rows = [];
			var columns = [];
			var rowPadrao = {};

   		/**
   		 *  Adicionado Botões Ações Editar Exlcuir
   		 */

   		 if(typeof this.btnAcao	 != 'undefined'){

   		 	for(var i=0; i< dataset.length; i++){

   		 		var auxBtn = this.getBtnAction(dataset[i],this.btnAcao);
   		 		var btn    = {btnAcao:auxBtn};
   		 		Object.assign(dataset[i],btn);

   		 	}

   		 }

   		/**
   		 * Determinar colunas
   		 */
   		 dataset.forEach(function(obj){
   		 	rowPadrao = $.extend(rowPadrao, obj);
   		 });

   		/**
   		 * Criar obj padrão
   		 */
   		 for(var key in rowPadrao ) {
   		 	rowPadrao[key] = '';
   		 }

   		 if (typeof this.linhaPadrao != 'undefined') {
   		 	rowPadrao = $.extend(rowPadrao, this.linhaPadrao);
   		 }



		/**
		 * Criar obj incluindo atributos que não possui
		 */
		 dataset.forEach(function(obj) {
   			var aux = JSON.parse(JSON.stringify(rowPadrao));//Clonar objeto
   			aux = Object.assign(aux, obj);
   			var obj = $.extend(aux, obj);

   			rows.push(obj);
   		});

		 return rows;
		}

		getColumn(dataset) {

			var objColuna = {};
			var colunas = [];

   		/**
   		 * Criar objeto coluns
   		 */
   		 dataset.forEach(function(obj){
   		 	objColuna = $.extend(objColuna, obj);
   		 });


   		 for(var nomeColuna in objColuna) {

   		 	colunas.push({data: nomeColuna});
   		 }


		/**
		 * Ordenar coluna
		 */
		 var ordemColuna = this.ordemColuna;
		 if(ordemColuna instanceof Array) {
		 	var ordenados = [];
		 	var naoOrdenados = [];

		 	ordemColuna.forEach(function(key, idx) {
		 		var item = colunas.filter(obj => {
		 			return obj.data === key
		 		});
		 		if(item != '') {
		 			ordenados.push(item[0]);
		 		}
		 	});

		 	colunas.forEach(function(obj) {
		 		if($.inArray(obj.data, ordemColuna) == -1) {
		 			naoOrdenados.push(obj);
		 		}
		 	});

		 	colunas = ordenados.concat(naoOrdenados);
		 }
		 var idDT = this.idElemento;
		 colunas.forEach(function(obj, iterador) {

		 	if ($($(idDT).find('th')[iterador]).css('display') == 'none') {
		 		$.extend(obj, {'visible':false} );
		 	}
		 	if ($($(idDT).find('th')[iterador]).attr('dataType') == 'date') {
		 		$.extend(obj, {
		 			render: function(data, type, row){
					                if(type === "sort" || type === "type"){
					                	if (data) {
						                	let dia = data.split(' ');
						                	let hora = "00:00"
						                	if (dia.length > 1) {
						                		hora = dia[1];
						                	}
						                	let auxData = dia[0].split('/');
						                	let auxHora = hora.split(':') ;

						                    return new Date(auxData[2],auxData[1], auxData[0], auxHora[0], auxHora[1]);
					                }else{
					                	return data
					                }
				                }
				                return data;
				            }

		 		} );
		 	}
		 });

		 return colunas;

		}

		getAtributoPadrao() {

			var buttons = this.getButton();

			var atributoPadrao = {
				dom: "<'row'<'col-md-12'B>>" +
				"<'row'<'col-md-6 nRegistros'l><'col-md-6 pesquisar'f>>" +
				"<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
				buttons,
				language: {
					"paginate": {
						"previous": " Anterior ",
						"next"    : " Próximo "
					},
					"emptyTable"    : "Sem registros",
					"sSearch"       : "Pesquisar :",
					"info"          : "_START_ a _END_  de _TOTAL_ Registros",
					"infoFiltered"  : "(Total de _MAX_ Registros )",
					"zeroRecords"   : "Nenhum resultado encontrado",
					"lengthMenu"    : "Mostrar _MENU_ por página",
					"infoEmpty"     : "Sem registros",

				},
				iDisplayLength: 10,
				lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]]
			};

			if(this.dataset instanceof Array && this.dataset.length > 0) {
				atributoPadrao.data = this.dataset;
				atributoPadrao.columns = this.column;
			}

			return atributoPadrao;
		}

		getButton() {

			var exportOptions = {};

			if(this.colunasExportar) {
				exportOptions.columns = this.colunasExportar;
			}

			var btnExcel = {
				extend: 'excel',
				text: '<img src="../imagens/Excel_2010.png" title="Gerar Excel" style="width:15px; height:15px;"></img>',
				filename: this.nomeArquivo,
				exportOptions: exportOptions
			};
			var btnPdf = {
				extend: 'pdf',
				text: '<img src="../imagens/icon_pdf.png" title="Gerar PDF" style="width:15px; height:15px;"></img>', orientation:'landscape',
				filename: this.nomeArquivo,
				exportOptions: exportOptions
			};
			var btnCopy = {
				extend: 'copy',
				text: '<img src="../imagens/copy.png" title="Copiar tabela" style="width:15px; height:15px;"></img>',
				exportOptions: exportOptions
			};
			var buttons = [];


			if(this.removerBotao !== undefined && this.removerBotao.length > 0) {
				if($.inArray('excel', this.removerBotao) == -1) {
					buttons.push(btnExcel);
				}
				if($.inArray('pdf', this.removerBotao) == -1) {
					buttons.push(btnPdf);
				}
				if($.inArray('copy', this.removerBotao) == -1) {
					buttons.push(btnCopy);
				}
			}
			else {
				buttons = [btnExcel, btnPdf, btnCopy];
			}

			return buttons;
		}

   	/**
   	 * @function Gera Button Editar e Excluir
   	 *
   	 */

   	 getBtnAction(dado,btnAcao){


   	 	var param  = '';
   	 	var botoes = '';

   	 	//Seta o Parametro informado pelo usuário para ações
   	 	jQuery.each(dado, function(chave, value){

   	 		if(chave === btnAcao.parametro){
   	 			param = value;
   	 		}

   	 	});

   	 	if(btnAcao.edit === true){

   	 		botoes  += '<img src="../imagens/sigo_img/icone-editar.png" title="Alterar" style="width: 20px; height: 20px;" class="icone editar" {0}="{1}" id="{2}">'.format(btnAcao.parametro, param, param);

   	 	}

   	 	if(btnAcao.delete === true){

   	 		botoes += '<img src="../imagens/excluir.png" title="Excluir" style="width: 15px; height: 15px" class="icone excluir" {0}="{1}" id="{2}">'.format(btnAcao.parametro, param, param);
   	 	}

   	 	return 	botoes;
   	 }

   	}