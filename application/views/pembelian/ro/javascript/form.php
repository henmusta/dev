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


		$('.datepicker').datepicker({format:'yyyy-mm-dd',todayHighlight:true});
		$('#tgl-nota').datepicker({format:'yyyy-mm-dd',todayHighlight:true});


		const currenciesOptions = {
			unformatOnSubmit            : true,
			decimalCharacterAlternative: ".",
			decimalPlaces: 0,
			//minimumValue: "0",
		};

		// const   discountItems 	= new AutoNumeric('#discountItems',currenciesOptions),
		// 		billItems 		= new AutoNumeric('#billItems',currenciesOptions);

		// ('#billItems')

		// $('#table-payments').on('changeTotalPayment', function(){

		// 	let billItemsAmount = billItems.getNumber();
		// 	let totalPaymentsAmount = 0;
		// 	$(this).find('[id^="total_"]').each(function(){
		// 		totalPaymentsAmount += AutoNumeric.getNumber(this);
		// 	});
		// 	totalPayments.set(totalPaymentsAmount);
		// 	totalDebt.set(billItemsAmount - totalPaymentsAmount);
		// 		// console.log(totalDebt);
		// 	var total = billItemsAmount - totalPaymentsAmount;
		// 	if( total <= 0 ){
		// 		$('#idbeli').prop('disabled', true);
		// 	}else{
		// 		$('#idbeli').prop('disabled', false);
		// 	}
			
		// });

		// $('#table-items').on('changeTotalItem',	function(){
		// 	let totalItemsAmount = 0;
		// 	$(this).find('[id^="item_total_"]').each(function(){
		// 		totalItemsAmount += AutoNumeric.getNumber(this);
		// 	});
		// 	let billItemsAmount = totalItemsAmount;
		// 	totalItems.set(totalItemsAmount);
		// 	// let discountItemsAmount = AutoNumeric.getNumber($('#discountItems').get(0));
		// 	billItemsAmount -= discountItemsAmount;
		// 	billItems.set(billItemsAmount);
		// 	// $('#table-payments').trigger('changeTotalPayment');
		// });
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
							<input id="nama-produk-`+ meta.row +`" class="form-control" value="`+ columnData +`" data-name="nama_barang" name="rincian[`+ meta.row +`][nama]">
							<input hidden id="pd_id-`+ meta.row +`" class="form-control" value="`+ rowData.id_produk +`" data-name="pd_id" name="rincian[`+ meta.row +`][pd_id]">
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
							<input id="item_harga_` + meta.row + `" class="form-control text-right" value="`+ columnData +`" name="rincian[`+ meta.row +`][harga]" data-column="harga" readonly>
						`).trim();
					}
				},
				{ 
					data 		: 'qty',
					className 	: 'text-right',
					width 		: '250px',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="qty_` + meta.row + `" class="form-control text-right" value="`+ columnData +`" name="rincian[`+ meta.row +`][qty]" readonly="readonly" data-column="qty">
								<input type="hidden" name="rincian[`+ meta.row +`][id_cabang]" value="<?php echo $this->user->id_cabang;?>">
						`).trim();
					}
				},
				{ 
					data 		: 'qty_diterima',
					width 		: '250px',
					className 	: 'text-right',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="qtyterima_` + meta.row + `" class="form-control text-right" value="`+ columnData  +`" name="rincian[`+ meta.row +`][qty_diterima]" data-column="total">
						`).trim();
					}
				},
				{ 
					data 		: 'sisa_qty',
					width 		: '250px',
					className 	: 'text-right',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="sisaqty_` + meta.row + `" class="form-control text-right" value="`+ columnData  +`" name="rincian[`+ meta.row +`][qty_terima]" readonly="readonly" data-column="total">
						`).trim();
					}
				},
				// { 
				// 	data 		: 'id_produk',
				// 	width 		: '50px',
				// 	className 	: 'text-center',
				// 	render 		: function ( columnData, type, rowData, meta ) {
				// 		return String(`
				// 			<button type="button" id="id_` + meta.row + `" class="btn btn-sm btn-outline-secondary btn-delete-row"><i class="fa fa-minus"></i></button>
				// 		`).trim();
				// 	}
				// }
			],
			initComplete : function(settings, json){
				let api = this.api();
				// $(api.table().footer()).find('.btn-add-row').click(function(){
				// 	api.row.add({ nama : '', harga : 0, qty : 1, total : 0 }).draw();
				// });
				// $(api.table().footer()).find('.btn-delete-row').click(function(){
				// 	api.row( ':last' ).remove().draw();
				// });

				// $('#discountItems').keyup(function(){
				// 	$('#table-items').trigger('changeTotalItem');
				// });
			},
			createdRow : function( row, data, index ){
				new AutoNumeric.multiple($(row).find('[id^="item"]').get(),currenciesOptions);
				$(row).find('#qtyterima_' + index + ', ' + '#sisaqty_' + index).keyup(function(){
					let qty 	= $(row).find('[id^="qty_"]').val(),
						qty_diterima 	= $(row).find('[id^="qtyterima_"]').val();
						$(row).find('#sisaqty_' + index).val(qty - qty_diterima);
				});
				// setTimeout(function(){	
				// 	$("#nama-produk-" + index).easyAutocomplete({
				// 		url 		: function(q){
				// 			return '<?= $module['url'];?>/api-data/autocomplete-produk?q=' + q + '&format=json' + '&kode-pemasok=' + $('#kode-pemasok').val();
				// 		},
				// 		getValue 	: 'nama',
				// 		list 		: {
				// 			match : { enabled:true },
				// 			onSelectItemEvent: function() {
            	// 			var value = $("#nama-produk-" + index).getSelectedItemData().harga_beli;
        		// 			AutoNumeric.getAutoNumericElement('#item_harga_' + index).set(value);
				// 				$('#item_harga_' + index).trigger('keyup');
   				// 			   }
				// 		}
				// 	});
				// },100)
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
