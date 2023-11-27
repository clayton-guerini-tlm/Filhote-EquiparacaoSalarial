class HChart {

	/**
	 * Gera gráfico utlizando chart.js
	 * @param {string} idElementoDestino: id html da div onde o gráfico será renderizado
	 * @param {string} tipoGrafico: tipo de gráfico do chart.js. Implementado (bar, pie)
	 * @param {array} matrizDados: datasets do gráfico padrao: "array('categoria' => valor)", mas pode haver multiplosníveis
	 * @param [optional] {string} titulo: Título do gráfico
	 * @param [optional] {string} formatoTextoBarra: formata label do gráfico. Implementado (percentual, financeiro)
	 * @param [optional] {function} onClick: executa a função enviada ao clicar em um dataset enviando como parâmetro o valor do dataset
	 * @param [optional] {objeto} customOptions: sobrepõe ou adiciona o options do gráfico com os valores enviados
	 * @param [optional] {array} ordemDataset: Ordem na qual os datasets devem aparacer, datasets não mencionados ficam por último
	 * @param [optional] {objeto} cores: Alterar as cores padrões, onde a key representa a ordem da cor e '-1' ou 'todos' altera todas elas.
	 * 									 Permite a inserção das nomeclaturas de cor do css ('#ffffff', 'rgba(50,205,50,0.7)', 'red')
	 * 									 Exemplo: cores = {1: red, 3: '#dddc12', 'todos': 'blue'}
	 * @param [optional] {string} escala: altera o modo como a escala do gráfico acontece. exemplo: 'logarithmic'
	 */
	constructor(idElementoDestino, tipoGrafico, matrizDados, {titulo, formatoTextoBarra, onClick, customOptions, ordemDataset, cores, escala} = {}) {

		/**
		 * Atributos
		 */
		this.idElemento = idElementoDestino;
		this.tipoGrafico = tipoGrafico;
		this.matrizDados = matrizDados;
		this.titulo = titulo;
		this.formatoTextoBarra = formatoTextoBarra;
		this.onClick = onClick;
		this.chartElement = this.idElemento + '_chart';
		this.customOptions = customOptions;
		this.ordemDataset = ordemDataset;
		this.cores = this.getCores(cores);
		this.escala = escala;

		if(!idElementoDestino || !matrizDados) {
			this.deletarGrafico();
			return false;
		}

		/**
		 * Categorias
		 */
		var arrayCategorias = [];

		$.each(this.matrizDados, function(categoria, val) {
			arrayCategorias.push(categoria);
		});

		this.categorias = arrayCategorias;

		/**
		 * Gerar gráfico
		 */
		this.gerarGrafico();

   	}

	gerarGrafico() {

		var objGrafico = {
			type: this.tipoGrafico,
			data: this.criarAtributoData(),
			options: this.criarAtributoOptions()
		};

	    var ctx = this.prepararElementoDestino();

		var myChart = new Chart(ctx, objGrafico);

	}

	criarAtributoData() {

		var objRetorno = {
			labels: [],
			datasets: []
		}

		objRetorno.labels = this.categorias;

		if(this.tipoGrafico == 'bar' || this.tipoGrafico == 'horizontalBar') {
			objRetorno.datasets = this.criarAtributoDatasetGraficoBar();
		}
		else if(this.tipoGrafico == 'pie') {
			objRetorno.datasets = this.criarAtributoDatasetGraficoPie();
		}

		return objRetorno;

	}

	criarAtributoDatasetGraficoBar() {

		var retorno = [];
		var arrayDatasets = {};
		var atributosDataset = {};
		var arrayCategorias = this.categorias;
		var cores = this.cores;
		var arrayIdxCategoria = {}

		/**
		 * Determinar indice de cada categoria
		 */
		$.each(arrayCategorias, function(idx, categoria) {
			arrayIdxCategoria[categoria] = idx;
		});

		/**
		 * Montar datasets
		 */
		$.each(this.matrizDados, function(categoriaAtual, prox) {
			$.each(prox, function(dataset, valor) {

				if(valor instanceof Object) {
					if(atributosDataset[dataset] === undefined) {
						atributosDataset[dataset]= {};
					}

					if(valor.type !== undefined) {
						atributosDataset[dataset]['type'] = valor.type;
					}

					/**
					 * Atribuir argsFunction para seu dataset
					 */
					if(valor.argsFunction !== undefined) {
						if(atributosDataset[dataset]['argsFunction'] === undefined) {
							atributosDataset[dataset]['argsFunction'] = [];
							for(var i = 0; i < arrayCategorias.length; i++) {
								atributosDataset[dataset]['argsFunction'][i] = null;
							}
						}
						atributosDataset[dataset]['argsFunction'][arrayIdxCategoria[categoriaAtual]] = valor.argsFunction;
					}

					valor = valor.valor;
				}

				if(arrayDatasets[dataset] === undefined) {
					arrayDatasets[dataset] = {};
					$.each(arrayCategorias, function(i, categoria) {
						arrayDatasets[dataset][categoria] = 0;
					});
				}

				arrayDatasets[dataset][categoriaAtual] = valor;

			});
		});

		$.each(arrayDatasets, function(dataset, valores) {

			var tmpArr = [];
			var key = '';

			for(key in valores) {
				tmpArr[tmpArr.length] = valores[key]
			}

			valores = tmpArr;

			var dados = {
				label: dataset,
				data: valores,
				backgroundColor: cores[retorno.length]
			};

			if(atributosDataset[dataset] !== undefined) {

				if(atributosDataset[dataset]['type'] !== undefined) {
					dados.type = atributosDataset[dataset]['type'];
					dados.fill = false;
					dados.borderColor = cores[14];
					dados.backgroundColor = 'black';
				}

				if(atributosDataset[dataset]['argsFunction'] !== undefined) {
					dados.argsFunction = atributosDataset[dataset]['argsFunction'];
				}

			};
			retorno.push(dados);
		});

		var ordemDataset = this.ordemDataset;
		if(ordemDataset != '' && ordemDataset !== undefined) {
			var ordenados = [];
			var naoOrdenados = [];

			ordemDataset.forEach(function(key, idx) {
				var item = retorno.filter(obj => {
				  return obj.label === key
				});
				if(item != '') {
					item[0].backgroundColor = cores[ordenados.length];
					ordenados.push(item[0]);
				}
			});

			retorno.forEach(function(obj){
				var idxCor = ordenados.length + naoOrdenados.length;
				if($.inArray(obj.label, ordemDataset) == -1) {
					obj.backgroundColor = cores[idxCor];
					naoOrdenados.push(obj);
				}
			});
			retorno = ordenados.concat(naoOrdenados);
		}

		return retorno;

	}

	criarAtributoDatasetGraficoPie() {

		var dataset = [];
		var labels = [];
		var valores = [];
		var argsFunction = [];

		$.each(this.matrizDados, function(categoria, valor) {

			labels.push(categoria);

			if(valor instanceof Object) {
				valores.push(valor.valor);

				if(valor.argsFunction !== undefined) {
					argsFunction.push(valor.argsFunction);
				}
			}else {
				valores.push(valor);
			}
		});

		dataset.push({label: labels, data: valores, argsFunction: argsFunction, backgroundColor: this.cores});

		return dataset;
	}

	prepararElementoDestino() {

		this.deletarGrafico();

		$('#'+this.idElemento).show();
		$('#'+this.idElemento).clone().attr('id', this.chartElement).insertAfter('#'+this.idElemento);
		$('#'+this.idElemento).hide();

	    return document.getElementById(this.chartElement).getContext('2d');

	}

	deletarGrafico() {

		if($('#'+this.chartElement).length) {
			$('#'+this.chartElement).remove();
		}

	}

	criarAtributoOptions() {

		var titulo = this.titulo;
		var attrTitle = {};

	    if(titulo != '' && titulo !== undefined) {
	    	attrTitle.display = true;
	    	attrTitle.fontSize = 16;
	    	attrTitle.padding = 20;
	    	attrTitle.text = titulo;
	    }

		var options = {
			title: attrTitle,
			layout: {
	            padding: {
	                top: 30
	            }
	        }
		};

		var funcaoCallback = this.onClick;

		if(this.onClick != '' && this.onClick !== undefined) {
			options = $.extend(options, {
				onClick: function(evt, item) {

				    if(item != '' && item !== undefined) {
						var bar = this.getElementAtEvent(evt)[0];
				        var index = bar._index;
				        var datasetIndex = bar._datasetIndex;
						var args = null;

						if(item[0]._chart.config.data.datasets[datasetIndex].argsFunction !== undefined) {
							args = item[0]._chart.config.data.datasets[datasetIndex].argsFunction[index];
						}

						window[funcaoCallback](args);
					}
				},
		      	onHover: function(e) {
        				if (this.getElementAtEvent(e).length) e.target.style.cursor = 'pointer';
         				else e.target.style.cursor = 'default';
        			}
			});
	    }

		if(this.tipoGrafico == 'bar' || this.tipoGrafico == 'horizontalBar') {
			options = $.extend(options, this.criarOptionPadraoBar());
		}else if(this.tipoGrafico == 'pie') {
			options = $.extend(options, this.criarOptionPadraoPie());
		}

		/**
		 * Atribuir options customizado
		 */
		var customOptions = this.customOptions
		for(var attr in customOptions) {
			if(customOptions.hasOwnProperty(attr) && options.hasOwnProperty(attr)) {
				options[attr] = customOptions[attr];
			}
		}

		return options;

	}

	criarOptionPadraoBar() {

		var formatoTextoBarra = this.formatoTextoBarra;
		var scales = this.getScale();
		var fillTextMargin = this.getFillTextMargin();

		var options = {
			responsive:true,
			tooltips: {
				enabled: false
			},
			hover: {
	        	animationDuration: 0
	        },
			scales: scales,
			legend:{
				position:'bottom'
			},
			animation: {
				duration: 500,
				easing: 'linear',
				onComplete: function (e) {
		            var chartInstance = this.chart,
	                ctx = chartInstance.ctx;
		            ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
		            ctx.textAlign = 'center';
		            ctx.textBaseline = 'bottom';

		            this.data.datasets.forEach(function (dataset, i) {
		                var meta = chartInstance.controller.getDatasetMeta(i);

		                meta.data.forEach(function (bar, index) {
		                    var data = dataset.data[index];

		                    switch(formatoTextoBarra) {
		                    	case 'percentual': data = data + '%'; break;
		                    	case 'financeiro': data = 'R$ ' + data; break;
		                    }
		                    if(meta.hidden !== true) {
		                    	ctx.fillText(data, bar._model.x + fillTextMargin.x, bar._model.y + fillTextMargin.y);
		                    }
		                });
		            });
		        }
			}
		};

		return options
	}

	criarOptionPadraoPie() {

		var formatoTextoBarra = this.formatoTextoBarra;

		var options = {
			tooltips: {
				enabled: true
			},
			hover: {
	        	animationDuration: 0
	        },
			scales: {
				xAxes:[{
					display: false
				}],
				yAxes:[{
					display: false
				}]
			},
			legend:{
				position:'right'
			},
			animation: {
				duration: 500,
				easing: 'easeOutQuart',
				onComplete:  function () {
					var ctx = this.chart.ctx;
					ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontFamily, 'normal', Chart.defaults.global.defaultFontFamily);
					ctx.textAlign = 'center';
					ctx.textBaseline = 'bottom';

					this.data.datasets.forEach(function (dataset) {
						for(var i = 0; i < dataset.data.length; i++) {
							var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model,
							total = dataset._meta[Object.keys(dataset._meta)[0]].total,
							mid_radius = model.innerRadius + (model.outerRadius - model.innerRadius)/2,
							start_angle = model.startAngle,
							end_angle = model.endAngle,
							mid_angle = start_angle + (end_angle - start_angle)/2;

							var x = mid_radius * Math.cos(mid_angle);
							var y = mid_radius * Math.sin(mid_angle);
							ctx.fillStyle = '#fff';

							var percent = String(Math.round(dataset.data[i]/total*100)) + "%";
							switch(formatoTextoBarra) {
		                    	case 'numberPercent':
		                    		percent = "("+dataset.data[i]+") " + String(Math.round(dataset.data[i]/total*100)) + "%";
		                    		break;
		                    }

							ctx.fillText(percent, model.x + x, model.y + y) + "%";
						}
					});
				}
			}
		};
		return options;
	}

	getScale() {

		var xAxes = [{
			display:true,
			ticks:{
				autoSkip: false
			}
		}];

		var yAxes = [];
		if(this.escala == 'logarithmic') {
			yAxes = [{
				type: 'logarithmic',
				gridLines: {
                	display: false,
              	},
				ticks:{
					display:false,
				}
			}];
		}
		else {
			yAxes = [{
				ticks:{
					beginAtZero:true
				}
			}];
		}

		if(this.tipoGrafico == 'horizontalBar') {
			var aux = xAxes;
			xAxes = yAxes;
			yAxes = aux;
		}

		var scales = {
			xAxes: xAxes,
			yAxes: yAxes
		}

		return scales;

	}

	getFillTextMargin() {
		var obj = {
			x: 0,
			y: 0
		};

		if(this.tipoGrafico == 'horizontalBar') {
			obj.x = -25;
			obj.y = -5;
		}
		else {
			obj.y = -5;
		}
		return obj;
	}

	getCores(coresCustomizadas) {

		var cores = [
			'rgba(255, 0, 0, 0.6)',
			'rgba(34, 97, 214, 0.6)',
			'rgba(214, 34, 199, 0.6)',
			'rgba(214, 117, 34, 0.7)',
			'rgba(93, 34, 214, 0.7)',
			'rgba(230,230,250)',
			'rgba(216,191,216)',
			'rgba(153, 255, 51,0.8)',
			'rgba(237, 162, 11, 0.6)',
			'rgba(37, 162, 11, 0.6)',
			'rgba(175,238,238)',
			'rgba(122, 51, 7, 0.6)',
			'rgba(122, 6, 53, 0.7)',
			'rgba(231, 53, 38,0.8)',
			'rgba(0, 0, 255,0.8)',
			'rgba(102, 51, 204,0.8)',
			'rgba(50,205,50,0.7)',
			'rgba(255,99,71,0.7)',
			'rgba(253, 245, 0,0.5)',
			'rgba(102, 51, 204,0.5)',
			'rgba(102, 204, 255,0.5)',
			'rgba(139, 0, 0,0.5)',
			'rgba(255, 102, 0,0.5)',
			'rgba(51, 102, 51,0.5)',
			'rgba(204, 153, 51,0.5)',
			'rgba(153, 153, 153,0.5)',
			'rgba(0, 0, 255,0.5)',
			'rgba(153, 255, 51,0.5)',
			'rgba(0, 153, 255,0.5)',
			'rgba(153, 204, 0,0.5)',
			'rgba(153, 204, 255,0.5)',
			'rgba(51, 102, 0,0.5)',
			'rgba(51, 51, 51,0.5)',
			'rgba(102, 0, 0,0.5)',
			'rgba(204, 255, 0,0.5)',
			'rgba(122, 6, 6, 0.5)'
		];

		if(coresCustomizadas != '') {
			for(var idx in coresCustomizadas) {
				if(idx == '-1' || idx == 'todos') {
					for(var i = 0; i < cores.length; i++) {
						if(coresCustomizadas[i] == undefined) {
							cores[i] = coresCustomizadas[idx];
						}
					}
				}

				if(coresCustomizadas.hasOwnProperty(idx) && cores[idx] != undefined) {
					cores[idx] = coresCustomizadas[idx];
				}
			}
		}
		/**
		 * Fixo 36 cores
		 */
		return cores;
	}
}