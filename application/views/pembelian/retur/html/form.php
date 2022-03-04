<?php 
$retur = isset($data) ? $data : (object)[];
?>
<div class="content">
	<form id="form" method="POST" action="<?= isset($module['action']) ? $module['action'] : NULL ;?>" autocomplete="off">
		<input type="hidden" name="pk" value="<?= isset($data->id) ? $data->id : NULL ;?>">
		<input type="hidden" id="id_cabang" name="retur[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
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
							
								<input id="tgl-nota" type="text" class="form-control" name="retur[tgl_nota]" required="required" value="<?= isset($retur->tgl_nota) ? $retur->tgl_nota : date('Y-m-d');?>">
							</div>
						</div>
						<div class="form-group form-row">
							<label class="col-md-3">Supplier</label>
							<div class="col-md-9">
								<select id="select-pemasok" class="form-control" name="retur[id_pemasok]">
								<?= isset($retur->pemasok->id) ? '<option value="'. $retur->pemasok->id .'" selected="selected">'. $retur->pemasok->nama .'</option>' : NULL ;?>
								</select>

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
									<button type="button" class="btn btn-sm btn-outline-secondary btn-add-row"><i class="fa fa-plus"></i></button>
								</div>
							</th>
							<th colspan="2" class="text-right">Subtotal</th>
							<th><input id="totalItems" type="text" class="form-control text-right" readonly="readonly" value="<?= isset($retur->nominal) ? $retur->nominal : NULL;?>"></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</form>
</div>