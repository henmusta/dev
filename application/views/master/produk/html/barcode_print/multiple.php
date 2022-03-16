<style>html { margin: 1px; font-size : 7px;}
.fix{
  width:100px; 
  height:40px;
  margin-top:7px; 
  margin-bottom:31.5px; 
  margin-right:5px;
  margin-left:5px;
  text-align: center;
}
</style>
<?php 
$i = 3;
$array = [];
foreach ($barcode as $value) {
  $generator = new Picqer\Barcode\BarcodeGeneratorDynamicHTML();
  $array['generator'.$i] = $generator->getBarcode($value->kode_produk, $generator::TYPE_CODE_128, 1, 14);
  $array['name'.$i] = $value->nama;
  $array['kode'.$i] = $value->kode_produk;
  $array['harga'.$i] = $value->harga_jual * 12;
  $array['merk'.$i] = $value->telp;
  $array['kode_p'.$i] = $value->kode_p;
  $array['kode_cabang'.$i] = $value->kode_cabang;
  $i++;
}
?>
<html>
  <table style="width:100%;">
    <?php for ($j=3; $j < $i; $j++) { 
      if ($j%3 == 0) {?>
      <tr>
      <?php }
      ?>
          <td>
            <div class="fix">
              <?php 
                  echo $array['merk'.$j]." / Rp. ".number_format($array['harga'.$j]);
                  echo $array['generator'.$j];
                  echo $array['kode_cabang'.$j] ."  ".$array['name'.$j]."  ".$array['kode_p'.$j] ; ?>
            </div>
          </td>
      <?php 
      if ($j > 3 && $j%3 == 2) { ?>
        </tr>
      <?php }
    }?>
  </table>
</html>       
