<div class="content">    
    <div class="block block-rounded">
        <div class="block-header">
            <h3 class="block-title"><small>Tanggal Print : <?= date('l, d F Y')?></small></h3>
            <div class="block-options">
                <a href="javascript:history.back();" class="btn-block-option"><i class="fa fa-reply"></i> Kembali</a>
            </div>
        </div>
        <div class="block-content">
            <div class="py-2 px-4">
                <div class="row mb-4">
                    <div class="col-6 font-size-sm">
                    <input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
                        <p class="h3"><?= $pembelian->nomor;?></p>
                        <p class="mb-0">Tanggal Input : <?= $pembelian->tgl_buat;?></p>
                        <p class="mb-0">Tanggal Nota : <?= $pembelian->tgl_nota;?></p>
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
                                <th class="text-right" style="width: 60px;">No</th>
                                <th class="text-right" style="width: 120px;">Qty</th>
                                <th>Produk</th>
                                <th class="text-right" style="width: 120px;">Harga Beli</th>
                                <th class="text-right" style="width: 120px;">Harga Jual</th>
                                <th class="text-right" style="width: 120px;">Satuan</th>
                                <th class="text-right" style="width: 120px;">Kode Laba</th>
                                <th class="text-right" style="width: 120px;">Laba</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 0; foreach($pembelian->rincian_pembelian AS $produk) : $no++; ?>
                            <tr>
                                <td class="text-right"><?= $no;?></td>
                                <td class="text-right"><?= number_format($produk->qty);?></td>
                                <td><?= $produk->nama;?></td>
                                <td class="text-right">
                                    <input class="autonumeric harga-beli form-control form-control-sm text-right" value="<?= $produk->harga;?>" readonly="readonly">
                                </td>
                                <td class="text-right">
                                    <input class="autonumeric harga-jual form-control form-control-sm text-right" value="<?= $produk->harga_jual;?>">
                                </td>
                                <td class="text-right">
                                <input class="form-control satuan form-control-sm text-right" id="satuan" value="<?php echo $this->cabang->satuan;?>" readonly="readonly"> 
                                </td>
                                <td class="text-right">
                                    <input class="form-control kode-laba form-control-sm" value="<?= $produk->kode_laba;?>" readonly="readonly">
                                </td>
                                <td class="text-right">
                                    <input class="autonumeric laba form-control form-control-sm text-right" value="<?= $produk->laba;?>" readonly="readonly" data-pk="<?= $produk->id_produk;?>" data-ps="<?php echo $this->cabang->satuan;?>" data-cb="<?php echo $this->cabang->id;?>">
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>