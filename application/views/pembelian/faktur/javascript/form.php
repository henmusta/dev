<?php $pembelian = isset($data) ? $data : (object)[];?>
<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){
		// const pathArray = window.location.pathname.split("/");
		// const segment_4 = pathArray[4];
		// // console.log(segment_1);
		// if(segment_4  == 'update'){
		// 	$('#btnpemasok').attr("hidden",true);
		// }else{
		// 	$('#btnpemasok').attr("hidden",false);
		// }
		$.fn.select2.defaults.set("width", "100%");
		$("#btnpemasok").on('click', function(){
			$("#form-new").attr("hidden",false);
			$("#form-old").attr("hidden",true);
			$("#clspemasok").attr("hidden",false);
			$("#btnpemasok").attr("hidden",true);
			$("#nama-pemasok").attr("disabled",true);
			$("#kode-pemasok").attr("disabled",true);
			$("#nama-pemasok1").attr("disabled",false);
			$("#kode-pemasok1").attr("disabled",false);
			$("#pemasok_new1").attr("disabled",false);
        });
		$("#clspemasok").on('click', function(){
			$("#form-new").attr("hidden",true);
			$("#form-old").attr("hidden",false);
			$("#clspemasok").attr("hidden",true);
			$("#btnpemasok").attr("hidden",false);
			$("#nama-pemasok").attr("disabled",false);
			$("#kode-pemasok").attr("disabled",false);
			$("#nama-pemasok1").attr("disabled",true);
			$("#kode-pemasok1").attr("disabled",true);
			$("#pemasok_new1").attr("disabled",true);
        });
		$('#nama-pemasok').select2({
			ajax : {
				url 		: '<?= $module['url'];?>/api-data/select2-pemasok',
				dataType 	: 'json',
				type 		: 'POST',
				data 		: function (params) {
							var query = {
								search: params.term,
								type: 'public'
							}
							return $.extend({},params,{ id_cabang: $('#id_cabang').val() });
						},
				processResults: function (myData) {
					var data = $.map(myData.results, function (obj) {
						obj.text = obj.text || obj.kode +' / '+ obj.nama;
						obj.id   = obj.nama
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
						console.log( e.params.data);
						// alert(data.id);
						$('#kode-pemasok').val(data.kode);
						// $('#alamat-pelanggan').val(data.alamat);
						// AutoNumeric.getAutoNumericElement('#item_harga_retur_' + index).set(data.harga_jual);
						// $('#item_harga_' + index).trigger('keyup');
						// AutoNumeric.getAutoNumericElement('#item_qty_retur_' + index).set(data.qty);
						// $('#item_qty_retur_' + index).trigger('keyup');
					}
				});

		$('.datepicker').datepicker({format:'yyyy-mm-dd',todayHighlight:true});
		$('#tgl-nota').datepicker({format:'yyyy-mm-dd',todayHighlight:true});


		const currenciesOptions = {
			unformatOnSubmit            : true,
			decimalCharacterAlternative: ".",
			decimalPlaces: 0,
			//minimumValue: "0",
		};

		const 	totalItems 		= new AutoNumeric('#totalItems',currenciesOptions),
				discountItems 	= new AutoNumeric('#discountItems',currenciesOptions),
				billItems 		= new AutoNumeric('#billItems',currenciesOptions),
				totalPayments 	= new AutoNumeric('#totalPayments',currenciesOptions),
				totalDebt 		= new AutoNumeric('#totalDebt',currenciesOptions);

		// ('#billItems')

		$('#table-payments').on('changeTotalPayment', function(){

			let billItemsAmount = billItems.getNumber();
			let totalPaymentsAmount = 0;
			$(this).find('[id^="total_"]').each(function(){
				totalPaymentsAmount += AutoNumeric.getNumber(this);
			});
			totalPayments.set(totalPaymentsAmount);
			totalDebt.set(billItemsAmount - totalPaymentsAmount);
				// console.log(totalDebt);
			var total = billItemsAmount - totalPaymentsAmount;
			if( total <= 0 ){
				$('#idbeli').prop('disabled', true);
			}else{
				$('#idbeli').prop('disabled', false);
			}
			
		});

		$('#table-items').on('changeTotalItem',	function(){
			let totalItemsAmount = 0;
			$(this).find('[id^="item_total_"]').each(function(){
				totalItemsAmount += AutoNumeric.getNumber(this);
			});
			let billItemsAmount = totalItemsAmount;
			totalItems.set(totalItemsAmount);
			let discountItemsAmount = AutoNumeric.getNumber($('#discountItems').get(0));
			billItemsAmount -= discountItemsAmount;
			billItems.set(billItemsAmount);
			$('#table-payments').trigger('changeTotalPayment');
		});
		const tableItems = $('#table-items').DataTable({
			paging		: false,
			searching 	: false,
			ordering 	: false,
			info 		: false,
			data 		: <?= isset($pembelian->rincian_pembelian) ? json_encode($pembelian->rincian_pembelian) : '[]' ;?>,
			columns : [
				{ 
					data : 'nama',
					render : function ( columnData, type, rowData, meta ) {
						return String(`
							<div style="max-width:100%;">
							<input id="nama-produk-`+ meta.row +`" class="form-control" value="`+ columnData +`" data-name="nama_barang" name="produk[`+ meta.row +`][nama]">
							<input hidden ="true" id="pd_id-`+ meta.row +`" class="form-control" value="`+ rowData.id_produk +`" data-name="pd_id" name="asu[`+ meta.row +`][pd_id]">
							</div>
						`).trim();
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
							<input id="qty_` + meta.row + `" class="form-control text-right" value="`+ columnData +`" name="rincian[`+ meta.row +`][qty]" data-column="qty">
								<input type="hidden" name="rincian[`+ meta.row +`][id_cabang]" value="<?php echo $this->user->id_cabang;?>">
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
					api.row.add({ nama : '', harga : 0, qty : 1, total : 0 }).draw();
				});
				$(api.table().footer()).find('.btn-delete-row').click(function(){
					api.row( ':last' ).remove().draw();
				});

				$('#discountItems').keyup(function(){
					$('#table-items').trigger('changeTotalItem');
				});
			},
			createdRow : function( row, data, index ){
				new AutoNumeric.multiple($(row).find('[id^="item"]').get(),currenciesOptions);
				$(row).find('#item_harga_' + index + ', ' + '#qty_' + index).keyup(function(){
					let harga 	= AutoNumeric.getNumber('#item_harga_' + index),
						qty 		= $(row).find('[id^="qty_"]').val();
					AutoNumeric.getAutoNumericElement('#item_total_' + index).set(harga * qty);
					$('#table-items').trigger('changeTotalItem');
				});
				setTimeout(function(){	
					$("#nama-produk-" + index).easyAutocomplete({
						url 		: function(q){
							return '<?= $module['url'];?>/api-data/autocomplete-produk?q=' + q + '&format=json' + '&kode-pemasok=' + $('#kode-pemasok').val();
						},
						getValue 	: 'nama',
						list 		: {
							match : { enabled:true },
							onSelectItemEvent: function() {
            				var value = $("#nama-produk-" + index).getSelectedItemData().harga_beli;
        					AutoNumeric.getAutoNumericElement('#item_harga_' + index).set(value);
								$('#item_harga_' + index).trigger('keyup');
   							   }
						}
					});
				},100)
			},
			rowCallback : function( row, data, displayNum, displayIndex, index ){
				//console.log("#nama-produk-" + index);
				let api = this.api();
				$(row).find('#id_'+ index).click(function(){
					api.row($(this).closest("tr").get(0)).remove().draw();
				});		
			},
			drawCallback : function( settings ){
				$('#table-items').trigger('changeTotalItem');
			}
		});
		const tablePayments 	= $('#table-payments').DataTable({
			paging		: false,
			searching 	: false,
			ordering 	: false,
			info 		: false,
			data 		: <?= isset($pembelian->rincian_pembayaran) ? json_encode($pembelian->rincian_pembayaran) : '[]' ;?>,
			columns : [
				{ 
					data 	: 'metode',
					width 	: '120px',
					render 	: function ( columnData, type, rowData, meta ) {
						return String(`
							<select class="form-control select2-metode" data-name="metode" required="required" name="pembayaran[`+ meta.row +`][metode]">
								<option value="tunai" `+ ( columnData == 'tunai' ? `selected="selected"` : ``) +`>Tunai</option>
								<option value="debit" `+ ( columnData == 'debit' ? `selected="selected"` : ``) +`>Debit</option>
								<option value="giro" `+ ( columnData == 'giro' ? `selected="selected"` : ``) +`>Giro</option>
							</select>
						`).trim();
					}
				},
				{ 
					data 	: 'id_akun',
					render 	: function ( columnData, type, rowData, meta ) {
						let selectedOption = (columnData != null && columnData != '' && columnData != 0) ? `<option selected="selected" value="`+ columnData +`">`+ rowData.nama_akun +`</option>`: ``;
						return String(`
							<select class="form-control select2-akun" data-column="id_akun" required="required" name="pembayaran[`+ meta.row +`][id_akun]">`+ selectedOption +`</select>
						`).trim();
					}
				},
				{ 
					data 		: 'id_giro',
					className 	: 'text-right',
					width 		:'150px',
					render 		: function ( columnData, type, rowData, meta ) {
						let selectedOption = (rowData.id_giro != null && rowData.id_giro != '' && rowData.id_giro != 0) ? `<option selected="selected" value="`+ columnData +`">`+ rowData.nomor_giro +`</option>`: ``; 
						return String(`
							<select class="form-control show-hide select2-giro" data-column="nomor_giro" name="pembayaran[`+ meta.row +`][id_giro]" disabled="disabled">
								`+ selectedOption +`
							</select>
						`).trim();
					}
				},
				{ 
					data 		: 'tgl_giro',
					width 		: '150px',
					className 	: 'text-right',
					render 		: function ( columnData, type, rowData, meta ) {
						let date = columnData != null ? columnData : '<?= isset($pembelian->tgl_nota) ? $pembelian->tgl_nota :"NULL";?>';
						return String(`
							<input class="form-control tgl-giro show-hide" data-column="tgl_giro" name="pembayaran[`+ meta.row +`][tgl_giro]" disabled="disabled" value="`+ date +`">
						`).trim();
					}
				},
				{ 
					data 		: 'potongan',
					width 		: '150px',
					className 	: 'text-right',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="potongan_`+ meta.row +`" class="form-control text-right" data-column="potongan" name="pembayaran[`+ meta.row +`][potongan]" value="`+ columnData +`">
						`).trim();
					}
				},
				{ 
					data 		: 'nominal',
					width 		: '150px',
					className 	: 'text-right',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="nominal_`+ meta.row +`" class="form-control text-right" data-column="nominal" name="pembayaran[`+ meta.row +`][nominal]" value="`+ columnData +`">
						`).trim();
					}
				},
				{ 
					data 		: 'total',
					width 		: '150px',
					className 	: 'text-right',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="total_`+ meta.row +`" class="form-control text-right" data-column="total" name="pembayaran[`+ meta.row +`][total]" value="`+ columnData +`" readonly="readonly">
						`).trim();
					}
				},
				{ 
					data 		: 'id_akun',
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
					api.row.add({ metode : 'cash', akun : 0, nomor_giro : 1, tgl_giro : 0, nominal : 0, potongan : 0, total : 0 }).draw();
				});
				// $(api.table().footer()).find('.btn-delete-row').click(function(){
				// 	api.row( ':last' ).remove().draw();
				// });

				// $('#discountItems').keyup(function(){
				// 	$('#table-items').trigger('changeTotalItem');
				// });
			},
			createdRow : function( row, data, index ){
				new AutoNumeric($(row).find('[id^="nominal"]').get(0),currenciesOptions);
				new AutoNumeric($(row).find('[id^="potongan"]').get(0),currenciesOptions);
				new AutoNumeric($(row).find('[id^="total_"]').get(0),currenciesOptions);
				$(row).find('.select2-metode').select2();
				$(row).find('.select2-akun').select2({
					ajax : {
						url 		: '<?= $module['url'];?>/api-data/select2-akun',
						dataType 	: 'json',
						type 		: 'POST',
						data 		: function (params) {
							var query = {
								search: params.term,
								type: 'public'
							}
							return $.extend({},params,{ metode: $(row).find('.select2-metode').val() });
						},
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
				$(row).find('.select2-giro').select2({
					ajax : {
						url 		: '<?= $module['url'];?>/api-data/select2-giro',
						dataType 	: 'json',
						type 		: 'POST',
						data 		: function (params) {
							var query = {
								search: params.term,
								type: 'public'
							}
							return $.extend({},params,{ id_cabang: $('#id_cabang').val() });
						},
						processResults: function (myData) {
							var data = $.map(myData.results, function (obj) {
								obj.text = obj.text || obj.nomor; // replace name with the property used for the text
								return obj;
							});
							return {
								results : data,
								pagination : myData.pagination
							};
						}
					}
				});
				$(row).find('.tgl-giro').datepicker({format:'yyyy-mm-dd',todayHighlight:true});
				if($(row).find('.select2-metode').val() == 'giro'){
					$(row).find('.show-hide').removeAttr('disabled');
				} else {
					$(row).find('.show-hide').val(null).attr('disabled','disabled');
				}
			},
			rowCallback : function( row, data, displayNum, displayIndex, index ){
				let api = this.api();
				$(row).find('.select2-metode').on({
					change : function(){
						let metode = $(this).val();
						$(row).find('.select2-akun').val(null).trigger('change');
						if(metode == 'giro'){
							$(row).find('.show-hide').removeAttr('disabled');
						} else {
							$(row).find('.show-hide').val(null).attr('disabled','disabled').trigger('change');
						}
					}
				});

				$(row).find('#id_'+ index).click(function(){
					api.row($(this).closest("tr").get(0)).remove().draw();
				});		
				$(row).find('#nominal_' + index  + ', #potongan_' + index).keyup(function(){
					let nominal 	= AutoNumeric.getNumber('#nominal_' + index),
						potongan 	= AutoNumeric.getNumber('#potongan_' + index);
					AutoNumeric.getAutoNumericElement('#total_' + index).set(nominal + potongan);
					$('#table-payments').trigger('changeTotalPayment');
				});				
			},
			drawCallback : function( settings ){
				$('#table-payments').trigger('changeTotalPayment');
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
