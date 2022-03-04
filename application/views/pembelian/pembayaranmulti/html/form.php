<div class="content">
	<div class="row">
		<div class="col-md-12">
			<form id="form" method="POST" action="<?= isset($module['action']) ? $module['action'] : NULL ;?>" autocomplete="off">
				<input type="hidden" name="pk" id="pk" value="<?= isset($pembayaran->gabung_nota) ? $pembayaran->gabung_nota : NULL ;?>">
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
							<div class="col-md-4">
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
							</div>
							<div class="col-md-4">
                               <div class="form-group form-row align-items-center">
									<label class="col-md-4 text-right">No Nota</label>
									<div class="col-md-6">
										<input type="text" id='nota' class="form-control" name="pembayaran[gabung_nota]" required="required" value="<?= isset($pembayaran->gabung_nota) ? $pembayaran->gabung_nota : null;?>">
									</div>
								</div>
							</div>
							<div class="col-md-4">
                               <div class="form-group form-row align-items-center">
									<label class="col-md-4 text-right">Tgl Bayar</label>
									<div class="col-md-6">
										<input type="text" id='tglbayar' class="form-control datepicker" name="pembayaran[tgl_bayar]" required="required" value="<?= isset($pembayaran->tgl_bayar) ? $pembayaran->tgl_bayar : null;?>">
									</div>
								</div>
							</div>
						</div>
                        <table id="table-items" class="table table-sm mt-4" width="100%">
							<thead>
								<tr>
									<th colspan="6">Pilih Nota</th>
								</tr>
								<tr>
									<th>Nota</th>
                                    <th>Tanggal Nota</th>
									<th>Tagihan</th>
									<th>Diskon</th>
									<th>Sisa Tagihan</th>
									<th></th>
								</tr>
							</thead>
							<tbody></tbody>
							<tfoot>
								<tr>
									<th colspan="5">
										<div class="btn-group">
											<button type="button" id="idbayar" class="btn btn-sm btn-outline-secondary btn-add-row"><i class="fa fa-plus"></i></button>
										</div>
									</th>
								</tr>
								<!-- <tr>
                                    <th colspan="4" class="text-right">Total Diskon</th>
                                    <th><input id="total_diskon" type="text" class="form-control text-right" readonly="readonly"></th>
                                </tr> -->
								<tr>
                                    <th colspan="4" class="text-right">Diskon Awal</th>
                                    <th><input id="total_disc_awal" type="text" class="form-control redraw-table text-right"  value="<?= isset($pembayaran->multi->diskon) ? $pembayaran->multi->diskon : 0;?>" name="pembayaran[diskon]" readonly="readonly"></th>
						        </tr>
								<tr>
                                    <th colspan="4" class="text-right">Tambah Diskon</th>
                                    <th><input id="total_disc" type="text" class="form-control redraw-table text-right"  value="0"name="pembayaran[tambah_diskon]" ></th>
						        </tr>
                                <tr>
                                    <th colspan="4" class="text-right">Total tagihan</th>
                                    <th><input id="total_bill" type="text" class="form-control redraw-table text-right"  value="0" name="pembayaran[total]"></th>
						        </tr>
                                <tr>
                                    <th colspan="4" class="text-right">Dibayarkan</th>
                                    <th><input id="dibayarkan" type="text" class="form-control redraw-table text-right"  value="0" name="pembayaran[bayar]"></th>
						        </tr>
                                <tr>
                                    <th colspan="4" class="text-right">Sisa Tagihan</th>
                                    <th><input id="sisa_tagihan" type="text" class="form-control text-right" readonly="readonly" value="" name="pembayaran[chek]"></th>
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
									<th colspan="4" class="text-right">Total Pembayaran</th>
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