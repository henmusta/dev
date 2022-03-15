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
			<img src="<?php echo base_url() ?>assets\media\photos\<?php echo $aplikasi->gambar; ?>" alt="" style="width: 180px; height: 80px; margin-right: 10px;">
    		</div>
				<h1 class="print-out-title"><?= strtoupper($module['name']);?><br>
					<small>Periode : <?= $start; ?> S/D  <?= $end; ?></small>
				</h1>
			</div>
			<div class="print-out-body">
			<table>
                        <thead>
                            <tr>
						`		<th class="text-right" style="width: 60px;">No</th>
                                <th>Nama</th>
                                <th>tgl Nota</th>
                                <th>No Nota</th>
                                <th>Jumlah Pembelian Rp.</th>
                                <th>Diskon Rp.</th>
								<th>Cek Nota Rp.</th>
								<th>Retur Rp.</th>
                                <th>Laba Jual Rp.</th>
                                <th>Laba Retur Rp.</th>
                                <th>Pembelian Bersih Rp.</th>
                                <th>Laba Akhir Rp.</th>
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
							<td>'. $row->nama_p . '</td>
							<td>'. $row->tgl_nota . '</td>
							<td>'. $row->nomor . '</td>
							<td style="text-align: right">'. number_format($row->jumlah) . '</td>
							<td style="text-align: right">'. number_format($row->diskon) . '</td>
							<td style="text-align: right">'. number_format($row->chek) . '</td>
							<td style="text-align: right">'. number_format($row->notaretur) . '</td>
							<td style="text-align: right">'. number_format($row->laba) . '</td>
							<td style="text-align: right">'. number_format($row->laba_retur) . '</td>
							<td style="text-align: right">'. number_format($row->total) . '</td>
							<td style="text-align: right">'. number_format($row->laba_akhir) . '</td>
						</tr>
						';
					}
					?>
                        </tbody>

					<tfoot>
						<?php

		
					$totallaba = 0;
					foreach($data['data'] AS $row) {
						$row = (object)$row;
						$totallaba += $row->laba;
					}

					echo '<tr>
					<th colspan="8" class="font-w600 text-right">Total Laba Jual</th>
					<th>'.'Rp.'. number_format($totallaba) .'</th>
					</tr>';


					$totallabaretur = 0;
					foreach($data['data'] AS $row) {
						$row = (object)$row;
						$totallabaretur += $row->laba_retur;
					}

					echo '<tr>
					<th colspan="9" class="font-w600 text-right">Total Laba Retur</th>
					<th>'.'Rp.'. number_format($totallabaretur) .'</th>
					</tr>';

					$totaltunai = 0;
					foreach($data['data'] AS $row) {
						$row = (object)$row;
						$totaltunai += $row->total;
					}
	
					echo '<tr>
					<th colspan="10" class="font-w600 text-right">Total Penjualan</th>
					<th>'.'Rp.'. number_format($totaltunai) . '</th>
					</tr>';

					
					$totallabaakhir = 0;
					foreach($data['data'] AS $row) {
						$row = (object)$row;
						$totallabaakhir += $row->laba_akhir;
					}

					echo '<tr>
					<th colspan="11" class="font-w600 text-right">Total Laba Akhir</th>
					<th>'.'Rp.'. number_format($totallabaakhir) .'</th>
					</tr>';
					?>
				</tfoot>
					
                    </table>
			</div>
			</div>
		</div>
	</div>
	<script>
	window.print();
	setTimeout(window.close, 0);
	</script>
</body>
</html>