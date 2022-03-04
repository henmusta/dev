<div class="content">
	<div class="row">
		<div class="col-md-6">
			<form id="form" method="POST" action="<?= isset($module['action']) ? $module['action'] : NULL ;?>" autocomplete="off">
				<input type="hidden" name="pk" value="<?= isset($data->id) ? $data->id : NULL ;?>">
				<div class="block">
					<div class="block-header">
						<h3 class="block-title">Form <?= isset($module['name']) ? $module['name'] : NULL ;?></h3>
						<div class="block-options">
							<div class="btn-group btn-group-sm">
								<a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
								<button type="submit" class="btn btn-outline-primary"><i class="fa fa-save"></i> Simpan</button>
							</div>
						</div>
					</div>
					<div class="block-content">						
						<div class="form-group form-row align-items-center">
							<label class="col-md-2 text-right">Kode</label>
							<div class="col-md-8">
							<input type="hidden" id="id_satuan" name="kode_laba[satuan]" value="<?php echo $this->cabang->satuan;?>">
							<input type="hidden" id="id_cabang" name="kode_laba[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
								<input type="text" class="form-control" name="kode_laba[kode]" required="required" value="<?= isset($data->kode) ? $data->kode : NULL ;?>">
							</div>
						</div>
						<div class="form-group form-row align-items-center">
							<label class="col-md-2 text-right">Laba</label>
							<div class="col-md-8">
								<input type="text" class="form-control currencies text-right" name="kode_laba[laba]" required="required" value="<?= isset($data->laba) ? $data->laba : NULL ;?>">
							</div>
						</div>					
					</div>
				</div>
			</form>
		</div>
	</div>
</div>