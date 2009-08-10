var delImg = 'src="/images/del.png" alt="delete" title="Delete"';
var editImg = 'src="/images/edit.png" alt="edit" title="Edit"';

$(function(){
	addDataValidator();
	addReferenciaValidator();
	addDinheiroValidator();

	$('.data').live('change',function(){
		dateFormat($(this));
	});
	
	$('.dinheiro').live('change',function(){
		this.value = toDinheiro($(this).val());
	});
	
    $('.menuCentral').hide();
    $('.fechar a').live('click',function(){
        $('.menuCentral').hide();
        $(this).parents('div:eq(1)').slideUp();
        //clearCombinacoes();
        return false;
    });

    $('.data').live('change',function(){
        var value = $(this).val();
        var size = value.length;
        if(size === 6){
            var day = value.substr(0,2);
            var month = value.substr(2,2);
            var year = '20' + value.substr(4,2);
            var newValue = day +'/'+ month +'/'+ year;
            $(this).val(newValue);
        }
    });

    $('#divRightMenu').hide();
    atualizarCombinacoesDoProduto();
	$('#formProdutos').validate();

	

   
    $('#suggestNcm').autocomplete("/async/suggest-ncm",{
			onItemSelect:setNcmID,
			delay:50,
			autoFill:true,
			mustMatch:1

		});


    $('#addCombination').live('click',function(){
        $('#divPreco').hide();
        $('#combinacaoSubmit').val('Adicionar');
        clearRightDiv();
        $('#divRightMenu').slideDown();
        $('#action').val('add');
        return false;
    });


    $('#suggestGrupoMaterialCor').autocomplete("/grupo-material-cor/suggest",{
		onItemSelect:addGMCToTable,
		delay:50,
		autoFill:true,
		mustMatch:1
	});


    $('#formCombinacoes').submit(function(e){
        wait();
        $.ajax({
            url:'/combinacoes/' + $('#action').val(),
            type:'POST',
            data:$(this).serialize() + '&produtoID=' + $('#id').val(),
            error:function(){alert('Erro ao tentar iserir/modificar GMC.');stopWaiting();},
            success:function(json){
                //alert(json);
                var response = $.secureEvalJSON(json);
                if(response.success === true){
                    clearRightDiv();
                    $('#divRightMenu').toggle();
                    atualizarCombinacoesDoProduto();
                    //atualizar tabela de combinacoes
                }else{
                    alertMessages(response.error.message);
                    stopWaiting();
                }
            }
        });
        e.preventDefault();
    });

    $('#addNewPrice').click(function(){
        createPrecoForm();
        return false;
    });

     $('#formPrecos').live('submit',function(e){
         wait();
         try{
            $(this).validate();
            if($(this).valid()){
                wait();
                $.ajax({
                    url:'/precos/save-register',
                    data:$(this).serialize() + '&combinacaoID=' + $('#combinacaoID').val(),
                    type:'POST',
                    success:updatePrecos,
                    error : function(){alert('Erro ao tentar atualizar/inserir preço.')}
                });
            }
         }catch(e){}
		 stopWaiting();
        e.preventDefault();
    });


    $('.editPreco').live('click',function(){
        wait();
        var eID = $(this).attr('id');
        var precoID = eID.substr(4);

        $.ajax({
            type :'POST',
            url : '/precos/get',
            data : 'precoID=' + precoID,
            success : createPrecoForm,
            error : function(){alert('Erro ao tentar atualizar preço.')}
        });
        return false;
    });

    $('.delPreco').live('click',function(){
        wait();
        $('.menuCentral').hide();
        var eID = $(this).attr('id');
        var precoID = eID.substr(3);
        var resp = confirm('Tem certeza que deseja deletar este preço?');
        if(resp){
            $.ajax({
                url:'/precos/del',
                data: 'precoID=' + precoID,
                type:'POST',
                error:function(){alert('Erro Interno.');},
                success:function(json){
                    try{
                        var response = $.secureEvalJSON(json);
                        if(response.success === true){
                            getPrecos();
							$('#divTodosPrecos').remove();
                        }
                        else{
                            showErrorMessage(response);
                        }
                    }catch(e){
                        return false;
                    }
                }
            });
        }
            return false;
    });



	//picture slider
	var pics = $('#pictures').val().split(';');
	curPic = 0;
	var speed = 1000 ;
	var picturePath = '/images/_produtos/';
	$('#nextPic').hover(function(){
		curPic = ( ++curPic == pics.length ) ? 0 : curPic
		var	pic = curPic;

		$('#pic')
			.hide()
			.attr('src', picturePath + pics[pic])
			.fadeIn(speed);

		return false;
	});
	$('#nextPic , #prevPic').click(function(){return false;});

	$('#prevPic').hover(function(){
		curPic = ( --curPic < 0 ) ? (pics.length - 1 ) : curPic
		var	pic = curPic;

		$('#pic')
			.hide()
			.attr('src',picturePath + pics[pic])
			.fadeIn(speed);

		return false;
	});


	$('#delPicture').click(function(){
		return false;
	});


	//precos
	$('#showPrecos').click(function(e){
		wait();
		$.ajax({
			url: '/precos/get-all-prices',
			type: 'POST',
			data: 'produtoID=' + $('#id').val(),
			error:function(){

				},
			success: function(json){
					number = 0;
					$('#divTodosPrecos').remove();
					$('<div id="divTodosPrecos" class="detalhes"></div>').prependTo('body');
					$('<div class="fechar">Preços<a href="#">x</a></div>').prependTo('#divTodosPrecos');
					$('<table id="tabelaTodosPrecos"><tr><th></th><th>Fábrica</th><th>Preço Fábrica</th><th>Preço Cliente</th>\n\
						<th>PVL</th><th>Data</th><th></th></tr><table>').appendTo('#divTodosPrecos');
					resp = $.secureEvalJSON(json);
					for (i = 0; i< resp.length; i++) {
						$('#combinacaoID').val(resp[i].combinacao.id);
						number = (i > 0) && (resp[i].combinacao.id == resp[i-1].combinacao.id) ? number : ++number
						row = '<tr class="' + getNextRowClass($('#divTodosPrecos')) + '">';
						row += '<td class="center">#'+ number +'</td>';
						row += '<td>' + resp[i].fabrica.nome + '</td>';
						row += '<td class="right">' + resp[i].preco.fabrica + '</td>';
						row += '<td class="right">' + resp[i].preco.venda + '</td>';
						row += '<td class="right">' + resp[i].preco.pvl + '</td>';
						row += '<td class="center">' + resp[i].preco.data + '</td>';
						row += '<td class="editDelCell">';
						row += '<img id="edit'+ resp[i].id +'" class="editPreco" title="Edit" alt="edit" src="/images/edit.png"/>';
						row += '<img id="del'+ resp[i].id +'" class="delPreco" title="Delete" alt="delete" src="/images/del.png"/>';
						row += '</tr>';

						$('#tabelaTodosPrecos tbody').append(row);
					}
					$('#divTodosPrecos').css('right', 0)
										.css('top', '70px');
					stopWaiting();
				}
		});


		return false;
	});





});//jquery end

