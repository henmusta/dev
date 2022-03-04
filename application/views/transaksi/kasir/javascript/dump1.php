<?php 
$transaksi = isset($data) ? $data : (object)[];?>
<style>
.hidden{
   display: none !important; 
   visibility : hidden !important;
}
</style>
<script type="text/javascript">
function showbiaya() {
   alert("Submit button clicked!");
   return true;
}
$(function(){
	'use strict'
	$(document).ready(function(){
		// var pathArray = window.location.pathname.split( '/' );
		// var segment = pathArray[5];
		// console.log(segment);
		// $('#tgl-nota').val(segment);
		$('.datepicker').datepicker({
			format:'yyyy-mm-dd',todayHighlight:true, autoclose:true
		}).on({
			change : function(){		
				
			}
		});

	

		// $('#penjualan').on('changeValue',function(){
		// 	let nama = $(this).val();
		// 	$.post(
		// 		'<?= $module['url'];?>/api-data/get-kode-pemasok-by-nama',
		// 		{nama:nama}, function(response){
		// 			if(response.status == 'success'){
		// 				$('#kode-pemasok').val(response.kode_pemasok);
		// 			} else {
		// 				$('#kode-pemasok').val(null);
		// 			}
		// 		},
		// 		'json'
		// 	);
		// });

		$.fn.select2.defaults.set("width", "100%");
			const currenciesOptions = {
			unformatOnSubmit            : true,
			decimalCharacterAlternative: ".",
			decimalPlaces: 0
		};
		const 	totalItems 		= new AutoNumeric('#setoran',currenciesOptions),
				kasakhir 		= new AutoNumeric('#kasakhir',currenciesOptions),
				totalbiaya 		= new AutoNumeric('#totalbiaya',currenciesOptions),
				penjualan 		= new AutoNumeric('#penjualan',currenciesOptions),
				rumus  			= new AutoNumeric('#rumus',currenciesOptions),
				register  		= new AutoNumeric('#register',currenciesOptions),
				kasawal  		= new AutoNumeric('#kasawal',currenciesOptions);

		

				$('#kasakhir').keyup(function(e){
					e.preventDefault();
					var a = parseInt(rumus.rawValue);
					var b = parseInt(kasakhir.rawValue);
					var c = b - a;
					console.log(b)
					register.set(c);
					if(a < b ){
						$("#cek").val("register");
						$("#cek").css("color", "black");
					}else{
						$("#cek").val("selisih");
						$("#cek").css("color", "red");
					}
				});

				$("#opnbiaya").on('click', function(){
			$("#data-biaya").removeClass('hidden');
			var a = parseInt(rumus.rawValue);
							var b = parseInt(kasakhir.rawValue);
							var c = b - a;
							console.log(b)
							register.set(c);
							if(a < b ){
								$("#cek").val("register");
								$("#cek").css("color", "black");
							}else if(a == b ){
								$("#cek").val("Balance");
								$("#cek").css("color", "black");
							}else{
								$("#cek").val("selisih");
								$("#cek").css("color", "red");
							}
        });

				$("#clsbiaya").on('click', function(){
					$("#formbiaya").addClass('hidden');
       				 });
				
				$.post("<?= $module['url'];?>/api-data/get-penjualan",{
					pk:$('#tgl-nota').val(),
					cb:$('#id_cabang').val()
				},function(response){
					penjualan.set(response.total);
				},'json');	
				$.post("<?= $module['url'];?>/api-data/get-rincian",{
					pk:$('#tgl-nota').val(),
					cb:$('#id_cabang').val()
				},function(response){
					// console.log(response.akun);
				},'json');	

				$.post("<?= $module['url'];?>/api-data/get-akun",{
					pk:$('#tgl-nota').val(),
					cb:$('#id_cabang').val()
				},function(response){
					// console.log(response.rincian_pelunasan);
				},'json');	
			
				
				$.post("<?= $module['url'];?>/api-data/get-saldo",{
					pk:$('#tgl-nota').val(),
					cb:$('#id_cabang').val()
				},function(response){
					// if()
					kasawal.set(response.total)
					console.log(response.total)
				},'json');

		$('#table-items').on('changeTotalItem',	function(){
			let totalItemsAmount = 0;
			$(this).find('[id^="nominal_kas_"]').each(function(){
				totalItemsAmount += AutoNumeric.getNumber(this);
			});
			totalItems.set(totalItemsAmount);
			$('#table-items').trigger('changeTotalPayment');
		});	

		$('#table-biaya').on('changeTotalItem',	function(){
			let totalItemsAmount = 0;
			$(this).find('[id^="nominal_masuk_"]').each(function(){
				totalItemsAmount += AutoNumeric.getNumber(this);
			});
			totalbiaya.set(totalItemsAmount);
			var a = parseInt(kasawal.rawValue);
			var b = parseInt(penjualan.rawValue);
			var c = parseInt(totalItems.rawValue);
			var d = parseInt(totalbiaya.rawValue);
			var e = ((a + b) - c) - d;
			// console.log(e);
			rumus.set(e);
			$('#table-biaya').trigger('changeTotalPayment');
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

		const tableItems = $('#table-items').DataTable({
			paging		: false,
			searching 	: false,
			ordering 	: false,
			info 		: false,
			data 		: <?= isset($transaksi->rincian_transaksi) ? json_encode($transaksi->rincian_transaksi) : '[]' ;?>,
			columns : [
				{ 
					data : 'id_akun',
					render : function ( columnData, type, rowData, meta ) {
						let selectedOption = (columnData != null && columnData != '' && columnData != 0) ? `<option selected="selected" value="`+ columnData +`">`+ rowData.akun +`</option>`: ``;
						return String(`<select class="form-control select2-biaya" value="`+ columnData +`" name="rincian[`+ meta.row +`][id_akun]" required="required">`+ selectedOption +`</select>`).trim();
					}
				},
				{ 
					data 		: 'total',
					className 	: 'text-right',
					width 		: '150px',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="nominal_kas_` + meta.row + `" class="form-control text-right" value="`+ columnData +`" name="rincian[`+ meta.row +`][nominal]" data-column="harga">
						`).trim();
					}
				}
			],
			initComplete : function(settings, json){
				let api = this.api();
				$(api.table().footer()).find('.btn-add-row').click(function(){
					api.row.add({ id : '', nominal : 0 }).draw();
				});
				$(api.table().footer()).find('.btn-delete-row').click(function(){
					api.row( ':last' ).remove().draw();
				});
			},
			createdRow : function( row, data, index ){
				new AutoNumeric.multiple($(row).find('[id^="nominal_kas_"]').get(),currenciesOptions);
				$(row).find('#nominal_kas_' + index).keyup(function(){
					$('#table-items').trigger('changeTotalItem');
				});
				new AutoNumeric.multiple($(row).find('[id^="id"]').get(),currenciesOptions);
				$(row).find('.select2-biaya').select2({
					ajax : {
						url 		: '<?= $module['url'];?>/api-data/select2-kas',
						dataType 	: 'json',
						type 		: 'POST',
						data 		: function (params) {
							var query = {
								search: params.term,
								type: 'public'
							}
						},
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
				})
			},
			// rowCallback : function( row, data, displayNum, displayIndex, index ){
			// 	new AutoNumeric.multiple($(row).find('[id^="nominal_masuk_"]').get(),currenciesOptions);
			// 	$(row).find('#nominal_masuk_' + index).keyup(function(){
			// 		$('#table-items').trigger('changeTotalItem');
			// 	});
			// },
			// drawCallback : function( settings ){
			// 	$('#table-items').trigger('changeTotalItem');
			// }
		});

		const tableBiaya = $('#table-biaya').DataTable({
			paging		: false,
			searching 	: false,
			ordering 	: false,
			info 		: false,
			data 		: <?= isset($transaksi->rincian_transaksi_biaya) ? json_encode($transaksi->rincian_transaksi_biaya) : '[]' ;?>,
			columns : [
				{ 
					data : 'id_akun',
					render : function ( columnData, type, rowData, meta ) {
						let selectedOption = (columnData != null && columnData != '' && columnData != 0) ? `<option selected="selected" value="`+ columnData +`">`+ rowData.akun +`</option>`: ``;
						return String(`<select class="form-control select2-biaya" value="`+ columnData +`" name="biaya[`+ meta.row +`][id_akun]" required="required">`+ selectedOption +`</select>`).trim();
					}
				},
				{ 
					data 		: 'total',
					className 	: 'text-right',
					width 		: '150px',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="nominal_masuk_` + meta.row + `" class="form-control text-right" value="`+ columnData +`" name="biaya[`+ meta.row +`][total]" data-column="harga">
						`).trim();
					}
				}
			],
			initComplete : function(settings, json){
				let api = this.api();
				$(api.table().footer()).find('.btn-add-row').click(function(){
					api.row.add({ id : '', nominal : 0 }).draw();
				});
				$(api.table().footer()).find('.btn-delete-row').click(function(){
					api.row( ':last' ).remove().draw();
				});
			},
			createdRow : function( row, data, index ){
				new AutoNumeric.multiple($(row).find('[id^="nominal_masuk_"]').get(),currenciesOptions);
				$(row).find('#nominal_masuk_' + index).keyup(function(){
					$('#table-biaya').trigger('changeTotalItem');
				});
				new AutoNumeric.multiple($(row).find('[id^="id"]').get(),currenciesOptions);
				$(row).find('.select2-biaya').select2({
				
				})
			},
			// rowCallback : function( row, data, displayNum, displayIndex, index ){
			// 	new AutoNumeric.multiple($(row).find('[id^="nominal_masuk_"]').get(),currenciesOptions);
			// 	$(row).find('#nominal_masuk_' + index).keyup(function(){
			// 		$('#table-items').trigger('changeTotalItem');
			// 	});
			// },
			// drawCallback : function( settings ){
			// 	$('#table-items').trigger('changeTotalItem');
			// }
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
