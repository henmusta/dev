<?php 
$transaksi = isset($data) ? $data : (object)[];
?>


<div class="content">
	<form id="form" method="POST" action="<?= isset($module['action']) ? $module['action'] : NULL ;?>" autocomplete="off">
		<input type="hidden" name="pk" value="<?= isset($data->id) ? $data->id : NULL ;?>">
				<div class="block">
					<div class="block-header">
						<h3 class="block-title">Input Transaksi Jakarta</h3>
						<div class="block-options">
						<div class="btn-group btn-group-sm">
							<a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
							<button type="submit" class="btn btn-outline-primary"><i class="fa fa-save"></i> Simpan</button>
						</div>
						</div>
					</div>
					<div class="block-content">
						<div class="form-group form-row">
							<div class="row">
						<label class="col">Biaya</label>
							<div class="col-md-6">
							<input type="hidden" id="id_cabang" name="tf_jakarta[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
							<input name="tf_jakarta[nominal]" id="totalItems" type="text" class="form-control text-right" readonly="readonly" value="<?= isset($data->nominal) ? $data->nominal : NULL;?>">	
							</div>
							<label class="col">Tanggal</label>
							<div class="col-md-3">
								<input id="tgl-nota" type="text" class="form-control" name="tf_jakarta[tgl_nota]" required="required" value="<?= isset($data->tgl_nota) ? $data->tgl_nota : date('Y-m-d');?>">
							</div>
						</div>
						</div>

						<table id="table-akun" class="table ">
							<thead>
								<th>AKUN</th>
								<th>Total</th>
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
							</tfoot>
						</table>
							
					</div>
				</div>
			</form>
</div>