function updatePrecos(json)
{
    try{
        var response = $.secureEvalJSON(json);
        if(response.success === true){

            getPrecos();
            $('.menuCentral').slideUp();

			$('#divTodosPrecos').remove();

            return true;
        }
        else{
            alertMessages(response.error.message);
        }
    }catch(e){
        return false;
    }
    return true;
}

function createPrecoForm(json){

    var buttonName = '';
    if(json){
        var preco = $.secureEvalJSON(json);
        buttonName = "Alterar";
    }
    else{
        preco = new Object();
        preco.id = '';
        preco.fabrica = new Object();
        preco.fabrica.id = '';
        preco.fabrica.nome = '';
        preco.pFabrica = '';
        preco.pVenda = '';
        preco.pvl = '';
        preco.data = '';
        buttonName = "Adicionar";
    }

        $('.menuCentral').html('<div class="fechar">Preços<a href="#">x</a></div>');
        $('.menuCentral').append('<form id="formPrecos"><div class="divInner">');
        $('.menuCentral .divInner').append('<input type="hidden" name="precoID" value="'
                                        + preco.id +'" size="13" maxlength="13"/>');

        $('.menuCentral .divInner').append('<label for="fabrica">Fábrica:</label><br/>');
        $('.menuCentral .divInner').append('<select id="fabricaID" name="fabricaID"></select><br/>');

        $('#fabricaID').ajaxAddOption('/fabricas/get',{},false,selectComboOption,
                                                [{'comboID':'fabricaID',
                                                'selectedValue':preco.fabrica.id}]);

        $('.menuCentral .divInner').append('<label for="pFabrica">Preço Fábrica:</label><br/>');
        $('.menuCentral .divInner').append('<input type="text" class="required dinheiro" id="pFabrica" name="pFabrica" value="'
                                        + preco.pFabrica +'" size="6" maxlength="6"/><br/>');
        $('.menuCentral .divInner').append('<label for="pVenda">Preço Venda:</label><br/>');
        $('.menuCentral .divInner').append('<input type="text" class="dinheiro" id="pVenda" name="pVenda" value="'
                                        + preco.pVenda +'" size="6" maxlength="6"/><br/>');
        $('.menuCentral .divInner').append('<label for="pvl">PVL:</label><br/>');
        $('.menuCentral .divInner').append('<input type="text" class="dinheiro" id="pvl" name="pvl" value="'
                                        + preco.pvl +'" size="6" maxlength="6"/><br/>');
        $('.menuCentral .divInner').append('<label for="dataQuotacao">Data:</label><br/>');
        $('.menuCentral .divInner').append('<input type="text" class="required data" id="dataQuotacao" name="dataQuotacao" value="'
                                        + preco.data +'"size="10" maxlength="10"/><br/></div>');
        $('#formPrecos').append('<input type="submit" class="button" value="'+ buttonName +'"/></form>');
        $('.menuCentral').slideDown('slow');
        stopWaiting();
}

