$(function(){
	$('#cpf').blur(function(){
		var cpf = new Cpf(this.value);
		if(this.value.length == 11)
			this.value = cpf.intToString(this.value);
		if ( cpf.isValid()){
			msg = "CPF Valido";
		}else{
			msg = "CPF INVÁLIDO!!!";
		}

		$('#valido').html(msg);
	});
	$('#cnpj').blur(function(){
		var cnpj = new Cnpj(this.value);
		if(this.value.length == 14)
			this.value = cnpj.intToString(this.value);
		if ( cnpj.isValid()){
			msg = "OK";
		}else{
			msg = "CNPJ INVÁLIDO!!!";
		}

		$('#cnpjValido').html(msg);
	});

});

