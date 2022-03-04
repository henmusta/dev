<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){
		function number(x) {
			return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
		}
		const dt = $('#dt').DataTable({		
			lengthMenu		: [[5, 10, 25, 50, 100, -1], ["5", "10", "25", "50", "100", "All"]],
			displayLength 	: -1,
			info			: false,
			searching		: false,
			bSort : false,
			order			: [[ 3, 'asc' ]],
			paging 			: false, 
			processing 		: true,
			serverSide 		: true,
			// rowsGroup       : [1],
			columnDefs: [
                {
                    "render": $.fn.dataTable.render.number( ',', '.', 2, 'Rp.' ) ,
                    "targets": [4, 5, 6]

                }
            ],
			ajax:{
				url 		: "<?= $module['url'];?>/api-data", 
				type 		: "POST",
				data 		: function(d){
					return $.extend({},d,{
						filter : {
							date_start 	: $('[name="filter[date_start]"]').val(),
							date_end	: $('[name="filter[date_end]"]').val(),
							id_cabang	: $('[name="filter[id_cabang]"]').val()
						}
					});
				}
			},
	
			columns 		: [
				{ 
				  data : 'tgl',
				  className:'text-center' },
				{ data : 'keterangan',  
				  className:'text-left',
				  render : function ( columnData, type, rowData, meta ) {
				     	var color = 'black';
						if(rowData.total_kredit != 0 ){
							color = 'red';
						}
						return '<span style="color:' + color + '">' + rowData.keterangan + '</span>';
					}
				},
				{ data : 'uraian' },
				{ data : 'nogiro', className:'text-center' },
			    { data : 'total_debit',  
				  className:'text-right',
				  render : function ( columnData, type, rowData, meta ) {
						if(rowData.total_debit != 0 ){
							return meta.settings.fnFormatNumber(rowData.total_debit);
						}else{
							return `-`;
						}
					}
				},
				{ data : 'total_kredit', 
				  className:'text-right',
				  render : function ( columnData, type, rowData, meta ) {
						if(rowData.total_kredit != 0 ){
							return  meta.settings.fnFormatNumber(rowData.total_kredit) ;
						}else{
							return `-`;
						}
					}
				 },
				{ data : 'new_saldo', 
				  className:'text-right',
				  render : function ( columnData, type, rowData, meta ) {
						if(rowData.new_saldo != 0 ){
							return  meta.settings.fnFormatNumber(rowData.new_saldo) ;
						}else{
							return `-`;
						}
					}
				 }
			],
			// rowGroup: {
			// 	dataSrc: [
			// 		'tgl'
			// 	]
    		// },
			drawCallback : function ( settings ) {
				$('#saldoAwal').html('Rp.'+number(settings.json.saldo_awal));
                var api = this.api();
                var rows = api.rows( {page:'current'} ).nodes();
                var last=null;
				var cek_giro = $("#cek_giro").val();
				var id_cabang = $("#id_cabang").val();
                api.column(0, {page:'current'} ).data().each( function ( group, i ) {
                    if ( last !== group ) {
                        var data = api.row(i).data();
                        $(rows).eq( i ).before(
                            '	<tr class="pocell" data-target="'+ group +'" style="background-color:#F0FFFF;"><td colspan="6">'+  group +'</td><td class="text-center">'+data.akhir_saldo+'<td><tr>'
                            // '<td>'+data.awal+'</td></tr>'
                        );
    
                        last = group;
                    }
                } );
           },
			// drawCallback	: function(settings) {
				
			// 	// console.log(settings.json.saldo_awal);
			// },
		});
		// $(".flatpickr").datepicker( {
		// format: "yyyy-mm",
		// viewMode: "months", 
		// minViewMode: "months",
		// autoclose: true
		// });
		// $('[name="filter[date_start]"]').on({
		// 	change: function(e){
		// 		dt.draw();
		// 	}
		// });

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
         window.open("<?php echo site_url('laporan/laporan-buku-bank/excel/')?>"+date_start+'/'+date_end+'/'+id_cabang)
        });
	});
});
</script>