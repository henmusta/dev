<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){

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
				{ 
					data : 'id', 
					className : 'text-center', 
					width: '40px', 
					orderable: false,
					render : function ( columnData, type, rowData, meta ) {
						return meta.row + meta.settings._iDisplayStart + 1;
					}
				},
				{ 
					data : 'kode_pemasok', width: '200px', 
					render : function(columnData, type, rowData, meta){
						return columnData + ' || ' + rowData.nama_pemasok;
					} 
				},
				{ data : 'nama' },
				{ data : 'harga_jual', width: '140px', className:'text-right', render : $.fn.dataTable.render.number( ',', '.', 0, '' ) },
			    <?php if($this->user->hak_akses != 'Kasir'){ ?>
				{ data : 'harga_beli', width: '140px', className:'text-right', render : $.fn.dataTable.render.number( ',', '.', 0, '' ) },
				{ data : 'laba', width: '140px', className:'text-right', render : $.fn.dataTable.render.number( ',', '.', 0, '' ) },
				<?php } ?>
				{ data : 'saldo', width: '140px', className:'text-right', render : $.fn.dataTable.render.number( ',', '.', 0, '' ) },
				{ 
					data : 'id', 
					className : 'text-center', 
					width: '40px', 
					orderable: false,
					render : function ( columnData, type, rowData, meta ) {
						return `
						<a class="dropdown-item" href="<?= $module['url'];?>/detail/`+ columnData +`">Detail</a>
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
								data: { pk: data.id },
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