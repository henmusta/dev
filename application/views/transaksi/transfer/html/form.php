<?php 
$transfer = isset($data) ? $data : (object)[];
?>
<div class="content">
	<form id="form" method="POST" action="<?= isset($module['action']) ? $module['action'] : NULL ;?>" autocomplete="off">
		<input type="hidden" name="pk" value="<?= isset($data->id) ? $data->id : NULL ;?>">
		<div class="block">
			<div class="block-header">
				<h3 class="block-title"><?= isset($module['name']) ? $module['name'] : NULL ;?></h3>
				<div class="block-options">
					<div class="btn-group btn-group-sm">
						<a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
						<button type="submit" class="btn btn-outline-primary"><i class="fa fa-save"></i> Simpan</button>
					</div>
				</div>
			</div>
			<div class="block-content">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group form-row">
							<label class="col-md-3">Tanggal</label>
							<div class="col-md-6">
								<input id="tgl-nota" type="text" class="form-control" name="transfer[tgl_nota]" required="required" value="<?= isset($data->tgl_nota) ? $data->tgl_nota : date('Y-m-d');?>">
							</div>
						</div>
						<div class="form-group form-row">
							<label class="col-md-3">Dari Kas/Bank</label>
							<div class="col-md-5">
								<select id="select-dari" class="form-control" name="transfer[id_kredit]">
								<?php if(isset($data->id_dari)) : ?>
									<option value="<?= $data->id_dari;?>" selected="selected"><?= $data->nama_dari;?></option>
								<?php endif;?>
								</select>
							</div>
						</div>
						<div class="form-group form-row">
							<label class="col-md-3">Ke Kas/Bank</label>
							<div class="col-md-5">
								<select id="select-ke" class="form-control" name="transfer[id_debit]">
								<?php if(isset($data->id_ke)) : ?>
									<option value="<?= $data->id_ke;?>" selected="selected"><?= $data->nama_ke;?></option>
								<?php endif;?>
								</select>
							</div>
						</div>
						<div class="form-group form-row">
							<label class="col-md-3">Jumlah</label>
							<div class="col-md-5">
								<input id="nominal" class="form-control text-right" name="transfer[nominal]" required="required" value="<?= isset($data->nominal) ? $data->nominal : NULL;?>">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>