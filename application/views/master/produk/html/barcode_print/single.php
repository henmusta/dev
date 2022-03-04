            <div style="border: 1px solid #000; padding:10px;">
                <?php 
                    $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
                    echo $generator->getBarcode($barcode->kode_produk, $generator::TYPE_CODE_128);
                    echo  $barcode->nama;
                ?>
            </div>