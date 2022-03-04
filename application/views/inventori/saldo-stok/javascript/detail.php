<script type="text/javascript">
	
$(function(){
	'use strict'
	$(document).ready(function(){	

		var pathArray = window.location.pathname.split( '/' );
		var segment = pathArray[5];

		$( "#excel" ).click(function() {
		    var   id_produk  = $('[name="filter[id_produk]"]').val();
         	window.open("<?php echo site_url('inventori/saldo-stok/excel/')?>"+id_produk)
        });

		var tot;
		$('#id_produk').val(segment);
		const dbeli = $('#pembelian').DataTable({
			"footerCallback": function ( row, rowData, start, end, display ) {
            var api = this.api(), rowData;
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };

				tot = api
						.column( 4 )
						.data()
						.reduce( function (a, b) {
							return intVal(a) + intVal(b);
						}, 0 );
		
				var pageTotal = api
						.column( 4, { page: 'current'} )
						.data()
						.reduce( function (a, b) {
							return intVal(a) + intVal(b);
						}, 0 );

					$( api.column( 4 ).footer() ).html(
					' '+ tot
					);
					$('#qtypembelian').val(tot);
					$("#qtypembelian").trigger("change");		
				},
			lengthMenu		: [[5, 10, 25, 50, 100, -1], ["5", "10", "25", "50", "100", "All"]],
			displayLength 	: 10,
            ordering        : false,
            info            : false,
            paging          : false,
			processing 		: true,
			serverSide 		: true,
            searching       : false,
			ajax:{
				url 		: "<?= $module['url'];?>/api-data/pembelian", 
				type 		: "POST",
				data 		: function(d){
					return $.extend({},d,{
						filter : {
							id_produk 	: $('[name="filter[id_produk]"]').val()
						}
					});
				}
			},
			columns 		: [
				{
					data: 'id',
                    className : 'text-center',
					render: function (data, type, row, meta) {
						return meta.row + meta.settings._iDisplayStart + 1;
					}
				},
                { data : 'tgl', className : 'text-center' },
                { data : 'nomor', className : 'text-center' },
                { data : 'pemasok', className : 'text-center' },
                { data : 'qty', className : 'text-center' }
	
			]
		});


		var tot1
        const djual = $('#penjualan').DataTable({
					"footerCallback": function ( row, rowData, start, end, display ) {
					var api = this.api(), rowData;
					var intVal = function ( i ) {
						return typeof i === 'string' ?
							i.replace(/[\$,]/g, '')*1 :
							typeof i === 'number' ?
								i : 0;
					};

				tot1 = api
						.column( 4 )
						.data()
						.reduce( function (a, b) {
							return intVal(a) + intVal(b);
						}, 0 );
		
				var pageTotal = api
						.column( 4, { page: 'current'} )
						.data()
						.reduce( function (a, b) {
							return intVal(a) + intVal(b);
						}, 0 );

					$( api.column( 4 ).footer() ).html(
					' '+ tot1
					);
					$('#qtypenjualan').val(tot1);
					$("#qtypenjualan").trigger("change");		
				},
			lengthMenu		: [[5, 10, 25, 50, 100, -1], ["5", "10", "25", "50", "100", "All"]],
			displayLength 	: 10,
            ordering        : false,
            info            : false,
            paging          : false,
			processing 		: true,
			serverSide 		: true,
            searching       : false,
			ajax:{
				url 		: "<?= $module['url'];?>/api-data/penjualan", 
				type 		: "POST",
				data 		: function(d){
					return $.extend({},d,{
						filter : {
							id_produk 	: $('[name="filter[id_produk]"]').val()
						}
					});
				}
			},
			columns 		: [
				{
					data: 'id',
                    className : 'text-center',
					render: function (data, type, row, meta) {
						return meta.row + meta.settings._iDisplayStart + 1;
					}
				},
                { data : 'tgl', className : 'text-center' },
                { data : 'nomor', className : 'text-center' },
                { data : 'nama_pelanggan', className : 'text-center' },
                { data : 'qty', className : 'text-center' }
			]
		});


        var tot2
        const dretur = $('#retur').DataTable({
					"footerCallback": function ( row, rowData, start, end, display ) {
					var api = this.api(), rowData;
					var intVal = function ( i ) {
						return typeof i === 'string' ?
							i.replace(/[\$,]/g, '')*1 :
							typeof i === 'number' ?
								i : 0;
					};

				tot2 = api
						.column( 5 )
						.data()
						.reduce( function (a, b) {
							return intVal(a) + intVal(b);
						}, 0 );
		
				var pageTotal = api
						.column( 5, { page: 'current'} )
						.data()
						.reduce( function (a, b) {
							return intVal(a) + intVal(b);
						}, 0 );

					$( api.column( 5 ).footer() ).html(
					' '+ tot2
					);
					$('#qtyretur').val(tot2);
					$("#qtyretur").trigger("change");		
				},
			lengthMenu		: [[5, 10, 25, 50, 100, -1], ["5", "10", "25", "50", "100", "All"]],
			displayLength 	: 10,
            ordering        : false,
            info            : false,
            paging          : false,
			processing 		: true,
			serverSide 		: true,
            searching       : false,
			ajax:{
				url 		: "<?= $module['url'];?>/api-data/retur", 
				type 		: "POST",
				data 		: function(d){
					return $.extend({},d,{
						filter : {
							id_produk 	: $('[name="filter[id_produk]"]').val()
						}
					});
				}
			},
			columns 		: [
				{
					data: 'id',
                    className : 'text-center',
					render: function (data, type, row, meta) {
						return meta.row + meta.settings._iDisplayStart + 1;
					}
				},
                { data : 'tgl', className : 'text-center' },
                { data : 'nomor', className : 'text-center' },
                { data : 'pemasok', className : 'text-center' },
                { data : 'jenis', className : 'text-center' },
                { data : 'qty', className : 'text-center' }
			]
			
		});

		
		var tot3
		const dopname = $('#opname').DataTable({
					"footerCallback": function ( row, rowData, start, end, display ) {
					var api = this.api(), rowData;
					var intVal = function ( i ) {
						return typeof i === 'string' ?
							i.replace(/[\$,]/g, '')*1 :
							typeof i === 'number' ?
								i : 0;
					};

				tot3 = api
						.column( 4 )
						.data()
						.reduce( function (a, b) {
							return intVal(a) + intVal(b);
						}, 0 );
		
				var pageTotal = api
						.column( 4, { page: 'current'} )
						.data()
						.reduce( function (a, b) {
							return intVal(a) + intVal(b);
						}, 0 );

					$( api.column( 4 ).footer() ).html(
					' '+ tot3
					);
					$('#qtyopname').val(tot3);
					$("#qtyopname").trigger("change");		
				},
			lengthMenu		: [[5, 10, 25, 50, 100, -1], ["5", "10", "25", "50", "100", "All"]],
			displayLength 	: 10,
            ordering        : false,
            info            : false,
            paging          : false,
			processing 		: true,
			serverSide 		: true,
            searching       : false,
			ajax:{
				url 		: "<?= $module['url'];?>/api-data/opname", 
				type 		: "POST",
				data 		: function(d){
					return $.extend({},d,{
						filter : {
							id_produk 	: $('[name="filter[id_produk]"]').val()
						}
					});
				}
			},
			columns 		: [
				{
					data: 'id',
                    className : 'text-center',
					render: function (data, type, row, meta) {
						return meta.row + meta.settings._iDisplayStart + 1;
					}
				},
                { data : 'tgl', className : 'text-center' },
                { data : 'nomor', className : 'text-center' },
                { data : 'pemasok', className : 'text-center' },
                { data : 'qty', className : 'text-center' }
			]
		});

		$('.select2-barang').select2({
			placeholder: "Barang",
			allowClear: true,
			ajax : {
				url 		: '<?= $module['url'];?>/api-data/select2-barang',
				type 		: 'POST',
				dataType 	: 'json',
				data 		: function (params) {
					var query = {
						search: params.term,
						type: 'public'
					}
					return $.extend({},params,{  'id_cabang': $('#id_cabang').val()});
				}
			}
		}).on('change',function(e){
			dt.draw();
            dg.draw();
			db.draw();
		});


	
	
	$( "#qtypembelian" ).change(function() {
		$( "#qtypenjualan" ).change(function() {
			$( "#qtyretur" ).change(function() {
				$( "#qtyopname" ).change(function() {
				var pembelian =	$('#qtypembelian').val();
				var retur =	$('#qtyretur').val();
				var penjualan =	$('#qtypenjualan').val();
				var opname =	$('#qtyopname').val();
				var totall = parseInt(pembelian) - parseInt(penjualan) + parseInt(retur) + parseInt(opname);
				$('#qtytotal').val(totall);
				console.log($('#qtyopname').val())
			});
		});
	});
});



	});

});

</script>