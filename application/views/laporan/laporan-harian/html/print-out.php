<?php
setlocale(LC_TIME, 'id_ID');
$start 	= strftime( "%d %B %Y", strtotime($period['tgl']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
	<style>
	* {
		box-sizing: border-box;
	}
	.print-out-header {
		border-bottom:2px solid #000;
		margin-bottom:1rem;
	}
	.print-out-header .print-out-title {
		font-weight:bold;
		font-size:2rem;
		text-align:left;
		margin-bottom:1rem;
		text-decoration: underline;
	}
	.print-out-header .print-out-subtitle {
		font-weight:bold;
		font-size:1rem;
		text-align:center;
		margin-bottom:1rem;
		border: 3px solid black;
	}
	.print-out-body {
		border-bottom:4px double #000;
	}

	.print-out-footer {
		margin-top:1rem;
	}
	table {
		width:100%;
		border-collapse: collapse;
	}
	table thead > tr > th {
		background-color:#ddd;
		border-bottom:3px double #000;
	}
	table tr > th,
	table tr > td {
		text-align:left;
		font-size:14px;
		padding:2px 6px;
	}
	table tr+tr > th,
	table tr+tr > td {
		border-top:1px solid #000;
	}
/*	table tr > th:nth-child(1),
	table tr > td:nth-child(1) {
		width:100px;
	}
	table tr > th:nth-child(2),
	table tr > td:nth-child(2) {
		width:calc(100% - 300px);
	}
	table tr > th:nth-child(3),
	table tr > td:nth-child(3) {
		text-align:right;
		width:100px;
	}
	table tr > th:nth-child(4),
	table tr > td:nth-child(5) {
		width:100px;
	}
	table tr.tr-item > td:nth-child(2) {
		padding-left:30px;
	}*/
	</style>
</head>
<body>
	<div class="container-fluid">
		<div class="print-out">
			<div class="print-out-header">
				<h1 class="print-out-title"><?= strtoupper($module['laporan']);?><br>
				</h1>
				<h5 class="print-out-subtitle"><?= strtoupper($module['laporan']);?><br>
				<small>Per : <?= strtoupper($module['tanggal']);?></small><br>
				<small><?= strtoupper($module['cabang']);?></small>
				</h5>
			</div>
			<table class="table w-100">
				<tbody>
				<tr>
				<th width="20%">PERSEDIAN AWAL</th>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->stok->persediaan)  ? $print->stok->persediaan : '0') ;?> </td>
						<td width="20%">-</td>
						<td width="20%">-</td>
  				</tr>
				  <th width="20%">PEMBELIAN</th>
						<td width="0%">:</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->rincian_pembelian->persediaan) ? $print->rincian_pembelian->persediaan : '0') ;?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
  				</tr>
				  <th width="20%">RETUR PEMBELIAN</th>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->retur_pembelian->totalretur) ? $print->retur_pembelian->totalretur : '0') ;?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
  				</tr>
				  <th width="20%">PEMBELIAN BERSIH</th>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->pembelian_bersih) ? $print->pembelian_bersih : '0') ;?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
  				</tr>
				  <th width="20%">TOTAL STOK AWAL</th>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->total_stok_awal) ? $print->total_stok_awal : '0');?> </td>
						<td width="20%">-</td>
  				</tr>
				</tbody>
			</table><br>
			<table class="table w-100">
				<tbody>
					<tr>
						<th width="20%">PENJUALAN TUNAI</th>
						<td width="0%">:</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->total_penjualan_tunai) ? $print->total_penjualan_tunai : '0');?> </td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<th width="20%">PENJUALAN KREDIT</th>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<th width="20%">TOTAL PENJUALAN</th>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<th width="20%">LABA PENJUALAN</th>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->laba_penjualan) ? $print->laba_penjualan : '0');?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<th width="20%">TOTAL PENJUALAN TUNAI</th>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->total_penjualan_tunai) ? $print->total_penjualan_tunai : '0');?> </td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<th width="20%">HPP</th>
						<!-- $print->hpp -->
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->hpp) ? $print->hpp : '0');?></td>
					</tr>
					<tr>
						<th width="20%">RETUR PENJUALAN</th>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%" style="text-decoration: underline;"> <?= number_format(isset($print->retur_penjualan->nominal) ? $print->retur_penjualan->nominal : '0');?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<th width="20%">LABA RETUR</th>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->laba_retur_penjualan->laba) ? $print->laba_retur_penjualan->laba : '0');?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<th width="20%">HPP RETUR PENJUALAN</th>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->hppretur) ? $print->hppretur : '0');?></td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<th width="20%">PERSEDIAAN AKHIR</th>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->persediaanakhir) ? $print->persediaanakhir : '0');?></td>
					</tr>
					<tr>
						<th width="20%">OMSET</th>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->rincian_penjualan_omset->omset) ? $print->rincian_penjualan_omset->omset : '0');?></td>
					</tr>
				</tbody>
			</table>
			<p class="print-out-header"></p>
			<p class="print-out-header"></P>
			<table class="table w-100">
				<tbody>
					<tr>
						<th width="20%">KAS AWAL</th>
						<td width="0%">:</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->kasir->modal) ? $print->kasir->modal : '0');?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<th width="20%">PENJUALAN TUNAI</th>
						<td width="0%">:</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->penjualan_tunai_kasir) ? $print->penjualan_tunai_kasir : '0');?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<!-- $print->total_pendapatan -->
					<tr>
						<th width="20%">PENERIMAAN PIUTANG</th>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->rincian_piutang->nominal) ? $print->rincian_piutang->nominal : '0');?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<th width="20%">KU, VIA BANK</th>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					<tr>
						<th width="20%">TOTAL PENDAPATAN</th>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->total_pendapatan) ? $print->total_pendapatan : '0');?></td>
					</tr>
					
					<tr>
						<th width="20%">TOTAL BIAYA PEMBELIAN</th>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->kasir->biaya) ? $print->kasir->biaya : '0');?></td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<th width="20%">SETORAN TUNAI</th>
						<td width="0%">:</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->rincian_kasir_tunai->total) ? $print->rincian_kasir_tunai->total : '0');?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<th width="20%">BRI</th>
						<td width="0%">:</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->rincian_kasir_bri->total) ? $print->rincian_kasir_bri->total : '0');?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<th width="20%">BCA</th>
						<td width="0%">:</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->rincian_kasir_bca->total) ? $print->rincian_kasir_bca->total : '0');?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<th width="20%">BNI</th>
						<td width="0%">:</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->rincian_kasir_bni->total) ? $print->rincian_kasir_bni->total : '0');?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<th width="20%">MANDIRI</th>
						<td width="0%">:</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->rincian_kasir_mandiri->total) ? $print->rincian_kasir_mandiri->total : '0');?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<td width="20%">JUMLAH SETORAN</th>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->kasir->setoran) ? $print->kasir->setoran : '0');?></td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<th width="20%">SALDO AKHIR</th>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->kasir->setoran) ? $print->kasir->nominal: '0');?></td>
					</tr>
				</tbody>
			</table>
			<p class="print-out-header"></p>
			<p class="print-out-header"></P>
			<table class="table w-100">
				<tbody>
					<tr>
						<td width="20%">LABA BRUTO</td>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->bruto) ? $print->bruto : '0');?></td>
						<td width="20%">-</td>
					</tr><br>
					<tr>
						<td width="20%">BEBAN GAJI KARYAWAN</td>
						<td width="0%">:</td>
						<td width="20%"><?= number_format(isset($print->rincian_biaya->gaji) ? $print->rincian_biaya->gaji : '0');?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<td width="20%">BEBAN SEWA TOKO PERBULAN</td>
						<td width="0%">:</td>
						<td width="20%"><?= number_format(isset($print->rincian_biaya->bulanan) ? $print->rincian_biaya->bulanan : '0');?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<td width="20%">BEBAN JAKARTA</td>
						<td width="0%">:</td>
						<td width="20%"><?= number_format(isset($print->rincian_biaya_jakarta->total) ? $print->rincian_biaya_jakarta->total : '0');?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<td width="20%">BEBAN LISTRIK/TELEPON</td>
						<td width="0%">:</td>
						<td width="20%"><?= number_format(isset($print->rincian_biaya->listrik) ? $print->rincian_biaya->listrik : '0');?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					
					<tr>
						<td width="20%">BEBAN ANGKUT</td>
						<td width="0%">:</td>
						<td width="20%"><?= number_format(isset($print->rincian_biaya->angkut) ? $print->rincian_biaya->angkut : '0');?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					
					<tr>
						<td width="20%">BEBAN PERALATAN</td>
						<td width="0%">:</td>
						<td width="20%"><?= number_format(isset($print->rincian_biaya->peralatan) ? $print->rincian_biaya->peralatan : '0');?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<td width="20%">BEBAN EXPEDISI</td>
						<td width="0%">:</td>
						<td width="20%"><?= number_format(isset($print->rincian_biaya->ekspedisi) ? $print->rincian_biaya->ekspedisi : '0');?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<td width="20%">BEBAN KONSUMSI</td>
						<td width="0%">:</td>
						<td><?= number_format(isset($print->rincian_biaya->konsumsi) ? $print->rincian_biaya->konsumsi : '0');?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<td width="20%">BEBAN LAIN-LAIN</td>
						<td width="0%">:</td>
						<td width="20%"><?= number_format(isset($print->rincian_biaya->dll) ? $print->rincian_biaya->dll : '0');?></td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<td width="20%">TOTAL BIAYA</td>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->totalbiaya) ? $print->totalbiaya : '0');?></td>
						<td width="20%">-</td>
					</tr>
					<tr>
						<td width="20%">TOTAL LABA</td>
						<td width="0%">:</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%">-</td>
						<td width="20%" style="text-decoration: underline;"><?= number_format(isset($print->labaoperasional) ? $print->labaoperasional : '0');?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<script>
	// window.print();
	// setTimeout(window.close, 0);
	</script>
</body>
</html>