class HUrl {
   static get(nome) {
   		var results = new RegExp('[\?&]' + nome + '=([^&#]*)').exec(window.location.href);
	    if(results == null)
	        return null;
	    else
	        return decodeURI(results[1]) || 0;
	}
}