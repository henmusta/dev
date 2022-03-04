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
							id_cabang	: $('[name="filter[id_cabang]"]').val()
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
				{ data : 'tgl_nota', className : 'text-center' },
				{ 
					data : 'tgl_nota', 
					className : 'text-center', 
					width: '40px', 
					orderable: false,
					render : function ( columnData, type, rowData, meta ) {
						return `
						<a class="dropdown-item" href="<?= $module['url'];?>/detail/`+ columnData +`">Detail</a>
						`;
					}
				}
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

	});
});
</script>