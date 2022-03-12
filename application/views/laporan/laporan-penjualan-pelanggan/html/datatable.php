<div class="content">
	<div class="block">
		<div class="block-content">
			<form class="form-inline md-2" target="_blank" method="POST" action="<?= $module['url'];?>/print-out">
				<label class="mr-2">Tanggal</label>
				<div class="input-group mr-2">
					<input type="text" class="form-control flatpickr" name="filter[date_start]" value="<?= date('Y-m-d')?>">
					<input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
					<div class="input-group-append">
						<div class="input-group-text">s/d</div>
					</div>
					<input type="text" class="form-control flatpickr" name="filter[date_end]" value="<?= date('Y-m-d')?>">
					<input type="text" class="form-control" name="filter[customer_id]" value="1">
				</div>
				<!-- <button type="submit" class="btn btn-outline-primary"><i class="fa fa-print"></i> Cetak</button> -->
			</form>
			<hr>
			<table id="dt" class="table table-sm table-vcenter table-bordered" width="100%">
				<thead>
					<tr>
                        <th>Nomor Nota</th>
						<th>Tanggal</th>
						<th>Jumlah Pembelian</th>
						<th>Diskon</th>
						<th>Cek Nota</th>
						<th>Retur</th>
						<th>Laba Jual</th>
						<th>Laba Retur</th>
						<th>Pembelian Bersih</th>
						<th>Laba Akhir</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</div>