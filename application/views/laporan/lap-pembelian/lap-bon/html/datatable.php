<div class="content">
	<div class="block">
		<div class="block-content">
			<form class="form-inline md-2" target="_blank" method="POST" action="<?= $module['url'];?>/print-out">
			<input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
				<label class="mr-2">Tanggal</label>
				<div class="input-group mr-2">
					<input type="text" class="form-control flatpickr" name="filter[date_start]" value="<?= date('Y-m-d')?>">
					<div class="input-group-append">
						<div class="input-group-text">s/d</div>
					</div>
					<input type="text" class="form-control flatpickr" name="filter[date_end]" value="<?= date('Y-m-d')?>">
						<label class="col-md-2">Pilih Toko</label>
						<select id="select-toko" class="form-control select2-toko" name="filter[id_pemasok]">
						 		<option value="" selected="selected"></option>
						</select>
				<label class="col-md-2">Cek Bon</label>
				<select id="cek_bon"  class="form-control col-md-3 cek-bon" name="filter[cek_bon]" >  
				    <!-- <option></option> -->
					<option value="1">Belum Lunas</option>
  					<option value="2">Sudah Lunas</option>
				</select>
							
						
				</div>
				<button type="submit" class="btn btn-outline-primary"><i class="fa fa-print"></i> Cetak</button>
				<button id="excel" name="excel" type="button" class="btn btn-outline-primary"><i class="fas fa-download"></i>Cetak Excel</button>
				<a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
			</form>
			<hr>
			<table id="dt" class="table table-sm table-vcenter table-bordered" width="100%">
			<thead>
      		  <tr>
				<th>Total</th>
					<th>tanggal</th>
					<th>Toko</th>
					<th>Nomor</th>
					<th>Total</th>
					<th>Jumlah</th>
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