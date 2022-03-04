

<div class="content">
	<div class="block">
		<div class="block-content">
			<div class="container">
			<a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
			<div class="row">
				<div class="col-sm">
				<form class="form-inline" target="_blank" method="POST" action="<?= $module['url'];?>/print-out-hari">
							<label class="mr-2">Harian</label>
							<div class="input-group mr-2">
								<input type="text" class="form-control flatpickrhri" name="filter[tglhari]" value="<?= date('Y-m-d')?>">
								<input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
								<button type="submit" class="btn btn-outline-primary"><i class="fa fa-print"></i> Cetak</button>
								<button id="excel_hari" name="excel_hari" type="button" class="btn btn-outline-primary"><i class="fas fa-download"></i>Cetak Excel</button>
							</div>
				</form>
				</div>
				<div class="col-sm">
				<form class="form-inline" target="_blank" method="POST" action="<?= $module['url'];?>/print-out-bulan">
				<label class="mr-2">Bulanan</label>
							<div class="input-group mr-2">
								<input type="text" class="form-control flatpickrbln" name="filter[tglbulan]" value="<?= date('Y-m')?>">
								<input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
								<button type="submit" class="btn btn-outline-primary"><i class="fa fa-print"></i> Cetak</button>
								<button id="excel_bulan" name="excel_bulan" type="button" class="btn btn-outline-primary"><i class="fas fa-download"></i>Cetak Excel</button>
							</div>
				</form>
				</div>
				<div class="col-sm">
				<form class="form-inline" target="_blank" method="POST" action="<?= $module['url'];?>/print-out-tahun">
				<label class="mr-2">Tahunan</label>
							<div class="input-group mr-2">
								<input type="text" class="form-control flatpickrthn" name="filter[tgltahun]" value="<?= date('Y')?>">
								<input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
								<button type="submit" class="btn btn-outline-primary"><i class="fa fa-print"></i> Cetak</button>
								<button id="excel_tahun" name="excel_tahun" type="button" class="btn btn-outline-primary"><i class="fas fa-download"></i>Cetak Excel</button>
							</div>
						
				</form>
				</div>
			</div>
			</div>
			
		</div>
	</div>
</div>

