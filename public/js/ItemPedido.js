$(function(){
	addDinheiroValidator();
	getCombinacoes();
	getTotalQuantidade();

	// pega preco sugerido
	$('#itemID, #fabricaID').live('change',function(){
		getPrecosSugeridos();
	});

	$('.dinheiro').change(function(){
		this.value = toDinheiro($(this).val());
	});

	//edita produto
	$('#editProduto').click(function(){
		var url = '/produtos/edit/id/' + $('#produtoID').val();
		//window.location.href=url;
		window.open(url)
	});

	//calcula
	$('.quantidade').change(function(){
		getTotalQuantidade();
	});


    $('#produtoID').change(function(){
		getCombinacoes();
	});

	$('#itemPedido').submit(function(e){
		if($(this).valid()){
			wait();
			$.ajax({
				type: 'POST',
				url: '/item-pedido/' + $('#action').val(),
				data: $(this).serialize(),
				error:function(){alert('Erro ao consultar adicionar item.');},
				success:
					function(json){
						try{
							var response = $.secureEvalJSON(json);
							if(response.success == true){
								window.location.href = '/pedidos/edit/id/'+$('#pedido').val();
							}else{
								alertMessages(response.error.message);
							}
						}catch(e){}
						stopWaiting();
					}
			});
		}
		e.preventDefault();
	});

});//jquery end


function getCombinacoes(){
	wait();
	$.ajax({
			type: 'POST',
			url: '/combinacoes/get-combinacoes',
			data: 'produtoID=' + $('#produtoID').val() + '&item-do-pedido=' + $('#id').val(),
			error:function(){alert('Erro ao consultar combinações.');},
			success:
				function(json){
					 var response = $.secureEvalJSON(json);
					 var combinacoes = response.combinacao;
					 var row = '';
					 var item;
					 var a;
					 var tr_style;
					 var item;
					 $('#tabelaCombinacoes').find('tr:gt(0)').remove();
					 for(var i = 0 ; i < combinacoes.length; i++){
						 item = combinacoes[i].item;
						 size = item.length;
						 tr_style = getNextRowClass($('#tabelaCombinacoes'));
						 for(a = 0 ; a < size; a++){
							checked = combinacoes[i].selected == true ? ' CHECKED ' : '';
							row = '<tr class="' + tr_style + '">';
							row += a == 0 ? '<td class="editDelCell" rowspan="' + size + '">'
								+'<input type="radio" class="radioCombinacoes" name="itemID" id="itemID" value="'+ combinacoes[i].id + '"'+ checked +'/></td>' : '';
							row += '<td>' + item[a].material.nome + '</td>';
							row += '<td>' + item[a].cor.nome + '</td>';
							row += '<td class="editDelCell">' + item[a].importancia + '</td>';
							row += '<td class="center">' +  item[a].gmc.codigo + '</td>';
							row += '</tr>';
							$('#tabelaCombinacoes').append(row);
						 }
					 }
					 getPrecosSugeridos();
					 stopWaiting();
				}
		});
}

//calcula total de unidades
function getTotalQuantidade(){
	var total=0
	var valor;
	$('.quantidade').each(function(){
		valor = parseInt($(this).val());
		if(!isNaN(valor))
			total += valor;
	});
	$('#totalQuantidade').text(total);
}


function getPrecosSugeridos(){
	var combinacao = $("input:radio[@name=itemID]").filter(':checked').val();
	if($('#fabricaID').val() && combinacao && $('#action').val() == 'edit'){
		wait();
		$.ajax({
			type:'POST',
			url:'/precos/get-last-price',
			data: 'combinacao=' + combinacao + '&fabrica=' + $('#fabricaID').val(),
			error:function(){alert('Erro ao buscar preço sugerido.'); stopWaiting();},
			success:
				function(json){
					var preco = $.secureEvalJSON(json);
					$('#precoFabricaSugerido').text(preco.fabrica);
					$('#precoVendaSugerido').text(preco.venda);
					$('#precoPvlSugerido').text(preco.pvl);
					$('#precoDataSugerido').text(preco.data != null ? preco.data : '');
					stopWaiting();
				}

		});
	}
}
