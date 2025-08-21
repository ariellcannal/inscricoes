function initRepasses() {
	$('button[data-task="create"]').removeClass('xcrud-action').off('click').click(function() {
		$.ajax({
			url: "/repasses/consolidar",
			type: "POST",
			success: function(response, status, jqXHR) {
				Xcrud.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(jqXHR.statusText);
				console.log(jqXHR.responseText);
			}
		});
	});
	$('.efetivar').off('click').click(function() {
		$.ajax({
			url: "/repasses/efetivar",
			data: {
				rep_id: $(this).data('primary')
			},
			type: "POST",
			success: function(response, textStatus, jqXHR) {
				console.log(textStatus);
				console.log(jqXHR.responseText);
				Xcrud.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(jqXHR.statusText);
				console.log(jqXHR.responseText);
				Xcrud.show_error(jqXHR.responseText);
			}
		});
	});
	$('.desefetivar').off('click').click(function() {
		$.ajax({
			url: "/repasses/desefetivar",
			data: {
				rep_id: $(this).data('primary')
			},
			type: "POST",
			success: function(response, status, jqXHR) {
				Xcrud.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(jqXHR.statusText);
				console.log(jqXHR.responseText);
				Xcrud.show_error(jqXHR.responseText);
			}
		});
	});
	$('.gerar_lote').off('click').click(function() {
		let table = $('.xcrud-list');
		console.log(table);
		//debugger;
		TableToExcel.convert(table[0], {
			name: `Lote.xlsx`,
			sheet: {
				name: 'Lote'
			}
		});
		return;
	});
	var select_sum = 0;
	$('.xcrud-mass .xcrud-mass-checkbox').off('change').change(function() {
		sum_row = $(this).closest('table').find('.xcrud-tf td[data-label="Valor"]').eq(0);
		if (!$(sum_row).data('sum')) {
			$(sum_row).data('sum', $(sum_row).text().replace(',', '.'));
		}
		row_val = parseFloat($(this).closest('tr').find('td[data-label="Valor"]').eq(0).text().replace(',', '.'));
		if ($(this).is(':checked')) {
			select_sum += row_val;
		} else {
			select_sum -= row_val;
		}
		if (select_sum > 0) {
			$(sum_row).text(select_sum + ' de ' + $(sum_row).data('sum'));
		}
		else {
			$(sum_row).text($(sum_row).data('sum'));
		}
	});
	
}
$(document).ready(initRepasses);
jQuery(document).on("xcrudafterrequest", function(event, container) {
	initRepasses();
});