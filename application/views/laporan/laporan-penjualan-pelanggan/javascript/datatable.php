<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){
		var buttonOptions = {
			title 		: '<h1 class="text-center">Laporan Stok Barang Material<h1>',
			filename 	: 'Laporan Stok ' + '<?= date('l, d F Y')?>'
		};
		$('#dt').DataTable({
			processing: true,
			serverSide: true,
			columnDefs: [{
				"searchable": false,
				"orderable": false,
				"targets": 0
			}],
			ajax: {
				url : "<?= $module['url'];?>/api-data",
				type : 'POST'
			},
			
			columns : [
					{
						data: 'id',
						render: function (data, type, row, meta) {
							return meta.row + meta.settings._iDisplayStart + 1;
						}
					},
					{ data : 'tgl_buat', className : 'text-center' },
					{ data : 'nomor', className : 'text-center' },

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