<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){

			$(".select2-cek_giro").select2({ 
				
			});
		$('#delete-all').click(function(){

			var id_cabang = $('#id_cabang').val();
					Swal.fire({
						title 	: "Anda Yakin Ingin Menghapus Semua Data Giro ?",
						text 	: "Data tidak dapat dikembalikan setelah di hapus!",
						type 	: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Ya, Hapus!",
						cancelButtonText: "Tidak, Batalkan",
					}).then((result) => {
						if (result.value) {
							$.ajax({
								url: '<?= $module['url'];?>/crud/deleteall',
								type: "POST",
								data: { id_cabang: id_cabang },
								dataType: "json",
								error 	: function(){
									One.helpers('notify', {type: 'danger', icon: 'fa fa-exclamation mr-1', message: 'Server\'s response not found'});
								
								},
								success : function(response) {
									if ( response.status == "success" ){
										One.helpers('notify', {type: 'success', icon: 'fa fa-check mr-1', message: response.message});
										dt.draw();
									} else {
										One.helpers('notify', {type: 'danger', icon: 'fa fa-exclamation mr-1', message: response.message});
										dt.draw();
									}
								}
							});
						}
					});
				});
		const dt = $('#dt').DataTable({
			lengthMenu		: [[250, -1], ["10 Groups", "All"]],
			displayLength 	: 250,
			order			: [[ 1, 'asc' ]],
			processing 		: true,
			serverSide 		: true,
			ajax:{
				url 		: "http://localhost/ahsana/laporan/laporan-giro1/api-data/datatable", 
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
			// rowGroup: {
			//     dataSrc: 'g',
            //     data : 'nomor',
			//     startClassName: 'tr-collapse',
	        //     startRender: function(rows, group, level, data) {
	        //         let html = $(`
	        //         	<tr data-target="`+ group +`" style="cursor:pointer;">
	        //         		<td>`+  group +`</td>
	                	
	        //         `); 
	        //         return html;
	        //     }
			// },
            drawCallback : function ( settings ) {
                var api = this.api();
                var rows = api.rows( {page:'current'} ).nodes();
                var last=null;
				var cek_giro = $("#cek_giro").val();
				var id_cabang = $("#id_cabang").val();
                api.column(0, {page:'current'} ).data().each( function ( group, i ) {
                    if ( last !== group ) {
                        var data = api.row(i).data();
                        $(rows).eq( i ).before(
                            '	<tr class="tr-collapse" data-target="'+ group +'" style="cursor:pointer;"><td>'+  group +'</td><td class="text-center"><a class="dropdown-item" href="http://localhost/ahsana/laporan/laporan-giro1/single/'+ data.awal +'/'+data.akhir+'/'+id_cabang+'">Detail</a><td><tr>'
                            // '<td>'+data.awal+'</td></tr>'
                        );
    
                        last = group;
                    }
                } );
           },
			columns 		: [
				{ data : 'g', visible : false },
				{ data : 'nomor' },
				{ 
					data : 'id', 
					className : 'text-center', 
					width: '300px', 
					orderable: false,
					render : function ( columnData, type, rowData, meta ) {
						return `
                        <input class="ket form-control form-control-sm text-right" value="`+ rowData.ket +`">
						`;
					}
				}
			],
    
			rowCallback : function(row, data){
				let api = this.api();
				$(row).addClass('d-none').attr('data-group', data.g);
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
		$('#dt tbody').on('click', 'tr.tr-collapse', function() {
			let g = $(this).attr('data-target');
			$('#dt tbody').find('tr[data-group="'+ g +'"]').toggleClass('d-none');
	    });

		$('form#form').validate({
            validClass      : 'is-valid',
            errorClass      : 'is-invalid',
            errorElement    : 'div',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.parent().append(error);
            },
			highlight		: function(element, errorClass, validClass) {
				$(element).removeClass(validClass).addClass(errorClass)
				.closest('.form-group').children('label').removeClass('text-success').addClass('text-danger');
			},
			unhighlight		: function(element, errorClass, validClass) {
				$(element).removeClass(errorClass).addClass(validClass)
				.closest('.form-group').children('label').removeClass('text-danger').addClass('text-success');
			},
			submitHandler	: function(form,eve) {
				eve.preventDefault();
				var btnSubmit 		= $(form).find("[type='submit']"),
					btnSubmitHtml 	= btnSubmit.html();

				$.ajax({
					cache 		: false,
					processData : false,
					contentType : false,
					type 		: 'POST',
					url 		: $(form).attr('action'),
					data 		: new FormData(form),
					dataType	: 'JSON',
					beforeSend:function() { 
						btnSubmit.addClass("disabled").html("<i class='fas fa-spinner fa-pulse fa-fw'></i> Loading ... ");
					},
					error 		: function(){
						btnSubmit.removeClass("disabled").html(btnSubmitHtml);
						$.notify({ icon: 'fa fa-exclamation mr-1', message: 'Server\'s response not found'}, {type: 'danger'});
					},
					success 	: function(response) {
						btnSubmit.removeClass("disabled").html(btnSubmitHtml);
						let timeout = 1000;
						if ( response.status == "success" ){
							$.notify( { icon: 'fa fa-check mr-1', message: response.message}, {type: 'success'});
							dt.draw();
						} else {
							$.notify( {icon: 'fa fa-exclamation mr-1', message: response.message},{type: 'danger'});
						}
					}
				});
			}
		});

	});
});
</script>