	/*
	 * Validador pra referencia de sapato Sxxxxx
	 */
function addReferenciaValidator(){
	$.validator.addMethod('referencia', function (value) {
		//s
		return /^[sS]\d{5}$/.test(value);
	}, 'Referência Inválida.');
}