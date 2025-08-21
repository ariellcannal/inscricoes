var current_task = $('.xcrud-ajax .xcrud-data[name="task"]').val();

function checkCPF() {
	if (App.environment == 'production' && typeof viewContent !== 'undefined' && !viewContent) {
		viewContent = true;
		fbq('track', 'ViewContent');
	}
	cpf = $('#alu_cpf').val();
	grp = $('#grp').val();
	if (cpf != "") {
		if (!validarCPF(cpf)) {
			$('#alu_cpf').addClass('is-invalid')
			$('input,select').attr('disabled', 'disabled');
			$(this).removeAttr('disabled').focus();
			$('#alu_cpf_erro').show();
		}
		else {
			$('#alu_cpf_erro').hide();
			$('#alu_cpf').removeClass('is-invalid')
			container = $(this);
			$.ajax({
				url: "/alunos/check_cpf",
				data: {
					cpf: cpf,
					grp: grp,
					check_cartao: true
				},
				type: "POST",
				dataType: 'json',
				beforeSend: function() {
					Xcrud.show_progress($(container).closest(".xcrud"));
				},
				timeout: 5000,
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(jqXHR.statusText);
					console.log(jqXHR.responseText);
				},
				complete: function() {
					Xcrud.hide_progress($(container).closest(".xcrud"));
				},
				success: function(aluno, status, jqXHR) {
					if (App.environment == 'production' && typeof InitiateCheckout !== 'undefined' && !InitiateCheckout) {
						InitiateCheckout = true;
						fbq('track', 'InitiateCheckout');
					}
					$('input,select').removeAttr('disabled');
					$('.xcrud-action').show();
					if (aluno !== null) {
						if (aluno['alu_ins'] != null && $('#grp_slug').val() != "") {
							window.location = '/inscricao/' + $('#grp_slug').val() + '/' + aluno['alu_ins']['ins_id'];
							return;
						}
						if (aluno['rec_cartaoCPF'] == "") {
							aluno['rec_cartaoCPF'] = cpf;
						}
						for (var coluna in aluno) {
							$('#' + coluna).val(aluno[coluna]);
							$('#' + coluna).trigger('change');
						}
						for (var coluna in aluno['alu_cartoes']) {
							var newOption = new Option(aluno['alu_cartoes'][coluna]['label'], aluno['alu_cartoes'][coluna]['id'], false, false);
							$('#alu_cartoes').prepend(newOption).trigger('change');
						}
						$('#alu_cartoes').val($('#alu_cartoes option:first').val());
						if (aluno['alu_foto'] != "") {
							$('#foto_wrapper').html(aluno['alu_foto']);
						}
						if (aluno['alu_cv'] != "") {
							$('#cv_wrapper').html(aluno['alu_cv']);
						}
						checkFOP();
						$('input').each(function() {
							if ($(this).val() == "") {
								$(this).focus();
								return false;
							}
						});
					}
					else {
						$('#alu_nome').focus();
						$('#rec_cartaoCPF').val(cpf);
						checkFOP();
					}
					$(container).closest(".xcrud").find(".xcrud-overlay").stop(true, true).css("display", "none");
					$('#alu_nome').focus();
				}
			});
		}
	}
}

function initCPF() {
	current_task = $('.xcrud-ajax .xcrud-data[name="task"]').val();
	if (current_task == "create") {
		$('#alu_cpf').removeAttr('disabled').focus().blur(checkCPF);
	}
}
$(document).on("xcrudbeforerequest", function() {
	showSpinner($('.spinner_control'));
});
$(document).on("xcrudafterrequest", function() {
	hideSpinner($('.spinner_control'));
	initCPF();
	$('#pix_code_button').click(function() {
		var copyText = document.getElementById("pix_code");
		copyText.select();
		copyText.setSelectionRange(0, 99999);
		navigator.clipboard.writeText(copyText.value);
		$('#pix_code_button').removeClass('btn-primary').addClass('btn-success').html('Copiado!');
		setTimeout(function() {
			$('#pix_code_button').removeClass('btn-success').addClass('btn-primary').html('Copiar Novamente');
		}, 5000);
	});
});
$(document).on("xcrudinit", function() {
	current_task = $('.xcrud-ajax .xcrud-data[name="task"]').val();
	if (current_task == "create") {
		$('input,select').attr('disabled', 'disabled');
	}
	else {
		$('#alu_cpf').attr('readonly', 'readonly');
		checkCPF();
	}
	if (current_task == "create") {
		$('.xcrud-action').hide();
	}
	if (current_task == "edit" || current_task == "create") {
		$('.dados_cartao').hide();
		$('.mesmo_cartao').hide();
		$('#fop').change(checkFOP);
		$('#alu_cartoes').change(selectCartao).css('width', '100%');
		$('input,select').change(function() {
			$(this).removeClass('is-invalid');
			$(this).closest('.form-group').removeClass('is-invalid');
		});
	}
	initCPF();
	$('#rec_cartaoValidadeMes,#rec_cartaoValidadeAno').change(function() {
		ano = $('#rec_cartaoValidadeAno').val();
		mes = $('#rec_cartaoValidadeMes').val();
		validade = mes + '/' + ano;
		$('#rec_cartaoValidade').val(validade);
	});
});

function checkFOP() {
	if (App.environment == 'production' && typeof AddPaymentInfo !== 'undefined' && !AddPaymentInfo) {
		AddPaymentInfo = true;
		fbq('track', 'AddPaymentInfo');
	}

	tem_parcelamento = $('#fop').val().split("_")[2];
	if (tem_parcelamento == 1) {
		$('.mesmo_cartao').show(100, selectCartao);
		if ($('#alu_cartoes option').length == 1) {
			$('#alu_cartoes').attr('disabled', true).trigger('change');
		}
	}
	else {
		$('.mesmo_cartao').hide(100);
		$('.dados_cartao').hide(100);
	}
}
function selectCartao() {
	if ($('#fop').val().split("_")[2] == "0") {
		return;
	}
	if ($('#alu_cartoes').val() == undefined) {
		$('#alu_cartoes').val($('#alu_cartoes option:eq(0)').val()).trigger('change');
	}
	else if ($('#alu_cartoes').val() == "novo") {
		$('.dados_cartao').slideDown(100);
	}
	else {
		$('.dados_cartao').slideUp(100);
		//$('#rec_cartaoValidadeMes').css('width', '100%');
		//$('#rec_cartaoValidadeAno').css('width', '100%');
	}
}

function initCard(numberInput, expiryInput, cvcInput, nameInput) {
	$('.xcrud').card({
		container: '.cartao',
		formSelectors: {
			numberInput: numberInput, // optional — default input[name="number"]
			expiryInput: expiryInput, // optional — default input[name="expiry"]
			cvcInput: cvcInput, // optional — default input[name="cvc"]
			nameInput: nameInput // optional - defaults input[name="name"]
		},
		debug: true,
		placeholders: {
			number: '**** **** **** ****',
			name: 'Seu Nome',
			expiry: '**/****',
			cvc: '***'
		},
		messages: {
			validDate: 'Validade',
			monthYear: 'mm/yy'
		}
	});
}
function showSpinner(container) {
	$(container).children('span').hide();
	$(container).append('<div class="spinner-border" role="status"><span class="visually-hidden">Aguarde...</span></div>');
	$(container).attr('disabled', 'disabled');
}
function hideSpinner(container) {
	$(container).children('span').show();
	$(container).children('.spinner-border').remove();
	$(container).removeAttr('disabled');
}