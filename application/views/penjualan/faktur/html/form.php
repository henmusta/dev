<?php 
$penjualan = isset($data) ? $data : (object)[];
?>
<style>
.wrapper{
  display: inline-flex;
  background: #fff;
  width: 400px;
  align-items: center;
  justify-content: space-evenly;
  border-radius: 5px;
  padding: 20px 15px;
}
.wrapper .option{
  background: #fff;
  height: 100%;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-evenly;
  margin: 0 10px;
  border-radius: 5px;
  cursor: pointer;
  padding: 5px;
  border: 2px solid lightgrey;
  transition: all 0.3s ease;
}
.wrapper .option .dot{
  height: 20px;
  width: 20px;
  background: #d9d9d9;
  border-radius: 50%;
  position: relative;
}
.wrapper .option .dot::before{
  position: absolute;
  content: "";
  top: 4px;
  left: 4px;
  width: 12px;
  height: 12px;
  background: #0069d9;
  border-radius: 50%;
  opacity: 0;
  transform: scale(1.5);
  transition: all 0.3s ease;
}
input[type="radio"]{
  display: none;
}
#print:checked:checked ~ .print,
#not_print:checked:checked ~ .not_print{
  border-color: #0069d9;
  background: #0069d9;
}
#print:checked:checked ~ .print .dot,
#not_print:checked:checked ~ .not_print .dot{
  background: #fff;
}
#print:checked:checked ~ .print .dot::before,
#not_print:checked:checked ~ .not_print .dot::before{
  opacity: 1;
  transform: scale(1);
}
.wrapper .option span{
  font-size: 20px;
  color: #808080;
}
#print:checked:checked ~ .print span,
#not_print:checked:checked ~ .not_print span{
  color: #fff;
}
</style>
<div class="content">
	<form id="form" method="POST" action="<?= isset($module['action']) ? $module['action'] : NULL ;?>" autocomplete="off">
		<input type="hidden" name="pk" value="<?= isset($data->id) ? $data->id : NULL ;?>">
		<input type="hidden" id="id_cabang" name="penjualan[id_cabang]" value="<?php echo $this->user->id_cabang; ?>">
		<div class="block">
			
			<div class="block-header">
				<h3 class="block-title"><?= isset($module['name']) ? $module['name'] : NULL ;?></h3>
						<div class="form-group">
							<!-- <label class="rdiobox"><input name="print" value="1" id="print" type="radio"> <span>Print Eceran</span></label>
							<label class="rdiobox"><input name="print" value="0" id="not_print" type="radio"checked> <span>Disable</span></label> -->
						
						</div>
				<div class="block-options">
				    <div class="btn-group">
						<div class="wrapper">
							<input type="radio" name="print" value="1" id="print">
							<input type="radio" name="print" id="not_print"  checked>
							<label for="print" class="option print">
								<div class="dot"></div>
								<span>Print Eceran</span>
								</label>
							<label for="not_print" class="option not_print">
								<div class="dot"></div>
								<span>Disable Print</span>
							</label>
						</div>
					</div>
					<div class="btn-group">
						<a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
						<button type="submit" class="btn btn-outline-primary"><i class="fa fa-save"></i> Simpan</button>
						<!-- <button id="btn-bill-print" class="btn btn-block btn-outline-primary" type="button"><i class="fas fa-print"></i> Print</button> -->
					</div>
				</div>
			</div>
			<div class="block-content">
			<div class="modal fade" id="barcode_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Tambah Produk</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
					<input type="text" class="form-control" id="id_produk" hidden>
					<input type="text" class="form-control" id="text_produk" hidden>
						<div class="form-group">
							<label for="nama-barang" class="col-form-label">Nama Produk</label>
							<input type="text" class="form-control" id="nama_barang" readonly>
						</div>
						<div class="form-group">
							<label for="kode-barang" class="col-form-label">Kode Produk</label>
							<input type="text" class="form-control" id="kode_barang" readonly>
						</div>
						<div class="form-group">
							<label for="harga" class="col-form-label">Harga</label>
							<input type="text" class="form-control" id="harga">
						</div>
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Stok</label>
							<input type="text" class="form-control" id="saldo_produk" readonly>
						</div>
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Qty</label>
							<input type="text" class="form-control" id="qty" value="1">
						</div>
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Total Harga</label>
							<input type="text" class="form-control" id="total_harga">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
						<button type="button" id="add_item" class="btn btn-primary">Tambah</button>
					</div>
					</div>
				</div>
			</div>
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
				<div class="row">
					<div class="col-md-6">
						<div class="form-group form-row">
							<label class="col-2">Kode Barcode</label>
								<div class="col-md-8 ml-1">
									<input id="code_id_value" type="text" class="form-control ml-5" placeholder="Gunakan Barcode Scanner" name="" value="">
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