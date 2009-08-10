$(function(){
	$('#myForm').validate();

	$('.dinheiro').change(function(){
		this.value = toDinheiro(this.value);
	});

	addCNPJValidator();
	$('.cnpj').blur(function(){
		var cnpj = new Cnpj();
		this.value = cnpj.intToString(this.value);
	});

	$('#estadoID').change(function(){
		//no action taken if no value
		if($(this).val() == ""){
			$("#cidadeID").removeOption(/./);
			return false;
		}
		var url='/cidades/mostrar-cidades/';
		var myOptions = {
				"uf" : $(this).val()
			}
		wait();
		$("#cidadeID").removeOption(/./);
		$('#cidadeID').ajaxAddOption(url,myOptions, false,stopWaiting);
	});
});//jquery end