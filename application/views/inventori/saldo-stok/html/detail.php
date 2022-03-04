<div class="content">
	<div class="block">
		<div class="block-content">
        <div class="block-header">
        <input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
            <h3 class="block-title"><small>Tanggal Print : <?=date_indo(date('Y-m-d'));?> </small></h3>
            <div class="block-options">
            <a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
            <button class="btn btn-outline-primary" onclick="One.helpers('print');"><i class="fa fa-print"></i> Cetak</button>
				<button id="excel" name="excel" type="button" class="btn btn-outline-primary"><i class="fas fa-download"></i>Cetak Excel</button>
            </div>
        </div>
        <form class="form-inline md-2" target="_blank" method="POST" action="<?= $module['url'];?>/print-out">
        <input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
        <input type="hidden" id="id_produk" name="filter[id_produk]" >
			
			</form>
			<hr>
			<hr>
            <div class="table-responsive push">
                
            <table id="pembelian" name="pembelian" class="table table-sm" width="100%">
                        <thead>
                            <tr>
                                <th colspan="9">Pembelian</th>
                            </tr>
                            <tr>
                                <th style="width: 10%;">No</th>
                                <th style="width: 20%;">Tanggal</th>
                                <th style="width: 20%;">Nomor Nota</th>
                                <th style="width: 30%;">Pemasok</th>
                                <th style="width: 20%;">QTY</th>   
                            </tr>
                        </thead>
                        <tbody>  
                        </tbody>
                        <tfoot>
                    <tr>
                        <th colspan="4" style="text-align:right">Total :</th>
                        <th></th>
                    </tr>
                        </tfoot>
            </table>

                <table id="penjualan" class="table table-sm" width="100%">
                      <thead>
                            <tr>
                                <th colspan="11">Penjualan</th>
                            </tr>
                            <tr>
                                <th style="width: 10%;">No</th>
                                <th style="width: 20%;">Tanggal</th>
                                <th style="width: 20%;">Nomor Nota</th>
                                <th style="width: 30%;">Pelanggan</th>
                                <th style="width: 20%;">QTY</th>   
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
               <tr>
                <th colspan="4" style="text-align:right">Total  :</th>
                <th></th>
            </tr>
        </tfoot>
                </table>
                <table id="retur" class="table table-sm" width="100%">
                      <thead>
                            <tr>
                                <th colspan="7">Retur</th>
                            </tr>
                            <tr>
                            <th style="width: 10%;">No</th>
                                <th style="width: 20%;">Tanggal</th>
                                <th style="width: 10%;">Nomor Nota</th>
                                <th style="width: 20%;">Pemasok</th>
                                <th style="width: 10%;">jenis</th>
                                <th style="width: 10%;">QTY</th>   
                            </tr>
                        </thead>
                        <tbody> 
                        </tbody>
                        <tfoot>
               <tr>
                <th colspan="5" style="text-align:right">Total  :</th>
                <th></th>
            </tr>
        </tfoot>
                </table>

                <table id="opname" name="opname" class="table table-sm" width="100%">
                        <thead>
                            <tr>
                                <th colspan="9">Stok Opname</th>
                            </tr>
                            <tr>
                                <th style="width: 10%;">No</th>
                                <th style="width: 20%;">Tanggal</th>
                                <th style="width: 20%;">Nomor Nota</th>
                                <th style="width: 30%;">Pemasok</th>
                                <th style="width: 20%;">QTY</th>   
                            </tr>
                        </thead>
                        <tbody>  
                        </tbody>
                        <tfoot>
                    <tr>
                        <th colspan="4" style="text-align:right">Total :</th>
                        <th></th>
                    </tr>
                        </tfoot>
            </table>

<div class="row justify-content-end">
    <div class="col-3">
    <label class="col-md-3">Pembelian</label>
            <input id="qtypembelian" name="qtypembelian" class="form-control form-control-sm text-right">
            <label class="col-md-3">Penjualan</label>
            <input id="qtypenjualan" name="qtypenjualan" class="form-control form-control-sm text-right" type="text" readonly>
            <label class="col-md-3">retur</label>
            <input id="qtyretur" name="qtyretur" class="form-control form-control-sm text-right" type="text" readonly>
            <label class="col-md-3">Opname</label>
            <input id="qtyopname" name="qtyopname" class="form-control form-control-sm text-right" type="text" readonly>
            <label class="col-md-3">Total</label>
            <input id="qtytotal" name="qtytotal" class="form-control form-control-sm text-right" type="text" readonly>
    </div>
</div>




            </div>
		</div>
	</div>
</div>
<!-- 
mudahsaja.com/cpanel
mudahsaj
NzsAl3q%fZ43 -->