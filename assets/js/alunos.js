function initAlunos() {
	$('.formulario').off('click').click(function() {
		token = Date.now() + ':';
		if ($(this).data('primary') && $(this).data('tel')) {
			token += $(this).data('primary');
			modo = 'atualizar';
			window.open('https://api.whatsapp.com/send?phone=55' + $(this).data('tel') + '&text=' + $(this).data('msg') + '/' + btoa(token), '_blank').focus(); return;
		}
		else {
			if (navigator.share) {	
				navigator.share({
					title: 'Cadastro de Alunos',
					text: $(this).data('msg') + '/' + btoa(token)
				})
					.then(() => console.log('Successful share'))
					.catch(error => console.log('Error sharing:', error));
			}
		}
	});
}
$(document).ready(initAlunos);
jQuery(document).on("xcrudafterrequest", function(event, container) {
	initAlunos();
});