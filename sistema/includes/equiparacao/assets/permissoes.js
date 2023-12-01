$(function(){

	var listaPermissoes = [];

	/**LOAD PÁGINA*/
    $(document).ready(function(){
		$('#id').val(null);
		buscaFilial();
    });


	
	/**
	 * BUSCAR ITENS
	 */
	$(document).on('submit', '#formFiltro', function (e) {
		e.preventDefault();
		
        var data = $.extend(
            {'funcaoAjax': 'CPermissoesEquiparacao::listarUsuarios'},
            HAjax.serializeForm($(this))
        );

        $.ajax({data}).done(function (response) {
           
            if(response.data.length > 0){    
            
                var dataset = HAjax.prepareReturn(response);
                new HDataTable('#tblFuncionario', {
                    dataset : dataset
                });
    
            }else{

                alerta('error','Nenhum registro encontrado.',function(){
                });
            }
        });
	});

	function buscarusuario() {

		var data = $.extend(
            {'funcaoAjax': 'CPermissoesEquiparacao::listarUsuarios'},
            HAjax.serializeForm($(this))
        );

        $.ajax({data}).done(function (response) {
           
            if(response.data.length > 0){    
            
                var dataset = HAjax.prepareReturn(response);
                new HDataTable('#tblFuncionario', {
                    dataset : dataset
                });
    
            }else{

                alerta('error','Nenhum registro encontrado.',function(){
                });
            }
        });
	}

	function buscaFilial() {

		$.ajax({
			data: {'funcaoAjax': 'CPermissoesEquiparacao::buscaFilial'
			}
		}).done(function (response) {
			   
		  if(response.data.length > 0){

			var dataset = HAjax.prepareReturn(response);

			$("#idFilial").empty();
			$('#idFilial').append('<option value=""> Selecione </option>');
			
			dataset.forEach(function (value, index) {
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

	function buscarPermissoesusuario() {

		$.ajax({
			data: {'funcaoAjax': 'CPermissoesEquiparacao::buscarFiliaisUsuario',
				'chapa': $('#chapa').val()
			}
		}).done(function (response) {

			
			   
		  if(response.data.length > 0){

			var dataset = HAjax.prepareReturn(response);

			listaPermissoes = dataset;

			$('#listaFiliais').html('');
			var dadosLista = '';
			console.log(dataset);

			$.each(dataset, function(index, val) {

				var tr = '';

				tr += '<tr>';
				tr += '<td>';

				if (val.marcado == 1 || val.marcado == '1') {

					checkId = "filial_"+val.id;
					tr += '<input type="checkbox" id="filial_{0}" name="filiais[]" class="filiais" value="{0}" checked><label for="filial_{0}"></label><br>'.format(val.id);
					
				} else {
					tr += '<input type="checkbox" id="filial_{0}" name="filiais[]" class="filiais" value="{0}"><label for="filial_{0}"></label><br>'.format(val.id);
				}
				tr += '</td>';
				tr += '<td>{0}</td>'.format(val.nomeFilial);

				tr += '</tr>';
				dadosLista += tr;

			});

			$('#listaFiliais').append(dadosLista);

		  }else{
	
			alerta('error','Erro ao carregar filiais.',function(){});
		  }
		});
	}


	/**
	 * Salvar grupo
	 */
	$("#salvarPermissoes").on( "click",function(){
		
		var selected = [];
        $('#listaFiliais input:checked[name="filiais[]"]').each(function(index, value) {
			selected.push($(value).val());
		});

		if (selected.length < 1) {
			alerta('error','Selecione ao menos uma filial.',function(){
				return;
			});
		}

    	$.ajax({
			data: {
				'funcaoAjax': 'CPermissoesEquiparacao::salvarPermissao',
				'chapa' : $('#chapa').val(),
				'filiais' : selected
			}
		}).done(function (response) {

			if(!response.success) {

				alerta('error', 'Erro ao salvar premissões!', function (){
				});
				return;
			}
			$('#id').val(null);

			$('#modalPermissoes').modal('hide');

			limparModalEdicao();

			alerta('success', 'Permissões salvas com sucesso', function (){
			});
		});
	});


	$(document).on('click', '.marcar-todos', function() {

		/*$('#listaFiliais').html('');

		$.each(listaPermissoes, function(index, val) {

			var tr = '';

			tr += '<tr>';
			tr += '<td>';

			checkId = "filial_"+val.id;
			tr += '<input type="checkbox" id="filial_{0}" name="filiais[]" class="filiais" value="{0}" checked><label for="filial_{0}"></label><br>'.format(val.id);	
			
			tr += '</td>';
			tr += '<td>{0}</td>'.format(val.nomeFilial);

			tr += '</tr>';
			dadosLista += tr;

		});*/

		$('#listaFiliais input').each(function(index, value) {

			console.log(value);

			if ($(".marcar-todos").prop( "checked")) {

				console.log($(this));

				$(this).prop("checked", true);
			} else {

				console.log($(this));

				$(this).prop("checked", false);
			}
		});
	});

	/**
   *Abre modal de edicao
   */
   $(document).on('click', '.editar-permissoes', function() {

		console.log($(this));
		$('#modalPermissoes').modal('show');
		$('#chapa').val($(this).data('chapa'));
		$('#nome').val($(this).data('nome'));
		buscarPermissoesusuario();
	}); 

	function limparModalEdicao() {

		$('#listaCidades').html('');
		$('#chapa').val(null);
		$('#nome').val(null);
	}

});