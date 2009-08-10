$(function(){
	//jquery.validate tipo data brasileira
	addDataValidator();

	$('#formPedido').validate();

	$('.data').change(function(){
		dateFormat($(this));
	});

	$('.artigos').hover(function(e){
			$('<div id="divDetalhesItem"><img src="/images/ajax-loader.gif"/></div>')
				.attr('class','divInfo')
				.css('top', e.pageY - 150)
				.css('left', e.pageX + 10)
				.appendTo('body');

				var id = $(this).attr('id');
				id = id.substr(2);
				
				getDetalhesItem(id);
		},
		function(){
			$('#divDetalhesItem').remove();
	});

});

function delItem(id,nome){
	var msg = "Tem certeza que deseja excluir o modelo '" + nome + "' deste pedido?";
	var resposta = confirm(msg);
	if(resposta){
		wait();
		$.ajax({
			url:'/item-pedido/del',
			data: 'del=sim&id='+id,
			type:'POST',
			error:function(){alert('Erro ao deletar modelo do pedido.');stopWaiting();},
			success:
				function(json){
					var response = $.secureEvalJSON(json);
					if (response == true){
						alertMessages(response.error.messages);
						stopWaiting();
					}else{
						window.location.reload();
					}
				}
		});
	}
	return false;
}

 function getDetalhesItem(id)
 {
	$.ajax({
		type:'POST',
		data: 'id=' + id,
		url: '/item-pedido/get-itens',
		error:
			function(){
				alert('Erro ao buscar itens');
			},
		success:
			function(json){
				var itens = $.secureEvalJSON(json);
				var row;
				var item;

				$('#divDetalhesItem').html('<table id="tabelaDetalhes"></table>');
				$('<tr><th>Material</th><th>Cor</th><th>Importância</th><th>Estação</th></tr>').appendTo('#tabelaDetalhes');
				for(var i = 0; i < itens.length; i++){
					item = itens[i];
					row =  '<tr class="'+ getNextRowClass($('#tabelaDetalhes')) +'">' ;
					row += '<td>'		+ item.material.nome;
					row += '</td><td>'	+ item.cor.nome;
					row += '</td><td>'	+ item.importancia;
					row += '</td><td>'	+ item.estacao.nome;
					row += '</td></tr>';

					$('#tabelaDetalhes').append(row);
				}
			}
	});
 }