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
                        <p class="h3"><?= $pembelian->nomor;?></p>
                        <p class="mb-0">Tanggal Buat : <?= $pembelian->tgl_buat;?> </p>
                        <p class="mb-0">Tanggal Nota : <?= $pembelian->tgl_nota;?> </p>
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
                                <th style="width: 120px;">Produk</th>
                                <th class="text-right" style="width: 120px;">Qty</th>
                                <th class="text-right" style="width: 120px;">Penerimaan</th>
                                <th class="text-right" style="width: 120px;">Sisa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                             $sum_qty = 0;
                             $sum_penerimaan = 0;
                             $sum_sisa = 0;
                             $no = 0; 
                             foreach($pembelian->rincian_pembelian AS $produk) : $no++;
                             $sum_penerimaan += $produk->qty_diterima;
                             $sum_qty += $produk->qty;
                             $sum_sisa += $produk->sisa_qty;
                            ?>
                            <tr>
                                <td class="text-right"><?= $no;?></td>
                                <td><?= $produk->nama;?></td>
                                <td class="text-right"><?= number_format($produk->qty);?></td>
                                <td class="text-right"><?= number_format($produk->qty_diterima);?></td>
                                <td class="text-right"><?= number_format($produk->sisa_qty);?></td>
                            </tr>
                            <?php endforeach;?>
                          
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="font-w600 text-right">Subtotal</td>
                                <td class="text-right"><?= number_format($sum_qty);?></td>
                                <td class="text-right"><?= number_format($sum_penerimaan);?></td>
                                <td class="text-right"><?= number_format($sum_sisa);?></td>
                            </tr>
                       </tfoot>
                    </table>
     
                </div>
            </div>
        </div>
    </div>
</div>