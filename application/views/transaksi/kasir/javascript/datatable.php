<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){
	$( "#kasir" ).click(function(e) {
			e.preventDefault();
			var date = $('.datepicker').val(); 
			var cabang = $('#id_cabang').val(); 
			location.href = "<?= $module['url'];?>/insert/"+date+"/"+cabang;
        // alert(selected);
	});
		$('.datepicker').datepicker({format:'yyyy-mm-dd',todayHighlight:true, clearBtn:true,  autoclose: true});
		const dt = $('#dt').DataTable({
			lengthMenu		: [[5, 10, 25, 50, 100, -1], ["5", "10", "25", "50", "100", "All"]],
			displayLength 	: 10,
			order			: [[ 0, 'desc' ]],
			processing 		: true,
			serverSide 		: true,
			columnDefs: [ {
								"searchable": false,
								"orderable": false,
								"data" : null,
								"targets": [ 0 ]}
						],
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
				{render: function (data, type, row, meta) {
					return meta.row + meta.settings._iDisplayStart + 1;
				}, width: '50px', className 	: 'text-center', },
				// { data : 'tgl_nota', className:'text-left', width: '120px' },
				{ 
					data : 'tgl_nota',
					render : function ( columnData, type, rowData, meta ) {
						return `<a href="<?= $module['url'];?>/single/`+ rowData.id +`">`+ columnData +`</a>`;
					}
				},
				{ 
					data : 'id', 
					className : 'text-center', 
					width: '40px', 
					orderable: false,
					render : function ( columnData, type, rowData, meta ) {
						return `
						<div class="dropdown dropleft">
							<button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-fw fa-cog"></i></button>
							<div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item" href="<?= $module['url'];?>/update/`+ rowData.id + '/' + rowData.tgl_nota +`">Edit</a>
								<a class="dropdown-item btn-row-delete" href="javascript:void(0);" data-pk="`+ columnData +`">Hapus</a>
							</div>
						</div>
						`;
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
			}
		});
		$('[name^="filter"]').change(function(){
			dt.draw(null,false);
		});
	});
});
</script>
