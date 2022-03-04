<script type="text/javascript">
var dt;
function update_active(id)
   {
    $.ajax({
        url : "<?= $module['url'];?>/update_active/" + id,
        type: "POST",
        dataType: "JSON",
        success: function(data)
        {
            dt.draw();            
        }
    });
}

function reload_table()
{
    table.ajax.reload(null,false); //reload datatable ajax 
}


        function update_not_active(id)
        {
            $.ajax({
                url : "<?= $module['url'];?>/update_not_active/" + id,
                type: "POST",
                dataType: "JSON",
                success: function(data)
                {
                    dt.draw();
                }
            });
        }
function change_account(id)
{
    $.ajax({
        url : "<?= $module['url'];?>/cek_giro/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            if(data.chek == 1)
            {
                update_not_active(id);
                setTimeout(function(){
                    dt.draw();
                }, 100);
            }
            else
            {
                update_active(id);
                setTimeout(function(){
                    dt.draw();
                }, 100);
            }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
        alert("gagal");
        }
    });
}
$(function(){
	'use strict'
	$(document).ready(function(){
		//$('.datepicker').datepicker({format:'yyyy-mm-dd',todayHighlight:true, clearBtn:true});
		 dt = $('#dt').DataTable({
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
					width: '100px', 
					orderable: false,
					render : function ( columnData, type, rowData, meta ) {

						if(columnData  == 0){
							return `<a type="button" class="btn btn-outline-warning js-click-ripple-enabled" data-toggle="click-ripple" style="overflow: hidden; position: relative; z-index: 1;">Belum Cair</a>`;
						}
						else{
							return `<a type="button" class="btn btn-outline-success js-click-ripple-enabled" data-toggle="click-ripple" style="overflow: hidden; position: relative; z-index: 1;">Sudah Cair</a>`;
						}
		
					}
				},
				{ data : 'nominal', className:'text-right', width:'180px', render: $.fn.dataTable.render.number( ',', '.', 0, '' ) },
				{ 
					data : 'id_bayar', 
					className : 'text-center', 
					width: '80px', 
					orderable: false,
					render : function ( columnData, type, rowData, meta ) {

                        if(rowData.cek  == 1){
							return '<div><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id='+ columnData +' name='+ columnData +' onclick="change_account('+ columnData +')" checked><label class="custom-control-label" for='+ columnData +'></label></div></div>';
						}
						else{
							return '<div><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id='+ columnData +' name='+ columnData +' onclick="change_account('+ columnData +')"><label class="custom-control-label" for='+ columnData +'></label></div></div>';
						}
					}
				}
			],
			rowCallback : function(row, data){
				// $(row).find('#toggle-demo').bootstrapToggle();
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