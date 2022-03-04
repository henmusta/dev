<?php $retur_penjualan = isset($data) ? $data : (object)[];?>
<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){
		$.fn.select2.defaults.set("width", "100%");
		$('#tgl-nota').datepicker({format:'yyyy-mm-dd',todayHighlight:true});
		$('#select-pelanggan').select2({
			ajax : {
				url 		: '<?= $module['url'];?>/api-data/select2-pelanggan',
				dataType 	: 'json',
				type 		: 'POST',
				processResults: function (myData) {
					var data = $.map(myData.results, function (obj) {
						obj.text = obj.text || obj.nama;
						obj.id   = obj.id
						return obj;
					});
					return {
						results : data
					};
				}
			}
		});

		$('#select-pelanggan').on('change',function(){
				$('.select2-penjualan').val(null).trigger('change');
			});
	
			$('#select2-penjualan').select2({
			ajax : {
				url 		: '<?= $module['url'];?>/api-data/select2-penjualan',
				dataType 	: 'json',
				type 		: 'POST',
				data 		: function (params) {
							var query = {
								search: params.term,
								type: 'public'
							}
							return $.extend({},params,{ id_pelanggan: $('#select-pelanggan').val() });
						},
				processResults: function (myData) {
					var data = $.map(myData.results, function (obj) {
						obj.text = obj.text || obj.id +' / '+ obj.nomor;
						obj.id   = obj.id
						return obj;
					});
					return {
						results : data
					};
				}
			}
		});

		$('#select2-penjualan').on('change',function(){
				$('.select2-produk').val(null).trigger('change');
			});

		const currenciesOptions = {
			unformatOnSubmit            : true,
			decimalCharacterAlternative: ".",
			decimalPlaces: 0,
			//minimumValue: "0",
		};
		const 	totalItems 		= new AutoNumeric('#totalItems',currenciesOptions);
		$('#table-items').on('changeTotalItem',	function(){
			let totalItemsAmount = 0;
			$(this).find('[id^="item_total_"]').each(function(){
				totalItemsAmount += AutoNumeric.getNumber(this);
			});
			let billItemsAmount = totalItemsAmount;
			totalItems.set(totalItemsAmount);
		});
		const tableItems = $('#table-items').DataTable({
			paging		: false,
			searching 	: false,
			ordering 	: false,
			info 		: false,
			data 		: <?= isset($retur_penjualan->rincian_retur_penjualan) ? json_encode($retur_penjualan->rincian_retur_penjualan) : '[]' ;?>,
			columns : [
				{ 
					data : 'id_produk',
					render : function ( columnData, type, rowData, meta ) {
						let selectedOption = (columnData != null && columnData != '' && columnData != 0) ? `<option selected="selected" value="`+ columnData +`">`+ rowData.nama +`</option>`: ``;
						return String(`<select class="form-control select2-produk" value="`+ columnData +`" name="rincian[`+ meta.row +`][id_produk]" required="required">`+ selectedOption +`</select>`).trim();
					}
				},
				{ 
					data 		: 'harga',
					className 	: 'text-right',
					width 		: '150px',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="item_harga_` + meta.row + `" class="form-control text-right" value="`+ columnData +`" name="rincian[`+ meta.row +`][harga]" data-column="harga">
						`).trim();
					}
				},
				{ 
					data 		: 'qty',
					className 	: 'text-right',
					width 		: '150px',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="item_qty_` + meta.row + `" class="form-control text-right" value="`+ columnData +`" name="rincian[`+ meta.row +`][qty]" data-column="qty">
						`).trim();
					}
				},
				{ 
					data 		: 'qty_awal',
					className 	: 'text-right',
					width 		: '150px',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="item_qty_retur_` + meta.row + `" class="form-control text-right" value="`+ columnData +`" name="rincian[`+ meta.row +`][qty_retur]" data-column="qty total" readonly="readonly">
						`).trim();
					}
				},
				{ 
					data 		: 'total',
					width 		: '150px',
					className 	: 'text-right',
					render 		: function ( columnData, type, rowData, meta ) {
						let total = parseInt(rowData.harga) * parseInt(rowData.qty);
						return String(`
							<input id="item_total_` + meta.row + `" class="form-control text-right" value="`+ total +`" name="rincian[`+ meta.row +`][total]" readonly="readonly" data-column="total">
						`).trim();
					}
				},
				{ 
					data 		: 'id_produk',
					width 		: '50px',
					className 	: 'text-center',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<button type="button" id="id_` + meta.row + `" class="btn btn-sm btn-outline-secondary btn-delete-row"><i class="fa fa-minus"></i></button>
						`).trim();
					}
				}
			],
			initComplete : function(settings, json){
				let api = this.api();
				$(api.table().footer()).find('.btn-add-row').click(function(){
					api.row.add({ nama : '', harga : 0, qty : 0, qty_retur: 0, total : 0 }).draw();
				});
				// $(api.table().footer()).find('.btn-delete-row').click(function(){
				// 	api.row( ':last' ).remove().draw();
				// });

				$('#discountItems').keyup(function(){
					$('#table-items').trigger('changeTotalItem');
				});
			},
			createdRow : function( row, data, index ){
				new AutoNumeric.multiple($(row).find('[id^="item"]').get(),currenciesOptions);
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
							return $.extend({},params,{ id_penjualan: $('#select2-penjualan').val() });
						},
						processResults: function (myData) {
							var data = $.map(myData.results, function (obj) {
								obj.text = obj.text || obj.kode_pemasok + ' / ' + obj.nama_produk;
								obj.id   = obj.id_produk
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
						AutoNumeric.getAutoNumericElement('#item_harga_' + index).set(data.harga_jual);
						$('#item_harga_' + index).trigger('keyup');
						AutoNumeric.getAutoNumericElement('#item_qty_retur_' + index).set(data.qty);
						$('#item_qty_retur_' + index).trigger('keyup');
					}
				});
			},
			rowCallback : function( row, data, displayNum, displayIndex, index ){
				let api = this.api();
				
				$(row).find('#id_'+ index).click(function(){
					api.row($(this).closest("tr").get(0)).remove().draw();
				});		

				$(row).find('#item_harga_' + index + ', ' + '#item_qty_' + index).on('keyup change', function(){
					let harga 	= AutoNumeric.getNumber('#item_harga_' + index),
						qty 		= AutoNumeric.getNumber('#item_qty_' + index),
						stok 		= AutoNumeric.getNumber('#item_qty_retur_' + index);
						if(qty > stok){
							AutoNumeric.getAutoNumericElement('#item_qty_' + index).set('');
							AutoNumeric.getAutoNumericElement('#item_total_' + index).set('');
					    	$('#item_qty_' + index).trigger('keyup');
							alert("Jumlah Qty Melebihi Stok Yang Ada");
						}
					AutoNumeric.getAutoNumericElement('#item_total_' + index).set(harga * qty);
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
