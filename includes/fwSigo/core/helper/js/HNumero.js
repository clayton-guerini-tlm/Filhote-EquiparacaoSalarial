class HNumero {
	
   	static convertDecimal2Moeda(valor , sigla = 'R$ ') {

   		var valorFormatado 	= Number(valor.replace(',', '.'));
		valorFormatado 		= valorFormatado.toFixed(2);
		valorFormatado 		= valorFormatado.replace('.', ',');
		valorFormatado 		= sigla + valorFormatado;

		return valorFormatado;
	}
}