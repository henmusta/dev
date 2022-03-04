<div class="content">
	<div class="block">
		<div class="block-content">
			<form class="form-inline md-2" target="_blank" method="POST" action="<?= $module['url'];?>/print-out">
			<input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
				<label class="mr-2">Tanggal</label>
				<div class="input-group mr-2">
					<input type="text" id="start" class="form-control flatpickr" name="filter[date_start]" value="<?= date('Y-m-01')?>" >
					<div class="input-group-append">
						<div class="input-group-text">s/d</div>
					</div>
					<input type="text" class="form-control flatpickr" name="filter[date_end]" value="<?= date('Y-m-d')?>">	
				</div>
				<button type="submit" class="btn btn-outline-primary"><i class="fa fa-print"></i> Cetak</button>
				<button id="excel" name="excel" type="button" class="btn btn-outline-primary"><i class="fas fa-download"></i>Cetak Excel</button>
				<a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
			</form>
			<hr>
			<table id="dt" class="table table-sm table-vcenter table-bordered" width="100%">
			<thead>
      		  <tr>
                <th>Tanggal</th>
                                <th>Keterangan</th>
                                 <th></th>
                                <th>Debit</th>
                                <th>Kredit</th>
                                <th class="font-w600 text-right">Saldo</th>
			  </tr>
			</thead>
			<tfoot>
				<tr>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
		
				</tr>
			</tfoot>
			</table>
		</div>
	</div>
</div>
<style>
tr.group, tr.group:hover {
    background-color: #ddd !important;
}
.pocell {
    font-weight:bold;
}
</style>