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
$i = 3;
$array = [];
if ($cabang->id_cabang == 6) {
  $kali = 20;
} else {
  $kali = 12;
}
foreach ($barcode as $value) {
  $generator = new Picqer\Barcode\BarcodeGeneratorDynamicHTML();
  $array['generator' . $i] = $generator->getBarcode($value->kode_produk, $generator::TYPE_CODE_128);
  $array['name' . $i] = $value->nama;
  $array['kode' . $i] = $value->kode_produk;
  $array['harga' . $i] = ($value->harga_jual * $kali) / 1000;
  $array['merk' . $i] = $value->telp;
  $array['kode_p' . $i] = $value->kode_p;
  $array['kode_cabang' . $i] = $value->kode_cabang;
  $i++;
}
?>
<html>
<table style="width:100%;">
    <?php for ($j = 3; $j < $i; $j++) {
    if ($j % 3 == 0) { ?>
    <tr>
        <?php }
      ?>
        <td>
            <div class="fix">
                <?php
          echo $array['merk' . $j] . " / " . $array['harga' . $j];
          echo $array['generator' . $j];
          echo $array['kode_cabang' . $j] . "  " . $array['name' . $j] . "  " . $array['kode_p' . $j]; ?>
            </div>
        </td>
        <?php
      if ($j > 3 && $j % 3 == 2) { ?>
    </tr>
    <?php }
    } ?>
</table>

</html>