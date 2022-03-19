<style>html { margin: 1px; font-size : 8px;}
.fix{
  width:95px; 
  height:35px;
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
                echo $barcode->telp . " / ".number_format($barcode->harga_jual * $barcode->satuan / 1000);
                echo $generator->getBarcode($barcode->kode_produk, $generator::TYPE_CODE_128, 1, 14);
                echo $barcode->kode_cabang."  ".$barcode->nama."  ".$barcode->kode_p;
                ?>
            </div>
          </td>
      </tr>
  </table>
</html>       
