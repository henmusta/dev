<div class="content">
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content text-center">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Pencairan Giro</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  <input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
			<div class="input-group input-group-sm">
				<div class="input-group-prepend">
					<div class="input-group-text">Tanggal</div>
					</div>
					<input type="text" id="tanggal" class="form-control form-control-sm datepicker " value="<?= date('Y-m-d');?>" name="filter[tgl_opname]">
				</div>
			</div>

    </div>
  </div>
</div>

	<div class="row">
		<div class="col-md-12">
			<div class="block">
				<div class="block-header">
					<h3 class="block-title">Data <?= $module['name'];?></h3>
				</div>
				<div class="block-content">
					<div class="table-responsive">
					<input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
						<table id="dt" class="table table-sm table-vcenter table-bordered" width="100%">
							<thead>
								<tr>
									<th>No</th>
									<th>Tgl nota</th>
                                    <th>Tgl giro</th>
                                    <th>nomor giro</th>
									<th>Status</th>
                                    <th>Total</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
