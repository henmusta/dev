<?php 
$transaksi = isset($data) ? $data : (object)[];
?>
<div class="content">
	<div class="row">
    <div class="col-md-12">
    <form id="form" method="POST" action="<?= isset($module['action']) ? $module['action'] : NULL ;?>" autocomplete="off">
    <input type="hidden" id="id_cabang" name="kasir[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
        <input type="hidden" name="pk" value="<?= isset($data->id) ? $data->id : NULL ;?>">
                <div class="block">
                    <div class="block-header">
                        <h3 class="block-title"><?= isset($module['name']) ? $module['name'] : NULL ;?></h3>
                        <div class="block-options">
                            <div class="btn-group btn-group-sm">
                                <a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
                                <button type="submit" class="btn btn-outline-primary"><i class="fa fa-save"></i> Simpan</button>
                            </div>
                        </div>
                    </div>
                    <div class="block-content">
                    <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-row">
                                        <label class="col-md-3">Tanggal</label>
                                        <div class="col-md-9">
                                            <input id="tgl-nota" type="text" class="form-control datepicker" name="kasir[tgl_nota]" required="required" value="<?= isset($tgl) ? $tgl : date('Y-m-d');?>">
                                        </div>
                                    </div>
                                    <div class="form-group form-row">
                                        <label class="col-md-3">Kas Awal</label>
                                        <div class="col-md-9">
                                            <input id="kasawal" type="text" class="form-control" name="kasir[modal]" required="required" value="<?= isset($data->modal) ? $data->modal : NULL;?>">
                                            <input type="hidden" name="kasir[id_kredit]" value="5">
                                        </div>
                                    </div>
                                    <div class="form-group form-row">
                                        <label class="col-md-3">Penjualan</label>
                                        <div class="col-md-9">
                                            <input id="penjualan" type="text" class="form-control" name="kasir[penjualan]" required="required"  readonly="readonly" value="<?= isset($data->penjualan) ? $data->penjualan : NULL;?>">
                                        </div>
                                    </div>
                                    <div class="form-group form-row">
                                        <label class="col-md-3">Setoran</label>
                                        <div class="col-md-9">
                                            <input id="setoran" type="text" class="form-control" name="kasir[setoran]" required="required"  readonly="readonly" value="<?= isset($data->setoran) ? $data->setoran : NULL;?>">
                                        </div>
                                    </div>
                                <div class="table-responsive push">
                                <table id="table-items" class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Akun Kas</th>
                                                <th>Nominal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-delete-row"><i class="fa fa-minus"></i></button>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-add-row"><i class="fa fa-plus"></i></button>
                                                </div>
                                            </th>			
                                        </tr>                             
                                     </tfoot>
                                </table>
                            </div>

                            <div class="form-group form-row">
                                <button type="button" class="col-md-3" id="opnbiaya" name="opnbiaya">Biaya</button>
                                    <div class="col-md-9">
                                        <input type="text" id="totalbiaya" name="totalbiaya" class="form-control" name="kasir[totalbiaya]" required="required"  readonly="readonly" value="<?= isset($data->biaya) ? $data->biaya : NULL;?>">
                                    </div>
                            </div>
                            <div id="data-biaya" name="data-biaya" class="hidden">
                                <div class="table-responsive ">
                                <table id="table-biaya" name="table-biaya" class="table table-sm" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>Akun Kas</th>
                                                <th>Nominal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                    </tbody>
                                    <tfoot>
                                        <!-- <tr>
                                            <th>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-delete-row"><i class="fa fa-minus"></i></button>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-add-row"><i class="fa fa-plus"></i></button>
                                                </div>
                                            </th>			
                                        </tr>                              -->
                                     </tfoot>
                                </table>
                            
                            </div>
                                    <div class="form-group form-row">
                                        <label class="col-md-3">Kas Akhir</label>
                                        <div class="col-md-9">
                                            <input id="kasakhir" type="text" class="form-control" name="kasir[kasakhir]" required="required" value="<?= isset($data->nominal) ? $data->nominal : NULL;?>">
                                        </div>
                                    </div>

                                    <div class="form-group form-row">
                                        <label class="col-md-3">Rumus</label>
                                        <div class="col-md-9">
                                            <input id="rumus" type="text" class="form-control" name="kasir[rumus]" required="required"  readonly="readonly" value="<?= isset($data->rumus) ? $data->rumus : NULL;?>">
                                        </div>
                                    </div>

                                    <div class="form-group form-row">
                                        <label class="col-md-3">Register</label>
                                        <div class="col-md-9">
                                            <input id="register" type="text" class="form-control" name="kasir[register]" required="required"  readonly="readonly" value="<?= isset($data->register) ? $data->register : NULL;?>">
                                        </div>
                                    </div>

                                    <div class="form-group form-row">
                                        <label class="col-md-3">cek register : </label> <input id="cek" type="text" class="form-control" name="cek" required="required"  readonly="readonly" value="">
                                        <!-- <>This is a paragraph.</p> -->
                                    </div>

                                    
                            </div>
                      </div>
                    </div>
                    </div>
                </div>
            </div>	
		</form>
    </div>
</div>

