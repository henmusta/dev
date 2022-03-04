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

		const 	totalPayments 	= new AutoNumeric('#totalPayments',currenciesOptions);

		$('#table-payments').on('changeTotalPayment', function(){

			let totalPaymentsAmount = 0;
			$(this).find('[id^="total_"]').each(function(){
				totalPaymentsAmount += AutoNumeric.getNumber(this);
			});
			totalPayments.set(totalPaymentsAmount);

		});

		const tablePayments 	= $('#table-payments').DataTable({
			paging		: false,
			searching 	: false,
			ordering 	: false,
			info 		: false,
			data 		: <?= isset($pelunasan->rincian) ? json_encode($pelunasan->rincian) : '[]' ;?>,
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
						let date = columnData != null ? columnData : '<?= isset($penjualan->tgl_nota) ? $penjualan->tgl_nota :"NULL";?>';
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
					data 		: 'potongan',
					width 		: '150px',
					className 	: 'text-right',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="potongan_`+ meta.row +`" class="form-control text-right" data-column="potongan" name="rincian[`+ meta.row +`][potongan]" value="`+ columnData +`">
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
				$(api.table().footer()).find('.btn-delete-row').click(function(){
					api.row( ':last' ).remove().draw();
				});
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

		$('#select2-pelanggan').select2({
			ajax : {
				url 		: '<?= $module['url'];?>/api-data/select2-pelanggan',
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
						obj.text = obj.text || obj.nama +' / '+ obj.alamat;
						obj.id   = obj.id
						return obj;
					});
					return {
						results : data
					};
				}
			}
		}).on('change',function(){
			$('#select2-penjualan').val(null).trigger('change');
			$('#asu').val($('#select2-penjualan').val());
		});


		// $('#select2-pelanggan').select2({
		// 	ajax : {
		// 		url 		: '<?= $module['url'];?>/api-data/select2-pelanggan',
		// 		type 		: 'POST',
		// 		dataType 	: 'json',
		// 		processResults: function (myData) {
		// 					var data = $.map(myData.results, function (obj) {
		// 						obj.text = obj.text || obj.nama +' / '+ obj.alamat;
		// 						obj.id = obj.id;
		// 						return obj;
		// 					});
		// 					return {
		// 						results : data
		// 					};
		// 				}
		// 	}
		// }).on('change',function(){
		// 	$('#select2-penjualan').val(null).trigger('change');
		// });
		$('#select2-penjualan').select2({
			ajax : {
				url 		: '<?= $module['url'];?>/api-data/select2-penjualan',
				type 		: 'POST',
				dataType 	: 'json',
				data 		: function (params) {
					var query = {
						search: params.term,
						type: 'public'
					}
					return $.extend({},params,{ 'id_pelanggan': $('#select2-pelanggan').val() });
				},
				
			}
		}).on({
			change : function(){
				$.post("<?= $module['url'];?>/api-data/get-penjualan",{pk:$(this).val()},function(response){
					$('#total_tagihan').html($.fn.dataTable.render.number( ',', '.', 0, '' ).display(response.total_tagihan));
					$('#total_pelunasan').html($.fn.dataTable.render.number( ',', '.', 0, '' ).display(response.total_pelunasan));
					$('#sisa_tagihan').html($.fn.dataTable.render.number( ',', '.', 0, '' ).display(response.sisa_tagihan));
				},'json');
			}
		});
	});
});
</script>