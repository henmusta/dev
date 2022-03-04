<div class="content">
	<div class="row">
		<div class="col-md-8">
			<form id="form" method="POST" action="<?= isset($module['action']) ? $module['action'] : NULL ;?>" autocomplete="off">
				<input type="hidden" name="pk" value="<?= isset($data->kode) ? $data->kode : NULL ;?>">
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
							<label class="col-md-2 text-right">Tipe</label>
							<div class="col-md-4">
								<select class="form-control" name="cabang[jenis]" required="required">
									<option value="Toko" <?= isset($data->jenis) && $data->jenis == 'Toko' ? 'selected="selected"' : NULL;?>>Toko</option>
								</select>
							</div>
						</div>						
						<div class="form-group form-row align-items-center">
							<label class="col-md-2 text-right">Kode</label>
							<div class="col-md-4">
								<input type="text" class="form-control" name="cabang[kode]" required="required" value="<?= isset($data->kode) ? $data->kode : NULL ;?>">
							</div>
						</div>
						<div class="form-group form-row align-items-center">
							<label class="col-md-2 text-right">Nama</label>
							<div class="col-md-8">
								<input type="text" class="form-control" name="cabang[nama]" required="required" value="<?= isset($data->nama) ? $data->nama : NULL ;?>">
							</div>
						</div>
						<div class="form-group form-row align-items-center">
							<label class="col-md-2 text-right">Whatsapp</label>
							<div class="col-md-4">
								<input type="text" class="form-control" name="cabang[wa]" value="<?= isset($data->wa) ? $data->wa : NULL ;?>">
							</div>
						</div>
						<div class="form-group form-row align-items-center">
							<label class="col-md-2 text-right">Telp</label>
							<div class="col-md-4">
								<input type="text" class="form-control" name="cabang[telp]" value="<?= isset($data->telp) ? $data->telp : NULL ;?>">
							</div>
						</div>
						<div class="form-group form-row align-items-center">
							<label class="col-md-2 text-right">Email</label>
							<div class="col-md-4">
								<input type="email" class="form-control" name="cabang[email]" value="<?= isset($data->email) ? $data->email : NULL ;?>">
							</div>
						</div>
						<div class="form-group form-row align-items-center">
							<label class="col-md-2 text-right">Alamat</label>
							<div class="col-md-8">
								<textarea class="form-control" name="cabang[alamat]"><?= isset($data->alamat) ? $data->alamat : NULL ;?></textarea>
							</div>
						</div>						
					</div>
				</div>
			</form>
		</div>
	</div>
</div>