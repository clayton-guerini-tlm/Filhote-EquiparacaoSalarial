class HString {
   	static soNumero(string) {
   		return string.replace(/[\(\)\.\s-/]+/g,'');
	}
}