function CPFOuCNPJ(cpf)
{
	this.value = cpf;
	this.cpf = new Cpf();
	this.cnpj = new Cnpj();

	this.isValid = function(cpf)
	{
		value = cpf ? cpf : this.cpf;

		if (this.cpf.isValid(value))
			return true;

		if (this.cnpj.isValid(value))
			return true;

		return false;
	}

	this.intToString = function(value)
	{
		try{
			value = this.cpf.clean(value);
			if(value.length == 11)
				return this.cpf.intToString(value);

			if(value.length == 14)
				return this.cnpj.intToString(value);
		}catch(err){}
		return value;
	}
}