<style>html { margin: 1px; font-size : 8px;}
.fix{
  width:120px; 
  height:40px; 
  margin-bottom:10px; 
  margin-left:5px;
}
</style>
<?php 
$html = "";
$i = 3;
$array = [];
foreach ($barcode as $value) {
  $generator = new Picqer\Barcode\BarcodeGeneratorDynamicHTML();
  $array['generator'.$i] = $generator->getBarcode($value->kode_produk, $generator::TYPE_CODE_128, 1, 14);
  // $array['generator'.$i] = '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode('081231723897', $generator::TYPE_CODE_128)) . '">';
  $array['name'.$i] = $value->nama;
  $i++;
}
?>
<html>
  <table>
    <?php for ($j=3; $j < $i; $j++) { 
      if ($j%3 == 0) {?>
      <tr>
      <?php }
      ?>
          <td>
            <div class="fix">
              <?php echo $array['generator'.$j];
                  echo $array['name'.$j]; ?>
            </div>
          </td>
      <?php 
      if ($j > 3 && $j%3 == 2) { ?>
        </tr>
      <?php }
    }?>
  </table>
</html>       
