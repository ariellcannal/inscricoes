function initRecebiveis() {
	$('.confirmar').off('click').click(function() {
		$.ajax({
			url: "/recebiveis/confirmar",
			data: {
				rec_id: $(this).data('primary')
			},
			type: "POST",
			success: function(response, status, jqXHR) {
				Xcrud.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				Xcrud.show_error(jqXHR.statusText);
				console.log(jqXHR.statusText);
				console.log(jqXHR.responseText);
			}
		});
	});

	$('.desconfirmar').off('click').click(function() {
		$.ajax({
			url: "/recebiveis/desconfirmar",
			data: {
				rec_id: $(this).data('primary')
			},
			type: "POST",
			success: function(response, status, jqXHR) {
				Xcrud.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				Xcrud.show_error(jqXHR.statusText);
				console.log(jqXHR.statusText);
				console.log(jqXHR.responseText);
			}
		});
	});

	$('.sincronizar').off('click').click(function() {
		$.ajax({
			url: "/recebiveis/sincronizar/" + $(this).data('primary'),
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
}

$(document).ready(initRecebiveis);
jQuery(document).on("xcrudafterrequest", function(event, container) {
	initRecebiveis();
});