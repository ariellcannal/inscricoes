var expired = false;
var Login = {
	validation: function() {
		$('.form-signin form').submit(function() {
			user_selector = 'form.auth #user';
			pass_selector = 'form.auth #pass';
			user = $(user_selector).val();
			pass = $(pass_selector).val();
			if (user == "" || pass == "") {
				alertify.alert('Atenção', 'Preencha usuário e senha, por favor.', 'warning');
			} else {
                                // URL para redirecionar após login
                                var redirect = $('#redirect_to').val();
                                var requestData = {
                                        user: user,
                                        pass: pass,
                                        redirect_to: redirect
                                };
                                requestData[csrf_token_name] = csrf_token;
				$.ajax({
					url: "/login/auth",
					data: requestData,
					type: "POST",
					dataType: 'json',
					success: function(data, status, jqXHR) {
						if (data.status == "success" && typeof data.redirect !== "undefined" && data.redirect) {
							window.location = data.redirect;
						}
					},
                                        error: function(jqXHR, textStatus) {
                                                const message = decodeURIComponent(escape(jqXHR.statusText));
                                                alertify.alert('Atenção', message, 'danger');
                                                console.log(jqXHR);
                                        }
				});
			}
			return false;
		});
	}
};
$(document).ready(function() {
	Login.validation();
})