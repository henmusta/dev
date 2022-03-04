<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<script>
$(function(){
	'use strict'
	function rupiah(text){
		return String(text).replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1,");
	}
	$(document).ready(function(){
		var iTable = $("table#<?php echo $id;?>").DataTable({
			lengthMenu		: [[5, 10, 25, 50, 100, -1], ["5", "10", "25", "50", "100", "All"]],
			displayLength 	: <?php echo (int)$display_length;?>,
			order			: [[ <?php echo (int)$order_column['number'];?>, '<?php echo $order_column['dir'];?>' ]],
			columns 		: [<?php foreach($columns AS $column) : echo $column; endforeach; ?>],
			processing 		: true,
			serverSide 		: true,
			ajax:{
				url 		: "<?php echo $source_url;?>", 
				type 		: "POST"
			},
			initComplete 	: function(settings, json) {
				$('#<?php echo $id;?>_filter input').unbind();
					$('#<?php echo $id;?>_filter input').bind('keyup', function(e) {
					if(e.keyCode == 13) {
						iTable.search( this.value ).ajax.reload();
					}
				}); 
			},
			drawCallback 	: function(){
				$('.btn-delete').click(function(){
					let pk = $(this).data('pk');
					Swal.fire({
						title 	: "Apakah Anda Yakin ?",
						text 	: "Data ini tidak akan dapat dikembalikan setelah terhapus !",
						type 	: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Ya, Hapus !",
						cancelButtonText: "Tidak, Batalkan",
					}).then((result) => {
						if (result.value) {
							$.ajax({
								url: '<?php echo $delete_url;?>',
								type: "POST",
								data: { pk: pk },
								dataType: "json",
								error 	: function(){
									One.helpers('notify', {type: 'danger', icon: 'fa fa-exclamation mr-1', message: 'Server\'s response not found'});
								},
								success : function(response) {
									if ( response.status == "success" ){
										One.helpers('notify', {type: 'success', icon: 'fa fa-check mr-1', message: response.message});
										iTable.ajax.reload( null, false );
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