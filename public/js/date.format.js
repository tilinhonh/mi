function dateFormat(field){
	var value = field.val();
	var size = value.length;
	if(size === 6 || size == 4){
		var day = value.substr(0,2);
		var month = value.substr(2,2);
		var year = (size == 6) ? '20' + value.substr(4,2) :  new Date().getFullYear();
		var newValue = day +'/'+ month +'/'+ year;
		field.val(newValue);
	}
}