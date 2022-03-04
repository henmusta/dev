<script type="text/javascript">

$(function(){
	'use strict'
	$(document).ready(function(){
		chart();
	

	function chart()
	{
		
		var tgl 	= $('[name="filter[tgl]"]').val()
		  $.ajax({
            url : "<?php echo site_url('beranda/chart')?>",
            type: "POST",
            dataType: "JSON",
			data : {tgl:tgl},
            success: function(data)
            {
				graph.setData(data["data"]);
				// // graph.setData(data);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
         
            }
          })
	}


        	const graph = Morris.Area({
                element : 'graph',
                // data: data["data"],
				xkey: 'tgl_nota',
				ykeys: ['total_tagihan'],
				labels: ['total_Penjualan']
             });

		
	           $('[name="filter[tgl]"]').on({
					change: function(e){
						graph.setData(chart());
					}
				});


		$(".flatpickr").datepicker( {
			format: "yyyy-mm",
			viewMode: "months", 
			minViewMode: "months",
			autoclose: true
		});

		

		const stok = $('#stok').DataTable({
			lengthMenu :[[5, 25, 50 ,-1],[5, 25, 50, "All"]],
			data : <?= json_encode($barang);?>,
			columns : [
				{ data : 'kode_pemasok' },
				{ data : 'nama_produk' },
				{ data : 'nama_pemasok' },
                { data : 'saldo' }
			]
		});

        const pelanggan = $('#pelanggan').DataTable({
            lengthMenu :[[5, 25, 50 ,-1],[5, 25, 50, "All"]],
			order : [[2, "desc"]],
			data : <?= json_encode($pelanggan);?>,
            
			columns : [
				{ data : 'nama' },
                { data : 'alamat' },
                { data : 'penjualan', className:'text-right', width:'180px', render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp' ) }
				// { data : 'penjualan' }
			]
		});


	});
});
</script>