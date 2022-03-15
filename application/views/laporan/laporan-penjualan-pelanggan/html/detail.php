<div class="content">
	<div class="block">
		<div class="block-content">
			<form class="form-inline md-2" target="_blank" method="POST" action="<?= $module['url'];?>/print-out">
			<input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
				<label class="mr-2">Tanggal</label>
				<div class="input-group mr-2">
					<input type="text" class="form-control flatpickr" id="date_start" name="filter[date_start]" value="<?= date('Y-m-d')?>">
					<div class="input-group-append">
						<div class="input-group-text">s/d</div>
					</div>
					<input type="text" class="form-control flatpickr" id="date_end" name="filter[date_end]" value="<?= date('Y-m-d')?>">
				</div>
				<a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
				<button type="submit" class="btn btn-outline-primary"><i class="fa fa-print"></i> Cetak</button>
				<button id="excel" name="excel" type="button" class="btn btn-outline-info"><i class="fas fa-download"></i>Cetak Excel</button>
			</form>
	
			<hr>
			<table id="dt" class="table table-sm table-vcenter table-bordered" width="100%">
				<thead>
					<tr>
                               <th class="text-right" style="width: 60px;">No</th>
                                <th>Nama</th>
                                <th>tgl Nota</th>
                                <th>No Nota</th>
                                <th>Jumlah Pembelian</th>
                                <th>Diskon</th>
								<th>Cek Nota</th>
								<th>Retur</th>
                                <th>Laba Jual</th>
                                <th>Laba Retur</th>
                                <th>Pembelian Bersih</th>
                                <th>Laba Akhir</th>
					</tr>
				</thead>
				<tbody></tbody>
				<tfoot>
               <tr>
                <th colspan="7">Total Laba Jual/Laba Retur/Total Penjualan/Laba Akhir :</th>
				<th ></th>
                <th ></th>
				<th ></th>
				<th ></th>
					<th ></th>

            	</tr>
        </tfoot>
			</table>
		</div>
	</div>
</div>