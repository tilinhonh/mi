function getNextRowClass(table)
{
    var style = table.find('tr:last').attr('class');
    return style == 'evenRow' ? 'oddRow' : 'evenRow';
}

/**
 * expects an array of messages
 */
function alertMessages(messages)
{
	var msg = '';
	var newLine;
	for(var i = 0; i< messages.length; i++){
		newLine = i > 0 ? "\n" : '';
		msg += newLine + "- " + messages[i];
	}
	alert(msg);
}

function getBaseUrl(){
	var url = window.location.href.split('/');
	return url[2];
}