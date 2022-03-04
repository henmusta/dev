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
							<label class="col-md-3 text-right">Nama Akun</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="akun[nama]" required="required" value="<?= isset($data->nama) ? $data->nama : NULL ;?>">
							</div>
						</div>						
					</div>
				</div>
			</form>
		</div>
	</div>
</div>