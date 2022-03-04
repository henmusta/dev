<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){        
		var pathArray = window.location.pathname.split( '/' );
		var segment = pathArray[5];
		console.log(segment);
        $('#date_start').val(segment);
        $('#date_end').val(segment);

		var buttonOptions = {
			title 		: '<h1 class="text-center">Laporan penjualan<h1>',
			filename 	: 'Laporan Stok ' + '<?= date('l, d F Y')?>'
		};
		const dt = $('#dt').DataTable({

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

			var tot1 = api
                .column( 9 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            var tot2 = api
                .column( 10 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            var tot3 = api
                .column( 11 )
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

			
			var pageTotal1 = api
                .column( 9, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            var pageTotal2 = api
                .column( 10, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            var pageTotal3 = api
                .column( 11, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            $( api.column( 8 ).footer() ).html(
              'Rp '+ tot
            );

			$( api.column( 9 ).footer() ).html(
              'Rp '+ tot1
            );

            $( api.column( 10 ).footer() ).html(
              'Rp '+ tot2
            );

            $( api.column( 11 ).footer() ).html(
              'Rp '+ tot3
            );

			console.log(tot1);
        },
			lengthMenu		: [[5, 10, 25, 50, 100, -1], ["5", "10", "25", "50", "100", "All"]],
			displayLength 	: 10,
			order			: [[ 1, 'asc' ]],
			processing 		: true,
			serverSide 		: true,
			ordering        : false,
			ajax:{
				url 		: "<?= $module['url'];?>/api-data/detailjual", 
				type 		: "POST",
				data 		: function(d){
					return $.extend({},d,{
						filter : {
							date_start     	: $('[name="filter[date_start]"]').val(),
                            date_end     	: $('[name="filter[date_end]"]').val(),
							id_cabang   	: $('[name="filter[id_cabang]"]').val()
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
				{ data : 'nama_p', width: '200px',  className : 'text-center'},
				{ data : 'tgl_nota', className : 'text-center' },
                { data : 'nomor', className : 'text-center' },
                { data : 'jumlah', className:'text-right', className:'text-right', width:'180px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
                { data : 'diskon',className:'text-right', className:'text-right', width:'180px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
				{ data : 'chek',className:'text-right', className:'text-right', width:'180px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
				{ data : 'notaretur',className:'text-right', className:'text-right', width:'180px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
                { data : 'laba', className : 'text-center', className:'text-right', width:'180px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
                 { data : 'laba_retur', className : 'text-center', className:'text-right', width:'180px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
                 { data : 'total', className:'text-right', className:'text-right', width:'180px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
                  { data : 'laba_akhir', className:'text-right', className:'text-right', width:'180px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
                // { data : 'kode_laba', className : 'text-center' },
				
			]
		});
		$('.flatpickr').flatpickr({
			dateFormat 	: "Y-m-d"
		});
		$('[name="filter[date_start]"], [name="filter[date_end]"]').on({
			change: function(e){
				dt.draw();
			}
		});


        $( "#excel" ).click(function() {
            var   date_start  = $('[name="filter[date_start]"]').val();
            var   date_end    = $('[name="filter[date_end]"]').val();
		    var   id_cabang  = $('[name="filter[id_cabang]"]').val();
         window.open("<?php echo site_url('laporan/laporan_penjualan/excel/')?>"+date_start+'/'+date_end+'/'+id_cabang)
        });
	});
});
</script>