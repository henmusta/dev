<div class="content">
    <div class="block block-rounded">
        <div class="block-header">
            <div class="block-options">
                <button type="button" class="btn-block-option" data-toggle="block-option"
                    data-action="fullscreen_toggle"><i class="si si-size-fullscreen"></i></button>
                    <button type="button" class="btn-block-option"  id="barcode_single"><i class="si si-printer mr-1"></i></button>
                        <input type="hidden" id="id" value="<?php echo $kode ?>">	
            </div>
        </div>
        <div class="block-content" id="report">
            <input type="hidden" name="tipe" value="multiple">
            <div class="row ">
            <?php foreach($barcode as $key=>$value){ ?>
                    <div class="col-sm-3 border border-secondary" style=" padding:10px;"><?php 
                        $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
                        echo $generator->getBarcode($value->kode_produk, $generator::TYPE_CODE_128);   echo  $value->nama; ?></div>
            <?php } ?>
            </div>
        </div>
    </div>
</div>

<!--  -->