<style>html { margin: 1px; font-size : 8px;}
.fix{
  width:120px; 
  height:40px; 
  margin-bottom:10px; 
  margin-left:5px;
}
</style>
<html>
  <table>
      <tr>
          <td>
            <div class="fix">
                <?php 
                $generator = new Picqer\Barcode\BarcodeGeneratorDynamicHTML();
                echo $generator->getBarcode($barcode->kode_produk, $generator::TYPE_CODE_128, 1, 14);
                echo $barcode->nama;
                ?>
            </div>
          </td>
      </tr>
  </table>
</html>       
