<iframe name="dummyframe" id="dummyframe" style="display: none;"></iframe>



<div class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="block">
			<pre id="example-console-rows"></pre>

			<form id="barcode_multiple" action="<?= isset($module['action']) ? $module['action'] : NULL ;?>" method="POST" target="dummyframe">
				<div class="block-header">
				<div class="block-options">
					<h3 class="block-title">Data <?= $module['name'];?></h3>
				</div>
				         <button class="btn btn-outline-success" style="text-align:center;">Cetak Barcode Multiple</button>
							<div class="col-md-4">
								<select id="select2-arsip" class="form-control" name="filter[arsip]" required="required">
									<option value="1">List Produk</value>
									<option value="0">List Arsip Produk</value>
								</select>
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
								<th></th>
								<th>No</th>
								<th>Pemasok</th>
								<th>Kode Produk</th>
								<th>Nama</th>
								<th>Harga Beli</th>
								<th>Harga Jual</th>
								<th>Kategori Harga</th>
								<th>Status</th>
								<th>Aksi</th>
								<th>Stok</th>
								<th>#</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				</div>
			</div>
		</form>
		</div>
	</div>
</div>