function addDinheiroValidator(){
	$.validator.addMethod('dinheiro', function (value) {
		//valida dinheiro no formato 125.201,58
		if(value == '')
			return true;
		return isDinheiro(value);
	}, '#.###,##');
}

function isDinheiro(value){
	//funciona mas nao valida corretamente o valor ,00
	//return /^(((\d{1,3})(\.(\d{3})){2}||(\d{1,3})\.(\d{3})||(\d{1,3})),\d{2})$/.test(value);
	return /^((\d{1,3}\.((\d{3}){1,5}))||(\d{1,3})),\d{2}$/.test(value);
}