function selectComboOption(options){
    $("#"+options.comboID).selectOptions(options.selectedValue, true);
}

function addGMCToTable(li)
{
    var val='';
    if(li){
		if(li.extra[0]){
			val=li.extra[0];
		}
	}
    $('#suggestGrupoMaterialCor').val('');
    if(val !== ''){// evita o post durante o page load
        $.ajax({
            url:'/grupo-material-cor/get-json',
            type:'POST',
            data:'gmcID=' + val,
            error:function(){alert('Erro ao tentar pegar GMC.');stopWaiting();},
            success:function(json){
                var gmc = $.secureEvalJSON(json);
                addGmcRows(gmc);
                stopWaiting();
            }
        });
    }
}


function addGmcRows(gmc)
{
    for(i=0; i< gmc.length; i++){
        if(gmcExists(gmc[i].id) == false){// nao existe ncm
            var row = '';
            var importancia = gmc[i].importancia ? gmc[i].importancia : getNextImportancia();
            tdClass = getNextRowClass($('#tableSelectedGMP'));
            row += '<tr id="gmcRow'+ gmc[i].id +'"class="'+ tdClass +'"><td>';
            row += '<input type="hidden" class="gmcID" name="gmcID[]" value="'+gmc[i].id+'"/>'
            row += gmc[i].codigo +'</td><td>';
            row += gmc[i].estacao.nome +'</td><td>';
            row += gmc[i].grupo.nome +'</td><td>';
            row += gmc[i].material.nome +'</td><td>';
            row += gmc[i].cor.nome +'</td><td class="editDelCell">';
            row += '<input type="text" class="importancia" name="importancia[]" size="2" maxlength="2" '
                        + 'value="'+ importancia +'"/>';
            row += '</td><td class="editDelCell">\n\
                        <img onclick="javascript:delGmcRow(\'gmcRow'+gmc[i].id +'\');"'+ delImg +'/></td>';
            row += '</tr>';
            $('#tableSelectedGMP tbody').append(row);
        }
    }
}


function delGmcRow(rowId){
    $('#'+rowId).remove();
}


function gmcExists(gcmId)
{
    var response = false;
    $('.gmcID').each(function(){
        if( $(this).val() == gcmId ){
            response = true;
        }
    });
    return response;
}

function getNextRowClass(table)
{
    var style = table.find('tr:last').attr('class');
    return style == 'evenRow' ? 'oddRow' : 'evenRow';
}


