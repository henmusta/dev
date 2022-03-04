<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){
		//$('.datepicker').datepicker({format:'yyyy-mm-dd',todayHighlight:true, clearBtn:true});
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
				{render: function (data, type, row, meta) {
					return meta.row + meta.settings._iDisplayStart + 1;
				}, width: '50px', className 	: 'text-center', },
				{ data : 'tgl_bayar', className:'text-center', width: '200px' },
				{ data : 'tgl_giro', className:'text-center', width: '200px'  },
				{ data : 'nomor_giro', className:'text-center', width: '200px'  },
				{ 
					data : 'cek', 
					className : 'text-center', 
					width: '40px', 
					orderable: false,
					render : function ( columnData, type, rowData, meta ) {

						if(columnData  == 0){
							return `
							<a> Belum Cair </a>	
							`;
						}
						else if(columnData  == 1){
							return `
							<a> Cair </a>
							`;
						}
						else{
							return `Ditolak`;
						}
						
					}
				},
				{ data : 'nominal', className:'text-right', width:'180px', render: $.fn.dataTable.render.number( ',', '.', 0, '' ) },
				{ 
					data : 'id', 
					className : 'text-center', 
					width: '80px', 
					orderable: false,
					render : function ( columnData, type, rowData, meta ) {
						return '<div style="height: 0px;"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id='+ columnData +' name='+ columnData +' onclick="change_account('+ columnData +')" checked><label class="custom-control-label" for='+ columnData +'></label><h6 class="float-right"><span class="badge badge-success">Cair</span></h6></div></div>';
					}
				}
			],
			rowCallback : function(row, data){
				$(row).find('#toggle-demo').bootstrapToggle();
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

		function change_account(id)
{
    $.ajax({
        url : "<?= $module['url'];?>/giro_cair/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            if(data.chek == 1)
            {
                update_not_active(id);
                setTimeout(function(){
                    // reload_table();
                }, 100);
            }
            else
            {
                update_active(id);
                setTimeout(function(){
                    // reload_table();
                }, 100);
            }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            $.alert({
                title: 'Error',
                icon: 'fa fa-times',
                type: 'red',
                content: 'Error get data from ajax',
                closeAnimation: 'zoom',
                buttons: {
                    okay: {
                        text: 'ok',
                        btnClass: 'btn-outline-danger',
                        action: function(){

                        }
                    }
                }
            }); 
        }
    });
}
function change_account(id)
{
    $.ajax({
        url : "user/ajax_edit/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            if(data.cek == 1)
            {
                update_not_active(id);
                setTimeout(function(){
                    reload_table();
                }, 100);
            }
            else
            {
                update_active(id);
                setTimeout(function(){
                    reload_table();
                }, 100);
            }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            $.alert({
                title: 'Error',
                icon: 'fa fa-times',
                type: 'red',
                content: 'Error get data from ajax',
                closeAnimation: 'zoom',
                buttons: {
                    okay: {
                        text: 'ok',
                        btnClass: 'btn-outline-danger',
                        action: function(){

                        }
                    }
                }
            }); 
        }
    });
}



function update_active(id)
   {
    $.ajax({
        url : "user/ajax_update_active/" + id,
        type: "POST",
        data: $('#form').serialize(),
        dataType: "JSON",
        success: function(data)
        {
            
        }
    });
}

function update_not_active(id)
{
    $.ajax({
        url : "user/ajax_update_not_active/" + id,
        type: "POST",
        data: $('#form').serialize(),
        dataType: "JSON",
        success: function(data)
        {
             
        }
    });
}
	});
});
</script>