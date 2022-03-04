<?php $pembelian = isset($data) ? $data : (object)[];?>
<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){

		$( "#excel" ).click(function() {
            var   giroawal  = <?= $awal ;?>;
            var   giroakhir    = <?= $akhir ;?>;
			var   id_cabang  = <?= $id_cabang ;?>;
         window.open("<?php echo site_url('laporan/laporan-giro/excel/')?>"+giroawal+'/'+giroakhir+'/'+id_cabang)
        });

		new AutoNumeric.multiple('.autonumeric',{
			unformatOnSubmit            : true,
			decimalCharacterAlternative: ".",
			decimalPlaces: 0
		});

		$('tr .ket').on({
			change : function(){
		    	let 
				el 		= this,
				params 	= {
					pk 				: $(el).data('pk'),
                    produk 		    : {
					keterangan 		: $(el).val()
					}
				}
				$.post('<?= $module['url'];?>/crud/update-ket',params,function(response){
					if ( response.status == "success" ){
						$.notify( { icon: 'fa fa-check mr-1', message: response.message}, {type: 'success'});
					} else {
						$.notify( {icon: 'fa fa-exclamation mr-1', message: response.message},{type: 'danger'});
					}
					$(el).closest('tr').find('.keterangan').val(response.keterangan);
				},'json');
			}
		});
	});
});
</script>