function getNextImportancia()
{
    var lastImportancia =$('#tableSelectedGMP')
                            .find('.importancia:last')
                            .val();
    return lastImportancia == undefined ? 0 : ++lastImportancia;
}


/**
 * pega valor recebido pelo autocomplete
 * e joga pra dentro o input ncmID
**/
function setNcmID(li)
{
	var text='';
	if(li){
		if(li.extra[0]){
			text=li.extra[0];
		}
	}
	$('#ncmID').val(text);
}


function atualizarCombinacoesDoProduto()
{
    wait();
    $.ajax({
        type:'POST',
        url:'/combinacoes/get-combinacoes',
        data: 'produtoID=' + $('#id').val(),
        error:function(){alert('Erro ao buscar combinacoes');stopWaiting();},
        success:function(json){
            $('#tabelaListaCombinacoes').find("tr:gt(0)").remove();
            var produto = $.secureEvalJSON(json);
            var combinacao = produto.combinacao;
            var row = '';
            var b = 0;
            var rowStyle = '';
            var item;
            var item_length = 0;
            for(var i=0; i < combinacao.length; i++){
                rowStyle = getNextRowClass($('#tabelaListaCombinacoes'));
                row = '';
                item_length = combinacao[i].item.length;
                for(b = 0; b < item_length; b++){
                    item = combinacao[i].item[b];
                    row += '<tr class="'+ rowStyle +'">';
                    if(b == 0)
                        row +=  '<td class="editDelCell" rowspan="'+ (item_length+1) +'"># ' + (i + 1) + '</td>';
                    //row +=  '<td>' + item.grupo.nome + '</td>';
                    row +=  '<td>' + item.estacao.nome + '</td>';
                    row +=  '<td>' + item.material.nome + '</td>';
                    row +=  '<td>' + item.cor.nome + '</td>';
                    row += '<td class="editDelCell">' + item.importancia + '</td>';
                    row +=  '<td>' + item.gmc.codigo ;
                    if($('#divisaoID').val() !== item.grupo.id)
                        row += '&nbsp;&nbsp;&nbsp;<font class="soft">' + item.grupo.nome + '</font>';
                    row +=   '</td>';
                    if(b == 0){
                        row +=  '<td class="editDelCell" rowspan="'+ (item_length +1) +'">'
                            + '<img ' + editImg +' onclick="editCombinacao(\''+ combinacao[i].id +'\');"/>'
                            + '<img onclick="deleteCombinacao(\''+ combinacao[i].id +'\',\'#'+ (i +1) +'\');" ' + delImg + '/></td>';
                    }
                    row += '</tr>';
                }
                row += '<tr class="'+ rowStyle +'"><td colspan="5"><font class="soft">(' + combinacao[i].ncm.codigo + ') '
                    + combinacao[i].ncm.descricao + '</font></td></tr>';

                $('#tabelaListaCombinacoes tbody').append(row);
            }
            stopWaiting();
        }
    });
    
 }

