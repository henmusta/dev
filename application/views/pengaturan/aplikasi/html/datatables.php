<div class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="block">
				<div class="block-header">
					<h3 class="block-title">Data <?= $module['name'];?></h3>
					<div class="block-options">
						<div class="btn-group btn-group-sm">
							<a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
							<input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->cabang->satuan;?>">
						</div>
					</div>
				</div>
				<div class="block-content">
				<div class="table-responsive">
					<table id="dt" class="table table-sm table-vcenter table-bordered" width="100%">
						<thead>
							<tr>
								<th class="text-center">Nama Aplikasi</th>
								<th>Gambar</th>
								<th>#</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				</div>
			</div>
		</div>
	</div>
</div>