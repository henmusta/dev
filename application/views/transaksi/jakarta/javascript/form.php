<?php $transaksi = isset($data) ? $data : (object)[];?>
<script type="text/javascript">
$(function(){

	'use strict'

	$.fn.select2.defaults.set("width", "100%");
	$('#tgl-nota').datepicker({format:'yyyy-mm-dd',todayHighlight:true});

	$(document).ready(function(){

		const currenciesOptions = {
			unformatOnSubmit            : true,
			decimalCharacterAlternative: ".",
			decimalPlaces: 0,
			//minimumValue: "0",
		};

		const 	totalItems 			= new AutoNumeric('#totalItems',currenciesOptions);
		
		$('#table-akun').on('changeTotalItem',	function(){
			let totalItemsAmount = 0;
			$(this).find('[id^="item_total_"]').each(function(){
				totalItemsAmount += AutoNumeric.getNumber(this);
			});
			let billItemsAmount = totalItemsAmount;
			totalItems.set(totalItemsAmount);
		});
		const tableItems = $('#table-akun').DataTable({
			paging		: false,
			searching 	: false,
			ordering 	: false,
			info 		: false,
			data 		: <?= isset($transaksi->rincian_transaksi) ? json_encode($transaksi->rincian_transaksi) : '[]' ;?>,
			columns : [
				{ 
					data : 'id_akun',
					render : function ( columnData, type, rowData, meta ) {
						let selectedOption = (columnData != null && columnData != '' && columnData != 0) ? `<option selected="selected" value="`+ columnData +`">`+ rowData.nama_akun +`</option>`: ``;
						return String(`<select class="form-control select2-akun" value="`+ columnData +`" name="rincian[`+ meta.row +`][id_akun]" required="required">`+ selectedOption +`</select>`).trim();
					}
				},
				{ 
					data 		: 'total',
					width 		: '300px',
					className 	: 'text-right',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="item_total_` + meta.row + `" class="form-control text-right" value="`+ columnData +`" name="rincian[`+ meta.row +`][total]" data-column="total">
						`).trim();
					}
				}
			],
			initComplete : function(settings, json){
				let api = this.api();
				$(api.table().footer()).find('.btn-add-row').click(function(){
					api.row.add({ nama : '', total : 0 }).draw();
				});
				$(api.table().footer()).find('.btn-delete-row').click(function(){
					api.row( ':last' ).remove().draw();
				});

				$('#discountItems').keyup(function(){
					$('#table-akun').trigger('changeTotalItem');
				});
			},
			createdRow : function( row, data, index ){
				new AutoNumeric.multiple($(row).find('[id^="item"]').get(),currenciesOptions);
				$(row).find('.select2-akun').select2({
				});
			},
			rowCallback : function( row, data, displayNum, displayIndex, index ){
				let api = this.api();
				$(row).find('#item_total_' + index).keyup(function(){
					let harga 	= AutoNumeric.getNumber('#item_total_' + index);
					AutoNumeric.getAutoNumericElement('#item_total_' + index).set(harga);
					$('#table-akun').trigger('changeTotalItem');
				});
			},
			drawCallback : function( settings ){
				$('#table-akun').trigger('changeTotalItem');
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
