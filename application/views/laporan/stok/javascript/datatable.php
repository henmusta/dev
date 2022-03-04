<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){
		var buttonOptions = {
			title 		: '<h1 class="text-center">Laporan Stok Barang Material<h1>',
			filename 	: 'Laporan Stok ' + '<?= date('l, d F Y')?>'
		};

		const dt = $('#dt').DataTable({
			lengthMenu		: [[5, 10, 25, 50, 100, -1], ["5", "10", "25", "50", "100", "All"]],
			displayLength 	: -1,
			info			: false,
			searching 		: false,
			order			: [[ 3, 'asc' ]],
			paging 			: false, 
			processing 		: true,
			serverSide 		: true,
			ajax:{
				url 		: "<?= $module['url'];?>/api-data/datatable", 
				type 		: "POST",
				data 		: function(d){
					return $.extend({},d,{
						filter : {
							date_start 	: $('[name="filter[date_start]"]').val(),
							date_end	: $('[name="filter[date_end]"]').val()
						}
					});
				}
			},
			rowGroup: {
				dataSrc: 'nama_jenis'
			},
			columns 		: [
				{ 
					data : 'id_barang', 
					ordered : false,
					className:'text-right',     
					render: function (data, type, row, meta) {
						return meta.row + meta.settings._iDisplayStart + 1;
					} 
				},
				{ data : 'id_barang', className:'text-right' },
				{ data : 'nama_barang' },
				{ data : 'nama_jenis', visible:false },
				{ data : 'saldo_barang', className:'text-right' },
				{ data : 'nama_satuan' }
			]
		});



	});
});
</script>