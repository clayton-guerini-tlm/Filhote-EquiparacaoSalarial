$(function(){


	/**LOAD PÁGINA*/
    $(document).ready(function(){
		$('#id').val(null);
		buscaUfs();
		buscaGrupos();
    });


	/**
	 * BUSCAR ESTADOS
	 */
	function buscaUfs() {

		$.ajax({
			data: {'funcaoAjax': 'CGrupoDeMunicipio::buscarUfs'
			}
		}).done(function (response) {
			   
		  if(response.data.length > 0){

			var dataset = HAjax.prepareReturn(response);

			$("#uf").empty();
			$('#uf').append('<option value=""> SELECIONE </option>');

			$("#uf_id").empty();
			$('#uf_id').append('<option value=""> SELECIONE </option>');
			
			dataset.forEach(function (value, index) {

				var opt = '';   
				opt += '<option value="{0}"> {1}'.format(value.id, value.uf);
				opt += '</option>';

				$('#uf').append(opt);
				$('#uf_id').append(opt);
			  });
	
		  }else{
	
			alerta('error','Erro ao carregar filiais.',function(){});
		  }
		});
	}

	/**
	 * BUSCAR ITENS
	 */
	$(document).on('submit', '#formFiltro', function (e) {
		e.preventDefault();

        var data = $.extend(
            {'funcaoAjax': 'CGrupoDeMunicipio::buscarGrupos'},
            HAjax.serializeForm($(this))
        );

        $.ajax({data}).done(function (response) {
           
            if(response.data.length > 0){    
            
                var dataset = HAjax.prepareReturn(response);
                new HDataTable('#tblGrupo', {
                    dataset : dataset
                });
    
            }else{

                alerta('error','Nenhum registro encontrado.',function(){
                });
            }
        });
	});


	/**
	 * Funcao responsavel por buscar grupos de municipios já cadastrados
	 */
	 function buscaGrupos() {

		var data = $.extend(
            {'funcaoAjax': 'CGrupoDeMunicipio::buscarGrupos'},
            HAjax.serializeForm($(this))
        );

        $.ajax({data}).done(function (response) {
           
            if(response.data.length > 0){    
            
                var dataset = HAjax.prepareReturn(response);
                new HDataTable('#tblGrupo', {
                    dataset : dataset
                });
    
            }else{

                alerta('error','Nenhum registro encontrado.',function(){
                });
            }
        });
	}

	function buscarCidadesGrupo() {
		var uf = $('#uf_id').val();

		$.ajax({
			data: {'funcaoAjax': 'CGrupoDeMunicipio::buscarCidades',
				'uf': uf,
				'idGrupo': $('#id').val()
			}
		}).done(function (response) {
			   
		  if(response.data.length > 0){

			var dataset = HAjax.prepareReturn(response);

			$('#listaCidades').html('');
			var dadosLista = '';

			$.each(dataset, function(index, val) {

				var tr = '';

				tr += '<tr>';
				tr += '<td>';

				if (val.marcado == 1 || val.marcado == '1') {

					checkId = "cidade_"+val.id;
					tr += '<input type="checkbox" id="cidade_{0}" name="cidades[]" class="cidades" value="{0}" checked><label for="cidade_{0}"></label><br>'.format(val.id);
					
				} else {
					tr += '<input type="checkbox" id="cidade_{0}" name="cidades[]" class="cidades" value="{0}"><label for="cidade_{0}"></label><br>'.format(val.id);
				}
				tr += '</td>';
				tr += '<td>{0}</td>'.format(val.nome);

				

				tr += '</tr>';
				dadosLista += tr;

			});

			$('#listaCidades').append(dadosLista);

		  }else{
	
			alerta('error','Erro ao carregar cidades.',function(){});
		  }
		});
	}

	//carrega opções de cidades
	$("#uf_id").change(function(){
		buscarCidadesGrupo();
	});


	/**
	 * Salvar grupo
	 */
	$("#salvarGrupo").on( "click",function(){
		
		var selected = [];
        $('#listaCidades input:checked[name="cidades[]"]').each(function(index, value) {
			selected.push($(value).val());
		});


		if (typeof($('#nome').val()) == 'undefined' || $('#nome').val() == null) {
			alerta('error','Nome do Grupo é obrigatorio.',function(){});
		}

		if (typeof($('#uf_id').val()) == 'undefined' || $('#uf_id').val() == null) {
			alerta('error','UF é obrigatorio.',function(){});
		}

		if (selected.length < 2) {
			alerta('error','Selecione pelo menos dois municipios.',function(){});
		}

    	$.ajax({
			data: {
				'funcaoAjax': 'CGrupoDeMunicipio::salvaGrupo',
				'id' : $('#id').val(),
				'nome' : $('#nome').val(),
				'uf_id' : $('#uf_id').val(),
				'cidades' : selected
			}
		}).done(function (response) {

			if(!response.success) {

				alerta('error', 'Erro ao salvar grupo! Verifique se uma das cidades selecionadas pertence a outro grupo', function (){
				});
				return;
			}
			$('#id').val(null);

			$('#modalGrupo').modal('hide');

			limparModalGrupo();

			alerta('success', 'Grupo salvo com sucesso', function (){
				buscaGrupos();
			});
		});
	});

	/**
   *Abre modal de edicao
   */
   $(document).on('click', '.editar-grupo', function() {

		console.log($(this));
		$('#modalGrupo').modal('show');
		$('#id').val($(this).data('id'));
		$('#nome').val($(this).data('nome'));
		$('#uf_id').val($(this).data('uf_id'));

		buscarCidadesGrupo();
	}); 

	function limparModalGrupo() {

		$('#listaCidades').html('');
		$('#id').val(null);
		$('#nome').val(null);
		$('#uf_id').val(null);
	}

	/**
   *Remove grupo
   */
	$(document).on('click', '.excluir-grupo', function() {

		console.log($(this));

		$.ajax({
			data: {
				'funcaoAjax': 'CGrupoDeMunicipio::removerGrupo',
				'id' : $(this).data('id'),
			}
		}).done(function (response) {
			alerta('success', 'Grupo removido com sucesso', function (){
				buscaGrupos();
			});
		});
	});

});