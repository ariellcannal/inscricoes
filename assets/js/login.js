var expired = false;
var Login = {
	validation : function() {
		$('.form-signin form').submit(function() {
			user_selector = 'form.auth #user';
			pass_selector = 'form.auth #pass';
			user = $(user_selector).val();
			pass = $(pass_selector).val();
			if (user == "" || pass == "") {
				alertify.alert('Atenção','Preencha usuário e senha, por favor.', 'warning');
			} else {
				$.ajax({
					url: "/login/auth",
					data: {
						user: user,
						pass: pass
					},
					type: "POST",
					dataType: 'json',
					success: function(data, status, jqXHR) {
						if (data.status == "error" && typeof data.error !== "undefined" && data.error) {
							alertify.alert(data.error);
						} else if (data.status == "success" && typeof data.redirect !== "undefined" && data.redirect) {
							window.location = data.redirect;
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						alertify.alert('Atenção',jqXHR.responseText, 'danger');
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