function addDataValidator(){
	$.validator.addMethod('data', function (value) {
		if(value === ''){
			return true;
		}

		if(/^\d{2}\/\d{2}\/\d{4}$/.test(value) === false ){
			return false;
		}

        var date = value.split('/');
        var day = date[0];
        var month = date[1];
        var year = date[2];

        //basic check
        if(day.length !== 2 || month.length !== 2 || year.length !== 4 ||
            day > 31 || month > 12 ){
            return false;
        }

        //30 days months
        var _30days = new Array(4,6,9,11);

        for(var a = 0; a < _30days.length; a++){
            if(month == _30days[a] && day > 30)
                return false;
        }

        //fevereiro
        if(month == 2){
            if(day > 29){
                return false
            }

            var leapYear = false;

            if(year % 400 == 0){
                leapYear = true;
            }else{
                if(year % 4 == 0 && year % 100 !== 0){
                    leapYear = true;
                }
            }
            if(!leapYear && day > 28){
                return false;
            }
        }
        return true;

	}, 'dd/mm/aaaa');
}