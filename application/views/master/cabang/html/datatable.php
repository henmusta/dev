<div class="content">
	<div class="block">
		<div class="block-header">
			<h3 class="block-title">Data <?= $module['name'];?></h3>
			<div class="block-options">
				<div class="btn-group btn-group-sm">
					<a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
					<!-- <a href="<?= $module['url'];?>/insert" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Tambah Baru</a> -->
				</div>
			</div>
		</div>
		<div class="block-content">
		<div class="table-responsive">
			<table id="dt" class="table table-sm table-vcenter table-bordered" width="100%">
				<thead>
					<tr>
						<th>Jenis</th>
						<th>Kode</th>
						<th>Nama</th>
						<th>Email</th>
						<th>WA</th>
						<th>Telp</th>
						<th>Alamat</th>
						<th>#</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
		</div>
	</div>
</div>