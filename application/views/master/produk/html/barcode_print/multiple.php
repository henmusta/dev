       <!-- <table>
            <?php foreach($barcode as $key=>$value){ ?>
            <tr>
                <td><?php 
                        $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
                        echo $generator->getBarcode($value->kode_produk, $generator::TYPE_CODE_128);   echo  $value->nama; ?></td>
            </tr>
            <?php } ?>
        </table> -->

        

<html>
    <head>
        <style>
            /** Define the margins of your page **/
            @page {
                margin: 100px 35px 100px 35px ;
            }

         

             table th {
              border: 1px solid black;
                /* border-bottom: 1px solid black; */
                margin-bottom: 4px;
              }

          
        </style>
    </head>
    <body>
        <main>
    
        <div style="margin-top: 20px">
          <div class="invoice-bottom">
            <hr>
            <div class="row header">
              <table>
                <tr>
                  <th width="350px" style="text-align: left;">Data Pemohon</th>
                  <th width="" style="text-align: left;">Data Perusahaan</th>
                </tr>
              </table>
            </div>
            <hr>
            <table class="alpha">
                  <tr>
                    <td class="A14" width="130px" style="vertical-align: top;
                    text-align: left;">Nama Pemohon</td>
                    <th class="A14" style="vertical-align: top;
                    text-align: center;">:</th>
                    <td class="A14" width="230px" style="vertical-align: top;
                    text-align: left;">{{ $marketingcustomer['nama_pelanggan'] }}</td>
                    <td class="A14" width="140px" style="vertical-align: top;
                    text-align: left;">Nama Perusahaan</td>
                    <th class="A14" style="vertical-align: top;
                    text-align: center;">:</th>
                    <td class="A14" width="230px" style="vertical-align: top;
                    text-align: left;">{{ $marketingcustomer['perusahaan'] }}</td>
                  </tr>
                  <tr>
                    <td class="A14" style="">Nomor KTP/SIM/ID</td>
                    <th class="A14" style="vertical-align: top;
                    text-align: center;">:</th>
                    <td class="A14" width="230px" style="vertical-align: top;
                    text-align: left;">{{ $marketingcustomer['id_card'] }}</td>
                    <!-- <td width="85px"></td> -->
                    <td class="A14" style="vertical-align: top;
                    text-align: left;">Npwp</td>
                    <th class="A14" style="vertical-align: top;
                    text-align: center;">:</th>
                    <td class="A14" width="230px" style="vertical-align: top;
                    text-align: left;">{{ $marketingcustomer['npwp'] }}</td>
                  </tr>
                  <tr>
                    <td class="A14" style="vertical-align: top;
                    text-align: left;">Alamat Pemohon</td>
                    <th class="A14" style="vertical-align: top;
                    text-align: center;">:</th>
                    <td class="A14" width="230px" style="vertical-align: top;
                    text-align: left;"><p>{{ $marketingcustomer['alamat'] }}</p></td>
                    <!-- <td width="85px"></td> -->
                    <td class="A14" style="vertical-align: top;
                    text-align: left;">Alamat Pemasangan</td>
                    <th style="vertical-align: top;
                    text-align: center;">:</th>
                    <td class="A14" width="230px" style="vertical-align: top;
                    text-align: left;">{{ $marketingcustomer['alamat_pemasangan'] }}</td>
                  </tr>
                  <tr>
                    <td class="A14" style="vertical-align: top;
                    text-align: left;">Nomor Handphone</td>
                    <th class="A14" style="vertical-align: top;
                    text-align: center;">:</th>
                    <td class="A14" width="230px" style="vertical-align: top;
                    text-align: left;">{{ $marketingcustomer['hp'] }}</td>
                    <!-- <td width="85px"></td> -->
                    <td class="A14" style="vertical-align: top;
                    text-align: left;">Telepon</td>
                    <th class="A14" style="vertical-align: top;
                    text-align: center;">:</th>
                    <td class="A14" width="230px" style="vertical-align: top;
                    text-align: left;">{{ $marketingcustomer['telp'] }}</td>
                  </tr>
                  <tr>
                    <td class="A14" style="vertical-align: top;
                    text-align: left;">Email</td>
                    <th class="A14" style="vertical-align: top;
                    text-align: center;">:</th>
                    <td class="A14" width="230px" style="vertical-align: top;
                    text-align: left;">{{ $marketingcustomer['email'] }}</td>
                    <!-- <td width="85px"></td> -->
                  </tr>
               
            </table>
            
            <hr>
            <div class="header">
              <table>
                <tr>
                  <th style="text-align: center;">Informasi Tagihan</th>
                </tr>
              </table>
            </div>
            <hr>
            <table class='beta'>
                    <thead>
                      <tr>
                        <th width="250px" class="text-center">Nama Paket</th>
                        <th  class="text-center">Bandwith</th>
                        <th width="250px" class="text-center">Keterangan</th>
                        <th width="150px"  class="text-center">Harga</th>
                      </tr>
                    </thead>
                    <tbody>

                      <tr>
                        <td class="A14">{{ $marketingcustomer['nama_paket'] }}</td>
                        <td class="A14" style="text-align:center">{{ $marketingcustomer['bandwith'] }}</td>
                        <td class="A14">{{ $marketingcustomer['keterangan'] }}</td>
                        <td class="A14" style="text-align:right">{{ number_format($marketingcustomer['harga']) }}</td>
                      </tr>

                    </tbody>
                    <tfoot>
                      <tr>
                        <th colspan="3" style="text-align: right">Diskon</th>
                        <td style="text-align: right; border-bottom: none;">{{ number_format($marketingcustomer['jml_diskon']) }}</td>
                      </tr>
                      <tr>
                        <th colspan="3" style="text-align: right">Harga Instalasi</th>
                        <td style="text-align: right">{{ number_format($marketingcustomer['biaya_instalasi']) }}</td>
                      </tr>
                      <tr>
                        <th colspan="3" style="text-align: right">Ppn</th>
                        <td style="text-align: right">{{ number_format($marketingcustomer['jml_ppn']) }}</td>
                      </tr>
                      <tr>
                        <th colspan="3" style="text-align: right">Harga Total</th>
                        <td style="text-align: right; font-weight:bold;">{{ number_format($marketingcustomer['harga_total']) }}</td>
                      </tr>
                    </tfoot>
              </table>
              <div width="100%">
                <label>Catatan</label>
                <textarea>1.</textarea>
              </div>
          </div>
          </div>
          <div style="margin-top: 10px">  
        </div>


<table id="header" style="width:100%; border: 0; margin-top: 20px">
  <tr>
    <td style="width: 50%">
      <table style="width: 100%; border: 0;">
        <tr>
            <td style=" text-align:center; width:280px;">Hormat Kami </td>
          <td></td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<table id="ttd" style="width:100%; border: 0; margin-top: 75px">
  <tr>
    <td style="width: 50%">
      <table style="width: 100%; border: 0;">
      <tr>
          <td style=" text-align:center; width:280px; text-decoration: underline;">Heni Sartika</td>
          <td></td>
       </tr> 
      <tr>
          <td style=" text-align:center;">Finance</td>
          <td></td>
      </tr> 
      </table>
    </td>
  </tr>
</table>
        </main>
    </body>
</html>