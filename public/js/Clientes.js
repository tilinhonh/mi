$(function(){

	addCpfOuCnpjValidator();
	$('.dinheiro').change(function(){
		this.value = toDinheiro($(this).val());
	});

	$('#formID').validate();
	checkDivisao();
	$('#divisaoID').change(function(){
		checkDivisao();
	});

	$('.CpfOuCnpj').change(function(){
		var o = new CPFOuCNPJ();
		this.value = o.intToString(this.value);
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

function esconderCamposParaEmpresas()
{
	$('#inscricaoEstadual, #nome_oficial, #contato').fadeOut().attr('disabled','disabled');
}

function mostrarCamposParaEmpresas()
{
	$('#inscricaoEstadual, #nome_oficial, #contato').fadeIn().removeAttr('disabled');
}

function checkDivisao(){
	if ($('#divisaoID').val() == 3){
		esconderCamposParaEmpresas();
	}
	else{
		mostrarCamposParaEmpresas();
	}
}