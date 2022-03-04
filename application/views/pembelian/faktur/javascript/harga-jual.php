<?php $pembelian = isset($data) ? $data : (object)[];?>
<script type="text/javascript">
$(function(){
	'use strict'
	$(document).ready(function(){
		new AutoNumeric.multiple('.autonumeric',{
			unformatOnSubmit            : true,
			decimalCharacterAlternative: ".",
			decimalPlaces: 0
		});

                        var cabang =  $('#id_cabang').val();
		                  $.ajax({
								url: '<?= $module['url'];?>/harga-jual',
								type: 'POST',
								data: { satuan: cabang },
								dataType: "json",
								success : function(data) {
            						alert(data);
								},
								error : function(data) {
									// do something
								}
							});

		$('.laba').on({
			changeLaba : function(){
				let 
				el 		= this,
				params 	= {
					pk 				: $(el).data('pk'),
					satuan   		: $(el).data('ps'),
					id_cabang   	: $(el).data('cb'),
					produk 		: {
						harga_beli 	: AutoNumeric.getNumber($(el).closest('tr').find('.harga-beli').get(0)),
						harga_jual 	: AutoNumeric.getNumber($(el).closest('tr').find('.harga-jual').get(0)),
						laba 		: AutoNumeric.getNumber(el)
					}
				}
				$.post('<?= $module['url'];?>/crud/update-harga',params,function(response){
					if ( response.status == "success" ){
						$.notify( { icon: 'fa fa-check mr-1', message: response.message}, {type: 'success'});
					} else {
						$.notify( {icon: 'fa fa-exclamation mr-1', message: response.message},{type: 'danger'});
					}
					$(el).closest('tr').find('.kode-laba').val(response.kode_laba);
				},'json');
			}
		});
		$('tr .harga-jual').on({
			keyup : function(){
				let	el_hargaJual 	= $(this),
				 	el_hargaBeli 	= el_hargaJual.closest('tr').find('.harga-beli'),
					el_laba 		= el_hargaJual.closest('tr').find('.laba');
				let harga_jual 	= AutoNumeric.getNumber(el_hargaJual.get(0)),
					harga_beli 	= AutoNumeric.getNumber(el_hargaBeli.get(0)),
					laba 		= harga_jual - harga_beli;
				AutoNumeric.getAutoNumericElement(el_laba.get(0)).set(laba);
			},
			focusout : function(e){
				e.stopPropagation();
				$(this).closest('tr').find('.laba').trigger('changeLaba');
			}
		});
	});
});
</script>


