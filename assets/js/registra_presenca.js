function distanceFrom(points) {
	var lat1 = points.lat1;
	var radianLat1 = lat1 * (Math.PI / 180);
	var lng1 = points.lng1;
	var radianLng1 = lng1 * (Math.PI / 180);
	var lat2 = points.lat2;
	var radianLat2 = lat2 * (Math.PI / 180);
	var lng2 = points.lng2;
	var radianLng2 = lng2 * (Math.PI / 180);
	var earth_radius = 3959; // or 6371 for kilometers
	var diffLat = (radianLat1 - radianLat2);
	var diffLng = (radianLng1 - radianLng2);
	var sinLat = Math.sin(diffLat / 2);
	var sinLng = Math.sin(diffLng / 2);
	var a = Math.pow(sinLat, 2.0) + Math.cos(radianLat1) * Math.cos(radianLat2) * Math.pow(sinLng, 2.0);
	var distance = earth_radius * 2 * Math.asin(Math.min(1, Math.sqrt(a)));
	return distance.toFixed(3);
}
function initCPF() {
	$('#alu_cpf').removeAttr('disabled').keyup(function() {
		cpf = $(this).val();
		if (cpf.length == 14) {
			if (!validarCPF(cpf)) {
				alertify.alert('CPF Inválido');
				$('input').attr('disabled', 'disabled');
				$(this).removeAttr('disabled').focus();
			}
			else {
				container = $(this);
				$.ajax({
					url: "/alunos/check_cpf",
					data: {
						cpf: cpf
					},
					type: "POST",
					dataType: 'json',
					beforeSend: function() {
						$(container).closest(".xcrud").find(".xcrud-overlay").width($(container).closest(".xcrud-container").width()).stop(true, true).fadeTo(300, 0.6);
					},
					timeout: 5000,
					error: function(jqXHR, textStatus, errorThrown) {
						console.log(textStatus);
						console.log(jqXHR.responseText);
					},
					complete: function() {
						$(container).closest(".xcrud").find(".xcrud-overlay").stop(true, true).css("display", "none");
						$('#alu_nome').focus();
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log(textStatus);
						console.log(jqXHR.responseText);
					},
					success: function(aluno, status, jqXHR) {
						$('input').removeAttr('disabled');
						$('.xcrud-action').show();
						if (aluno !== null) {
							if (aluno['rec_cartaoCPF'] == "") {
								aluno['rec_cartaoCPF'] = cpf;
							}
							for (var coluna in aluno) {
								$('#' + coluna).val(aluno[coluna]);
							}
							$('input').each(function() {
								if ($(this).val() == "") {
									$(this).focus();
									return false;
								}
							});
						}
						$('#alu_nomeArtistico').focus();
					}
				});
			}
		}
	}).focus();
}

$(document).on("xcrudafterrequest", function() {
	initCPF();
});

$(document).ready(function() {
	$('input').each(function() {
		if ($(this).val() == "") {
			$(this).attr('disabled', 'disabled');
		}
	})
	$('input').change(function() {
		$(this).removeClass('is-invalid');
	});
	initCPF();
	function getLocation() {
		navigator.geolocation.getCurrentPosition(function(pos) {
			const crd = pos.coords;
			console.log(`Latitude : ${crd.latitude}`);
			console.log(`Longitude: ${crd.longitude}`);
			console.log(`Mais ou menos ${crd.accuracy} metros.`);
			distance = distanceFrom({
				// Galpão
				'lat1': -23.5277693,
				'lng1': -46.6592077,
				// Aluno
				'lat2': pos.coords.latitude,
				'lng2': pos.coords.longitude
			});
			console.log('Distância do Galpão: ' + distance);
			if (distance > 0.5) {
				$('.longe').show();
			}
			else {
				$('.form').show();
			}
		}, function(err) {
			if (err.code == 1) {
				alertify.alert('Para registrar a sua presença é preciso autorizar o compartilhamento da localização do seu dispositivo. Por favor autorize e abra esta página novamente.');
			}
			console.log(`ERROR(${err.code}): ${err.message}`);
		}, {
			enableHighAccuracy: true,
			timeout: 5000,
			maximumAge: 0
		});
	}
	getLocation();
});