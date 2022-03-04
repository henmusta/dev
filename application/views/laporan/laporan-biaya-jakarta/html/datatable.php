<div class="content">
	<div class="block">
		<div class="block-content">
			<form class="form-inline md-2" target="_blank" method="POST" action="<?= $module['url'];?>/print-out">
			<input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
				<label class="mr-2">Tanggal</label>
				<div class="input-group mr-2">
					<input type="text" class="form-control flatpickr" name="filter[start_date]" value="<?= date('Y-m-d')?>">
					<div class="input-group-append">
						<div class="input-group-text">s/d</div>
					</div>
					<input type="text" class="form-control flatpickr" name="filter[end_date]" value="<?= date('Y-m-d')?>">
				</div>
				<a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
				<button type="submit" class="btn btn-outline-primary"><i class="fa fa-print"></i> Cetak</button>
				<button id="excel" name="excel" type="button" class="btn btn-outline-primary"><i class="fas fa-download"></i>Cetak Excel</button>
			</form>
			<hr>
			<table id="dt" class="table table-sm table-vcenter table-bordered" width="100%">
				<thead>
					<tr>
							<th>No</th>
							<th style="width:10%">Tgl</th>
							<th>Mingguan</th>
							<th>PLN</th>
							<th>PAM</th>
							<th>Inernet/TV</th>
							<th>ATK</th>
							<th>Telpon</th>
							<th>Peralatan</th>
							<th>Iuran Rumah</th>
							<th>Plastik</th>
							<th>Tiket</th>
							<th>Kuli</th>
							<th>Dll</th>
							<th>Total</th>
							
					</tr>
				</thead>
				<tbody></tbody>
				<tfoot>
               <tr>
                <th colspan="2" style="text-align:left">Total keseluruhan:</th>
                <th></th>
            </tr>
        </tfoot>
			</table>
			
		</div>
	</div>
</div>