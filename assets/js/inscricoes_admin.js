var msgWhatsApp = '';

function initInscricoes() {
	$('.whatsAppMsg').click(function() {
		msg = $(this).data('msg');
		if (msg == 'custom') {
			msgWhatsApp = $('#customWhatsAppMsg').val();
		}
		else {
			msgWhatsApp = msg;
		}
		$.ajax({
			url: "/inscricoes/whatsAppMsg/",
			type: "POST",
			data: {
				msg: msgWhatsApp
			},
			success: function(response, status, jqXHR) {

			},
			error: function(jqXHR, textStatus, errorThrown) {
				Xcrud.show_error(jqXHR.statusText);
				console.log(jqXHR.statusText);
				console.log(jqXHR.responseText);
			}
		});
		$('.whatsAppMsgRow').data('msg', msgWhatsApp);
		$('.whatsAppMsgRow').attr('data-msg', msgWhatsApp);
		$('#whatsAppMsg').modal('hide');
	});
	$('.whatsAppMsgRow').attr('data-msg', msgWhatsApp);

	$('.sincronizar').off('click').click(function() {
		$.ajax({
			url: "/transacoes/sincronizar/",
			type: "POST",
			data: {
				ins_id: $(this).data('primary')
			},
			success: function(response, status, jqXHR) {
				Xcrud.show_success(response);
				Xcrud.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				Xcrud.show_error(jqXHR.statusText);
				console.log(jqXHR.statusText);
				console.log(jqXHR.responseText);
			}
		});
	});
	$('.ins_tag').off('click').click(function() {
		val = $('#customWhatsAppMsg').val();
		$('#customWhatsAppMsg').focus().val('').val(val + $(this).text());
	});
}

function shareButton(title, text, url) {
	//console.log(title); console.log(text); console.log(url);
	window.open(url, "WppWeb"); return;
	const ua = navigator.userAgent.toLowerCase();
	const isAndroid = ua.includes("android");
	const isWindows = ua.includes("windows");
	if (navigator.share && isAndroid) {
		// Android com suporte ao Web Share API
		navigator.share({
			title: title,
			text: text,
			url: url
		}).catch(err => console.error("Erro ao compartilhar:", err));
	} else if (isWindows) {
		// Windows → abre WhatsApp Web
		window.location.href = url;
		//window.open(url, "WppWeb");
	} else {
		// Outros casos → abre a URL padrão
		window.location.href = url;
	}
}

$(document).ready(initInscricoes);
jQuery(document).on("xcrudafterrequest", function(event, container) {
	initInscricoes();
});