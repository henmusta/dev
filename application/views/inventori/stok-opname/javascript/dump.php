<?php $stok_opname = isset($data) ? $data : (object)[];?>
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
			//minimumValue: "0",
		};
		
		const tableItems = $('#table-items').DataTable({
			paging		: false,
			searching 	: false,
			ordering 	: false,
			info 		: false,
			data 		: <?= isset($stok_opname->rincian_stok_opname) ? json_encode($stok_opname->rincian_stok_opname) : '[]' ;?>,
			columns : [
				{ 
					data : 'id_produk',
					render : function ( columnData, type, rowData, meta ) {
						let selectedOption = (columnData != null && columnData != '' && columnData != 0) ? `<option selected="selected" value="`+ columnData +`">`+ rowData.nama +`</option>`: ``;
						return String(`<select class="form-control select2-produk" value="`+ columnData +`" name="rincian[`+ meta.row +`][id_produk]" required="required">`+ selectedOption +`</select>`).trim();
					}
				},
				{ 
					data 		: 'qty_komputer',
					className 	: 'text-right',
					width 		: '150px',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="qty_komputer` + meta.row + `" class="form-control text-right" value="`+ columnData +`" name="rincian[`+ meta.row +`][qty_komputer]" data-column="qty_komputer" readonly="readonly">
						`).trim();
					}
				},
				{ 
					data 		: 'qty_fisik',
					className 	: 'text-right',
					width 		: '150px',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="qty_fisik` + meta.row + `" class="form-control text-right" value="`+ columnData +`" name="rincian[`+ meta.row +`][qty_fisik]" data-column="qty_fisik">
							<input id="qty_harga` + meta.row + `" class="form-control text-right" value="`+ rowData.harga_jual +`" name="rincian[`+ meta.row +`][qty_harga]" data-column="qty_komputer">
						`).trim();
					}
				},
				{ 
					data 		: 'qty_selisih',
					width 		: '150px',
					className 	: 'text-right',
					render 		: function ( columnData, type, rowData, meta ) {
						let qty_selisih = parseInt(rowData.qty_komputer) - parseInt(rowData.qty_fisik);
						return String(`
							<input id="qty_selisih` + meta.row + `" class="form-control text-right" value="`+ qty_selisih +`" name="rincian[`+ meta.row +`][qty_selisih]" readonly="readonly" data-column="qty_selisih">
						`).trim();
					}
				}
			],
			initComplete : function(settings, json){
				let api = this.api();
				$(api.table().footer()).find('.btn-add-row').click(function(){
					api.row.add({ nama : '', qty_komputer : 0, qty_fisik : 0, qty_selisih : 0 }).draw();
				});
				$(api.table().footer()).find('.btn-delete-row').click(function(){
					api.row( ':last' ).remove().draw();
				});

				$('#discountItems').keyup(function(){
					$('#table-items').trigger('changeTotalItem');
				});
			},
			createdRow : function( row, data, index ){
				new AutoNumeric.multiple($(row).find('[id^="qty"]').get(),currenciesOptions);
				$(row).find('.select2-produk').select2({
					ajax : {
						url 		: '<?= $module['url'];?>/api-data/select2-produk',
						dataType 	: 'json',
						type 		: 'POST',
						data 		: function (params) {
							var query = {
								search: params.term,
								type: 'public'
							}
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
				}).on({
					'select2:select' : function(e){
						let data = e.params.data;
						AutoNumeric.getAutoNumericElement('#qty_komputer' + index).set(data.saldo);
						$('#qty_komputer' + index).trigger('keyup');
						AutoNumeric.getAutoNumericElement('#qty_harga' + index).set(data.harga_beli);
						$('#qty_harga' + index).trigger('keyup');
					}
				});
			},
			rowCallback : function( row, data, displayNum, displayIndex, index ){
				let api = this.api();
				$(row).find('#qty_komputer' + index + ', ' + '#qty_fisik' + index).keyup(function(){
					let qty_komputer 	= AutoNumeric.getNumber('#qty_komputer' + index),
						qty_fisik 		= AutoNumeric.getNumber('#qty_fisik' + index);
					AutoNumeric.getAutoNumericElement('#qty_selisih' + index).set(qty_fisik - qty_komputer);
					$('#table-items').trigger('changeTotalItem');
				});
			},
			drawCallback : function( settings ){
				$('#table-items').trigger('changeTotalItem');
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
