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
                        <p class="h3"><?= $stok_opname->nomor;?></p>
                        <div class="row">
                            <div class="col">
                                <p class="mb-0">Tanggal Nota : <?= $stok_opname->tgl_opname;?></p>
                            </div>
                            <div class="col">
                               <p class="mb-0">Tanggal Buat : <?= $stok_opname->tgl_buat;?> </p>
                           </div>
                        </div>
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
                                <th class="text-center" style="width: 150px;">Qty Komputer</th>
                                <th class="text-center" style="width: 150px;">Qty Fisik</th>
                                <th class="text-center" style="width: 150px;">Qty Selisih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 0; foreach($stok_opname->rincian_stok_opname AS $produk) : $no++; ?>
                            <tr>
                                <td class="text-right"><?= $no;?></td>
                                <td><?= $produk->nama;?></td>
                                <td class="text-center"><?= number_format($produk->qty_komputer);?></td>
                                <td class="text-center"><?= number_format($produk->qty_fisik);?></td>
                                <td class="text-center"><?= number_format($produk->qty_selisih);?></td>
                            </tr>
                        <?php endforeach;?>
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