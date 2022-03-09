<style>html { margin: 1px; font-size : 8px;}
.fix{
  width:100px; 
  height:40px;
  margin-top:18px; 
  margin-bottom:20px; 
  margin-right:5px;
  margin-left:5px;
}
</style>
<html>
  <table style="width:100%">
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
