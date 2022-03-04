<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<script>
$(function(){
	'use strict'
	$(document).ready(function(){
		$.fn.select2.defaults.set("width", "100%");
		$('#select-cabang').select2({
			ajax : {
				url 		: 'pengaturan/pengguna/select2-cabang',
				dataType 	: 'json',
				type 		: 'POST',
				data 		: function (params) {
					var query = {
						search: params.term,
						type: 'public'
					}
					return params;//$.extend({},params,{jenis:$('#jenis-hak').val()});
				},
				processResults: function (myData) {
					var data = $.map(myData.results, function (obj) {
						obj.text = obj.text || obj.nama;
						return obj;
					});
					return {
						results : data
					};
				}
			}
		}).on('select2:select', function(e){
			$('#jenis-hak').val(e.params.data.jenis);
			$('#select-hak-akses').val(null).trigger('change');
		});
		$('#select-hak-akses').select2({
			ajax : {
				url 		: 'pengaturan/pengguna/select2-hak-akses',
				dataType 	: 'json',
				type 		: 'POST',
				data 		: function (params) {
					var query = {
						search: params.term,
						type: 'public'
					}
					return $.extend({},params,{jenis:$('#jenis-hak').val()});
				},
				processResults: function (myData) {
					var data = $.map(myData.results, function (obj) {
						obj.text = obj.text || obj.nama;
						return obj;
					});
					return {
						results : data
					};
				}
			}
		});
		$('form#<?php echo $id;?>').validate({
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
					type 		: 'POST',
					url 		: '<?php echo $action;?>',
					data 		: new FormData(form),
					dataType	: 'JSON',
					beforeSend:function() { 
						btnSubmit.addClass("disabled").html("<i class='fas fa-spinner fa-pulse fa-fw'></i> Loading ... ");
					},
					error 		: function(){
						btnSubmit.removeClass("disabled").html(btnSubmitHtml);
						One.helpers('notify', {type: 'danger', icon: 'fa fa-exclamation mr-1', message: 'Server\'s response not found'});
					},
					success 	: function(response) {
						btnSubmit.removeClass("disabled").html(btnSubmitHtml);
						let timeout = 1000;
						if ( response.status == "success" ){
							One.helpers('notify', {type: 'success', icon: 'fa fa-check mr-1', message: response.message});
							setTimeout(function(){
								if(response.redirect == "reload" || response.redirect == null || response.redirect == ""){
									location.reload();
								} else if (response.redirect == "history.back()") {
									window.history.back();
								} else if(response.redirect != "") {
									location.href = response.redirect;
								}
							},timeout);
						} else {
							One.helpers('notify', {type: 'danger', icon: 'fa fa-exclamation mr-1', message: response.message});
						}
					}
				});
			}
		});
		
        <?php 
        if(isset($validation) && is_array($validation)  && count($validation) > 0) : 
        	foreach($validation AS $key => $val) :
        ?>
        var vMessage_<?php echo strtolower($key);?> = "Is invalid.";
		var <?php echo strtolower($key);?> = $('#<?php echo strtolower($key);?>');
		    <?php echo strtolower($key);?>.rules("add",{
		        remote: { 
		            url     : "<?php echo $val['url'];?>", 
		            type    : "POST",
		            dataType: 'json',
		            data 	: {
		            	term 		: function(){
		            		return $('#<?php echo strtolower($key);?>').val();
		            	},
		            	fieldname 	: '<?php echo $val['field'];?>',
						pk 			: $('input[name="pk"]').val()
					},
					dataFilter: function(data) {
						var response = JSON.parse(data);
						vMessage_<?php echo strtolower($key);?> = response.message;
						return response.results;
					}
		        },
		        messages : { 
		            remote: function(){
		            	return vMessage_<?php echo strtolower($key);?>;
		            }
		        }
		    });
        <?php 
    		endforeach;
    	endif;
    	?>
	});
});
</script>
