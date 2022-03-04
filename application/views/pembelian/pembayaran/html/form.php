<div class="content">
	<div class="row">
		<div class="col-md-12">
			<form id="form" method="POST" action="<?= isset($module['action']) ? $module['action'] : NULL ;?>" autocomplete="off">
				<input type="hidden" name="pk" id="pk" value="<?= isset($pembayaran->id) ? $pembayaran->id : NULL ;?>">
				<input type="hidden" name="bill" id="bill">
				<input type="hidden" id="id_cabang" name="pembayaran[id_cabang]" value="<?php echo $this->user->id_cabang; ?>">
				<div class="block">
					<div class="block-header">
						<h3 class="block-title">Form <?= isset($module['name']) ? $module['name'] : NULL ;?></h3>
						<div class="block-options">
							<div class="btn-group btn-group-sm">
								<a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
								<button id="simpantabel" type="submit" class="btn btn-outline-primary"><i class="fa fa-save"></i> Simpan</button>
							</div>
						</div>
					</div>
					<div class="block-content">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-row align-items-center">
									<label class="col-md-4 text-right">Supllier</label>
									<div class="col-md-6">
										<select id="select2-pemasok" class="form-control" name="pembayaran[id_pemasok]" required="required">
										<?php if(isset($pembayaran->pemasok->id) && !empty(isset($pembayaran->pemasok->id))) : ?>
										<option selected="selected" value="<?= $pembayaran->pemasok->id;?>"><?= $pembayaran->pemasok->nama;?></option>
										<?php endif; ?>
										</select>
									</div>
								</div>
								<div class="form-group form-row align-items-center">
									<label class="col-md-4 text-right">Nota Pembelian</label>
									<div class="col-md-6">
										<select id="select2-pembelian" class="form-control" name="pembayaran[id_pembelian]" required="required">
										<?php if(isset($pembayaran->pembelian->id) && !empty(isset($pembayaran->pembelian->id))) : ?>
										<option selected="selected" value="<?= $pembayaran->pembelian->id;?>"><?= $pembayaran->pembelian->nomor;?></option>
										<?php endif; ?>
										</select>
									</div>
								</div>		
								<div class="form-group form-row align-items-center">
									<label class="col-md-4 text-right">Tgl Nota</label>
									<div class="col-md-6">
										<input type="text" id="tglnota" class="form-control" name="pembayaran[tgl_nota]" required="required" value="<?= isset($pembayaran->pembelian->tgl_nota) ? $pembayaran->pembelian->tgl_nota : null;?>">
									</div>
								</div>
								<div class="form-group form-row align-items-center">
									<label class="col-md-4 text-right">Tgl buat</label>
									<div class="col-md-6">
										<input type="text" id="tglbuat" class="form-control" name="pembayaran[tgl_buat]" required="required" value="<?= isset($pembayaran->pembelian->tgl_buat) ? $pembayaran->pembelian->tgl_buat : null;?>">
									</div>
								</div>
								<div class="form-group form-row align-items-center">
									<label class="col-md-4 text-right">Tgl Bayar</label>
									<div class="col-md-6">
										<input type="text" id='tglbayar' class="form-control datepicker" name="pembayaran[tgl_bayar]" required="required" value="<?= isset($pembayaran->tgl_bayar) ? $pembayaran->tgl_bayar : null;?>">
									</div>
								</div>
								<div class="form-group form-row align-items-center" hidden="true">
									<label class="col-md-4 text-right">No Nota Pembayaran</label>
									<div class="col-md-6">
										<input type="text" id="nomor" class="form-control" name="pembayaran[nomor]" required="required" value="<?= isset($pembayaran->nomor) ? $pembayaran->nomor : null;?>">
									</div>
								</div>
							</div>
							<div id="preview-pembelian" class="col-md-6">
								<table class="table table-sm table-bordered">
									<tr>
										<td style="width:200px;">Tagihan</td>
										<td id="total_tagihan" class="text-right">0</td>
									</tr>
									<tr>
										<td style="width:200px;">Sudah dibayarkan</td>
										<td id="total_pembayaran" class="text-right">0</td>
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
									<th colspan="7">Rincian Pembayaran</th>
								</tr>
								<tr>
									<th>Metode</th>
									<th>Dari Kas/Bank</th>
									<th>Nomor Giro</th>
									<th>Tgl Giro</th>
									<th>Cashback</th>
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