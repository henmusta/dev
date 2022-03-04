<div class="content">
	<div class="row">
		<div class="col-md-12">
        <div class="block">
		<div class="block-content">
        <div class="row">
				<div class="col-4">
					<div class="alert alert-dismissible alert-primary">
						<span id="barangmasuk" style="text-align: left"> Barang Masuk</span>
					</div>
				</div>
			</div>
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
				<button type="submit" class="btn btn-outline-primary"><i class="fa fa-print"></i> Cetak</button>
			</form>
			<hr>
			<table id="dt" class="table table-sm table-vcenter table-bordered" width="100%">
				<thead>
					<tr>
						<th>Tanggal Nota</th>
						<th>Keterangan</th>
						<th>Uraian</th>
						<th>Debit</th>
						<th>Kredit</th>
						<th>Saldo</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>

    <div class="block">
		<div class="block-content">
        <div class="row">
				<div class="col-4">
					<div class="alert alert-dismissible alert-primary">
                        <span id="barangkeluar" style="text-align: left"> Barang keluar</span>
					</div>
				</div>
			</div>
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
				<button type="submit" class="btn btn-outline-primary"><i class="fa fa-print"></i> Cetak</button>
			</form>
			<hr>
			<table id="dt" class="table table-sm table-vcenter table-bordered" width="100%">
				<thead>
					<tr>
						<th>Tanggal Nota</th>
						<th>Keterangan</th>
						<th>Uraian</th>
						<th>Debit</th>
						<th>Kredit</th>
						<th>Saldo</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>


		</div>
	</div>
</div>