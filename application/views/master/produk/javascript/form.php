
<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){
        // new AutoNumeric.multiple('.currencies', {
        //     unformatOnSubmit            : true,
        //     decimalPlaces               : 0,
        //     digitGroupSeparator         : ',',
        //     decimalCharacter            : '.',
        //     decimalCharacterAlternative : ',',
        //     currencySymbol              : 'Rp.',
        //     currencySymbolPlacement     : 'p'
        // });
		const currenciesOptions = {
			unformatOnSubmit            : true,
			decimalCharacterAlternative: ".",
			decimalPlaces: 0,
		};

		const 	hargabeli 			= new AutoNumeric('#harga_beli',currenciesOptions),
				hargajual 		    = new AutoNumeric('#harga_jual',currenciesOptions),
				labaakhir            	= new AutoNumeric('#laba',currenciesOptions);
		
			$("#harga_jual").on('keyup',function(){
				var harga_jual = hargajual.getNumber();
				var harga_beli = hargabeli.getNumber();
				var laba = parseInt(harga_jual) - parseInt(harga_beli);
				labaakhir.set(laba);
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

		$('#select2-pemasok').select2({
			ajax : {
				url 		: 'master/produk/api-data/select2-pemasok',
				type 		: 'POST',
				dataType 	: 'json'
			}
		});
	});
});
</script>