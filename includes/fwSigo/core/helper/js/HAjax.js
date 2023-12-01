class HAjax {
   static prepareReturn(response, actionSuccess = null, actionFail = null) {

	    if (response.success && response.data !== null) {
	        return response.data;
	    } else if (response.success) {
	        HMensagem.alerta('success', response.message, function () {
	            if (actionSuccess)
	                new Function(actionSuccess)();
	        });
	    } else {
	        HMensagem.alerta(response.data == 'v' ? 'warning' : 'error', !!response.message ? response.message : 'TESTE', function () {
	            console.log(response.data);
	            if (actionFail)
	                new Function(actionFail)();
	        });
	        return false;
		}
	}

	static serializeForm(form) {
	    var o = {};
	    var a = form.serializeArray();
	    $.each(a, function () {
	        if (o[this.name]) {
	            if (!o[this.name].push) {
	                o[this.name] = [o[this.name]];
	            }
	            o[this.name].push(this.value || '');
	        } else {
	            o[this.name] = this.value || '';
	        }
	    });
	    return o;
	}
}