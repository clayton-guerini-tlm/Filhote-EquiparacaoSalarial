$(function(){


	/**LOAD PÁGINA*/
    $(document).ready(function(){
		buscaFilial();
		buscaDiretorias();
		buscaGerentes();
    });

	
	/**
	 * BUSCAR Lideres
	 */
	function buscaFilial() {

		$("#idFilial").empty();

		$.ajax({
			data: {
				'funcaoAjax': 'CEquiparacaoSalarial::buscaFilial',
				'chapaDiretor': $('#chapaDiretor').val()
			}
		}).done(function (response) {
			   
		  if(response.data.length > 0){

			var dataset = HAjax.prepareReturn(response);

			
			$('#idFilial').append('<option value=""> Selecione </option>');
			
			dataset.forEach(function (value, index) {

				console.log(value);

				var opt = '';   
				opt += '<option value="{0}"> {1}'.format(value.idFilial, value.filial);
				opt += '</option>';

				$('#idFilial').append(opt);
			  });
	
		  }else{
	
			alerta('error','Erro ao carregar filiais.',function(){});
		  }
		});
	}

	/**
	 * BUSCAR Diretoria
	 */
	 function buscaDiretorias() {

		$("#chapaDiretor").empty();

        $.ajax({
			data: {'funcaoAjax': 'CEquiparacaoSalarial::buscaDiretorias',
				'idFilial': $('#idFilial').val()
			}
		}).done(function (response) {
			   
		  if(response.data.length > 0){

		  	console.log(response);

			var dataset = HAjax.prepareReturn(response);

			if (dataset) {

				$('#chapaDiretor').append('<option value=""> Selecione </option>');
				
				dataset.forEach(function (value, index) {

					console.log(value);

					var opt = '';   
					opt += '<option value="{0}"> {1}'.format(value.chapaFuncionario, value.nomeFuncionario);
					opt += '</option>';

					$('#chapaDiretor').append(opt);
				 });
			}
		  }else{
	
			//alerta('error','Erro ao carregar Seções do usuário.',function(){});
		  }
		});
	}

	/**
	 * BUSCAR Lideres
	 */
	function buscaGerentes() {

		$("#chapaLider").empty();

        $.ajax({
			data: {'funcaoAjax': 'CEquiparacaoSalarial::buscaLideres',
				'idFilial': $('#idFilial').val(),
				'chapaDiretor': $('#chapaDiretor').val(),
				'codColigada': $('#codColigada').val()
			}
		}).done(function (response) {
			   
		  if(response.data.length > 0){

		  	console.log(response);

			var dataset = HAjax.prepareReturn(response);

			if (dataset) {

				$('#chapaLider').append('<option value=""> Selecione </option>');
				
				dataset.forEach(function (value, index) {

					console.log(value);

					var opt = '';   
					opt += '<option value="{0}"> {1}'.format(value.chapaFuncionario, value.nomeFuncionario);
					opt += '</option>';

					$('#chapaLider').append(opt);
				 });
			}
		  }else{
	
			//alerta('error','Erro ao carregar Seções do usuário.',function(){});
		  }
		});
	}
	
	/**
	 * BUSCAR ITENS
	 */
	$(document).on('submit', '#formFiltro', function (e) {
		e.preventDefault();

        var data = $.extend(
            {'funcaoAjax': 'CEquiparacaoSalarial::listarFuncionariosEquiparacao'},
            HAjax.serializeForm($(this))
        );

        $.ajax({data}).done(function (response) {
           
            if(response.data.length > 0){    
            
                var dataset = HAjax.prepareReturn(response);
                new HDataTable('#tblEquiparacao', {
                    dataset : dataset
                });
    
            }else{

                alerta('error','Nenhum registro encontrado.',function(){
                });
            }
        });
	});

	function atualizaListagem() {

		$('.confirmar').trigger('click');
	}


	//carrega opções de seção
	$("#chapaLider").change(function(){

		var chapaLider = $('#chapaLider').val();
		var codColigada = $('#codColigada').val();
	
		$.ajax({
			data: {'funcaoAjax': 'CEquiparacaoSalarial::buscaCentrosDeCustosPorChapa',
				'chapaLider': chapaLider,
				'codColigada': codColigada
			}
		}).done(function (response) {
			   
		  if(response.data.length > 0){

			var dataset = HAjax.prepareReturn(response);

			$("#codSecao").empty();
			$('#codSecao').append('<option value=""> Selecione </option>');
			
			dataset.forEach(function (value, index) {

				console.log(value);

				var opt = '';   
				opt += '<option value="{0}"> {0}'.format(value.codSecao);
				opt += '</option>';

				$('#codSecao').append(opt);
			});

			$('#codSecao').prop("disabled", false);
	
		  }else{
	
			alerta('error','Erro ao carregar Seções do usuário.',function(){});
		  }
		});
	});

	//Atualizar lista de gerentes por diretor
	$("#chapaDiretor").change(function(){
		buscaGerentes();
		buscaFilial();

	});


	//Atualizar lista de gerentes
	$("#idFilial").change(function(){
		buscaGerentes();
	});

	

	$('.equiparacaoSalarial').click(function () {
        $('#formFiltro')[0].reset();
        $('#div-enviar-folha').hide("slow");
        $('#div-gerenciar-coparticipacao').show("slow");
        $('.div-titulo').html('').append('EQUIPARACAO SALARIAL');
    });

    $('.enviarFolha').click(function () {
        // $('#formFolha')[0].reset();
        $('#div-gerenciar-coparticipacao').hide("slow");
        $('#div-enviar-folha').show("slow");
        $('.div-titulo').html('').append('ENVIAR PARA FOLHA');
    });


	$('.acoesEquiparacao').click(function () {
        $('#formFiltro')[0].reset();
        $('#div-enviar-folha').hide("slow");
        $('#div-gerenciar-coparticipacao').show("slow");
        $('.div-titulo').html('').append('EQUIPARACAO SALARIAL');
    });

	/**
   	*Abre justficativa
   	*/
   $(document).on('click', '.acoesEquiparacao', function() {

		var id = $(this).data('id');

		$('#idEquiparacao').val(id);

		$.ajax({
			data: {
				'funcaoAjax': 'CEquiparacaoSalarial::buscarEquiparacao',
				'id' : id
			}
		}).done(function (response) {

			if(!response.success) {

				$('#idEquiparacao').val(id);

				alerta('error', 'Erro ao buscar equiparação!', function (){
				});
				return;
			}

			var dataset = HAjax.prepareReturn(response);
			$('#justificativa').val(dataset[0].justificativa);
			$('#validado').val(dataset[0].validado);
			$('#chapa').val(dataset[0].chapa);
			$('#nomeFuncionario').val(dataset[0].nomeFuncionario);

			console.log(dataset);
		    $('#justificativaModal').modal('show');
		});
	}); 

	/**
   	*Salva justificativa
   	*/
	   $(document).on('click', '.naoEquiparacao', function() {

		$.ajax({
			data: {
				'funcaoAjax': 'CEquiparacaoSalarial::salvarJustificativa',
				'idEquiparacao' : $('#idEquiparacao').val(),
				'justificativa' : $('#justificativa').val(),
				'validado' : $('#validado').val(),
				'chapa' : $('#chapa').val(),
				'nomeFuncionario' : $('#nomeFuncionario').val()
			}
		}).done(function (response) {

			if(!response.success) {

				alerta('error', 'Erro ao Salvar equiparação!', function (){
				});
				return;
			}
			alerta('success', 'Justificativa salva!', function (){
				$('#justificativaModal').modal('hide');
				atualizaListagem();
			});
		});
	}); 
/**
   	*Abre Historico
   	*/
	   $(document).on('click', '.acoesMotivos', function() {

		var chapa = $(this).data('chapa');

		$('#chapa').val(chapa);

		$.ajax({
			data: {
				'funcaoAjax': 'CEquiparacaoSalarial::buscarMotivos',
				'chapa' : chapa
			}
		}).done(function (response) {

			var dados = HAjax.prepareReturn(response);
            var tr = '';

        	$.each(dados, function(idx, value) {

    
					tr += '<tr style="height:25px;">';
					tr += '<td>{0}</td>'.format(value.chapa);
					tr += '<td>{0}</td>'.format(value.nomeFuncionario);
					tr += '<td>{0}</td>'.format(value.justificativa);
					tr += '<td>{0}</td>'.format(value.nomeValidado);
					tr += '<td>{0}</td>'.format(value.dataCadastro);

				tr += '</tr>';
        	});

        	$('#tbodyMotivo').html(tr);
            new HDataTable('#tblMotivo');
		});
		$('#motivosModal').modal('show');
	}); 



	//$("#idModelNaoEquiparacao").load('http://localhost/SIGO_INTEGRADO_3/SIGO_MG/sigo_ddg/EQUIPARACAO_SALARIAL/?mainapp=equiparacao&app=modal_justificativa_nao_equiparacao');


});