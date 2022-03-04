<div class="content">
	<div class="block">
		<div class="block-content">
        <form class="form-inline md-2" target="_blank" method="POST" action="<?= $module['url'];?>/print-out">
			<input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
				<label class="mr-2">Tanggal</label>
				<div class="input-group mr-2">
					<input type="text" class="form-control flatpickr" id="date_start" name="filter[date_start]" value="<?= date('Y-m-d')?>">
					<div class="input-group-append">
						<div class="input-group-text">s/d</div>
					</div>
					<input type="text" class="form-control flatpickr" id="date_end" name="filter[date_end]" value="<?= date('Y-m-d')?>">
				</div>
                <a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
				<button type="submit" class="btn btn-outline-primary"><i class="fa fa-print"></i> Cetak</button>
				<button id="excel" name="excel" type="button" class="btn btn-outline-primary"><i class="fas fa-download"></i>Cetak Excel</button>
			</form>
			<hr>
			<hr>
            <div class="table-responsive push">                
                <table id="tunai" class="table table-sm" width="100%">
                        <thead>
                            <tr>
                                <th colspan="9">Pembelian Cash/debit</th>
                            </tr>
                            <tr>
                                <th class="text-right" style="width: 60px;">No</th>
                                <th>Nama Toko</th>
                                <th>tanggal</th>
                                <th>No Nota</th>
                                <th>Metode</th>
                                <th>Jumlah</th>
                                <th class="text-right" style="width: 120px;">Diskon</th>
                                <th class="text-right" style="width: 120px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>  
                        </tbody>
                        <tfoot>
               <tr>
                <th colspan="7" style="text-align:right">Total Cash/debit :</th>
                <th></th>
            </tr>
        </tfoot>
                    </table>
                    <table id="giro" class="table table-sm" width="100%">
                      <thead>
                            <tr>
                                <th colspan="11">Pembelian Giro</th>
                            </tr>
                            <tr>
                                <th class="text-right" style="width: 60px;">No</th>
                                <th>Nama Toko</th>
                                <th>tanggal</th>
                                <th>No Nota</th>
                                <th>Tgl Giro</th>
                                <th>No Giro</th>
                                <th>Jumlah</th>
                                <th class="text-right" style="width: 120px;">Diskon</th>
                                <th class="text-right" style="width: 120px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
               <tr>
                <th colspan="7" style="text-align:right">Total Giro :</th>
                <th></th>
            </tr>
        </tfoot>
                </table>
                <table id="bon" class="table table-sm" width="100%">
                      <thead>
                            <tr>
                                <th colspan="5">Pembelian Bon</th>
                            </tr>
                            <tr>
                                <th class="text-right" style="width: 60px;">No</th>
                                <th>Nama Toko</th>
                                <th>tanggal</th>
                                <th>No Nota</th>
                                <th class="text-right" style="width: 120px;">Total</th>
                            </tr>
                        </thead>
                        <tbody> 
                        </tbody>
                        <tfoot>
               <tr>
                <th colspan="4" style="text-align:right">Total Bon :</th>
                <th></th>
            </tr>
        </tfoot>
                </table>
            </div>
		</div>
	</div>
</div>
<!-- 
mudahsaja.com/cpanel
mudahsaj
NzsAl3q%fZ43 -->