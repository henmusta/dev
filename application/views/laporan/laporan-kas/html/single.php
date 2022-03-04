<div class="content">    
    <div class="block block-rounded">
        <div class="block-header">
            <h3 class="block-title"><small>Tanggal Print : <?=date_indo(date('Y-m-d'));?> </small></h3>
            <div class="block-options">
            <a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
            <button class="btn btn-outline-primary" onclick="One.helpers('print');"><i class="fa fa-print"></i> Cetak</button>
				<button id="excel" name="excel" type="button" class="btn btn-outline-primary"><i class="fas fa-download"></i>Cetak Excel</button>
            </div>
        </div>
        <div class="block-content">
            <div class="py-2 px-4">
                <div class="row mb-4">
                    <div class="col-6 font-size-sm">
                        <!-- <p class="mb-0">Bulan : <?= $kas->tgl_buat;?> </p> -->

                    </div>
                </div>
            <div class="table-responsive push">
                
                <table class="table table-sm table-vcenter table-bordered" id="myTable">
                        <thead>
                            <tr>
                                <th colspan="9">Laporan Kas</th>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Debit</th>
                                <th>Kredit</th>
                                <th class="font-w600 text-right">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                    <?php 
              $a=array();
            //   $b=array();
              foreach($kas->rincian AS $row){
                if(!in_array($row->tanggal,$a)) 
                    { 
                    array_push($a,$row->tanggal);
                    echo '
                            <tr class="info">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="font-w600 text-right">Rp.'.number_format($row->nominal).'</td>
                            </tr>
                            ';
                        }

              echo "<tr>";
              echo "<td>{$row->tanggal}</td>";
              echo "<td>{$row->keterangan}</td>";
              if ($row->debit == 0)
              {
                echo '<td>-</td>';
              }else{
                echo '<td>Rp.' .number_format($row->debit). '</td>';
              }

              if ($row->kredit == 0)
              {
                echo '<td>-</td>';
              }else{
                echo '<td>Rp.' .number_format($row->kredit). '</td>';
              }
          
              echo "</tr>";
            } 
            ?>
                </tbody>
                <tfoot>
						<?php
						// $totaldebit = 0;
						// foreach($kas->rincian AS $row) {
						// 	$row = (object)$row;
						// 	$totaldebit += $row->debit;
						// }
                        // $totalkredit = 0;
						// foreach($kas->rincian AS $row) {
						// 	$row = (object)$row;
						// 	$totalkredit += $row->kredit;
						// }
                        // $saldoawal = $row->nominal;
                        // $biaya = $row->biaya;
                        // $saldoakhir = ( $totaldebit) - $totalkredit;
		
						echo '<tr>
						<th colspan="4" class="font-w600 text-right">Saldo Akhir</th>
						<th class="font-w600 text-right">'.'Rp. '. number_format($kas->rumus) . '</th>
						</tr>';
						?>
				</tfoot>
					
                    </table>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $( "#excel" ).click(function() {
            var   tgl  = <?= $tgl ;?>;
            var   cb    = <?= $cb ;?>;
         window.open("<?php echo site_url('laporan/laporan-kas/excel/')?>"+tgl+'/'+cb)
        });
       var span = 1;
       var prevTD = "";
       var prevTDVal = "";
       $("#myTable tr td:nth-child(1)").each(function() {
          var $this = $(this);
          if ($this.text() == prevTDVal) {
             span++;
             if (prevTD != "") {
                prevTD.attr("rowspan", span);
                $this.remove();
             }
          } else {
             prevTD     = $this;
             prevTDVal  = $this.text();
             span       = 1;
          }
       });
    });
</script>