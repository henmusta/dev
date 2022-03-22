<script type="text/javascript">
$(function(){
	'use strict'
	function format ( res ) {
		var html =  `<table>
			<thead>
				<tr>
					<th>Nama Produk</th>
					<th>Qty</th>
					<th>Harga</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>`;
			$.each(res, function (x, data){
				html +=`
				<tr>
					<td>${data.nama_produk}</td>
					<td>${data.qty}</td>
					<td>${data.harga}</td>
					<td>${data.total}</td>
				</tr>`;
			})
			html += `
			</tbody>
		</table>`;
		return html;
	}
	$(document).ready(function(){
		var buttonOptions = {
			title 		: '<h1 class="text-center">Laporan Stok Barang Material<h1>',
			filename 	: 'Laporan Stok ' + '<?= date('l, d F Y')?>'
		};
		$('#dt tbody').on('click', 'td.dt-control', function () {
			var tr = $(this).closest('tr');
			var row = dt.row( tr );
			if ( row.child.isShown() ) {
				// This row is already open - close it
				row.child.hide();
				tr.removeClass('dt-hasChild shown');
			}
			else {
				var id_penjualan = row.data();
				$.ajax({
					url : "<?= $module['url'];?>/getdetail/" + id_penjualan[1],
					type : 'GET',
				}).done(function(res){
					tr.addClass('dt-hasChild shown');
					row.child( format(res)).show();
				});
			}
		} );
		var dt = $('#dt').DataTable({
			
			dom: 'Bfrtip',
			buttons: [
				'copy', 'csv', 'excel', 'pdf', 'print'
			],
			rowGroup: {
                startRender: function(rows, group) {
                    return '<span class="font-w600 fw-bold">' + group + '</span>';
                },
                endRender: function(rows, group) {
                    var a = 0,
						b = 0,
						c = 0;
                    rows.data().each(function(i, d) {
                        a += parseInt(i[4]);
                        b += parseInt(i[5]);
                        c += parseInt(i[6]);
                    });
                    var ra = $.fn.dataTable.render.number('.', ',', 2, 'Rp. ').display(a);
                    var rb = $.fn.dataTable.render.number('.', ',', 2, 'Rp. ').display(b);
                    var rc = $.fn.dataTable.render.number('.', ',', 2, 'Rp. ').display(c);
                    return $('<tr class="font-w600 fw-bold">')
                        .append('<td colspan="4">Total</td>')
                        .append('<td class="text-end">' + ra + '</td>')
                        .append('<td class="text-end">' + rb + '</td>')
                        .append('<td class="text-end">' + rc + '</td>');
                },
                dataSrc: [0]
            },
		}); 
		$('.flatpickr').flatpickr({
			dateFormat 	: "Y-m-d"
		});
		$('[name="filter[date_start]"], [name="filter[date_end]"]').on({
			change: function(e){
				dt.draw();
			}
		});
		$('#selectCustomer').select2({
			allowClear: true,
			placeholder: 'Pilih Pelanggan',
			ajax: {
				url: "<?= $module['url'];?>/getcustomer",
				dataType: 'json',
				delay: 250,
				processResults: function(data) {
					return {
						results: $.map(data, function(item) {
							return {
								text: item.text,
								id: item.id
							}
						})
					};
				},
				cache: true
			}
		});
		$('#btnSubmit').click(function(e) {
            e.preventDefault();
			var filter = $('#form_filter').serializeArray();
			$.ajax({
                url: "<?= $module['url'];?>/api-data",
                data: filter,
                type: 'post',
                success: function(res) {
					dt.clear().draw();
					$.each(res, function (x, data){
						dt.row.add([
							data.nama_p,
							data.tgl_nota,
							data.nomor,
							data.nama_produk,
							data.qty,
							data.harga,
							data.total,
						]);
					});
					dt.draw();
                },
                error: function(res) {
					console.log(res)
                }
            })
		});

	});
});
</script>