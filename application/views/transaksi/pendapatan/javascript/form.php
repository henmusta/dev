<?php $pembelian = isset($data) ? $data : (object)[];?>
<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){
		$.fn.select2.defaults.set("width", "100%");

		$('#tgl-nota').datepicker({format:'yyyy-mm-dd',todayHighlight:true});

		const currenciesOptions = {
			unformatOnSubmit            : true,
			decimalCharacterAlternative: ".",
			decimalPlaces: 0,
			minimumValue: "0",
		};

		const 	nominal = new AutoNumeric('#nominal',currenciesOptions);

		$('#select-kas').select2({
			ajax : {
				url 		: '<?= $module['url'];?>/api-data/select2-kas',
				dataType 	: 'json',
				type 		: 'POST',
				processResults: function (myData) {
					var data = $.map(myData, function (obj) {
						obj.text = obj.text || obj.nama; // replace name with the property used for the text
						return obj;
					});
					return {
						results : data
					};
				}
			}
		});
		$('#select-pendapatan').select2({
			ajax : {
				url 		: '<?= $module['url'];?>/api-data/select2-pendapatan',
				dataType 	: 'json',
				type 		: 'POST',
				processResults: function (myData) {
					var data = $.map(myData, function (obj) {
						obj.text = obj.text || obj.nama;
						return obj;
					});
					return {
						results : data
					};
				}
			}
		});

		$('form#form').validate({
            validClass      : 'is-valid',
            errorClass      : 'is-invalid',
            errorElement    : 'div',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.parent().append(error);
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
					type 		: 'POST',
					url 		: $(form).attr('action'),
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
								if(response.redirect == "reload"){
									location.reload();
								} else if (response.redirect == "history.back()") {
									window.history.back();
								} else if(response.redirect != "") {
									location.href = response.redirect;
								}
							},timeout);
						} else {
							$.notify( {icon: 'fa fa-exclamation mr-1', message: response.message},{type: 'danger'});
						}
					}
				});
			}
		});

	});
});
</script>
