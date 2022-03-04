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
                        <p class="h3"><?= $penjualan->nomor;?></p>
                        <p class="mb-0">Tanggal input : <?= $penjualan->tgl_buat;?> </p>
                       
                    </div>
                    <div class="col-6 text-right font-size-sm">
                        <p class="h3"><?= $penjualan->pelanggan->nama;?></p>
                        <address><?= $penjualan->pelanggan->alamat;?><br><?= $penjualan->pelanggan->telp;?></address>
                    </div>
                </div>
                <div class="table-responsive push">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th colspan="5">Rincian Nota</th>
                            </tr>
                            <tr>
                                <th class="text-right" style="width: 60px;">No</th>
                                <th>Pelanggan</th>
                                <th class="text-right" style="width: 120px;">Nota</th>
                                <th class="text-right" style="width: 120px;">Tanggal</th>
                                <th class="text-right" style="width: 120px;">Nota Pelunasan</th>
                                <th class="text-right" style="width: 120px;">Tagihan</th>
                                <th class="text-right" style="width: 120px;">Pelunasan</th>
                              
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 0; foreach($penjualan->nota AS $produk) : $no++; ?>
                            <tr>
                                <td class="text-right"><?= $no;?></td>
                                <td><?= $produk->pelanggan;?></td>
                                <td class="text-center"><?= $produk->nota ?></td>
                                <td > <?= $produk->tgl_input;?></td>
                                <td class="text-center"><?= $produk->nota_pelunasan ?></td>
                                <td class="text-right"><?= number_format($produk->total_tagihan);?></td>
                                <td class="text-right"><?= number_format($produk->total_pelunasan);?></td>
                          
                            </tr>
                            <?php endforeach;?>
                            <tr>
                                <td colspan="6" class="font-w600 text-right">Subtotal</td>
                                <td class="text-right"><?= number_format($penjualan->penjualan->total_rincian);?></td>
                            </tr>
                            <tr>
                                <td colspan="6" class="font-w600 text-right">Diskon</td>
                                <td class="text-right"><?= number_format($penjualan->penjualan->diskon);?></td>
                            </tr>
                            <tr>
                                <td colspan="6" class="font-w600 text-right">Tagihan</td>
                                <td class="text-right"><?= number_format($penjualan->penjualan->total_tagihan);?></td>
                            </tr>
                            <tr>
                                <td colspan="6" class="font-w600 text-right">Sisa Tagihan</td>
                                <td class="text-right"><?= number_format($penjualan->penjualan->sisa_tagihan);?></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th colspan="8">Rincian Pembayaran</th>
                            </tr>
                            <tr>
                                <th class="text-right" style="width: 60px;">No</th>
                                <th>Metode</th>
                                <th>Dari Kas/Bank</th>
                                <!-- <th>Nomor Giro</th>
                                <th>Tgl Giro</th> -->
                                <th class="text-right" style="width: 120px;">Nominal</th>
                                <th class="text-right" style="width: 120px;">Potongan</th>
                                <th class="text-right" style="width: 120px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 0; foreach($penjualan->rincian AS $row) : $no++; ?>
                            <tr>
                                <td class="text-right"><?= $no;?></td>
                                <td><?= ucfirst($row->metode);?></td>
                                <td><?= $row->nama_akun;?></td>
                          
                                <td class="text-right" style="width: 120px;"><?= number_format($row->nominal);?></td>
                                <td class="text-right" style="width: 120px;"><?= number_format($row->potongan);?></td>
                                <td class="text-right" style="width: 120px;"><?= number_format($row->total);?></td>
                            </tr>
                            <?php endforeach;?>
                            <tr>
                                <td colspan="5" class="font-w600 text-right">Total Pelunasan</td>
                                <td class="text-right"><?= number_format($penjualan->penjualan->total_pelunasan);?></td>
                            </tr>
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>