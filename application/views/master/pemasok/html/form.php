<div class="content">
	<div class="row">
		<div class="col-md-8">
			<form id="form" method="POST" action="<?= isset($module['action']) ? $module['action'] : NULL ;?>" autocomplete="off">
				<input type="hidden" name="pk" value="<?= isset($data->id) ? $data->id : NULL ;?>">
				<input type="hidden" id="id_cabang" name="pemasok[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
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
							<label class="col-md-2 text-right">Kode Supplier</label>
							<div class="col-md-4">
								<input type="text" class="form-control" name="pemasok[kode]" required="required" value="<?= isset($data->kode) ? $data->kode : NULL ;?>">
							</div>
						</div>
						<div class="form-group form-row align-items-center">
							<label class="col-md-2 text-right">Nama Toko</label>
							<div class="col-md-8">
								<input type="text" class="form-control" name="pemasok[nama]" required="required" value="<?= isset($data->nama) ? $data->nama : NULL ;?>">
							</div>
						</div>
						<div class="form-group form-row align-items-center">
							<label class="col-md-2 text-right">Nama Merk</label>
							<div class="col-md-4">
								<input type="text" class="form-control" name="pemasok[telp]" value="<?= isset($data->telp) ? $data->telp : NULL ;?>">
							</div>
						</div>
						<div class="form-group form-row align-items-center">
							<label class="col-md-2 text-right">Email</label>
							<div class="col-md-4">
								<input type="email" class="form-control" name="pemasok[email]" value="<?= isset($data->email) ? $data->email : NULL ;?>">
							</div>
						</div>
						<div class="form-group form-row align-items-center">
							<label class="col-md-2 text-right">Alamat</label>
							<div class="col-md-8">
								<textarea class="form-control" name="pemasok[alamat]"><?= isset($data->alamat) ? $data->alamat : NULL ;?></textarea>
							</div>
						</div>						
					</div>
				</div>
			</form>
		</div>
	</div>
</div>