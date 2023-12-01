class HMensagem {
   	static alerta(typeAlerta, conteudo, funcaoRetorno = function() {}) {

	    var type = '';

	    switch (typeAlerta) {
	        case 'success':
	            type = 'blue';
	            break;

	        case 'error':
	            type = 'red';
	            break;

	        case 'warning':
	            type = 'orange';
	            break;

	        default:
	            type = 'blue';

	    }

	    var parametros = {
	        title: TITULO_DEFAULT,
	        content: "<h3> " + conteudo + " </h3>",
	        type: type,
	        typeAnimated: true,
	        boxWidth: '500px',
	        useBootstrap: false,
	        buttons: {
	            ok: {
	                text: 'OK',
	                btnClass: 'btn-' + type,
	                action: funcaoRetorno
	            }
	        }
	    };

	    $.confirm(parametros);

	}

	static confirmar(mensagem, funcaoSim = function() {}, funcaoNao = function(){}) {

	    var parametros = {
	        title: TITULO_DEFAULT,
	        content: "<h3> " + mensagem + " </h3>",
	        type: 'red',
	        typeAnimated: true,
	        boxWidth: '500px',
	        useBootstrap: false,
	        buttons: {
	            yes: {
	                text: 'SIM',
	                btnClass: 'btn-red',
	                action: funcaoSim
	            },
	            no: {
	                text: 'NÃƒO',
	                btnClass: 'btn-red',
	                action: funcaoNao
	            },
	            cancel: {
	                text: 'CANCELAR',
	                btnClass: 'btn-red',
	                action: function () {}
	            }
	        }
	    };

	    $.confirm(parametros);

	}

}