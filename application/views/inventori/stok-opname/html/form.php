<?php 
$stok_opname = isset($data) ? $data : (object)[];
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
							<label class="col-md-3">Tanggal Nota</label>
							<div class="col-md-6">
							<input type="hidden" id="id_cabang" name="stok_opname[id_cabang]" value="<?php echo $this->user->id_cabang; ?>">
								<input id="tgl-nota" type="text" class="form-control" name="stok_opname[tgl_opname]" required="required" value="<?= isset($stok_opname->tgl_opname) ? $stok_opname->tgl_opname : date('Y-m-d');?>">
							</div>
						</div>
						<div class="form-group form-row">
							<label class="col-md-3">Nomor Nota</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="stok_opname[nomor]" required="required" value="<?= isset($stok_opname->nomor) ? $stok_opname->nomor : NULL;?>">
							</div>
						</div>
					</div>
<!-- 					<div class="col-md-6">
						<div class="form-group form-row">
							<label class="col-md-4">Kode Pelanggan</label>
							<div class="col-md-8">
								<input id="kode-pelanggan" type="text" class="form-control" name="pelanggan[kode]" required="required" value="<?= isset($penjualan->pelanggan->kode) ? $penjualan->pelanggan->kode : NULL;?>">
							</div>
						</div>
						<div class="form-group form-row">
							<label class="col-md-4">Nama Pelanggan</label>
							<div class="col-md-8">
								<input id="nama-pelanggan" type="text" class="form-control" name="pelanggan[nama]" required="required" value="<?= isset($penjualan->pelanggan->nama) ? $penjualan->pelanggan->nama : NULL;?>">
							</div>
						</div>
					</div> -->
				</div>
				<table id="table-items" class="table table-sm">
					<thead>
						<tr>
							<th colspan="4">Rincian Barang</th>
						</tr>
						<tr>
							<th>Nama Barang</th>
							<th>Qty Komputer</th>
							<th>Qty Fisik</th>
							<th>Qty Selisih</th>
							<th></th>
						</tr>
					</thead>
					<tbody></tbody>
					<tfoot>
						<tr>
							<th>
								<div class="btn-group">
									<button type="button" class="btn btn-sm btn-outline-secondary btn-delete-row"><i class="fa fa-minus"></i></button>
									<button type="button" class="btn btn-sm btn-outline-secondary btn-add-row"><i class="fa fa-plus"></i></button>
								</div>
							</th>
							
						</tr>
						<tr>
							<th><input type="hidden" id="billItems" type="text" class="form-control text-right" readonly="readonly"  value="0"></th>
						</tr>
					</tfoot>
				</table>
		
			</div>
		</div>
	</form>
</div>