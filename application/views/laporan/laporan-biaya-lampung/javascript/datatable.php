<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){

		$(".datepicker").datepicker( {
    format: "yyyy-mm",
    startView: "months", 
    minViewMode: "months"
});

		var buttonOptions = {
			title 		: '<h1 class="text-center">Laporan Biaya Lampung<h1>',
			filename 	: 'Laporan Biaya Lampung ' + '<?= date('l, d F Y')?>'
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
                .column( 10 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
           var pageTotal = api
                .column( 10, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            $( api.column( 10 ).footer() ).html(
              'Rp '+ tot
            );

			console.log(tot);
        },
			lengthMenu		: [[5, 10, 25, 50, 100, -1], ["5", "10", "25", "50", "100", "All"]],
			displayLength 	: 10,
			ordering        : false,
			processing 		: true,
			serverSide 		: true,
			ajax:{
				url 		: "<?= $module['url'];?>/api-data/datatable", 
				type 		: "POST",
				data 		: function(d){
					return $.extend({},d,{
						filter : {
							start_date  : $('[name="filter[start_date]"]').val(),
							end_date    : $('[name="filter[end_date]"]').val(),
							id_cabang 	: $('[name="filter[id_cabang]"]').val()
						}
					});
				}
			},
			columns 		: [
				{ 
					data : 'id', 
					className : 'text-center', 
					width: '40px', 
					orderable: false,
					render : function ( columnData, type, rowData, meta ) {
						return meta.row + meta.settings._iDisplayStart + 1;
					}
				},
				{ data : 'tgl', className : 'text-center' },
				{ data : 'gaji',className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
				{ data : 'bulanan',className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
				{ data : 'listrik',className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
				{ data : 'angkut',className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
				{ data : 'ekspedisi',className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
				{ data : 'peralatan',className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
				{ data : 'konsumsi',className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
				{ data : 'dll',className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
				{ data : 'total',className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
				// { data : 'konsumsi', width: '200px',  className : 'text-center'},
			]
			
		});

		$('.flatpickr').flatpickr({
			dateFormat 	: "Y-m-d"
		});
		$('[name="filter[start_date]"], [name="filter[end_date]"]').on({
			change: function(e){
				dt.draw();
			}
		});

		$( "#excel" ).click(function() {
            var   start_date  = $('[name="filter[start_date]"]').val();
			var   end_date  = $('[name="filter[end_date]"]').val();
		    var   id_cabang  = $('[name="filter[id_cabang]"]').val();
            window.open("<?php echo site_url('laporan/laporan_biaya_lampung/excel/')?>"+start_date+'/'+end_date+'/'+id_cabang)
        });
	});
});
// $(document).ready(function() {
//     $('#dt').DataTable( {

//     } );
// } );
// </script>