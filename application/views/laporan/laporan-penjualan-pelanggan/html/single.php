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
            <div class="py-2 px-4">
            <div class="row mb-4">
                    <div class="col-6 font-size-sm">
                        <p class="h3">Laporan Penjualan</p>
                        <img src="<?php echo base_url() ?>assets\media\photos\logo.jpeg" alt="" style="width: 100px; height: auto; margin-left: 22px;">
                    </div>
                    <div class="col-6 text-right font-size-sm">
                    <p class="mb-0">Tanggal Nota : <?= $penjualan->tgl_nota;?> </p>
                    </div>
                </div>
            <div class="table-responsive push">
                
                <table class="table table-sm table-vcenter table-bordered">
                        <thead>
                           
                            <tr>
                                <th class="text-right" style="width: 60px;">No</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>No Nota</th>
                                <th>Jumlah Pembelian</th>
                                <th>Potongan</th>
                                <th>Pembelian Bersih</th>
                                <th>Laba</th>
                                <th>Kode Laba</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 0; foreach($penjualan->rincian_penjualan AS $produk) : $no++; ?>
                            <tr>
                                <td class="text-right"><?= $no;?></td>
                                <td><?= $produk->nama_p;?></td>
                                <td><?= $produk->alamat;?></td>
                                <td><?= $produk->nomor;?></td>
                                <td class="text-right"><?= number_format($produk->jumlah);?></td>
                                <td class="text-right"><?= number_format($produk->potong);?></td>
                                <td class="text-right"><?= number_format($produk->total);?></td>
                                <td><?= $produk->laba;?></td>
                                <td><?= $produk->kode_laba;?></td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
     
                <table class="table table-sm">
                        <tbody>
                            <tr>
                                <td colspan="8" class="font-w600 text-right">Total pembelian Keseluruhan</td>
                                <!-- <td class="text-right"><?= number_format($pembelian->total_pembayaran);?></td> -->
                            </tr>
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>