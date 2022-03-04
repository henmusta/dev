<div class="content">
    <div class="block block-rounded">
        <div class="block-header">
            <div class="block-options">
                <button type="button" class="btn-block-option" data-toggle="block-option"
                    data-action="fullscreen_toggle"><i class="si si-size-fullscreen"></i></button>
                <button type="button" class="btn-block-option"  id="barcode_single"><i class="si si-printer mr-1"></i></button>
            </div>
        </div>
        <div class="block-content" id="report">
        <input type="text" id="id" value="<?php echo $data->id ?>">	
            <div>
                <?php 
                    $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
                    echo $generator->getBarcode($data->kode_produk, $generator::TYPE_CODE_128);
                    echo  $data->nama;
                ?>
            </div>
        </div>
    </div>
</div>