<div class="content">    
    <div class="block block-rounded">
        <div class="block-header">
        <div class="d-flex w-100 mb-5 mt-3">
		</div>
            <h3 class="block-title"><small>Faktur NO : <?= $penjualan->nomor;?></small></h3>
            <!-- <h3 class="block-title"><small>Tanggal Print : <?= longdate_indo(date('Y-m-d')); ?></small></h3> -->
            <div class="block-options">
                <a href="javascript:history.back();" class="btn-block-option"><i class="fa fa-reply"></i> Kembali</a>
                <button type="button" class="btn-block-option" onclick="One.helpers('print');">
                    <i class="si si-printer mr-1"></i> Print Faktur
                </button>
            </div>
        </div>
        <div class="block-content">
            <div class="py-2 px-4">
                <div class="row mb-4">
                    <div class="col-5 font-size-sm text-center">
                    <div class="logo">
				        <img src="<?php echo base_url() ?>assets\media\photos\<?php echo $aplikasi->gambar; ?>" alt="" style="width: 180px; height: 80px; margin-right: 10px;">
			        </div>
                        <p>
                        <?= $penjualan->cabang->alamat;?><br>
                        <?= $penjualan->cabang->telp;?><br>
                        <?= $penjualan->cabang->wa;?><br>
                        </p>
                    </div>
                    <div class="col-2"></div>
                    <div class="col-5 text-left font-size-sm">
                        <p>Bandar Lampung, <?= $penjualan->tgl_nota;?></p>
                        <p class="h6">Yth Tuan/Toko</p>
                        <p class="h6"><?= $penjualan->pelanggan->nama;?></p>
                        <address>Di <?= $penjualan->pelanggan->alamat;?><br><?= $penjualan->pelanggan->telp;?></address>
                    </div>
                </div>
                <div class="table-responsive push">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th colspan="6">Rincian Produk</th>
                            </tr>
                            <tr>
                                <th class="text-center" style="width: 60px;">No</th>
                                <th class="text-center" style="width: 120px;">Banyaknya </th>
                                <th>Pemasok</th>
                                <th>Produk</th>
                                <th class="text-right" style="width: 120px;">Harga</th>
                                <th class="text-right" style="width: 120px;">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 0; foreach($penjualan->rincian_penjualan AS $produk) : $no++; ?>
                            <tr>
                                <td class="text-center"><?= $no;?></td>
                                <td class="text-center"><?= number_format($produk->qty);?></td>
                                <td><?= $produk->nama_pemasok;?></td>
                                <td><?= $produk->nama;?></td>
                                <td class="text-right"><?= number_format($produk->harga);?></td>
                                <td class="text-right"><?= number_format($produk->total);?></td>
                            </tr>
                            <?php endforeach;?>
                            <tr>
                                <td colspan="5" class="font-w600 text-right">Subtotal</td>
                                <td class="text-right"><?= number_format($penjualan->total_rincian);?></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="font-w600 text-right">Total Retur</td>
                                <td class="text-right"  style="text-decoration: underline;"><?= number_format($penjualan->retur->nominal);?></td>
                            </tr><hr>
                            <tr>
                                <td colspan="5" class="font-w600 text-right">Cek Nota</td>
                                <td class="text-right"><?= number_format($penjualan->chek);?></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="font-w600 text-right">Diskon</td>
                                <td class="text-right"><?= number_format($penjualan->diskon);?></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="font-w600 text-right">total Jumlah Tagihan</td>
                                <td class="text-right"><?= number_format($penjualan->total_rincian - $penjualan->retur->nominal - $penjualan->diskon + $penjualan->chek);?></td>
                            </tr>
                            <!-- <tr>
                                <td colspan="4" class="font-w600 text-right">Tagihan</td>
                                <td class="text-right"><?= number_format($penjualan->sisa_tagihan);?></td>
                            </tr> -->
                        </tbody>
                    </table>

                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th colspan="5">Rincian Retur Penjualan</th>
                            </tr>
                            <tr>
                                <th class="text-center" style="width: 60px;">No</th>
                                <th class="text-center" style="width: 120px;">qty </th>
                                <th>Produk</th>
                                <th class="text-right" style="width: 120px;">Harga</th>
                                <th class="text-right" style="width: 120px;">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 0; foreach($penjualan->rincian_retur AS $produk) : $no++; ?>
                            <tr>
                                <td class="text-center"><?= $no;?></td>
                                <td class="text-center"><?= number_format($produk->qty);?></td>
                                <td><?= $produk->nama_produk;?></td>
                                <td class="text-right"><?= number_format($produk->harga);?></td>
                                <td class="text-right"><?= number_format($produk->nominal);?></td>
                            </tr>
                            <?php endforeach;?>
                            <tr>
                                <td colspan="4" class="font-w600 text-right">Totalretur</td>
                                <td class="text-right"><?= number_format($penjualan->retur->nominal);?></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th colspan="8">Rincian pelunasan</th>
                            </tr>
                            <tr>
                                <th class="text-right" style="width: 60px;">No</th>
                                <th>Metode</th>
                                <th>Dari Kas/Bank</th>
                                <th>Nomor Giro</th>
                                <th>Tgl Giro</th>
                                <th class="text-right" style="width: 120px;">Nominal</th>
                                <th class="text-right" style="width: 120px;">Potongan</th>
                                <th class="text-right" style="width: 120px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 0; foreach($penjualan->rincian_pelunasan AS $row) : $no++; ?>
                            <tr>
                                <td class="text-right"><?= $no;?></td>
                                <td><?= ucfirst($row->metode);?></td>
                                <td><?= $row->nama_akun;?></td>
                                <td><?= $row->nomor_giro;?></td>
                                <td><?= $row->tgl_giro;?></td>
                                <td class="text-right" style="width: 120px;"><?= number_format($row->nominal);?></td>
                                <td class="text-right" style="width: 120px;"><?= number_format($row->potongan);?></td>
                                <td class="text-right" style="width: 120px;"><?= number_format($row->total);?></td>
                            </tr>
                            <?php endforeach;?>
                            <tr>
                                <td colspan="7" class="font-w600 text-right">Total Pelunasan</td>
                                <td class="text-right"><?= number_format($penjualan->pelunasan->nominal);?></td>
                            </tr>
                            <!-- <tr>
                                <td colspan="7" class="font-w600 text-right">Total Retur</td>
                                <td class="text-right"  style="text-decoration: underline;"><?= number_format($penjualan->retur->nominal);?></td>
                            </tr><hr> -->
                            <tr>
                                <td colspan="7" class="font-w600 text-right">Total Pembayaran</td>
                                <td class="text-right"><?= number_format($penjualan->pelunasan->nominal);?></td>
                            </tr>
                            <tr>
                                <td colspan="7" class="font-w600 text-right">Sisa Tagihan</td>
                                <td class="text-right"><?= number_format( ($penjualan->total_tagihan - $penjualan->total_pelunasan));?></td>
                            </tr>
                        </tbody>
                    </table>
                   
                </div>
                <div class="col-md-4 col-xl-3">
                    <div class="block block-rounded block-bordered">
                        <div class="block-header">
                            <h3 class="block-title">PERHATIAN !!!</h3>
                        </div>
                        <div class="block-content">
                            <p>
                            Barang yang sudah dibeli tidak dapat
                            ditukar / dikembalikan
                            kecuali ada perjanjian sebelumnya
                            </p>
                        </div>
                    </div>
                </div>
<!--                 <p class="font-size-sm text-muted text-center py-3 my-3 border-top">
                    Thank you very much for doing business with us. We look forward to working with you again!
                </p> -->
            </div>
        </div>
    </div>
</div>
