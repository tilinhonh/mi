$(function(){
    $('#suggest').keyup(function(){
        return false;
        var val = $(this).val();
        if(val.length > 2){
            getCidades();
        }
    });
    $('#search').click(function(){
        if($('#suggest').val().length > 2){
            getCidades();
        }
        else{alert('Preencha o campo de pesquisa.');}
        
        return false;
    });

});




/**
 * recebe json e monta tabela
 */
function createTable(json)
{

    //[{"id":"12179","nome":"NOVO HAMBURGO","uf":"RS","estado":"Rio Grande do Sul"}]
    $('#tabelaCidades tr:gt(0)').remove();
    var cidade = $.secureEvalJSON(json);
    var tr='';
    var style = 'evenRow';
    
    for(var i=0; i< cidade.length; i++){
        style = (style == 'evenRow') ? 'oddRow' : 'evenRow';

        tr='<tr class="'+ style +'"><td>';
        tr += cidade[i].nome + '</td><td>';
        tr += cidade[i].uf + '</td>';
        tr += '<td class="editDelCell">'
			     + ' <a href="cidades/edit/id/' + cidade[i].id +'">'
                 +       '<img src="/images/edit.png" alt="Editar" title="Editar" />'
                 +   '</a>'
                 +   '<a href="cidades/del/id/' + cidade[i].id +'">'
                 +       '<img src="/images/del.png" alt="Excluir" title="Excluir" />'
                 +   '</a>'
			     + '</td><td>&nbsp;</td></tr>';
        

        $('#tabelaCidades tbody').append(tr);
    }
    stopWaiting();
}

function getCidades()
{
    wait();
    $.ajax({
        url:'/cidades/suggest',
        data: 'q=' + $('#suggest').val(),
        type:'POST',
        success: createTable,
        error:function(){alert('Erro no AJAX call');}
    });
}