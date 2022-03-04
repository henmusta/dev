<script type="text/javascript">
	$( "#barcode_single" ).click(function() {
             let params = new URLSearchParams({
                tipe: 'single',
                id: $('#id').val()
              });
        window.open("<?php echo site_url('master/produk/barcode_print?')?>"+ params.toString())
    });
</script>