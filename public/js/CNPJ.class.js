function Cnpj(cnpj)
{
	this.cnpj = cnpj;
	this.verificadorUm;
	this.verificadorDois;



	/**
	 * checa cnpj
	 * IMPORTANTE: se o cnpj começa com 0, deve se usar aspas '01234567890123'
	 * 
	 * @var string cnpj
	 * @return bool
	 */
	this.isValid = function(cnpj)
	{
		this.setCnpj(cnpj);
		
		if(this.cnpj.length !== 14)
			return false;

		if (false === this.verificaDigitoUm())
			return false;

		if (false === this.verificaDigitoDois())
			return false;

		return true;
	}


	/**
	 *	Checa se o cnpj está no formatado ( 12.345.678/0001-12 )
	 *	@return bool
	 */
	this.isString = function(value)
	{
		return /^(\d){2}(\.\d{3}){2}\/(\d){4}-(\d){2}$/.test(value);
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
			if (string.length !== 14)
				throw new Exception();
			string = string.substr(0,2) + '.' +
					 string.substr(2,3) + '.' +
					 string.substr(5,3) + '/' +
					 string.substr(8,4) + '-' +
					 string.substr(12,2);

		}catch(Err){}
		return string ? string : '';
	}


	/**
	 * removes any non digit char
	 */
	this.clean = function(value){
			return value.replace(/\D/g,'');
	}
	

	this.verificaDigitoUm = function()
	{
		var soma = 0;
		var multiplicador = [5,4,3,2,9,8,7,6,5,4,3,2];
		for(var i = 0; i < multiplicador.length; i++){
			soma += parseInt(this.cnpj.charAt(i)) * multiplicador[i];
		}
		var digito	= this.getDigito(soma);
		if (digito != this.verificadorUm)
			return false;
		return true;
	}

	this.verificaDigitoDois = function()
	{
		var soma = 0;
		var multiplicador = [6,5,4,3,2,9,8,7,6,5,4,3,2];
		for(var i = 0; i < multiplicador.length; i++){
			soma += parseInt(this.cnpj.charAt(i)) * multiplicador[i];
		}
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
			soma += parseInt(this.cnpj.charAt(i)) * (multiplicador -i);
		return soma;
	}


	/**
	 * sets cnpj
	 *
	 * @var string cnpj
	 * @return cnpj
	 */
	this.setCnpj = function(cnpj)
	{
		if (cnpj)
			this.cnpj = cnpj.toString();
		this.cnpj = this.clean(this.cnpj);
		this.setVerificadores();
		return this;
	}


	/**
	 * busca dígitos verificadores
	 * @return this
	 */
	this.setVerificadores = function()
	{
		this.verificadorUm = this.cnpj.charAt(12);
		this.verificadorDois = this.cnpj.charAt(13);
		return this;
	}




}