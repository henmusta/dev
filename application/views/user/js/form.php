<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<script>
$(function(){
	'use strict'
	$(document).ready(function(){

		var fieldName 	= $('input[name="user[username]"]'),
			fieldEmail	= $('input[name="user[email]"]'),
			fieldOldPassword	= $('input[name="old_password"]'),
			fieldConfirmPassword	= $('input[name="confirm_password"]');

		$.notifyDefaults({
			placement: {
				from 	: "top",
				align 	: "center"
			}
		});

		$('form#<?php echo $form_update['id'];?>').validate({
            validClass      : 'is-valid',
            errorClass      : 'is-invalid',
            errorElement    : 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
			highlight		: function(element, errorClass, validClass) {
				$(element).removeClass(validClass).addClass(errorClass)
				.closest('.form-group').children('label').removeClass('text-success').addClass('text-danger');
			},
			unhighlight		: function(element, errorClass, validClass) {
				$(element).removeClass(errorClass).addClass(validClass)
				.closest('.form-group').children('label').removeClass('text-danger').addClass('text-success');
			},
			submitHandler	: function(form,eve) {
				eve.preventDefault();
				var btnSubmit 		= $(form).find("[type='submit']"),
					btnSubmitHtml 	= btnSubmit.html();

				$.ajax({
					cache 		: false,
					processData : false,
					contentType : false,
					type 		: '<?php echo $form_update['method'];?>',
					url 		: '<?php echo $form_update['action'];?>',
					data 		: new FormData(form),
					dataType	: 'JSON',
					beforeSend:function() { 
						btnSubmit.addClass("disabled").html("<i class='fas fa-spinner fa-pulse fa-fw'></i> Loading ... ");
					},
					error 		: function(){
						btnSubmit.removeClass("disabled").html(btnSubmitHtml);
						$.notify({ icon: 'fa fa-exclamation mr-1', message: 'Server\'s response not found'}, {type: 'danger'});
					},
					success 	: function(response) {
						btnSubmit.removeClass("disabled").html(btnSubmitHtml);
						let timeout = 1000;
						if ( response.status == "success" ){
							$.notify( { icon: 'fa fa-check mr-1', message: response.message}, {type: 'success'});
							setTimeout(function(){
								location.reload();
							},timeout);
						} else {
							$.notify( {icon: 'fa fa-exclamation mr-1', message: response.message},{type: 'danger'});
						}
					}
				});
			}
		});
		$('form#<?php echo $form_change_password['id'];?>').validate({
            validClass      : 'is-valid',
            errorClass      : 'is-invalid',
            errorElement    : 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
			highlight		: function(element, errorClass, validClass) {
				$(element).removeClass(validClass).addClass(errorClass)
				.closest('.form-group').children('label').removeClass('text-success').addClass('text-danger');
			},
			unhighlight		: function(element, errorClass, validClass) {
				$(element).removeClass(errorClass).addClass(validClass)
				.closest('.form-group').children('label').removeClass('text-danger').addClass('text-success');
			},
			submitHandler	: function(form,eve) {
				eve.preventDefault();
				var btnSubmit 		= $(form).find("[type='submit']"),
					btnSubmitHtml 	= btnSubmit.html();

				$.ajax({
					cache 		: false,
					processData : false,
					contentType : false,
					type 		: '<?php echo $form_change_password['method'];?>',
					url 		: '<?php echo $form_change_password['action'];?>',
					data 		: new FormData(form),
					dataType	: 'JSON',
					beforeSend:function() { 
						btnSubmit.addClass("disabled").html("<i class='fas fa-spinner fa-pulse fa-fw'></i> Loading ... ");
					},
					error 		: function(){
						btnSubmit.removeClass("disabled").html(btnSubmitHtml);
						$.notify({ icon: 'fa fa-exclamation mr-1', message: 'Server\'s response not found'}, {type: 'danger'});
					},
					success 	: function(response) {
						btnSubmit.removeClass("disabled").html(btnSubmitHtml);
						let timeout = 1000;
						if ( response.status == "success" ){
							$.notify( { icon: 'fa fa-check mr-1', message: response.message}, {type: 'success'});
							setTimeout(function(){
								location.reload();
							},timeout);
						} else {
							$.notify( {icon: 'fa fa-exclamation mr-1', message: response.message},{type: 'danger'});
						}
					}
				});
			}
		});

        fieldName.rules("add",{
            remote: { 
                url     : "<?php echo $validation['check-username']?>", 
                type    : "POST"
            },
            messages : { 
                remote: "Username ini sudah digunakan.",
            }
        });
        fieldEmail.rules("add",{
            remote: { 
                url     : "<?php echo $validation['check-email']?>", 
                type    : "POST"
            },
            messages : { 
                remote: "Email ini sudah digunakan."
            }
        });
        fieldOldPassword.rules("add",{
            remote: { 
                url     : "<?php echo $validation['check-old-password']?>", 
                type    : "POST"
            },
            messages : { 
                remote: "Ini bukan password lama anda."
            }
        });
        fieldConfirmPassword.rules("add",{
            equalTo: '#new_password',
            messages : { 
                remote: "Password tidak sama."
            }
        });
	});
});
</script>
