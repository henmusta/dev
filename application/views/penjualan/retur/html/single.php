<div class="content">    
    <div class="block block-rounded">
        <div class="block-header">
            <h3 class="block-title"><small>Tanggal Print : <?= date('l, d F Y')?></small></h3>
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
                    <div class="col-6 font-size-sm">
                        <p class="h3"><?= $pembelian->nomor;?></p>
                        <p class="mb-0">Tanggal Buat : </p>
                        <p class="mb-0">Tanggal Nota : </p>
                    </div>
                    <div class="col-6 text-right font-size-sm">
                        <p class="h3"><?= $pembelian->pemasok->nama;?></p>
                        <address><?= $pembelian->pemasok->alamat;?><br><?= $pembelian->pemasok->telp;?></address>
                    </div>
                </div>
                <div class="table-responsive push">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th colspan="5">Rincian Produk</th>
                            </tr>
                            <tr>
                                <th class="text-right" style="width: 60px;">No</th>
                                <th>Produk</th>
                                <th class="text-right" style="width: 120px;">Harga</th>
                                <th class="text-right" style="width: 120px;">Qty</th>
                                <th class="text-right" style="width: 120px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 0; foreach($pembelian->rincian_pembelian AS $produk) : $no++; ?>
                            <tr>
                                <td class="text-right"><?= $no;?></td>
                                <td><?= $produk->nama;?></td>
                                <td class="text-right"><?= number_format($produk->harga);?></td>
                                <td class="text-right"><?= number_format($produk->qty);?></td>
                                <td class="text-right"><?= number_format($produk->total);?></td>
                            </tr>
                            <?php endforeach;?>
                            <tr>
                                <td colspan="4" class="font-w600 text-right">Subtotal</td>
                                <td class="text-right"><?= number_format($pembelian->total_rincian);?></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="font-w600 text-right">Diskon</td>
                                <td class="text-right"><?= number_format($pembelian->diskon);?></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="font-w600 text-right">Tagihan</td>
                                <td class="text-right"><?= number_format($pembelian->total_tagihan);?></td>
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
                                <th>Nomor Giro</th>
                                <th>Tgl Giro</th>
                                <th class="text-right" style="width: 120px;">Nominal</th>
                                <th class="text-right" style="width: 120px;">Potongan</th>
                                <th class="text-right" style="width: 120px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 0; foreach($pembelian->rincian_pembayaran AS $row) : $no++; ?>
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
                                <td colspan="7" class="font-w600 text-right">Total Pembayaran</td>
                                <td class="text-right"><?= number_format($pembelian->pembayaran->nominal);?></td>
                            </tr>
                            <tr>
                                <td colspan="7" class="font-w600 text-right">Sisa Tagihan</td>
                                <td class="text-right"><?= number_format($pembelian->total_tagihan - $pembelian->pembayaran->nominal);?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
<!--                 <p class="font-size-sm text-muted text-center py-3 my-3 border-top">
                    Thank you very much for doing business with us. We look forward to working with you again!
                </p> -->
            </div>
        </div>
    </div>
</div>