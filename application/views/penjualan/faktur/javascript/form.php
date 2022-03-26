<?php $penjualan = isset($data) ? $data : (object)[];?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bluebird/3.3.5/bluebird.min.js"></script>
<script type="text/javascript">
$(function(){
	'use strict'
	function appendFormdata(FormData, data, name) {
			name = name || '';
			if (typeof data === 'object') {
				$.each(data, function(index, value) {
					if (name == '') {
						appendFormdata(FormData, value, index);
					} else {
						appendFormdata(FormData, value, name + '[' + index + ']');
					}
				})
			} else {
				FormData.append(name, data);
			}
		}
	$(document).ready(function(){
		document.addEventListener("keypress", function(e) {
			if (e.target.tagName !== "INPUT") {
				var input = document.querySelector("#code_id_value");
				input.focus();
				input.value = e.key;
				e.preventDefault();
			}
		});


		const currenciesOptions = {
			unformatOnSubmit            : true,
			decimalCharacterAlternative: ".",
			decimalPlaces: 0,
			//minimumValue: "0",
		};

		const 	totalItems 			= new AutoNumeric('#totalItems',currenciesOptions),
				discountItems 		= new AutoNumeric('#discountItems',currenciesOptions),
				billItems 			= new AutoNumeric('#billItems',currenciesOptions),
				totalPayments 		= new AutoNumeric('#totalPayments',currenciesOptions),
				totalretur	 		= new AutoNumeric('#totalretur',currenciesOptions),
				harga	 		    = new AutoNumeric('#harga',currenciesOptions),
				total_harga	 		= new AutoNumeric('#total_harga',currenciesOptions),
				totretur	 		= new AutoNumeric('#totretur',currenciesOptions),
				totalcek	 		= new AutoNumeric('#totalcek',currenciesOptions),
				totalReceivable 	= new AutoNumeric('#totalReceivable',currenciesOptions);		

		var delay = (function(){
		var timer = 0;
		return function(callback, ms){
			clearTimeout(timer);
			timer = setTimeout(callback,ms);
		};
    	})();
		
		$( "#code_id_value" ).on('input', function(e) {
			e.stopPropagation();
			var $this = $(this);
			var value = $this.val();
		
			if (value.length > 0) {
				value = parseInt(value);
			}
			else {
				value = "";
			}
				if (value !== '') {
					delay(function(){
						$.ajax({
							type: 'POST',
							url : '<?= $module['url'];?>/api-data/get_kode_produk',
							dataType 	: ' json',
							data: 'kode_produk=' + value,
							success: function(response){   
							
									$("#nama_barang").val(response.nama);
									$("#kode_barang").val(response.kode_produk);
									$("#harga").val(response.harga_jual);
									$("#total_harga").val(response.harga_jual);
									$("#id_produk").val(response.id);
									$("#text_produk").val(response.text);
									$("#saldo_produk").val(response.saldo);
									if(response.id != null){
										$('#barcode_form').modal('show');
									}
									new AutoNumeric('#harga',currenciesOptions);
									new AutoNumeric('#total_harga',currenciesOptions);
							},
							error: function (xhr, ajaxOptions, thrownError){
							console.log(thrownError);
							}
						});
					}, 800);
				}
	
		});

		$("#qty, #harga").on("keyup change", function(e) {
			let harga_new = new AutoNumeric('#harga',currenciesOptions);
			let harga_total = harga_new.getNumber() * $("#qty").val();
			total_harga.set(harga_total);
		});

		$('#barcode_form').on('hidden.bs.modal', function () {
			$("#code_id_value").val('');
			$("#nama_barang").val('');
			$("#kode_barang").val('');
			$("#harga").val('');
		});

		$("#btnretur").on('click', function(){
			$("#table-retur").attr("hidden",false);
			$("#clsretur").attr("hidden",false);
			$("#btnretur").attr("hidden",true);
        });
		$("#clsretur").on('click', function(){
			$("#table-retur").attr("hidden",true);
			$("#clsretur").attr("hidden",true);
			$("#btnretur").attr("hidden",false);
        });

		$("#btnpelanggan").on('click', function(){
			$("#form-new").attr("hidden",false);
			$("#form-old").attr("hidden",true);
			$("#clspelanggan").attr("hidden",false);
			$("#btnpelanggan").attr("hidden",true);
			$("#nama-pelanggan").attr("disabled",true);
			$("#id_pelanggan").attr("disabled",true);
			$("#alamat-pelanggan").attr("disabled",true);
			$("#nama-pelanggan1").attr("disabled",false);
			$("#id_pelanggan1").attr("disabled",false);
			$("#alamat-pelanggan1").attr("disabled",false);
        });
		$("#clspelanggan").on('click', function(){
			$("#form-new").attr("hidden",true);
			$("#form-old").attr("hidden",false);
			$("#clspelanggan").attr("hidden",true);
			$("#btnpelanggan").attr("hidden",false);
			$("#nama-pelanggan").attr("disabled",false);
			$("#id_pelanggan").attr("disabled",false);
			$("#alamat-pelanggan").attr("disabled",false);
			$("#nama-pelanggan1").attr("disabled",true);
			$("#id_pelanggan1").attr("disabled",true);
			$("#alamat-pelanggan1").attr("disabled",true);
        });

		$.fn.select2.defaults.set("width", "100%");
		$('#tgl-nota').datepicker({format:'yyyy-mm-dd',todayHighlight:true});
		$('.nama-pelanggan').typeahead({
		  source: function(query, result)
		  {
		  	$.ajax({
				url:"<?= $module['url'];?>/api-data/pelanggan-autocomplete",
				method:"POST",
				data:{phrase:query},
				dataType:"json",
				success:function(data)
				{
					result($.map(data, function(item){
					return item.nama.toString();
					}));
				}
			})
		  }
		 });

    		 $('.nama-pelanggan').on('keyup',function(){

		    });


		$('#select-pelanggan').select2({
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
						$('#id_pelanggan').val(data.id_pelanggan);
						$('#alamat-pelanggan').val(data.alamat);
						// AutoNumeric.getAutoNumericElement('#item_harga_retur_' + index).set(data.harga_jual);
						// $('#item_harga_' + index).trigger('keyup');
						// AutoNumeric.getAutoNumericElement('#item_qty_retur_' + index).set(data.qty);
						// $('#item_qty_retur_' + index).trigger('keyup');
					}
				});

	


		$('#table-retur').on('changeTotalItem',	function(){
			let totalItemsAmount = 0;
			$(this).find('[id^="item_total_retur_"]').each(function(){
				totalItemsAmount += AutoNumeric.getNumber(this);
			});
			let billItemsAmount = totalItemsAmount;
			totalretur.set(totalItemsAmount);
			totretur.set(totalItemsAmount);
			$('#table-payments').trigger('changeTotalPayment');
		});

		$('#table-payments').on('changeTotalPayment', function(){
			let billItemsretur  = totalretur.getNumber();
			let billItemsAmount = billItems.getNumber();
			let billcek = totalcek.getNumber();
			let totalPaymentsAmount = 0;
			let totalCekAmount = 0;
			let cek = 0;
			$(this).find('[id^="total_"]').each(function(){
				totalPaymentsAmount += AutoNumeric.getNumber(this);
			});

			totalPayments.set(totalPaymentsAmount);

			$(this).find('[id^="chek_"]').each(function(){
				totalCekAmount += AutoNumeric.getNumber(this);
			});

			
			totalcek.set(totalCekAmount);
			totalReceivable.set((totalCekAmount + billItemsAmount) - billItemsretur - totalPaymentsAmount );

			if (totalReceivable.get() > 0) {
				$('#additem').show();
			}else{
				$('#additem').hide();
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
		var count = 0;
		const tableItems = $('#table-items').DataTable({
			paging		: false,
			searching 	: false,
			ordering 	: false,
			info 		: false,
			data 		: <?= isset($penjualan->rincian_penjualan) ? json_encode($penjualan->rincian_penjualan) : '[]' ;?>,
			columns : [
				{ 
					data : 'id_produk',
					name : 'produk_id',
					render : function ( columnData, type, rowData, meta ) {	

					let selectedOption;
					if(rowData.nama_produk != null || rowData.nama_produk != '' || rowData.nama_produk != 0){
						selectedOption = `<option selected="selected" value="`+ columnData +`">`+ $("#text_produk").val() + `</option>`;
					}else{
						selectedOption = (columnData != null && columnData != '' && columnData != 0) ? `<option selected="selected" value="`+ columnData +`">`+ rowData.kode_pemasok+ ` / ` + rowData.nama_pemasok +  ` / ` + rowData.nama_produk +`</option>`: ``;
					}
					
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
							<input type="hidden" name="rincian[`+ meta.row +`][id_cabang]" value="<?php echo $this->user->id_cabang;?>">
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
					data 		: 'saldo',
					className 	: 'text-right',
					width 		: '150px',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="item_stok_` + meta.row + `"class="form-control text-right" value="`+ columnData +`" name="rincian[`+ meta.row +`][qty_total]" data-column="stok" readonly="readonly">
						`).trim();
					}
				},
				{ 
					data 		: 'total',
					width 		: '150px',
					className 	: 'text-right',
					render 		: function ( columnData, type, rowData, meta ) {
						let total = parseInt(rowData.harga) * parseInt(rowData.qty);
						// alert(rowData.harga);
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
					api.row.add({ id_produk : '', harga : 0, qty : 1, item_stok : 0, total : 0 }).draw();
				});
				$('#add_item').click(function() {
					let harga_new = new AutoNumeric('#harga',currenciesOptions);
					let tot = harga_new.getNumber();
					api.row.add({ id_produk : $("#id_produk").val() , harga :  tot,qty : $("#qty").val(),  saldo : $("#saldo_produk").val(),  total : 0}).draw();
					$('#barcode_form').modal('hide');
					// $('#table-items').trigger('changeTotalItem');
                });
				$('#discountItems').keyup(function(){
					$('#table-items').trigger('changeTotalItem');
				});
			},
			createdRow : function( row, data, index ){
				new AutoNumeric.multiple($(row).find('[id^="item"]').get(),currenciesOptions);
				$(row).find("#item_qty_" + index + ', ' + '#item_stok_' + index ).keyup(function() {
					let stok 	= AutoNumeric.getNumber('#item_stok_' + index),
						qty 	= AutoNumeric.getNumber('#item_qty_' + index);
						if(qty > stok){
							AutoNumeric.getAutoNumericElement('#item_qty_' + index).set('');
					    	$('#item_qty_' + index).trigger('keyup');
							alert("Jumlah Qty Melebihi Stok Yang Ada");
						}
				});
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
							// return $.extend({},params,{ id_cabang: $('#id_cabang').val() });
							return $.extend({},params,{ id_cabang: $('#id_cabang').val() });
						},
						processResults: function (myData) {
							var data = $.map(myData.results, function (obj) {
								obj.text = obj.text || obj.kode +' / '+ obj.nama_produk;
								obj.id = obj.id_produk;
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
						AutoNumeric.getAutoNumericElement('#item_stok_' + index).set(data.saldo);
						$('#item_stok_' + index).trigger('keyup');
						AutoNumeric.getAutoNumericElement('#item_harga_' + index).set(data.harga_jual);
						$('#item_harga_' + index).trigger('keyup');
					}
				});
			},
			rowCallback : function( row, data, displayNum, displayIndex, index ){
				let api = this.api();
				$(row).find('#item_harga_' + index + ', ' + '#item_qty_' + index).keyup(function(){
					let harga 	= AutoNumeric.getNumber('#item_harga_' + index),
						qty 		= AutoNumeric.getNumber('#item_qty_' + index);
						// alert(harga);
					AutoNumeric.getAutoNumericElement('#item_total_' + index).set(harga * qty);
					$('#table-items').trigger('changeTotalItem');
				});

		


				$(row).find('#id_'+ index).click(function(){
					var currentPage = tableItems.page();
					count++;
					tableItems.row.add({ id_produk : '', harga : 0, qty : 1, item_stok : 0, total : 0 }).draw();
					//move added row to desired index (here the row we clicked on)
					var index = tableItems.row(this).index(),
						rowCount = tableItems.data().length-1,
						insertedRow = tableItems.row(rowCount).data(),
						tempRow;

					for (var i=rowCount;i>index;i--) {
						tempRow = tableItems.row(i-1).data();
						tableItems.row(i).data(tempRow);
						tableItems.row(i-1).data(insertedRow);
					}     
					//refresh the current page
					tableItems.page(currentPage).draw(false);
				});
				
				// $(row).find('#id_'+ index).click(function(){
				// 	api.row($(this).closest("tr").get(0)).add({ id_produk : '', harga : 0, qty : 1, item_stok : 0, total : 0 }).draw();
				// 	// api.row($(this).closest("tr").get(0)).add({ nama : '', harga : 0, qty_input : 0, qty_retur: 0, total_retur : 0 }).draw();
				// });
				// $(row).find('#id_'+ index).click(function(){
				// 	api.row($(this).closest("tr").get(0)).remove().draw();
				// });
			},
			drawCallback : function( settings ){
				$('#table-items').trigger('changeTotalItem');
			}
		});
		const tableRetur = $('#table-retur').DataTable({
			paging		: false,
			searching 	: false,
			ordering 	: false,
			info 		: false,
			data 		: <?= isset($penjualan->retur) ? json_encode($penjualan->retur) : '[]' ;?>,
			columns : [
				{ 
					data : 'id_produk',
					render : function ( columnData, type, rowData, meta ) {
						let selectedOption = (columnData != null && columnData != '' && columnData != 0) ? `<option selected="selected" value="`+ columnData +`">`+ rowData.nama +`</option>`: ``;
						return String(`<select class="form-control select2-produk-retur" value="`+ columnData +`" name="retur[`+ meta.row +`][id_produk]" required="required">`+ selectedOption +`</select>`).trim();
					}
				},
				{ 
					data 		: 'harga_jual',
					className 	: 'text-right',
					width 		: '150px',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="item_harga_retur_` + meta.row + `" class="form-control text-right" value="`+ columnData +`" name="retur[`+ meta.row +`][harga]" data-column="harga">
						`).trim();
					}
				},
				{ 
					data 		: 'qty_input',
					className 	: 'text-right',
					width 		: '150px',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="item_qty_input_` + meta.row + `" class="form-control text-right" value="`+ columnData +`" name="retur[`+ meta.row +`][qty]" data-column="qty" required> 
						`).trim();
					}
				},
				{ 
					data 		: 'qty_retur',
					className 	: 'text-right',
					width 		: '150px',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="item_qty_retur_` + meta.row + `" class="form-control text-right" value="`+ columnData +`" name="retur[`+ meta.row +`][qty_retur]" data-column="qty total" readonly="readonly" >
						`).trim();
					}
				},
				{ 
					data 		: 'total_retur',
					width 		: '150px',
					className 	: 'text-right',
					render 		: function ( columnData, type, rowData, meta ) {
						let total = parseInt(rowData.harga_jual) * parseInt(rowData.qty_input);
						return String(`
							<input id="item_total_retur_` + meta.row + `" class="form-control text-right" value="`+ total +`"" readonly="readonly" data-column="total">
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
					api.row.add({ nama : '', harga : 0, qty_input : 0, qty_retur: 0, total_retur : 0 }).draw();
				});
			},
			createdRow : function( row, data, index ){
				new AutoNumeric.multiple($(row).find('[id^="item"]').get(),currenciesOptions);
				$(row).find('.select2-produk-retur').select2({
					ajax : {
						url 		: '<?= $module['url'];?>/api-data/select2-produk-retur',
						dataType 	: 'json',
						type 		: 'POST',
						data 		: function (params) {
							var query = {
								search: params.term,
								type: 'public'
							}
							return $.extend({},params,{ id_pelanggan: $('#id_pelanggan').val(), id_cabang: $('#id_cabang').val()});
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
						AutoNumeric.getAutoNumericElement('#item_harga_retur_' + index).set(data.harga_jual);
						$('#item_harga_' + index).trigger('keyup');
						AutoNumeric.getAutoNumericElement('#item_qty_retur_' + index).set(data.qty);
						$('#item_qty_retur_' + index).trigger('keyup');
					}
				});
			},
			rowCallback : function( row, data, displayNum, displayIndex, index ){
				let api = this.api();
				$(row).find('#item_harga_retur_' + index + ', ' + '#item_qty_input_' + index).on('keyup change', function(){
					let harga 	= AutoNumeric.getNumber('#item_harga_retur_' + index),
						qty 		= AutoNumeric.getNumber('#item_qty_input_' + index),
						stok 		= AutoNumeric.getNumber('#item_qty_retur_' + index);
						if(qty > stok){
							AutoNumeric.getAutoNumericElement('#item_qty_input_' + index).set('');
							AutoNumeric.getAutoNumericElement('#item_total_retur_' + index).set('');
					    	$('#item_qty_input_' + index).trigger('keyup');
							alert("Jumlah Qty Melebihi Stok Yang Ada");
						}else{
							AutoNumeric.getAutoNumericElement('#item_total_retur_' + index).set(harga * qty);
							$('#table-retur').trigger('changeTotalItem');
						}
				
				});
				$(row).find('#id_'+ index).click(function(){
					api.row($(this).closest("tr").get(0)).remove().draw();
				});
			},
			drawCallback : function( settings ){
				$('#table-retur').trigger('changeTotalItem');
			}
		});
		const tablePayments 	= $('#table-payments').DataTable({
			paging		: false,
			searching 	: false,
			ordering 	: false,
			info 		: false,
			data 		: <?= isset($penjualan->rincian_pelunasan) ? json_encode($penjualan->rincian_pelunasan) : '[]' ;?>,
			columns : [
				{ 
					data 	: 'metode',
					width 	: '120px',
					render 	: function ( columnData, type, rowData, meta ) {
						console.log(meta.row);
						return String(`
							<select class="form-control select2-metode" data-name="metode" required="required" name="pelunasan[`+ meta.row +`][metode]">
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
							<select class="form-control select2-akun" data-column="id_akun" required="required" name="pelunasan[`+ meta.row +`][id_akun]">`+ selectedOption +`</select>
						`).trim();
					}
				},
				{ 
					data 		: 'nominal',
					width 		: '150px',
					className 	: 'text-right',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="nominal_`+ meta.row +`" class="form-control text-right" data-column="nominal" name="pelunasan[`+ meta.row +`][nominal]" value="`+ columnData +`" required>
						`).trim();
					}
				},
				{ 
					data 		: 'potongan',
					width 		: '150px',
					className 	: 'text-right',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="potongan_`+ meta.row +`" class="form-control text-right" data-column="potongan" name="pelunasan[`+ meta.row +`][potongan]" value="`+ columnData +`" required>
						`).trim();
					}
				},
				{ 
					data 		: 'chek',
					width 		: '150px',
					className 	: 'text-right',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
						<input id="chek_`+ meta.row +`" class="form-control text-right" data-column="chek" name="pelunasan[`+ meta.row +`][chek]" value="`+ columnData +`" required>
						`).trim();
					}
				},
				{ 
					data 		: 'total',
					width 		: '150px',
					className 	: 'text-right',
					render 		: function ( columnData, type, rowData, meta ) {
						return String(`
							<input id="total_`+ meta.row +`" class="form-control text-right" data-column="total" name="pelunasan[`+ meta.row +`][total]" value="`+ columnData +`" readonly="readonly">
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
					api.row.add({ metode : 'cash', akun : 0, nomor_giro : 1, tgl_giro : 0, nominal : 0, potongan : 0, chek : 0, total : 0 }).draw();
				});
				// $(api.table().footer()).find('.btn-delete-row').click(function(){
				// 	api.row( ':last' ).remove().draw();
				// });
			},
			createdRow : function( row, data, index ){
				new AutoNumeric($(row).find('[id^="nominal"]').get(0),currenciesOptions);
				new AutoNumeric($(row).find('[id^="potongan"]').get(0),currenciesOptions);
				new AutoNumeric($(row).find('[id^="total_"]').get(0),currenciesOptions);
				new AutoNumeric($(row).find('[id^="chek_"]').get(0),currenciesOptions);
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
			},
			rowCallback : function( row, data, displayNum, displayIndex, index ){
				let api = this.api();
				
				$(row).find('.select2-metode').on({
					change : function(){
						let metode = $(this).val();
						$(row).find('.select2-akun').val(null).trigger('change');
					}
				});
				
				$(row).find('#nominal_' + index  + ', #potongan_' + index + ', #chek_' + index).keyup(function(){
					let nominal 	= AutoNumeric.getNumber('#nominal_' + index),
						potongan 	= AutoNumeric.getNumber('#potongan_' + index),
						check   	= AutoNumeric.getNumber('#chek_' + index);
					AutoNumeric.getAutoNumericElement('#total_' + index).set((nominal + potongan) + check);
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
							if($("input[name='print']:checked").val() == 1){
							  $.ajax({
								cache 		: false,
								processData : false,
								contentType : false,
								type 		: 'POST',
								url 		: '<?= $module['url'];?>/bill-print',
								data 		: new FormData(form),
								beforeSend:function() {
									btnSubmit.addClass("disabled").html("<i class='fas fa-spinner fa-pulse fa-fw'></i> Loading ... ");
								},
								error 		: function(){
									btnSubmit.removeClass("disabled").html(btnSubmitHtml);
									$.notify({ icon: 'fa fa-exclamation mr-1', message: 'Server\'s response not found'}, {type: 'danger'});
								},
								success 	: function(response) {
									btnSubmit.removeClass("disabled").html(btnSubmitHtml);
									sendPrinterData(response);
								}
							  });
							}
					
							$.notify( { icon: 'fa fa-check mr-1', message: response.message}, {type: 'success'});
							setTimeout(function(){
								if(response.redirect == "reload"){
									location.reload();
								} else if (response.redirect == "history.back()") {
									window.history.back();
								} else if(response.redirect != "") {
									location.href = response.redirect;
								}
								// sendPrinterData(response);
							},timeout);
						} else {
							$.notify( {icon: 'fa fa-exclamation mr-1', message: response.message},{type: 'danger'});
						}
					}
				});
			}
		});

		function jspmWSStatus() {
			if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Open)
				return true;
			else if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Closed) {
				alert('JSPrintManager (JSPM) is not installed or not running! Download JSPM Client App from https://neodynamic.com/downloads/jspm');
				return false;
			}
			else if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Blocked) {
				alert('JSPM has blocked this website!');
				return false;
			}
        }

		//    $('button#btn-bill-print').click(function(e){
		// 		e.stopPropagation();
		// 		e.preventDefault();
		// 		let form 			= document.getElementById('form'),
		// 		btnSubmit 		= $(this),
		// 		btnSubmitHtml 	= btnSubmit.html(),
		// 		formData 		= new FormData(form),
		// 		items_data 		= { produk_items : tableItems.data() };
		// 		// console.log(formData);
			
		// 	});

					function sendPrinterData(response){
				       JSPM.JSPrintManager.auto_reconnect = true;
						JSPM.JSPrintManager.start();
						JSPM.JSPrintManager.WS.onStatusChanged = function() {
						if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Open) {
							var cpj = new JSPM.ClientPrintJob();
							var esc = '\x1B'; //ESC byte in hex notation
							var newLine = '\x0A'; //LF byte in hex notation
							var cmds = esc + "@"; //Initializes the printer (ESC @) //Emphasized + Double-height + Double-width mode selected (ESC ! (8 + 16 + 32)) 56 dec => 38 hex
							cmds += esc + '!' + '\x00';
							cmds += response + newLine + newLine + "\x1B@\x1DV1";
							cpj.clientPrinter = new JSPM.InstalledPrinter("EPSON TM-T82X Receipt");
							cpj.printerCommands = cmds;
							cpj.sendToClient();
							console.log(cmds);
						}
					  }
			        }
				// sendPrinterData();

		// 	function print(o) {
        // if (jspmWSStatus()) {
        //     //Create a ClientPrintJob
        //     var cpj = new JSPM.ClientPrintJob();
        //     //Set Printer type (Refer to the help, there many of them!)
        //     if ($('#useDefaultPrinter').prop('checked')) {
        //         cpj.clientPrinter = new JSPM.DefaultPrinter();
        //     } else {
        //         cpj.clientPrinter = new JSPM.InstalledPrinter($('#installedPrinterName').val());
        //     }
        //     //Set content to print...
        //     //Create ESP/POS commands for sample label
        //     var esc = '\x1B'; //ESC byte in hex notation
        //     var newLine = '\x0A'; //LF byte in hex notation
        
        //     var cmds = esc + "@"; //Initializes the printer (ESC @)
        //     cmds += esc + '!' + '\x00'; //Emphasized + Double-height + Double-width mode selected (ESC ! (8 + 16 + 32)) 56 dec => 38 hex
		// 	cmds += res;
        //     cpj.printerCommands = cmds;
        //     //Send print job to printer!
        //     cpj.sendToClient();
        // }
    // }

		
	});
});
</script>
