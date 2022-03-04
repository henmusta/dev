<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){
		var buttonOptions = {
			title 		: '<h1 class="text-center">Laporan Stok Barang Material<h1>',
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
			order			: [[ 1, 'asc' ]],
			processing 		: true,
			serverSide 		: true,
			ajax:{
				url 		: "<?= $module['url'];?>/api-data/datatable", 
				type 		: "POST",
				data 		: function(d){
					return $.extend({},d,{
						filter : {
							date_start 	: $('[name="filter[date_start]"]').val(),
							date_end	: $('[name="filter[date_end]"]').val(),
							id_cabang		: $('[name="filter[id_cabang]"]').val()
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
				{ data : 'qty_retur', className : 'text-center' },
				{ data : 'namap' },
				{ data : 'harga', className:'text-right', width:'180px', render: $.fn.dataTable.render.number( ',', '.', 0, '' ) },
				{ data : 'total', className:'text-right', width:'180px', render: $.fn.dataTable.render.number( ',', '.', 0, '' ) }
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
         window.open("<?php echo site_url('laporan/lap-pembelian/laporan-retur/excel/')?>"+date_start+'/'+date_end+'/'+id_cabang)
        });
	});
});
</script>