<link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css" rel="stylesheet" />
<div class="content">
	<div class="row">
		<div class="col-md-8">
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
							<label class="col-md-2 text-right">Pemasok</label>
							<div class="col-md-4">
								<select id="select2-pemasok" class="form-control" name="produk[id_pemasok]" required="required">
								<?php 
								if(isset($data->pemasok) && isset($data->pemasok->id)) {
									echo '<option value="'. $data->pemasok->id .'" selected="selected">'. $data->pemasok->nama .'</option>';
								} 
								?>
								</select>
							</div>
						</div>
						<input type="hidden" name="produk[id_cabang]" value="<?php echo isset($this->user->id_cabang) ? $this->user->id_cabang : NULL ;?>">
						<div class="form-group form-row align-items-center">
							<label class="col-md-2 text-right">Nama</label>
							<div class="col-md-8">
								<input type="text" class="form-control" name="produk[nama]" required="required" value="<?= isset($data->nama) ? $data->nama : NULL;?>">
							</div>
						</div>
						<div class="form-group form-row align-items-center">
							<label class="col-md-2 text-right">Harga Beli</label>
							<div class="col-md-4">
								<input type="text" id="harga_beli" class="form-control currencies text-right" name="produk[harga_beli]" required="required" value="<?= isset($data->harga_beli) ? $data->harga_beli : NULL;?>">
							</div>
						</div>
						<div class="form-group form-row align-items-center">
							<label class="col-md-2 text-right">Harga Jual</label>
							<div class="col-md-4">
								<input type="text" id="harga_jual" class="form-control currencies text-right" name="produk[harga_jual]" required="required" value="<?= isset($data->harga_jual) ? $data->harga_jual : NULL;?>">
							</div>
						</div>		
						<div class="form-group form-row align-items-center">
							<label class="col-md-2 text-right">Laba</label>
							<div class="col-md-4">
								<input type="text" id="laba" class="form-control currencies text-right" name="produk[laba]" required="required" value="<?= isset($data->laba) ? $data->laba : NULL;?>">
							</div>
						</div>					
						<div class="form-group form-row align-items-center">
							<label class="col-md-2 text-right">Kategori Harga</label>
							<div class="col-md-4">
								<select name="produk[id_satuan]" class="form-control" id="">
									<option selected disabled>Pilih Kategori</option>
									<?php foreach ($satuan as $key => $value) { ?>
										<option value="<?= $value->id?>" <?= isset($data->id_satuan) && $data->id_satuan == $value->id ? 'selected' : '';?>><?= $value->name?></option>
									<?php } ?>
								</select>
							</div>
						</div>					
					</div>
				</div>
			</form>
		</div>
	</div>
</div>