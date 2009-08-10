function Cpf(cpf)
{
	this.cpf = cpf;
	this.verificadorUm;
	this.verificadorDois;



	/**
	 * checks if cpf is valid
	 * IMPORTANTE: se o cpf começa com 0, deve se usar aspas '012345678-09'
	 * 
	 * @var string cpf
	 * @return bool
	 */
	this.isValid = function(cpf)
	{
		this.setCpf(cpf);

		if(this.cpf.length !== 11)
			return false;

		if (false === this.verificaDigitoUm())
			return false;

		if (false === this.verificaDigitoDois())
			return false;

		return true;
	}


	/**
	 *	Checa se o cpf está no foratado ( 123.456.789-09 )
	 *	@return bool
	 */
	this.isString = function(value)
	{
		return /^(\d){3}(\.\d{3}){2}-(\d){2}$/.test(value);
	}

	/**
	 * 
	 *
	 */
	this.intToString = function(value)
	{
		try{
			var string = value ? value : this.cnpj;
			if(this.isString(string))
				return string;

			string = this.clean(string);
			if (string.length !== 11)
				throw new Exception();
			string = string.substr(0,3) + '.' +
					 string.substr(3,3) + '.' +
					 string.substr(6,3) + '-' +
					 string.substr(9,2);

		}catch(Err){}
		return string ? string : '';
	}


	/**
	 * removes any non digit char
	 */
	this.clean = function(value){
		return value.replace(/\D/g,'');
	}
	

	/**
	 * verifica o primeiro digito
	 */
	this.verificaDigitoUm = function()
	{
		var soma	= this.getSoma(10);
		var digito	= this.getDigito(soma);
		if (digito != this.verificadorUm)
			return false;
		return true;
	}


	this.verificaDigitoDois = function()
	{
		var soma	= this.getSoma(11);
		var digito	= this.getDigito(soma);
		if (digito != this.verificadorDois)
			return false;
		return true;
	}



	this.getDigito = function(soma)
	{
		var resto = soma % 11;
		if(resto == 0 || resto ==1)
			return 0;
		return 11-resto;
	}
	

	this.getSoma = function(multiplicador)
	{
		var soma = 0;
		for(var i = 0; i < (multiplicador-1); i++)
			soma += parseInt(this.cpf.charAt(i)) * (multiplicador -i);
		return soma;
	}


	/**
	 * sets cpf
	 *
	 * @var string cpf
	 * @return Cpf
	 */
	this.setCpf = function(cpf)
	{
		if (cpf)
			this.cpf = cpf.toString();
		this.cpf = this.clean(this.cpf);
		this.setVerificadores();
		return this;
	}


	/**
	 * busca dígitos verificadores
	 * @return this
	 */
	this.setVerificadores = function()
	{
		this.verificadorUm = this.cpf.charAt(9);
		this.verificadorDois = this.cpf.charAt(10);
		return this;
	}




}