function wait(n,message)
{
	if(message==undefined)
		var message='Aguarde...';
	$('#status').html(message).show();
}

function stopWaiting(n)
{
	$('#status').fadeOut();
}