if (!String.prototype.format) {
	
	String.prototype.format = function() {
		
		var args = arguments;
		
		return this.replace(/{(\d+)}/g, function(match, number) {
			return typeof args[number] != 'undefined' ? args[number] : match;
		});
		
	};
	
}

if (!String.prototype.left) {
	
	String.prototype.left = function() {
		
		var args = arguments;
		
		if(isNaN(args[0])) {
			return null;
		}
		
		return this.substring(0, args[0]);
		
	};
	
}

if (!String.prototype.right) {
	
	String.prototype.right = function() {
		
		var args = arguments;
		
		if(isNaN(args[0])) {
			return null;
		}
		
		return this.substr(this.length - args[0]);
		
	};
	
}