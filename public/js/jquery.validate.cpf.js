	/*
	 * Validador pra referencia de sapato Sxxxxx
	 */
function addCPFValidator(){
	$.validator.addMethod('cpf', function (value) {
		var cpf =  new Cpf();
		return cpf.isValid(value);
	}, 'CPF inválido.');
}