var viewContent = false;
var InitiateCheckout = false;
var AddPaymentInfo = false;
function validarCPF(cpf) {
	cpf = cpf.replace(/[^\d]+/g, '');
	if (cpf == '') return false;
	// Elimina CPFs invalidos conhecidos	
	if (cpf.length != 11 ||
		cpf == "00000000000" ||
		cpf == "11111111111" ||
		cpf == "22222222222" ||
		cpf == "33333333333" ||
		cpf == "44444444444" ||
		cpf == "55555555555" ||
		cpf == "66666666666" ||
		cpf == "77777777777" ||
		cpf == "88888888888" ||
		cpf == "99999999999")
		return false;
	// Valida 1o digito	
	add = 0;
	for (i = 0; i < 9; i++)
		add += parseInt(cpf.charAt(i)) * (10 - i);
	rev = 11 - (add % 11);
	if (rev == 10 || rev == 11)
		rev = 0;
	if (rev != parseInt(cpf.charAt(9)))
		return false;
	// Valida 2o digito	
	add = 0;
	for (i = 0; i < 10; i++)
		add += parseInt(cpf.charAt(i)) * (11 - i);
	rev = 11 - (add % 11);
	if (rev == 10 || rev == 11)
		rev = 0;
	if (rev != parseInt(cpf.charAt(10)))
		return false;
	return true;
}
function consultaCEP(el) {
	var cep = $(el).val().replace(/\D/g, "");
	var focus = '#endereco';
	$.ajax({
		url: "/ajax/consultaCEP",
		data: {
			cep: cep
		},
		type: "POST",
		dataType: "json",
		timeout: 5000,
		beforeSend: function() {
			$('[consulta-cep="endereco"]').prop('disabled', true);
			$('[consulta-cep="bairro"]').prop('disabled', true);
			$('[consulta-cep="cidade"]').prop('disabled', true);
			$('[consulta-cep="estado"]').prop('disabled', true);
			$('[consulta-cep="numero"]').prop('disabled', true);
			$('[consulta-cep="complemento"]').prop('disabled', true);
		},
		success: function(json) {
			$('[consulta-cep="endereco"]').val('');
			$('[consulta-cep="bairro"]').val('');
			$('[consulta-cep="cidade"]').val('');
			$('[consulta-cep="estado"]').val('');
			$('[consulta-cep="numero"]').val('');
			$('[consulta-cep="complemento"]').val('');

			if (json.logradouro != '') {
				$('[consulta-cep="endereco"]').val(json.logradouro);
				$('[consulta-cep="bairro"]').val(json.bairro);
				$('[consulta-cep="cidade"]').val(json.cidade);
				$('[consulta-cep="estado"]').val(json.estado).trigger('change');
				if (json.numero != undefined) {
					$('[consulta-cep="numero"]').val(json.numero);
					focus = "complemento";
				} else {
					focus = "numero";
				}
			} else {
				focus = "endereco";
			}
		},
		complete: function() {
			$('[consulta-cep="endereco"]').prop('disabled', false);
			$('[consulta-cep="bairro"]').prop('disabled', false);
			$('[consulta-cep="cidade"]').prop('disabled', false);
			$('[consulta-cep="estado"]').prop('disabled', false);
			$('[consulta-cep="numero"]').prop('disabled', false);
			$('[consulta-cep="complemento"]').prop('disabled', false);
			$('[consulta-cep="' + focus + '"]').focus();
		},
		error: function(error, status) {
			console.log(error);
		}
	});
}
$(document).ready(function() {
	$('#navbarSideCollapse').click(function() {
		$('.offcanvas-collapse').toggleClass('open');
	});
	/*RECAPTCHA*/
	if (typeof App !== 'undefined' && App.environment == 'production' && App?.recaptcha) {
		$('*[data-recaptcha]').click(function(e) {
			e.preventDefault();
			grecaptcha.enterprise.ready(async () => {
				const token = await grecaptcha.enterprise.execute(App.recaptcha, { action: $(this).data('recaptcha') });
			});
		});
	}
	/*PIXEL*/
	if (typeof App !== 'undefined' && App.environment == 'production' && App?.pixel) {
		!function(f, b, e, v, n, t, s) {
			if (f.fbq) return; n = f.fbq = function() {
				n.callMethod ?
					n.callMethod.apply(n, arguments) : n.queue.push(arguments)
			};
			if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0';
			n.queue = []; t = b.createElement(e); t.async = !0;
			t.src = 'https://connect.facebook.net/en_US/fbevents.js';
			s = b.getElementsByTagName(e)[0];
			s.parentNode.insertBefore(t, s)
		}(window, document, 'script');

		$(window).on('scroll', function() {
			if (!viewContent && ($(window).scrollTop() + $(window).height() >= $(document).height())) {
				viewContent = true;
				fbq('track', 'ViewContent');
			}
		});
		setTimeout(function() {
			if (!viewContent) {
				viewContent = true;
				fbq('track', 'ViewContent');
			}
		}, 5000);
		fbq('init', App.pixel);
		fbq('track', 'PageView');
	}
	/*ANALYTICS*/
	if (typeof App !== 'undefined' && App.environment == 'production' && App?.analytics) {
		window.dataLayer = window.dataLayer || [];
		function gtag() { dataLayer.push(arguments); }
		gtag('js', new Date());
		gtag('config', App.analytics);
	}
});
