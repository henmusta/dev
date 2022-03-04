<div class="content">
	<div class="row">
		<div class="col-md-8">
			<div class="block">
				<div class="block-header">
					<h3 class="block-title">Data <?= $module['name'];?></h3>
					<div class="block-options">
					<input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
						<div class="btn-group btn-group-sm">
							<a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
							<a type="button" id="delete-all" class="btn btn-outline-danger"><i class="fa fa-trash"></i> Hapus Semua</a>
						</div>
					</div>
				</div>
				<div class="block-content">
					<div class="table-responsive">
						<table id="dt" class="table table-sm table-vcenter table-bordered" width="100%">
							<thead>
								<tr>
									<th>Group</th>
									<th>Nomor</th>
									<th>#</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<form id="form" method="POST" action="<?= isset($module['action']) ? $module['action'] : NULL ;?>" autocomplete="off">
				<div class="block">
					<div class="block-header">
						<h3 class="block-title">Form <?= $module['name'];?></h3>
						<div class="block-options">
							<div class="btn-group btn-group-sm">
								<button type="submit" class="btn btn-outline-primary"><i class="fa fa-save"></i> Simpan</button>
							</div>
						</div>
					</div>
					<div class="block-content">
						<div class="form-group">
							<label for="">Prefiks</label>
							<input type="text" class="form-control" name="giro[prefiks]">
							<input type="hidden" id="id_cabang" name="giro[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
						</div>
						<div class="form-group">
							<label for="">Nomor Urut</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<div class="input-group-text">Awal</div>
								</div>
								<input type="number" maxlength="2" class="form-control" id="nomor_awal" name="giro[nomor_awal]" value="1"/>
								<div class="input-group-append">
									<div class="input-group-text">Akhir</div>
								</div>
								<input type="number" maxlength="2" class="form-control"  id="nomor_akhir"  name="giro[nomor_akhir]" value="25"/>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Tambah Giro</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary">Save changes</button>
				</div>
			</div>
		</div>
	</div>
</div>