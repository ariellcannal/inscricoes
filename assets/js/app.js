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
	alertify.defaults.notifier.escapeInput = false;
	$('#navbarSideCollapse').click(function() {
		$('.offcanvas-collapse').toggleClass('open');
	});
	
	/*RECAPTCHA - Apenas carregado em páginas específicas com o atributo data-recaptcha*/
	// O script do recaptcha é carregado dinamicamente por página
	if (typeof grecaptcha !== 'undefined') {
		$('*[data-recaptcha]').click(function(e) {
			e.preventDefault();
			const action = $(this).data('recaptcha');
			const recaptchaKey = $('meta[name="google-recaptcha-key"]').attr('content');
			
			if (recaptchaKey) {
				grecaptcha.enterprise.ready(async () => {
					const token = await grecaptcha.enterprise.execute(recaptchaKey, { action: action });
					// Aqui você pode enviar o token via AJAX ou formulário
				});
			}
		});
	}
});
