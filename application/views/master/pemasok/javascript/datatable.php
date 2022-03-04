<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){

		const dt = $('#dt').DataTable({
			lengthMenu		: [[5, 10, 25, 50, 100, -1], ["5", "10", "25", "50", "100", "All"]],
			displayLength 	: 10,
			order			: [[ 0, 'desc' ]],
			processing 		: true,
			serverSide 		: true,
			ajax:{
				url 		: "<?= $module['url'];?>/api-data/datatable", 
				type 		: "POST",
				data 		: function(d){
					let obj = {};
					$('[name^="filter"]').each(function(){
						var key = $(this).attr('name');
						var val = $(this).val();
						obj[key] = val;
					});
					return $.extend({},d,obj);
				}
			},
			columns 		: [
				{ data : 'kode', width:'60px' },
				{ data : 'nama' },
				{ data : 'telp' },
				{ data : 'email' },
				{ data : 'alamat' },
				{ 
					data : 'idp', 
					className : 'text-center', 
					width: '40px', 
					orderable: false,
					render : function ( columnData, type, rowData, meta ) {
						return `
						<div class="dropdown dropleft">
							<button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-fw fa-cog"></i></button>
							<div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item" href="<?= $module['url'];?>/update/`+ columnData +`">Edit</a>
								<a class="dropdown-item btn-row-delete" href="javascript:void(0);" data-pk="`+ rowData.idp +`">Delete</a>
							</div>
						</div>
						`;
					}
				}
			],
			rowCallback : function(row, data){
				let api = this.api();
				
				$(row).find('.btn-row-delete').click(function(){
					Swal.fire({
						title 	: "Anda Yakin ?",
						text 	: "Data tidak dapat dikembalikan setelah di hapus!",
						type 	: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Ya, Hapus!",
						cancelButtonText: "Tidak, Batalkan",
					}).then((result) => {
						if (result.value) {
							$.ajax({
								url: '<?= $module['url'];?>/crud/delete',
								type: "POST",
								data: { pk: data.idp },
								dataType: "json",
								error 	: function(){
									One.helpers('notify', {type: 'danger', icon: 'fa fa-exclamation mr-1', message: 'Server\'s response not found'});
								},
								success : function(response) {
									if ( response.status == "success" ){
										One.helpers('notify', {type: 'success', icon: 'fa fa-check mr-1', message: response.message});
										api.ajax.reload( null, false );
									} else {
										One.helpers('notify', {type: 'danger', icon: 'fa fa-exclamation mr-1', message: response.message});
									}
								}
							});
						}
					});
				});
			}
		});

	});
});
</script>