function editCombinacao(id){
    wait();
    $('#divRightMenu').hide();
    getPrecos(id);
    $('#divPreco').show();
    $('#combinacaoSubmit').val('Modificar');
    $.ajax({
        url:'/combinacoes/get-combinacoes',
        type:'POST',
        data: 'combinacaoID=' + id,
        error:function(){alert('Não foi possívle buscar dados da combinação');stopWaiting();},
        success:function(json){
            //adiciona items da combinacao na divRightMenu
            //tableSelectedGMP
            clearRightDiv();
            var combinacao = $.secureEvalJSON(json);
            if(combinacao.success === false){
                alertMessages(combinacao.error.message);
            }else{
                var row = '';
                var importancia;
                var trClass = '';
                var thisItem;
                $('#action').val('update');
                for (var i = 0; i < combinacao.item.length; i++) {
                    /*
                     {"id":"44","ncm":{"id":"","codigo":"","descricao":""},
                        "item":[{"id":"88","importancia":"0","gmc":{"codigo":"2.4.36"},
                            "grupo":{"id":"2","nome":"MICHAEL KORS"},"material":{"id":"4","nome":"PU"},"cor":{"id":"36","nome":"AZUL"}}]}
                    //*/
                    newItem = combinacao.item[i];
                    trClass = getNextRowClass($('#tableSelectedGMP'));
                    row = '<tr id="gmcRow'+ newItem.id +'"class="'+ trClass +'"><td>';
                    row += '<input type="hidden" class="gmcID" name="gmcID[]" value="'+ newItem.gmc.id+'"/>'
                    row += newItem.gmc.codigo +'</td><td>';
                    row += newItem.estacao.nome +'</td><td>';
                    row += newItem.grupo.nome +'</td><td>';
                    row += newItem.material.nome +'</td><td>';
                    row += newItem.cor.nome +'</td><td class="editDelCell">';
                    
                    row += '<input type="text" class="importancia" name="importancia[]" size="2" maxlength="2"'
                                + 'value="'+ newItem.importancia +'"/>';
                    
                    row += '</td><td class="editDelCell">'
                                +'<img onclick="javascript:delGmcRow(\'gmcRow'+ newItem.id +'\');"'+ delImg +'/></td>';
                    row += '</tr>';
                    $('#tableSelectedGMP tbody').append(row);
                }
                if(combinacao.ncm.id){
                    var ncmText = '(' + combinacao.ncm.codigo + ') - ' + combinacao.ncm.descricao;
                    $('#suggestNcm').val(ncmText);
                    $('#ncmID').val(combinacao.ncm.id);
                }
                $('#combinacaoID').val(combinacao.id);
                $('#divRightMenu').show();
            }
        }
    });
}

function getPrecos(id){
    //pega os preços
    $('#tabelaPrecos').hide();
    wait();
    $.ajax({
        type:'POST',
        url:'/precos/get',
        data: 'combinacaoID=' + (id ? id : $('#combinacaoID').val()),
        success:refreshTabelaPrecos,
        error:function(){alert('Erro ao pegar Preços.');}
    });
    
}

function refreshTabelaPrecos(json){
		try{
			wait();
			var preco = $.secureEvalJSON(json);
			var text = '';
			$('#tabelaPrecos').find("tr:gt(0)").remove();

			for(var i=0; i < preco.length; i++){
				text = '<tr class="'+ getNextRowClass($('#tabelaPrecos')) +'">';
				text += '<td>' + preco[i].fabrica.nome;
				text += '</td>';
				text += '<td class="dinheiro">' + preco[i].pFabrica;
				text += '</td >';
				text += '<td class="dinheiro">' + preco[i].pVenda;
				text += '</td>';
				text += '<td class="dinheiro">' + preco[i].pvl;
				text += '</td>';
				text += '<td>' + preco[i].data;
				text += '</td>';
				text += '<td class="editDelCell">'
						+ '<img id="edit'+ preco[i].id +'" class="editPreco" '+ editImg +'/>'
						+ '<img id="del'+ preco[i].id +'" class="delPreco" '+ delImg +'/>'
				text += '</td>';
				text += '</tr>';
				$('#tabelaPrecos tbody').append(text);

			}
		}catch(e){}
			$('#tabelaPrecos').show();
			stopWaiting();

}

    function deleteCombinacao(id, numero)
    {
        $('#divRightMenu').toggle();
        clearRightDiv();
        var msg = 'Tem certeza que deseja exclir a combinacao ' + numero + '?';
        var resposta = confirm(msg);
        if(resposta){
            $.ajax({
                url:'/combinacoes/delete',
                data:'combinacaoID=' + id,
                type: 'POST',
                error:function(){alert('Não foi possível excluir combinacao.')},
                success:function(json){
                    var resp = $.secureEvalJSON(json);
                    if(resp.success === true){
                        atualizarCombinacoesDoProduto();
                    }else{
                        alertMessages(resp.error.message);
                    }
                }
            });
        }
    }

    /**
     * expects an array of messages
     */
    function alertMessages(messages)
    {
        var msg = '';
        for(var i = 0; i< messages.length; i++){
            msg += "\n- " + messages[i];
        }
        alert(msg);
    }
function clearRightDiv(){
    $('#tableSelectedGMP').find("tr:gt(0)").remove();
    $('#ncmID, #suggestNcm, #combinacaoID').val('');
}