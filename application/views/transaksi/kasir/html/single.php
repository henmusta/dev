<!-- <div class="content">    
    <div class="block block-rounded">
        <div class="block-header">
            <h3 class="block-title"><small>Tanggal Print : <?=date_indo(date('Y-m-d'));?> </small></h3>
            <div class="block-options">
                <a href="javascript:history.back();" class="btn-block-option"><i class="fa fa-reply"></i> Kembali</a>
                <button type="button" class="btn-block-option" onclick="One.helpers('print');">
                    <i class="si si-printer mr-1"></i> Print Faktur
                </button>
            </div>
        </div>
				<div class="block-content">

						<div class="logo">
							<img src="<?php echo base_url() ?>assets\media\photos\logo.jpeg" alt="" style="width: 100px; height: auto; margin-left: 22px;">
						</div>

						<div class="py-2 px-4">
							
						</div>

					

				</div>
        </div>
    </div>
</div> -->



<div class="content">    
    <div class="block block-rounded">
        <div class="block-header">
            <h3 class="block-title"><small>Tanggal Print : <?=date_indo(date('Y-m-d'));?> </small></h3>
            <div class="block-options">
                <a href="javascript:history.back();" class="btn-block-option"><i class="fa fa-reply"></i> Kembali</a>
                <button type="button" class="btn-block-option" onclick="One.helpers('print');">
                    <i class="si si-printer mr-1"></i> Print Faktur
                </button>
            </div>
        </div>
        <div class="block-content">
        <div class="logo">
			<img src="<?php echo base_url() ?>assets\media\photos\logo.jpeg" alt="" style="width: 100px; height: auto; margin-left: 22px;">
		</div>
            <div class="py-2 px-4">
			<div class="row mb-4">
								<table style="width:100%">
									<tr>
										<th width="10%">Tanggal</th>
										<th width="0%">: <?= $transaksi->tgl_nota;?> </th>
									</tr>
									<tr>
										<th width="10%">Kas Awal</th>
										<th width="0%">: Rp. <?= number_format($transaksi->modal);?> </th>
									</tr>
									<tr>
										<th width="10%">Penjualan</th>
										<th width="0%">: Rp. <?= number_format($transaksi->penjualan);?> </th>
									</tr>
									<tr>
										<th width="10%">Setoran</th>
										<th width="0%">: Rp. <?= number_format($transaksi->setoran);?> </th>
									</tr>
								</table> 
							</div>
						
						<div class="table-responsive push">
							<table class="table table-sm">
								<thead>
									<tr>
										<th colspan="5">Rincian Kas/Bank</th>
									</tr>
									<tr>
										<th>No</th>
										<th>Nama</th>
										<th class="text-right" style="width: 60px;">nominal</th>
									</tr>
								</thead>
								<tbody>
								<?php $no = 0; foreach($transaksi->rincian_transaksi AS $row) : $no++; ?>
								<tr>
									<td class="text-center"><?= $no;?></td>
									<td><?= $row->akun;?></td>
									<td class="text-right" style="width: 120px;"><?= number_format($row->total);?></td>
								</tr>
								<?php endforeach;?>
							<!-- <tr>
                                <td colspan="7" class="font-w600 text-right">Total Rincian Kas/bank</td>
                                <td class="text-right"><?= number_format($pembelian->total_pembayaran);?></td>
                            </tr> -->
							
								</tbody>
							</table>
						</div>


						<div class="py-2 px-4">
							<div class="row mb-4">
								<table style="width:100%">
									<tr>
										<th width="15%"> Total Biaya</th>
										<th width="0%">: <?= number_format($transaksi->biaya);?> </th>
									</tr>
								</table> 
							</div>
						</div>
						
						<div class="table-responsive push">
							<table class="table table-sm">
								<thead>
									<tr>
										<th colspan="8">Rincian Biaya</th>
									</tr>
									<tr>
										<th>No</th>
										<th>Nama</th>
										<th class="text-right" style="width: 120px;">Nominal</th>
									</tr>
								</thead>
								<tbody>
								<?php $no = 0; foreach($transaksi->rincian_transaksi_biaya AS $row) : $no++; ?>
								<tr>
									<td class="text-center"><?= $no;?></td>
									<td><?= $row->akun;?></td>
									<td class="text-right" style="width: 120px;"><?= number_format($row->total);?></td>
								</tr>
								<?php endforeach;?>
								</tbody>
							</table>
						</div>

						<div class="py-2 px-4">
							<div class="row mb-4">
								<table style="width:100%">
									<tr>
										<th width="15%"> Kas</th>
										<th width="0%">: Rp. <?= number_format($transaksi->nominal);?> </th>
									</tr>
									<tr>
										<th width="15%"> Rumus</th>
										<th width="0%">: Rp. <?= number_format($transaksi->rumus);?> </th>
									</tr>
									<tr>
										<th width="15%"> Register</th>
										<th width="0%">: Rp. <?= number_format($transaksi->register);?> </th>
									</tr>
									<tr>
									<?php 
									if ($transaksi->nominal > $transaksi->rumus)
									{
									  echo '<th width="15%"> Cek </th>
									        <th width="0%">: Register</th>';
									}else if ($transaksi->nominal == $transaksi->rumus)
									{
									  echo '<th width="15%"> Cek </th>
									        <th width="0%">: Balance</th>';
									}else{
									  echo '<th width="15%"> Cek </th>
									 		<th width="0%"  style="background-color:#ff0000">: Selisih/Kesalahan</th>';
									}
									?>
									</tr>

									
								</table> 
							</div>
						</div>
            </div>
        </div>
    </div>
</div>