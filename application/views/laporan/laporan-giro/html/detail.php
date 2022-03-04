<div class="content">    
    <div class="block block-rounded">
    <div class="block-header">
    <h5 class="block-title"><?= $nama ;?></h5>
            <h3 class="block-title"><small>Tanggal Print : <?=date_indo(date('Y-m-d'));?> </small></h3>
            <div class="block-options">
                <a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
                <button class="btn btn-outline-primary" onclick="One.helpers('print');"><i class="fa fa-print"></i> Cetak</button>
				<button id="excel" name="excel" type="button" class="btn btn-outline-primary"><i class="fas fa-download"></i>Cetak Excel</button>
            </div>
        </div>
        <div class="block-content">
            <div class="py-2 px-4">
                <div class="table-responsive push">
                    <table class="table table-sm table-vcenter table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 60px;">No</th>
                                <th class="text-center" style="width: 300px;">no_giro</th>
                                <th>Nama Toko</th>
                                <th class="text-center">Tgl Giro</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-right" style="width: 300px;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 0; foreach($giro->rincian_giro AS $produk) : $no++; ?>
                            <tr>
                              <td class="text-center"><?= $no;?></td>
                                <td class="text-center"><?= $produk->nomor;?></td>
                                <td class="text-center"><?= $produk->toko;?></td>
                                <td class="text-center"><?= $produk->tgl_giro;?></td>
                                <td class="text-right"><?= number_format($produk->jumlah);?></td>
                                <td class="text-right">
                                    <input class="ket form-control form-control-sm text-right" value="<?= $produk->keterangan;?>" data-pk="<?= $produk->id;?>">
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
        <!-- <div class="block-content">

        </div> -->
    </div>
</div>
