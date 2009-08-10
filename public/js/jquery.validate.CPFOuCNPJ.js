	/*
	 * Validador pra referencia de sapato Sxxxxx
	 */
function addCpfOuCnpjValidator(){
	$.validator.addMethod('CpfOuCnpj', function (value) {
		if(value.length > 0){
			var cpf =  new Cpf();
			if (cpf.isValid(value))
				return true;

			var cnpj =  new Cnpj();
			if (cnpj.isValid(value))
				return true;

			return false;
		}
		return true;

	}, 'CPF/CNPJ inválido.');
}