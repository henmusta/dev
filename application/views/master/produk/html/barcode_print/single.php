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
<html>
  <table style="width:100%">
      <tr>
          <td>
            <div class="fix">
                <?php 
                $generator = new Picqer\Barcode\BarcodeGeneratorDynamicHTML();
                echo "Rp. ".number_format($barcode->harga_jual);
                echo $generator->getBarcode($barcode->kode_produk, $generator::TYPE_CODE_128, 1, 14);
                echo $barcode->kode_produk;
                ?>
            </div>
          </td>
      </tr>
  </table>
</html>       
