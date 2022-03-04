<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){

		var pathArray = window.location.pathname.split( '/' );
		var segment = pathArray[6];
		console.log(segment);
		$('#date_start').val(segment);
		$('#date_end').val(segment);
		var buttonOptions = {
			title 		: '<h1 class="text-center">Laporan Stok Barang Material<h1>',
			filename 	: 'Laporan Stok ' + '<?= date('l, d F Y')?>'
		};		
		const dt = $('#tunai').DataTable({
			"footerCallback": function ( row, rowData, start, end, display ) {
            var api = this.api(), rowData;
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };

           var tot = api
                .column( 7 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
           var pageTotal = api
                .column( 7, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            $( api.column( 7 ).footer() ).html(
              'Rp '+ tot
            );
			console.log(tot);
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
				url 		: "<?= $module['url'];?>/api-data/detailtunai", 
				type 		: "POST",
				data 		: function(d){
					return $.extend({},d,{
						filter : {
							date_start 	: $('[name="filter[date_start]"]').val(),
							date_end 	: $('[name="filter[date_end]"]').val(),
							id_cabang 	: $('[name="filter[id_cabang]"]').val(),
							id_pemasok 	: $('[name="filter[id_pemasok]"]').val()
						}
					});
				}
			},
			columns 		: [
				{
					data: 'id',
					render: function (data, type, row, meta) {
						return meta.row + meta.settings._iDisplayStart + 1;
					}
				},
				{ data : 'namap', className : 'text-center' },
                { data : 'tgl_buat', className : 'text-center' },
                { data : 'nomor', className : 'text-center' },
                { data : 'metode_beli', className : 'text-center' },
                { data : 'total_tagihan', className:'text-right', width:'180px', render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp. ' ) },
                { data : 'diskon', className:'text-right', width:'180px', render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp. ' ) },
                { data : 'total_pembayaran', className:'text-right', width:'180px', render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp. ' ) },
	
			]
		});

		

        const dg = $('#giro').DataTable({
			"footerCallback": function ( row, rowData, start, end, display ) {
            var api = this.api(), rowData;
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };

           var tot = api
                .column( 8 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
           var pageTotal = api
                .column( 8, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            $( api.column( 8 ).footer() ).html(
              'Rp '+ tot
            );
			console.log(tot);
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
				url 		: "<?= $module['url'];?>/api-data/detailgiro", 
				type 		: "POST",
				data 		: function(d){
					return $.extend({},d,{
						filter : {
							date_start 	: $('[name="filter[date_start]"]').val(),
							date_end 	: $('[name="filter[date_end]"]').val(),
							id_cabang 	: $('[name="filter[id_cabang]"]').val(),
							id_pemasok 	: $('[name="filter[id_pemasok]"]').val()
						}
					});
				}
			},
			columns 		: [
				{
					data: 'id',
					render: function (data, type, row, meta) {
						return meta.row + meta.settings._iDisplayStart + 1;
					}
				},
				{ data : 'nama_pemasok', className : 'text-center' },
                { data : 'tgl', className : 'text-center' },
                { data : 'nomor', className : 'text-center' },
                { data : 'tgl_giro', className : 'text-center' },
                { data : 'nogiro', className : 'text-center' },
                { data : 'total_tagihan', className:'text-right', width:'180px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' ) },
                { data : 'diskon', className:'text-right', width:'180px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' ) },
                { data : 'total_pembayaran', className:'text-right', width:'180px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' ) },
	
			]
		});

        const db = $('#bon').DataTable({
			"footerCallback": function ( row, rowData, start, end, display ) {
            var api = this.api(), rowData;
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };

           var tot = api
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
              'Rp '+ tot
            );
			console.log(tot);
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
				url 		: "<?= $module['url'];?>/api-data/detailbon", 
				type 		: "POST",
				data 		: function(d){
					return $.extend({},d,{
						filter : {
							date_start 	: $('[name="filter[date_start]"]').val(),
							date_end 	: $('[name="filter[date_end]"]').val(),
							id_cabang 	: $('[name="filter[id_cabang]"]').val(),
							id_pemasok 	: $('[name="filter[id_pemasok]"]').val()
						}
					});
				}
			},
			columns 		: [
				{
					data: 'id',
					render: function (data, type, row, meta) {
						return meta.row + meta.settings._iDisplayStart + 1;
					}
				},
				{ data : 'nama', className : 'text-center' },
                { data : 'tgl_buat', className : 'text-center' },
                { data : 'nomor', className : 'text-center' },
                { data : 'sisa_tagihan', className:'text-right', width:'180px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' ) },
			]
		});

		$('.flatpickr').flatpickr({
			dateFormat 	: "Y-m-d"
		});
		$('[name="filter[date_start]"], [name="filter[date_end]"]').on({
			change: function(e){
				dt.draw();
                dg.draw();
				db.draw();
			}
		});

		// $('.flatpickr').flatpickr({
		// 	dateFormat 	: "Y-m-d"
		// });
		// $('[name="filter[tgl]"]').on({
		// 	change: function(e){
		// 		dt.draw();
        //         dg.draw();
		// 		db.draw();
		// 	}
		// });
		$('.select2-toko').select2({
			placeholder: "",
			allowClear: true,
			ajax : {
				url 		: '<?= $module['url'];?>/api-data/select2-toko',
				type 		: 'POST',
				dataType 	: 'json',
				data 		: function (params) {
					var query = {
						search: params.term,
						type: 'public'
					}
					return $.extend({},params,{ 'id_cabang': $('#id_cabang').val() });
				}
			}
		}).on('change',function(e){
			dt.draw();
            dg.draw();
			db.draw();
			$('#toko').val($('#select2-toko').val());
		});

		$('#select2-toko').on('change',function(){
			$('#select2-barang').val(null).trigger('change');
		});

        $( "#excel" ).click(function() {
            var   date_start  = $('[name="filter[date_start]"]').val();
            var   date_end    = $('[name="filter[date_end]"]').val();
		    var   id_cabang  = $('[name="filter[id_cabang]"]').val();
         	window.open("<?php echo site_url('laporan/lap-pembelian/laporan-pembelian/excel/')?>"+date_start+'/'+date_end+'/'+id_cabang)
        });
		
	});
});
</script>