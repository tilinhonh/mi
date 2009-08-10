/**
 * Validacao pra CNPJ
 */

function addCNPJValidator(){
	$.validator.addMethod('cnpj', function (value) {
		var cnpj =  new Cnpj();
		return cnpj.isValid(value);
	}, 'CNPJ inválido.');
}