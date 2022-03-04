<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){
		$.fn.select2.defaults.set("width", "100%");
		$('.datepicker').datepicker({format:'yyyy-mm-dd'});
		const currenciesOptions = {
			unformatOnSubmit            : true,
			decimalCharacterAlternative: ".",
			decimalPlaces: 0
		};

        $('#select2-pemasok').select2({
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
						obj.id   = obj.ids
						return obj;
					});
					return {
						results : data
					};
				}
			}
		}).on('change',function(){
			$('#select2-pembelian').val(null).trigger('change');
		});

		const 	totalBill 		= new AutoNumeric('#total_bill', currenciesOptions),
		        totalDiskon 	= new AutoNumeric('#total_disc', currenciesOptions),
				totalDiskonawal = new AutoNumeric('#total_disc_awal', currenciesOptions),
				totalbayar		= new AutoNumeric('#dibayarkan', currenciesOptions),
				totalsisa		= new AutoNumeric('#sisa_tagihan', currenciesOptions),
				totalPayments 	= new AutoNumeric('#totalPayments',currenciesOptions);


		
		$('#table-items').on('changeTotalItem',	function(){
			let totalItemsAmount = 0;
			let totaldisc = 0;
			let discountItemsAmount = 0;
			let discountTotalAmount = 0;
			let totalDiskonAmount = 0;
			$(this).find('[id^="item_total_rincian"]').each(function(){
				totalItemsAmount += AutoNumeric.getNumber(this);
			});

			$(this).find('[id^="item_diskon_"]').each(function(){
				totaldisc += AutoNumeric.getNumber(this);
			});

			$(this).find('[id^="item_diskon_"]').each(function(){
				totalDiskonAmount += AutoNumeric.getNumber(this);
			});
			discountItemsAmount = AutoNumeric.getNumber($('#total_disc').get(0));
			totalDiskonawal.set(totaldisc + discountItemsAmount);
			discountTotalAmount = AutoNumeric.getNumber($('#total_disc_awal').get(0));
			let billItemsAmount = totalItemsAmount;
			// let billDiskonAmount = totalItemsAmount;
			// alert(totalItemsAmount);
			let total_amount = billItemsAmount -= discountTotalAmount ;
			totalBill.set(total_amount);
		
			let bayarItemsAmount = AutoNumeric.getNumber($('#dibayarkan').get(0));
			billItemsAmount -= bayarItemsAmount;
			totalsisa.set(billItemsAmount);
			$('#table-payments').trigger('changeTotalPayment');
		});

		$('#table-payments').on('changeTotalPayment', function(){
			var bill = $("#total_bill").val();
			var sisa = $("#sisa_tagihan").val();
			let totalPaymentsAmount = 0;
			$(this).find('[id^="total_"]').each(function(){
				totalPaymentsAmount += AutoNumeric.getNumber(this);
			});
			totalPayments.set(totalPaymentsAmount);
			var total = totalPaymentsAmount;
			totalbayar.set(totalPaymentsAmount);

			if( sisa == 0 ){
				$("#simpantabel").prop('disabled', false);
				$('#idbeli').prop('disabled', true);
			}else{
				$("#simpantabel").prop('disabled', true);
				$('#idbeli').prop('disabled', false);
			}
		
		});
        const tableItems = $('#table-items').DataTable({
			paging		: false,
			searching 	: false,
			ordering 	: false,
			info 		: false,
			data 		: <?= isset($pembayaran->nota) ? json_encode($pembayaran->nota) : '[]' ;?>,
			columns : [
				{ 
					data : 'id',
					render : function ( columnData, type, rowData, meta ) {
						let selectedOption = (columnData != null && columnData != '' && columnData != 0) ? `<option selected="selected" value="`+ columnData +`">`+ rowData.nomor +`</option>`: ``;
						return String(`<select class="form-control select2-pembelian" value="`+ columnData +`" name="nota[`+ meta.row +`][id]" required="required">`+ selectedOption +`</select> 	
						<input type="hidden" id="nomor` + meta.row + `" class="form-control text-right" value="`+ rowData.nomor +`" name="nota[`+ meta.row +`][nomor]" data-column="tgl" readonly="readonly">`).trim();
					}
				},{ 
					data 		: 'tgl_nota',
					className 	: 'text-right',
					width 		: '150px',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="tgl` + meta.row + `" class="form-control text-right" value="`+ columnData +`" name="nota[`+ meta.row +`][tgl_buat]" data-column="tgl" readonly="readonly">
						
						`).trim();
					}
				},{ data 		: 'total_rincian',
					className 	: 'text-right',
					width 		: '150px',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="item_total_rincian` + meta.row + `" class="form-control text-right" value="`+ columnData +`" name="nota[`+ meta.row +`][total_rincian]" data-column="total_rincian" readonly="readonly">
						`).trim();
					}
				},
				{ data 		: 'diskon',
					className 	: 'text-right',
					width 		: '150px',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="item_diskon_` + meta.row + `" class="form-control text-right" value="`+ columnData +`" name="nota[`+ meta.row +`][diskon]" data-column="sisa" readonly="readonly">
						`).trim();
					}
				},{ data 		: 'sisa_tagihan',
					className 	: 'text-right',
					width 		: '150px',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="item_sisa_tagihan_` + meta.row + `" class="form-control text-right" value="`+ columnData +`" name="nota[`+ meta.row +`][sisa_tagihan]" data-column="sisa" readonly="readonly">
						`).trim();
					}
				},
				{ 
					data 		: 'id',
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
					api.row.add({ id : '', tgl_nota :'' , sisa_tagihan : 0 , diskon : 0, total_rincian : 0}).draw();
				});
				// $(api.table().footer()).find('.btn-delete-row').click(function(){
				// 	api.row( ':last' ).remove().draw();
				// });
				$('#dibayarkan').keyup(function(){
					$('#table-items').trigger('changeTotalItem');
				});
				$('#total_disc').keyup(function(){
					$('#table-items').trigger('changeTotalItem');
				});
			},
			createdRow : function( row, data, index ){
				new AutoNumeric.multiple($(row).find('[id^="item"]').get(),currenciesOptions);
                let tgl = ($(row).find('[id^="tgl"]').get());
				let nomor = ($(row).find('[id^="nomor"]').get());
				$(row).find('.select2-pembelian').select2({
					ajax : {
						url 		: '<?= $module['url'];?>/api-data/select2-pembelian',
						dataType 	: 'json',
						type 		: 'POST',
						data 		: function (params) {
							var query = {
								search: params.term,
								type: 'public'
							}
							return $.extend({},params,{ 'id_pemasok': $('#select2-pemasok').val() });
						},
						processResults: function (myData) {
							var data = $.map(myData.results, function (obj) {
								obj.text = obj.text || obj.nomor;
								obj.id   = obj.id
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
					
						$('#item_sisa_tagihan_' + index).trigger('keyup');
						AutoNumeric.getAutoNumericElement('#item_diskon_' + index).set(data.diskon);
						AutoNumeric.getAutoNumericElement('#item_total_rincian' + index).set(data.total_rincian);
						AutoNumeric.getAutoNumericElement('#item_sisa_tagihan_' + index).set(data.total_rincian - data.diskon);
                        $('#tgl' + index).val(data.tgl_nota);
						$('#nomor' + index).val(data.nomor);
						// $('#diskon' + index).val(data.diskon);
                        $(row).find('#item_sisa_tagihan_').trigger('keyup');
						$(row).find('#item_total_').trigger('keyup');
                        $(row).find('#tgl').trigger('keyup');
						$(row).find('#nomor').trigger('keyup');
						$('#table-items').trigger('changeTotalItem');
					}
				});
			},
			rowCallback : function( row, data, displayNum, displayIndex, index ){
				let api = this.api();
				
				$(row).find('#id_'+ index).click(function(){
					api.row($(this).closest("tr").get(0)).remove().draw();
				});	
				$(row).find('#item_harga_retur_' + index + ', ' + '#item_qty_input_' + index).on('keyup change', function(){
                        $('#table-items').trigger('changeTotalPayment');
				
				});
			},
			drawCallback : function( settings ){
				
			}
		});

		const tablePayments 	= $('#table-payments').DataTable({
			paging		: false,
			searching 	: false,
			ordering 	: false,
			info 		: false,
			data 		: <?= isset($pembayaran->rincian) ? json_encode($pembayaran->rincian) : '[]' ;?>,
			columns : [
				{ 
					data 	: 'metode',
					width 	: '120px',
					render 	: function ( columnData, type, rowData, meta ) {
						return String(`
							<select class="form-control select2-metode" data-name="metode" required="required" name="rincian[`+ meta.row +`][metode]">
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
							<select class="form-control select2-akun" data-column="id_akun" required="required" name="rincian[`+ meta.row +`][id_akun]">`+ selectedOption +`</select>
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
							<select class="form-control show-hide select2-giro" data-column="nomor_giro" name="rincian[`+ meta.row +`][id_giro]" disabled="disabled">
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
							<input class="form-control tgl-giro show-hide" data-column="tgl_giro" name="rincian[`+ meta.row +`][tgl_giro]" disabled="disabled" value="`+ date +`">
						`).trim();
					}
				},
				{ 
					data 		: 'nominal',
					width 		: '150px',
					className 	: 'text-right',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="nominal_`+ meta.row +`" class="form-control text-right" data-column="nominal" name="rincian[`+ meta.row +`][nominal]" value="`+ columnData +`">
						`).trim();
					}
				},
				{ 
					data 		: 'total',
					width 		: '150px',
					className 	: 'text-right',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="total_`+ meta.row +`" class="form-control text-right" data-column="total" name="rincian[`+ meta.row +`][total]" value="`+ columnData +`" readonly="readonly">
						`).trim();
					}
				},
				{ 
					data 		: 'id',
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
					api.row.add({ metode : 'cash', akun : 0, nomor_giro : 1, tgl_giro : 0, nominal : 0,  total : 0 }).draw();
				});
				// $(api.table().footer()).find('.btn-delete-row').click(function(){
				// 	api.row( ':last' ).remove().draw();
				// });
			},
			createdRow : function( row, data, index ){
				new AutoNumeric($(row).find('[id^="nominal"]').get(0),currenciesOptions);
				new AutoNumeric($(row).find('[id^="total_"]').get(0),currenciesOptions);
				// new AutoNumeric($(row).find('[id^="potongan_"]').get(0),currenciesOptions);
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
				$(row).find('.tgl-giro').datepicker({format:'yyyy-mm-dd'});
				if($(row).find('.select2-metode').val() == 'giro'){
					$(row).find('.show-hide').removeAttr('disabled');
				} else {
					$(row).find('.show-hide').val(null).attr('disabled','disabled');
				}
			},
			rowCallback : function( row, data, displayNum, displayIndex, index ){
				let api = this.api();
				
				$(row).find('#id_'+ index).click(function(){
					api.row($(this).closest("tr").get(0)).remove().draw();
				});
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
				$(row).find('#nominal_' + index ).keyup(function(){
					let nominal 	= AutoNumeric.getNumber('#nominal_' + index);
					// let potongan 	= AutoNumeric.getNumber('#potongan_' + index);
					AutoNumeric.getAutoNumericElement('#total_' + index).set(nominal);
					$('#table-payments').trigger('changeTotalPayment');
					$('#table-items').trigger('changeTotalItem');
				});				
			},
			drawCallback : function( settings ){
				$('#table-payments').trigger('changeTotalPayment');
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