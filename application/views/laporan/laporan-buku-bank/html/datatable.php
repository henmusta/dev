<div class="content">
	<div class="block">
		<div class="block-content">
			<form class="form-inline md-2" target="_blank" method="POST" action="<?= $module['url'];?>/print-out">
			<input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
				<label class="mr-2">Tanggal</label>
				<div class="input-group mr-2">
					<input type="text" class="form-control flatpickr" name="filter[date_start]" value="<?= date('Y-m-d')?>">
					<div class="input-group-append">
						<div class="input-group-text">s/d</div>
					</div>
					<input type="text" class="form-control flatpickr" name="filter[date_end]" value="<?= date('Y-m-d')?>">
				</div>
				<a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
				<button type="submit" class="btn btn-outline-primary"><i class="fa fa-print"></i> Cetak</button>
				<button id="excel" name="excel" type="button" class="btn btn-outline-primary"><i class="fas fa-download"></i>Cetak Excel</button>
			</form>
			<hr>
			<div class="row justify-content-end">
				<div class="col-4">
					<div class="alert alert-dismissible alert-primary">
						Saldo Awal : <span id="saldoAwal" style="text-align: right">10100</span>
					</div>
				</div>
			</div>
			<table id="dt" class="table table-sm table-vcenter table-bordered" width="100%">
				<thead>
					<tr>
						<th rowspan="2" class="text-center">Tanggal Nota</th>
						<th rowspan="2" class="text-center">Keterangan</th>
						<th colspan="2" class="text-center">Uraian</th>
						<th rowspan="2" class="text-center">Debit</th>
						<th rowspan="2" class="text-center">Kredit</th>
						<th rowspan="2" class="text-center">Saldo</th>
					</tr>
					<tr>
						<th class="text-center">Pemasok</th>
						<th class="text-center">Nomor</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</div>
<style>
.num {
  mso-number-format:General;
}
</style>