<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){
		var buttonOptions = {
			title 		: '<h1 class="text-center">Laporan Stok Barang Material<h1>',
			filename 	: 'Laporan Stok ' + '<?= date('l, d F Y')?>'
		};
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
						c = 0,
						e = 0,
						f = 0,
						g = 0,
						h = 0,
						j = 0;
                    rows.data().each(function(i, d) {
                        a += parseInt(i[3]);
                        b += parseInt(i[4]);
                        c += parseInt(i[5]);
                        e += parseInt(i[6]);
                        f += parseInt(i[7]);
                        g += parseInt(i[8]);
                        h += parseInt(i[9]);
                        j += parseInt(i[10]);
                    });
                    var ra = $.fn.dataTable.render.number('.', ',', 2, 'Rp. ').display(a);
                    var rb = $.fn.dataTable.render.number('.', ',', 2, 'Rp. ').display(b);
                    var rc = $.fn.dataTable.render.number('.', ',', 2, 'Rp. ').display(c);
                    var re = $.fn.dataTable.render.number('.', ',', 2, 'Rp. ').display(e);
                    var rf = $.fn.dataTable.render.number('.', ',', 2, 'Rp. ').display(f);
                    var rg = $.fn.dataTable.render.number('.', ',', 2, 'Rp. ').display(g);
                    var rh = $.fn.dataTable.render.number('.', ',', 2, 'Rp. ').display(h);
                    var rj = $.fn.dataTable.render.number('.', ',', 2, 'Rp. ').display(j);
                    return $('<tr class="font-w600 fw-bold">')
                        .append('<td colspan="3">Total</td>')
                        .append('<td class="text-end">' + ra + '</td>')
                        .append('<td class="text-end">' + rb + '</td>')
                        .append('<td class="text-end">' + rc + '</td>')
                        .append('<td class="text-end">' + re + '</td>')
                        .append('<td class="text-end">' + rf + '</td>')
                        .append('<td class="text-end">' + rg + '</td>')
                        .append('<td class="text-end">' + rh + '</td>')
                        .append('<td class="text-end">' + rj + '</td>');
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
							data.jumlah,
							data.diskon,
							data.chek,
							data.notaretur,
							data.laba,
							data.laba_retur,
							data.total,
							data.laba_akhir,
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