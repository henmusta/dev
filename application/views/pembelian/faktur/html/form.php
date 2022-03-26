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
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div id="form-old">
							<div class="form-group form-row">
							<input id="pemasok_new" type="text" hidden="true" name="cek[new]" value="old" >
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
							<button id="btnpemasok" name="btnpemasok" type="button" class="btn btn-outline-primary"><i class="fa fa-plus"></i>Pemasok Baru</button>
							<button id="clspemasok" name="clspemasok" type="button"  class="btn btn-outline-secondary" hidden="true"><i class="fa fa-reply"></i>Pemasok Lama</button>
						</div><br>
					<div class="form-new" id="form-new" name="form-new" hidden="true">
						<div class="form-group form-row">
						<input id="pemasok_new1" type="text" name="cek[new]" value="new" hidden="true" disabled="disabled">
							<label class="col-md-3">Nama Supplier</label>
							<div class="col-md-9">
								<input id="nama-pemasok1" type="text" class="form-control" name="pemasok[nama]" required="required" value="<?= isset($pembelian->pemasok->nama) ? $pembelian->pemasok->nama : NULL;?>" disabled="disabled">
							</div>
						</div>
						<div class="form-group form-row">
							<label class="col-md-3">Kode Supplier</label>
							<div class="col-md-9">
								<input id="kode-pemasok1" type="text" class="form-control" name="pemasok[kode]" required="required" value="<?= isset($pembelian->pemasok->kode) ? $pembelian->pemasok->kode : NULL;?>" disabled="disabled">
							</div>
						</div>
						</div>
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
							<th>Total</th>
							<th></th>
						</tr>
					</thead>
					<tbody></tbody>
					<tfoot>
						<tr>
							<th>
								<div class="btn-group">
									<button type="button" id="addrowbeli" class="btn btn-sm btn-outline-secondary btn-add-row"><i class="fa fa-plus"></i></button>
								</div>
							</th>
							<th colspan="2" class="text-right">Subtotal</th>
							<th><input id="totalItems" type="text" class="form-control text-right" readonly="readonly" value="<?= isset($pembelian->total_rincian) ? $pembelian->total_rincian : NULL;?>"></th>
						</tr>
						<tr>
							<th colspan="3" class="text-right">Diskon</th>
							<th><input id="discountItems" type="text" class="form-control redraw-table text-right" value="<?= isset($pembelian->diskon) ? $pembelian->diskon : NULL;?>" name="pembelian[diskon]"></th>
						</tr>
						<tr>
							<th colspan="3" class="text-right">Total Tagihan</th>
							<th><input id="billItems" type="text" class="form-control text-right" readonly="readonly"  value="0"></th>
						</tr>
					</tfoot>
				</table>
				<table id="table-payments" class="table table-sm mt-4">
					<thead>
						<tr>
							<th colspan="7">Rincian Pembayaran</th>
						</tr>
						<tr>
							<th>Metode</th>
							<th>Dari Kas/Bank</th>
							<th>Nomor Giro</th>
							<th>Tgl Giro</th>
							<th>Potongan</th>
							<th>Nominal</th>
							<th>Total</th>
							<th></th>
						</tr>
					</thead>
					<tbody></tbody>
					<tfoot>
						<tr>
							<th>
								<div class="btn-group">
									<button type="button" id="idbeli" class="btn btn-sm btn-outline-secondary btn-add-row"><i class="fa fa-plus"></i></button>
								</div>
							</th>
							<th colspan="5" class="text-right">Total Pembayaran</th>
							<th><input id="totalPayments" type="text" class="form-control text-right" readonly="readonly" value="<?= isset($pembelian->total_pembayaran) ? $pembelian->total_pembayaran : NULL;?>"></th>
						</tr>
						<tr>
							<th colspan="6" class="text-right">Sisa Tagihan</th>
							<th><input id="totalDebt" type="text" class="form-control text-right" readonly="readonly" value="0"></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</form>
</div>