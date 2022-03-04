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
			filename 	: 'Laporan Biaya Jakarta ' + '<?= date('l, d F Y')?>'
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
                .column( 14 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
           var pageTotal = api
                .column( 14, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            $( api.column( 2 ).footer() ).html(
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
							start_date     	: $('[name="filter[start_date]"]').val(),
							end_date     	: $('[name="filter[end_date]"]').val(),
							id_cabang 	    : $('[name="filter[id_cabang]"]').val()
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
				{ data : 'mingguan', className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' ) },
				{ data : 'pln', className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
				{ data : 'pam',className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
				{ data : 'internettv',className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
				{ data : 'atk',className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
				{ data : 'telepon',className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
				{ data : 'peralatan', className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
                { data : 'iuranrumah',className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
                { data : 'plastik', className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
                { data : 'tiket', className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
                { data : 'kuli',className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
                { data : 'dll', className:'text-right', width:'200px', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp.' )},
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
	});
	$( "#excel" ).click(function() {
            var   start_date  = $('[name="filter[start_date]"]').val();
		    var   end_date    = $('[name="filter[end_date]"]').val();
		    var   id_cabang   = $('[name="filter[id_cabang]"]').val();
         window.open("<?php echo site_url('laporan/laporan_biaya_jakarta/excel/')?>"+start_date+'/'+end_date+'/'+id_cabang)
        });
});
// $(document).ready(function() {
//     $('#dt').DataTable( {

//     } );
// } );
// </script>