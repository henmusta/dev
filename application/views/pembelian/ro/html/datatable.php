<div class="content">
	<div class="block">
		<div class="block-header">
			<h3 class="block-title">Data <?= $module['name'];?></h3>
			<input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
			<div class="input-group input-group-sm" style="width:240px;">
				<div class="input-group-prepend">
					<div class="input-group-text">Tgl Input</div>
				</div>
				<input type="text" class="form-control form-control-sm datepicker d-inline-block m-0" value="">
			</div>
		</div>
		<div class="block-content">
			<div class="table-responsive">
				<table id="dt" class="table table-sm table-vcenter table-bordered" width="100%">
					<thead>
						<tr>
							<th>No</th>
							<th>Tgl Input</th>
							<th>Tgl Nota</th>
							<th>Supplier</th>
							<th>No.Nota</th>
							<th>Status Ro</th>
							<th>#</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>