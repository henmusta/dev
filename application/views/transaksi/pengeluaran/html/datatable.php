<div class="content">
	<div class="block">
		<div class="block-header">
			<h3 class="block-title">Data <?= $module['name'];?></h3>
			<div class="input-group input-group-sm" style="width:240px;">
				<div class="input-group-prepend">
					<div class="input-group-text">Tgl Pembelian</div>
				</div>
				<input type="text" class="form-control form-control-sm datepicker d-inline-block m-0" value="<?= date('Y-m-d');?>" name="filter[tgl_nota]">
			</div>
			<div class="block-options">
				<div class="btn-group btn-group-sm">
					<a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
					<a href="<?= $module['url'];?>/insert" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Tambah Baru</a>
				</div>
			</div>
		</div>
		<div class="block-content">
			<div class="table-responsive">
				<table id="dt" class="table table-sm table-vcenter table-bordered" width="100%">
					<thead>
						<tr>
							<th>Tgl</th>
							<th>Kas</th>
							<th>Biaya</th>
							<th>Nominal</th>
							<th>#</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>