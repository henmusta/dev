<?php
setlocale(LC_TIME, 'id_ID');
$start 	= strftime( "%d %B %Y", strtotime($period['date_start']));
$end 	= strftime( "%d %B %Y", strtotime($period['date_end']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
	<base href="<?= base_url('/');?>">
	<title><?= $module['name'] . " Periode " . $start .  " S/D " . $end ;?></title>
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
		font-size:1.5rem;
		text-align:center;
		margin-bottom:1rem;
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
			<div class="text-center" style="text-align:center;">
        		<img src="<?php echo base_url() ?>assets\media\photos\logo.jpeg" alt="" style="width: 100px; height: auto;">
    		</div>
				<h1 class="print-out-title"><?= strtoupper($module['name']);?><br>
				<small>Periode : <?= $start; ?> S/D  <?= $end; ?></small>
				</h1>
			</div>
			<div class="print-out-body">
				<table>
						<thead>
                            <tr>
                                <th colspan="9">Pembelian Cash/debit</th>
                            </tr>
                            <tr>
						    	<th class="text-right" style="width: 60px;">No</th>
                                <th>Nama Toko</th>
                                <th>Tgl Input</th>
                                <th>No Nota</th>
                                <th>Metode</th>
                                <th>Jumlah</th>
                                <th class="text-right" style="width: 120px;">Diskon</th>
                                <th class="text-right" style="width: 120px;">Total</th>
                            </tr>
                        </thead>
                     
					<tbody>
					<?php
					$no = 1;
					foreach($data['data'] AS $row)  {
						$row = (object)$row;
						echo '
						<tr class="tr-item">
						    <td>'. $no++ . '</td>
							<td>'. $row->namap . '</td>
							<td>'. $row->tgl_buat . '</td>
							<td>'. $row->nomor . '</td>
							<td>'. $row->metode_beli . '</td>
							<td>Rp.'. number_format($row->total_tagihan) . '</td>
							<td>Rp.'. number_format($row->diskon) . '</td>
							<td>Rp.'. number_format($row->total_pembayaran) . '</td>
						</tr>
						';
					}
					
					?>
					
					</tbody>
					<tfoot>
					<?php
					$totaltunai = 0;
					foreach($data['data'] AS $row) {
						$row = (object)$row;
						$totaltunai += $row->total_pembayaran;
					}
					echo '<tr><td colspan="7">TOTAL</td>
					<td>Rp.' .  number_format($totaltunai) . '</td>';
					?>
					</tfoot>
				</table><br><br>
				<table>
				<thead>
                            <tr>
                                <th colspan="11">Pembelian Giro</th>
                            </tr>
                            <tr>
                                <th class="text-right" style="width: 60px;">No</th>
                                <th>Nama Toko</th>
                                <th>Tgl Input</th>
                                <th>No Nota</th>
                                <th>Tgl Giro</th>
                                <th>No Giro</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                     
					<tbody>
					<?php
					$category = ""; 
					$no = 1;
					foreach($data1['data'] AS $row)  {
						$row = (object)$row;
						echo '
						<tr class="tr-item">
						    <td>'. $no++ . '</td>
							<td>'. $row->nama_pemasok . '</td>
							<td>'. $row->tgl_buat . '</td>
							<td>'. $row->nomor . '</td>
							<td>'. $row->tgl_giro . '</td>
							<td>'. $row->nogiro . '</td>
							<td>Rp.'. number_format($row->total_pembayaran) . '</td>
						</tr>
						';
					}
					
					?>
					
					</tbody>
					<tfoot>
					<?php
					$totalgiro = 0;
					foreach($data1['data'] AS $row) {
						$row = (object)$row;
						$totalgiro += $row->total_pembayaran;
					}
					echo '<tr><td colspan="6">TOTAL</td>
					<td>Rp.' .  number_format($totalgiro) . '</td>';
					?>
					</tfoot>
				</table><br><br>
				<table>
						<thead>
                            <tr>
                                <th colspan="5">Pembelian Bon</th>
                            </tr>
                            <tr>
                                <th class="text-right" style="width: 60px;">No</th>
                                <th>Nama Toko</th>
                                <th>Tgl Input</th>
                                <th>No Nota</th>
                                <th class="text-right" style="width: 120px;">Total</th>
                            </tr>
                        </thead>
                     
					<tbody>
					<?php
					$category = ""; 
					$no = 1;
					foreach($data2['data'] AS $row)  {
						$row = (object)$row;
						echo '
						<tr class="tr-item">
						    <td>'. $no++ . '</td>
							<td>'. $row->nama . '</td>
							<td>'. $row->tgl_nota . '</td>
							<td>'. $row->nomor . '</td>
							<td>Rp.'. number_format($row->sisa_tagihan) . '</td>
						</tr>
						';
					}
					?>
					
					</tbody>
					<tfoot>
					<?php
					$totalbon = 0;
					foreach($data2['data'] AS $row) {
						$row = (object)$row;
						$totalbon += $row->sisa_tagihan;
					}
					echo '<tr><td colspan="4">TOTAL</td>
					<td>Rp.' .  number_format($totalbon) . '</td>';
					?>
					</tfoot>
				</table><br><br>
			<table class="table table-sm">
				<tfoot>
					<?php
					$totaltunai = 0;
					foreach($data['data'] AS $row) {
						$row = (object)$row;
						$totaltunai += $row->nominal;
					}
					$totalgiro = 0;
					foreach($data1['data'] AS $row) {
						$row = (object)$row;
						$totalgiro += $row->total_pembayaran;
					}
					$totalbon = 0;
					foreach($data2['data'] AS $row) {
						$row = (object)$row;
						$totalbon += $row->sisa_tagihan;
					}

					$totalall = $totaltunai + $totalgiro + $totalbon;
					echo '<tr><td>TOTAL BELANJA KESELURUHAN</td>
					<td>'.'Rp. '. number_format($totalall) . '</td>';
					?>
					</tfoot>
                </table>
			</div>
		</div>
	</div>
	<script>
	window.print();
	setTimeout(window.close, 0);
	</script>
</body>
</html>