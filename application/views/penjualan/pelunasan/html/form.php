<div class="content">
	<div class="row">
		<div class="col-md-12">
			<form id="form" method="POST" action="<?= isset($module['action']) ? $module['action'] : NULL ;?>" autocomplete="off">
				<input type="hidden" name="pk" value="<?= isset($pelunasan->id) ? $pelunasan->id : NULL ;?>">
				<div class="block">
					<div class="block-header">
						<h3 class="block-title">Form <?= isset($module['name']) ? $module['name'] : NULL ;?></h3>
						<input type="hidden" id="id_cabang" name="pelunasan[id_cabang]" value="<?php echo $this->user->id_cabang; ?>">
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
								<div class="form-group form-row align-items-center">
									<label class="col-md-4 text-right">Pelanggan</label>
									<div class="col-md-6">
										<select id="select2-pelanggan" class="form-control" name="pelunasan[id_pelanggan]" required="required">
										<?php if(isset($pelunasan->pelanggan->id) && !empty(isset($pelunasan->pelanggan->id))) : ?>
										<option selected="selected" value="<?= $pelunasan->pelanggan->id;?>"><?= $pelunasan->pelanggan->nama;?></option>
										<?php endif; ?>
										</select>
									</div>
								</div>
								<div class="form-group form-row align-items-center">
									<label class="col-md-4 text-right">Nota penjualan</label>
									<div class="col-md-6">
										<select id="select2-penjualan" class="form-control" name="pelunasan[id_penjualan]" required="required">
										<?php if(isset($pelunasan->penjualan->id) && !empty(isset($pelunasan->penjualan->id))) : ?>
										<option selected="selected" value="<?= $pelunasan->penjualan->id;?>"><?= $pelunasan->penjualan->nomor;?></option>
										<?php endif; ?>
										</select>
									</div>
								</div>		
								<div class="form-group form-row align-items-center">
									<label class="col-md-4 text-right">Tgl Nota</label>
									<div class="col-md-6">
										<input type="text" class="form-control datepicker" name="pelunasan[tgl_bayar]" required="required" value="<?= isset($pelunasan->tgl_bayar) ? $pelunasan->tgl_bayar : null;?>">
									</div>
								</div>
								<div class="form-group form-row align-items-center">
									<label class="col-md-4 text-right">No Nota pelunasan</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="pelunasan[nomor]" required="required" value="<?= isset($pelunasan->nomor) ? $pelunasan->nomor : null;?>">
									</div>
								</div>
							</div>
							<div id="preview-penjualan" class="col-md-6">
								<table class="table table-sm table-bordered">
									<tr>
										<td style="width:200px;">Tagihan</td>
										<td id="total_tagihan" class="text-right">0</td>
									</tr>
									<tr>
										<td style="width:200px;">Sudah dibayarkan</td>
										<td id="total_pelunasan" class="text-right">0</td>
									</tr>
									<tr>
										<td style="width:200px;">Sisa Tagihan</td>
										<td id="sisa_tagihan" class="text-right">0</td>
									</tr>
								</table>
							</div>
						</div>
						<table id="table-payments" class="table table-sm mt-4">
							<thead>
								<tr>
									<th colspan="7">Rincian pelunasan</th>
								</tr>
								<tr>
									<th>Metode</th>
									<th>Dari Kas/Bank</th>
									<th>Nomor Giro</th>
									<th>Tgl Giro</th>
									<th>Nominal</th>
									<th>Potongan</th>
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
									<th colspan="5" class="text-right">Total pelunasan</th>
									<th><input id="totalPayments" type="text" class="form-control text-right" readonly="readonly" value="0"></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>