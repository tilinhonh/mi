function toDinheiro(value){

	//var value = element.val();
	var newValue = '';
	var decimal = 0;
	var intString = 0;

	//se é dinheiro, nada é feito
	if(isDinheiro(value))
		return value;

	//verifica se tem virgula
	var comma = value.lastIndexOf(',');
	if (comma < 0)
		comma = false;

	//se tiver comma, montar o valor
	if (comma) {
		var split = value.split(',');
		intString = split[0];
		decimal = parseInt(split[1]);
	}else{
		intString = value;
	}

	if (decimal === 0) {
		decimal = '00';
	}
	else if (decimal > 0 && decimal < 10) {
		decimal += '0';
	}
	else {
		decimal = decimal.toString().substr(0,2);
	}

	//removes non digits chars (1.234.567 -> 1234567)
	intString = intString.toString().replace(/\D/g,'')
	
	var currChar = '';
	var count = 0;
	var newIntString = '';

	for (i = intString.length; i  > 0; i--) {
		currChar = intString.substr(i-1,1);
		//alert(currChar);
		if (count++ % 3 === 0 && count > 1) {
			newIntString =  currChar + '.' + newIntString;
		}
		else{
			newIntString =  currChar + newIntString;
		}
	}

	newValue = newIntString + ',' + decimal;
	
	return newValue;
}