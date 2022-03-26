<style>
html {
    margin: 1px;
    font-size: 9px;
    font-weight: bold;
}

.fix {
    width: 95px;
    height: 30px;
    margin-top: 3.5px;
    margin-bottom: 36.5px;
    margin-right: 6px;
    margin-left: 7px;
    text-align: center;
}
</style>
<?php
if ($cabang->id_cabang == 6) {
  $kali = 20;
} else {
  $kali = 12;
} ?>
<html>
<table style="width:100%">
    <tr>
        <td>
            <div class="fix">
                <?php
        $generator = new Picqer\Barcode\BarcodeGeneratorDynamicHTML();
        echo $barcode->telp . " / " . $barcode->harga_jual * $kali / 1000;
        echo $generator->getBarcode($barcode->kode_produk, $generator::TYPE_CODE_128, 1, 14);
        echo $barcode->kode_cabang . "  " . $barcode->nama . "  " . $barcode->kode_p;
        ?>
            </div>
        </td>
    </tr>
</table>

</html>