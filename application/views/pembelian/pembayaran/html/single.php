<div class="content">    
    <div class="block block-rounded">
        <div class="block-header">
            <h3 class="block-title"><small>Tanggal Print : <?=date_indo(date('Y-m-d'));?> </small></h3>
            <div class="block-options">
                <a href="javascript:history.back();" class="btn-block-option"><i class="fa fa-reply"></i> Kembali</a>
                <button type="button" class="btn-block-option" onclick="One.helpers('print');">
                    <i class="si si-printer mr-1"></i> Print Faktur
                </button>
            </div>
        </div>
        <div class="block-content">
        <div class="logo">
        <img src="<?php echo base_url() ?>assets\media\photos\<?php echo $aplikasi->gambar; ?>" alt="" style="width: 180px; height: 80px; margin-right: 10px;">
		</div>
            <div class="py-2 px-4">
                <div class="row mb-4">
                    <div class="col-6 font-size-sm">
                        <p class="h3"><?= $pembayaran->nomor;?></p>
                        <p class="mb-0">Tanggal Bayar : <?= $pembayaran->tgl_bayar;?> </p>
                    </div>
                    <div class="col-6 text-right font-size-sm">
                        <p class="h3"><?= $pembayaran->pemasok->nama;?></p>
                        <address><?= $pembayaran->pemasok->alamat;?><br><?= $pembayaran->pemasok->telp;?></address>
                    </div>
                </div>
                <div class="table-responsive push">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th colspan="5">Rincian Produk</th>
                            </tr>
                            <tr>
                                <th class="text-center">No</th>
								<th class="text-center">Tgl Bayar</th>
								<th class="text-center">Tgl Buat</th>
								<th class="text-center">Supplier</th>
								<th class="text-center">No Nota Pembelian</th>
								<th class="text-center">Nominal</th>
						
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 0; foreach($pembayaran->nota AS $produk) : $no++; ?>
                            <tr>
                                <td class="text-center"><?= $no;?></td>
                                <td><?= $produk->tgl_bayar;?></td>
                                <td class="text-left"><?= $produk->tgl_buat;?></td>
                                <td class="text-center"><?= $produk->nama_p;?></td>
                                <td class="text-center"><?= $produk->nomor;?></td>
                                <td class="text-right"><?= number_format($produk->total_tagihan);?></td>
                               
                            </tr>
                            <?php endforeach;?>
                            <tfoot>
                                <?php
                                $totaltagihan = 0;
                                foreach($pembayaran->nota AS $row) {
                                    $row = (object)$row;
                                    $totaltagihan += $row->total_tagihan;
                                }
                                echo '<tr>
                                <td colspan="4"></td>
                                <td>Total Tagihan</td>
                                <td  class="text-right">Rp. ' .    number_format($totaltagihan) . '</td>';
                            
                                $totalbayar = 0;
                                foreach($pembayaran->rincian AS $row) {
                                    $row = (object)$row;
                                    $totalbayar += $row->total;
                                }
                                echo '<tr>
                                <td colspan="4"></td>
                                <td>Dibayarkan</td>
                                <td  class="text-right">Rp.  ' .   number_format($totalbayar) . '</td>';
                               
                                $sisatagihan = ($totaltagihan - $totalbayar);
                                echo '<tr>
                                <td colspan="4"></td>
                                <td>Sisa Tagihan</td>
                                <td  class="text-right">Rp. ' . number_format($sisatagihan) . '</td>';
                                ?>
					        </tfoot>
         
                        </tbody>
                    </table>
                    <table class="table table-sm">
							<thead>
								<tr>
									<th colspan="7">Rincian Pembayaran</th>
								</tr>
								<tr>
                                    <th>No</th>
									<th>Metode</th>
									<th>Dari Kas/Bank</th>
									<th>Nomor Giro</th>
									<th>Tgl Giro</th>
									<th>Nominal</th>
									<th class="text-right">Total</th>
								</tr>
							</thead>
                            <tbody>
                            <?php $no = 0; foreach($pembayaran->rincian AS $row) : $no++; ?>
                            <tr>
                                <td class="text-right"><?= $no;?></td>
                                <td><?= ucfirst($row->metode);?></td>
                                <td><?= $row->nama_akun;?></td>
                                <td><?= $row->nomor_giro;?></td>
                                <td><?= $row->tgl_giro;?></td>
                                <td class="text-right" style="width: 120px;"><?= number_format($row->nominal);?></td>
                                <td class="text-right" style="width: 120px;"><?= number_format($row->total);?></td>
                            </tr>
                            <?php endforeach;?>
                         
                        </tbody>
                        <tfoot>
                        <?php
                           $totalbayar = 0;
                           foreach($pembayaran->rincian AS $row) {
                               $row = (object)$row;
                               $totalbayar += $row->total;
                           }
                           echo '<tr>
                           <td colspan="5"></td>
                           <td>Total Pembayaran</td>
                           <td  class="text-right"> Rp. ' .   number_format($totalbayar) . '</td>';
                        ?>
                        </tfoot>	
						</table>

                   
                </div>
            </div>
        </div>
    </div>
</div>