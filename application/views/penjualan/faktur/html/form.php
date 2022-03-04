<?php 
$penjualan = isset($data) ? $data : (object)[];
?>
<div class="content">
	<form id="form" method="POST" action="<?= isset($module['action']) ? $module['action'] : NULL ;?>" autocomplete="off">
		<input type="hidden" name="pk" value="<?= isset($data->id) ? $data->id : NULL ;?>">
		<input type="hidden" id="id_cabang" name="penjualan[id_cabang]" value="<?php echo $this->user->id_cabang; ?>">
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
							<label class="col-md-3">Tanggal Input</label>
							<div class="col-md-6">
								<input id="tgl-nota" type="text" class="form-control" name="penjualan[tgl_nota]" required="required" value="<?= isset($penjualan->tgl_nota) ? $penjualan->tgl_nota : date('Y-m-d');?>">
							</div>
						</div>
						<div class="form-group form-row">
							<label class="col-md-3">Nomor Nota</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="penjualan[nomor]" required="required" value="<?= isset($penjualan->nomor) ? $penjualan->nomor : NULL;?>">
							</div>
						</div>
					</div>
					<div class="col-md-6">
					<input type="hidden" name="pelanggan[id_cabang]" value="<?php echo $this->user->id_cabang; ?>">
						<div id="form-old" name="form-old">
							<div class="form-group form-row">
							<label class="col-md-3">Pelanggan</label>
								<div class="col-md-9">
									<select id="select-pelanggan" class="form-control" name="pelanggan[nama]">
									<?= isset($penjualan->pelanggan->nama) ? '<option value="'. $penjualan->pelanggan->nama .'" selected="selected">'. $penjualan->pelanggan->nama .'</option>' : NULL ;?>
									</select>
								</div>
							</div>
							<div class="form-group form-row" hidden="true">
								<label class="col-md-4">id</label>
								<div class="col-md-8">
									<input id="id_pelanggan" type="text" class="form-control id_pelanggan" name="id_pelanggan" required="required" value="<?= isset($penjualan->pelanggan->id) ? $penjualan->pelanggan->id : NULL;?>">
								</div>
							</div>
							<div class="form-group form-row">
								<label class="col-md-4">Alamat Pelanggan</label>
								<div class="col-md-8">
									<input id="alamat-pelanggan" type="text" class="form-control" name="pelanggan[alamat]" required="required" value="<?= isset($penjualan->pelanggan->alamat) ? $penjualan->pelanggan->alamat : NULL;?>">
								</div>
							</div>
						</div>
						<div>
							<button id="btnpelanggan" name="btnpelanggan" type="button" class="btn btn-outline-primary"><i class="fa fa-plus"></i>Pelanggan Baru</button>
							<button id="clspelanggan" name="clspelanggan" type="button"  class="btn btn-outline-secondary" hidden="true"><i class="fa fa-reply"></i>Pelanggan Lama</button>
						</div><br>
						<div class="form-new" id="form-new" name="form-new" hidden="true" >
							<div class="form-group form-row">
								<label class="col-md-4">Nama Pelanggan</label>
								<div class="col-md-8">
									<input id="nama-pelanggan1" type="text" class="form-control nama-pelanggan" name="pelanggan[nama]" required="required" value="<?= isset($penjualan->pelanggan->nama) ? $penjualan->pelanggan->nama : NULL;?>" disabled="disabled">
								</div>
							</div>
							<div class="form-group form-row" hidden="true">
								<label class="col-md-4">id</label>
								<div class="col-md-8">
									<input id="id_pelanggan1" type="text" class="form-control id_pelanggan" name="id_pelanggan" required="required" value="<?= isset($penjualan->pelanggan->id) ? $penjualan->pelanggan->id : NULL;?>" disabled="disabled">
								</div>
							</div>
							<div class="form-group form-row">
								<label class="col-md-4">Alamat Pelanggan</label>
								<div class="col-md-8">
									<input id="alamat-pelanggan1" type="text" class="form-control" name="pelanggan[alamat]" required="required" value="<?= isset($penjualan->pelanggan->alamat) ? $penjualan->pelanggan->alamat : NULL;?>" disabled="disabled">
								</div>
							</div>
						</div>
					</div>
				
					
				</div>
				<table id="table-items" class="table table-sm" width="100%">
					<thead>
						<tr>
							<th colspan="6">Rincian Barang</th>
						</tr>
						<tr>
							<th>Nama Barang</th>
							<th>Harga</th>
							<th>Qty</th>
							<th>Stok</th>
							<th>Total</th>
							<th></th>
						</tr>
					</thead>
					<tbody></tbody>
					<tfoot>
						<tr>
							<th>
								<div class="btn-group">
									<button type="button" class="btn btn-sm btn-outline-secondary btn-add-row"><i class="fa fa-plus"></i></button>
								</div>
							</th>
							<th colspan="3" class="text-right">Subtotal</th>
							<th><input id="totalItems" type="text" class="form-control text-right" readonly="readonly" value="<?= isset($penjualan->total_rincian) ? $penjualan->total_rincian : NULL;?>"></th>
						</tr>
						<tr>
							<th colspan="4" class="text-right">Diskon</th>
							<th><input id="discountItems" type="text" class="form-control redraw-table text-right"  value="<?= isset($penjualan->diskon) ? $penjualan->diskon : 0;?>" name="penjualan[diskon]"></th>
						</tr>
						<tr>
							<th colspan="4" class="text-right">cek nota</th>
							<th><input id="totalcek" type="text" class="form-control text-right" readonly="readonly" value="<?= isset($penjualan->chek) ? $penjualan->chek : NULL;?>" name="penjualan[chek]"></th>
						</tr>
						<tr>
							<th colspan="4" class="text-right">Retur</th>
							<th><input id="totretur" type="text" class="form-control text-right" readonly="readonly" value="<?= isset($penjualan->notaretur) ? $penjualan->notaretur : 0;?>" name="penjualan[notaretur]"></th>
						</tr>
						<tr>
							<th colspan="4" class="text-right">Total Tagihan</th>
							<th><input id="billItems" type="text" class="form-control text-right" readonly="readonly"  value="0"></th>
						</tr>
					</tfoot>
				</table><br>
				<div>
					<button id="btnretur" name="btnretur" type="button" class="btn btn-outline-primary"><i class="fa fa-plus"></i>Retur</button>
				</div>
				<br>
				<table id="table-retur" class="table table-sm" hidden="true" width="100%">
					<thead>
						<tr>
							<th colspan="6">Barang Retur</th>
						</tr>
						<tr>
							<th>Nama Barang</th>
							<th>Harga</th>
							<th>Qty Retur</th>
							<th>Qty Total</th>
							<th>Total</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
					<tfoot>
						<tr>
							<th>
								<div class="btn-group">
									<button type="button" class="btn btn-sm btn-outline-secondary btn-add-row"><i class="fa fa-plus"></i></button>
								</div>
							</th>
							<th colspan="3" class="text-right">Sub total Retur</th>
							<th><input id="totalretur" type="text" class="form-control text-right" readonly="readonly" value="<?= isset($penjualan->dataretur->nominal) ? $penjualan->dataretur->nominal : NULL;?>" ></th>
						</tr>
					</tfoot><br>
				</table>
				<br>
				<table id="table-payments" class="table table-sm mt-4">
					<thead>
						<tr>
							<th colspan="7">Rincian Pembayaran</th>
						</tr>
						<tr>
							<th>Metode</th>
							<th>Dari Kas/Bank</th>
							<th>Nominal</th>
							<th>Cashback</th>
							<th>Cek Nota</th>
							<th>Total</th>
							<th></th>
						</tr>
					</thead>
					<tbody></tbody>
					<tfoot>
						<tr>
							<th>
								<div class="btn-group" id="additem">
									<button type="button" class="btn btn-sm btn-outline-secondary btn-add-row"><i class="fa fa-plus"></i></button>
								</div>
							</th>
							<th colspan="4" class="text-right">Total Pembayaran</th>
							<th><input id="totalPayments" type="text" class="form-control text-right" readonly="readonly" value="<?= isset($penjualan->total_pembayaran) ? $penjualan->total_pembayaran : NULL;?>"></th>
						</tr>
					
						<tr>
							<th colspan="5" class="text-right">Sisa Tagihan</th>
							<th><input id="totalReceivable" type="text" class="form-control text-right" readonly="readonly" value="0"></th>
						</tr>
					</tfoot>
				</table>
				<br>
			</div>
		</div>
	</form>
</div>