function initTransacoes() {
	$('.sincronizar').off('click').click(function() {
		$.ajax({
			url: "/transacoes/sincronizar/" + $(this).data('primary'),
			type: "POST",
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


	$('.estornar').off('click').click(function() {
		container = $(this);
		$.ajax({
			url: "/transacoes/estornar",
			data: {
				otr_id: $(this).data('otr'),
				otr_valorCancelamento: $('#otr_valorCancelamento_' + $(this).data('otr')).val()
			},
			cache: false,
			type: 'POST',
			timeout: 10000,
			beforeSend: function() {
				$('.estornar').html('Aguarde...').attr('disabled', 'disabled');
			},
			success: function(response, status, jqXHR) {
				if (response == '1') {
					alertify.alert('Estorno', 'Estorno realizado com sucesso', function() {
						$(container).closest('.modal').on('hidden.bs.modal', function(e) {
							Xcrud.reload();
						}).modal('hide');
					});
				}
				else {
					alertify.alert('Resposta da operadora', response);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				Xcrud.show_error(jqXHR.statusText);
				console.log(jqXHR.statusText);
				console.log(jqXHR.responseText);
			},
			complete: function() {
				$('.estornar').html('Estornar').removeAttr('disabled');
			},
		});
	});
}

$(document).ready(initTransacoes);
jQuery(document).on("xcrudafterrequest", function(event, container) {
	initTransacoes();
});