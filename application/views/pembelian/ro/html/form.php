<?php 
  $pembelian = isset($data) ? $data : (object)[];
?>
<div class="content">
	<form id="form" method="POST" action="<?= isset($module['action']) ? $module['action'] : NULL ;?>" autocomplete="off">
		<input type="hidden" name="pk" value="<?= isset($data->id) ? $data->id : NULL ;?>">
		<input type="hidden" id="id_cabang" name="pembelian[id_cabang]" value="<?php echo $this->user->id_cabang; ?>">
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
					<input type="hidden"  name="pemasok[id_cabang]" value="<?php echo $this->user->id_cabang; ?>">
						<div class="form-group form-row">
							<label class="col-md-3">Tanggal Input</label>
							<div class="col-md-6">
								<input id="tgl-buat" type="text" class="form-control datepicker" name="pembelian[tgl_buat]" required="required" value="<?= isset($pembelian->tgl_buat) ? $pembelian->tgl_buat : date('Y-m-d');?>">
							</div>
						</div>
						<div class="form-group form-row">
						<label class="col-md-3">Tanggal Nota</label>
							<div class="col-md-6">
								<input id="tgl-nota" type="text" class="form-control" name="pembelian[tgl_nota]" required="required" value="<?= isset($pembelian->tgl_nota) ? $pembelian->tgl_nota : date('Y-m-d');?>">
							</div>
						</div>
						<div class="form-group form-row">
							<label class="col-md-3">Nomor Nota</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="pembelian[nomor]" required="required" value="<?= isset($pembelian->nomor) ? $pembelian->nomor : NULL;?>">
							</div>
						</div>
						<div class="form-group form-row">
							<label class="col-md-3">Status Penerimaan Barang</label>
							<div class="col-md-6">
							    <select id="select2-status_ro" class="form-control" name="pembelian[status_ro]" required="required">
									<option value="0" <?php isset($pembelian->status_ro) && $pembelian->status_ro == '0' ? 'selected' : null; ?>>Draft</option>
                           			<option value="1" <?php isset($pembelian->status_ro) && $pembelian->status_ro == '1' ? 'selected' : null; ?>>Fix</option>
									<!-- <option value="0">DRAFT</value>
									<option value="1">FIX</value> -->
								</select>
							</div>
						</div>
			
					</div>
					<div class="col-md-6">
						<div id="form-old">
							<div class="form-group form-row">
							<label class="col-md-3">Pemasok</label>
								<div class="col-md-9">
									<select id="nama-pemasok" class="form-control" name="pemasok[nama]">
									<?= isset($pembelian->pemasok->nama) ? '<option value="'. $pembelian->pemasok->nama .'" selected="selected">'. $pembelian->pemasok->nama .'</option>' : NULL ;?>
									</select>
								</div>
							</div>
							<div class="form-group form-row">
								<label class="col-md-3">Kode Supplier</label>
								<div class="col-md-9">
									<input id="kode-pemasok" type="text" class="form-control" name="pemasok[kode]" required="required" value="<?= isset($pembelian->pemasok->kode) ? $pembelian->pemasok->kode : NULL;?>">
								</div>
							</div>
						</div>
						<div>
							<!-- <button id="btnpemasok" name="btnpemasok" type="button" class="btn btn-outline-primary"><i class="fa fa-plus"></i>Pemasok Baru</button>
							<button id="clspemasok" name="clspemasok" type="button"  class="btn btn-outline-secondary" hidden="true"><i class="fa fa-reply"></i>Pemasok Lama</button> -->
						</div><br>
				
					</div>
					
				</div>
				<table id="table-items" class="table table-sm">
					<thead>
						<tr>
							<th colspan="4">Rincian Barang</th>
						</tr>
						<tr>
							<th>Nama Barang</th>
							<th>Harga</th>
							<th>Qty</th>
							<th>Penerimaan</th>
							<th>Sisa Qty</th>
						</tr>
					</thead>
					<tbody></tbody>
					<tfoot>
					
					</tfoot>
				</table>
			
			</div>
		</div>
	</form>
</div>