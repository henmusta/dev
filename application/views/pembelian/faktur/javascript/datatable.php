<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){
		$('.datepicker').datepicker({format:'yyyy-mm-dd',todayHighlight:true, clearBtn:true});
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
				{ 
					data : 'id', 
					className : 'text-center', 
					width: '40px', 
			orderable: false,
					render : function ( columnData, type, rowData, meta ) {
						return meta.row + meta.settings._iDisplayStart + 1;
					}
				},
				{ data : 'tgl_buat', className:'text-right', width: '180px' },
				{ data : 'tgl_nota', className:'text-right', width: '180px' },
				{ data : 'nama_pemasok', width: '180px' },
				{ 
					data : 'nomor',
					width: '180px',
					render : function ( columnData, type, rowData, meta ) {
						return `<a href="<?= $module['url'];?>/single/`+ rowData.id +`">`+ columnData +`</a>`;
					}
				},
				{ data : 'total_rincian', className:'text-right', width:'180px', render: $.fn.dataTable.render.number( ',', '.', 0, '' ) },
				{ data : 'diskon', className:'text-right', width:'180px', render: $.fn.dataTable.render.number( ',', '.', 0, '' ) },
				{ data : 'total_tagihan', className:'text-right', width:'180px', render: $.fn.dataTable.render.number( ',', '.', 0, '' ) },
				{ data : 'total_pembayaran', className:'text-right', width:'180px', render: $.fn.dataTable.render.number( ',', '.', 0, '' ) },
				{ 
					data : 'status_ro', 
					className : 'text-center', 
					width: '150px', 
					orderable: false,
					render : function ( columnData, type, rowData, meta ) {

						if(columnData  == 0){
							return `<a type="button" class="btn btn-outline-warning js-click-ripple-enabled" data-toggle="click-ripple" style="overflow: hidden; position: relative; z-index: 1;">Draft</a>`;
						}
						else{
							return `<a type="button" class="btn btn-outline-success js-click-ripple-enabled" data-toggle="click-ripple" style="overflow: hidden; position: relative; z-index: 1;">Fix</a>`;
						}
		
					}
				},
				{ data : 'sisa_tagihan', className:'text-right', width:'180px', render: $.fn.dataTable.render.number( ',', '.', 0, '' ) },
				{ 
					data : 'id', 
					className : 'text-center', 
					width: '40px', 
					orderable: false,
					render : function ( columnData, type, rowData, meta ) {
						let html = ``;
						if('<?php echo $this->user->hak_akses;?>' === 'Super Admin'){
							html = `<div class="dropdown dropleft">
								<button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-fw fa-cog"></i></button>
								<div class="dropdown-menu dropdown-menu-right">
									<a class="dropdown-item btn-row-harga" href="<?= $module['url'];?>/harga-jual/`+ columnData +`?satuan=<?php echo $this->cabang->satuan;?> "  data-satuan="">Harga Jual</a>
									<a class="dropdown-item" href="<?= $module['url'];?>/update/`+ columnData +`" >Edit</a>
									<a class="dropdown-item btn-row-delete" href="javascript:void(0);" data-pk="`+ columnData +`">Hapus</a>
								</div>
							</div>`;
						}else if('<?php echo $this->user->hak_akses;?>' === 'Kasir'){
							html = `<div class="dropdown dropleft">
								<button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-fw fa-cog"></i></button>
								<div class="dropdown-menu dropdown-menu-right">
							</div>`;
						}
						else if('<?= date('Y-m-d');?>' === rowData.tgl_nota && rowData.sisa_tagihan != 0){
							html = `<div class="dropdown dropleft">
								<button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-fw fa-cog"></i></button>
								<div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item btn-row-harga" href="<?= $module['url'];?>/harga-jual/`+ columnData +`?satuan=<?php echo $this->cabang->satuan;?> "  data-satuan="">Harga Jual</a>
									<a class="dropdown-item " href="<?= $module['url'];?>/update/`+ columnData +`">Edit</a>
									<a class="dropdown-item btn-row-delete" href="javascript:void(0);" data-pk="`+ columnData +`">Hapus</a>
								</div>
							</div>`;
						}
						else{
							html = `<div class="dropdown dropleft">
								<button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-fw fa-cog"></i></button>
								<div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item btn-row-harga" href="<?= $module['url'];?>/harga-jual/`+ columnData +`?satuan=<?php echo $this->cabang->satuan;?> "  data-satuan="">Harga Jual</a>
									<a class="dropdown-item btn-row-delete" href="javascript:void(0);" data-pk="`+ columnData +`">Hapus</a>
								</div>
							</div>`;
						}
						return html;
					}
				}
			],
			rowCallback : function(row, data){
				let api = this.api();
				$(row).find('.btn-row-delete').click(function(){
					let pk = $(this).attr('data-pk');
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
								data: { pk: pk },
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

				$(row).find('.btn-row-harga').click(function(){
					// alert("waw");
					let pk = $(this).attr('data-pk');
					// var cabang =  $('#id_cabang').val();
		                  $.ajax({
								url: '<?= $module['url'];?>/harga-jual',
								type: 'POST',
								data: { satuan: pk },
								dataType: "json",
								success : function(data) {
            						alert(data);
								},
								error : function(data) {
									// do something
								}
							});
						// }
					// });
				});
			}
		});
		$('[name^="filter"]').change(function(){
			dt.draw(null,false);
		});
	});
});
</script>