<?php
setlocale(LC_TIME, 'id_ID');
$start 	= strftime( "%d %B %Y", strtotime($period['start']));
$end 	= strftime( "%d %B %Y", strtotime($period['end']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
	<base href="<?= base_url('/');?>">
	<title><?= $module['name'];?></title>
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
		/* width:100%; */
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
				<h1 class="print-out-title"><?= strtoupper($module['name']);?><br>
				<small>Periode : <?= $start . ' S/D : ' . $end; ?></small>
				</h1>
			</div>
			<div class="print-out-body">
				<table>
					<thead>
						<tr>
							<!-- <th>No</th> -->
							<th style="width:10%">Tgl</th>
							<th>Gaji Karyawan</th>
							<th>Bulanan</th>
							<th>Listrik/telepon</th>
							<th>Angkut</th>
							<th>Ekspedisi</th>
							<th>Peralatan</th>
							<th>Konsumsi</th>
							<th>Dll</th>
							<th>Total</th>
						</tr>
					</thead>
					<tbody>
					<?php
					$category = ""; 
					$totalRegularTime = 0;
					foreach($data['data'] AS $row) {
						$row = (object)$row;
						echo '
						<tr class="tr-item">
							<td >'. $row->tgl_nota . '</td>
							<td>'. $row->gaji . '</td>
							<td>'. $row->bulanan . '</td>
							<td>'. $row->listrik . '</td>
							<td>'. $row->angkut . '</td>
							<td>'. $row->ekspedisi . '</td>
							<td>'. $row->peralatan . '</td>
							<td>'. $row->konsumsi . '</td>
							<td>'. $row->dll . '</td>
							<td>'. $row->total . '</td>
						</tr>
						';
					}
					?>
					</tbody>
					<tfoot>
					<?php
					$totalRegularTime = 0;
					foreach($data['data'] AS $row) {
						$row = (object)$row;
						$totalRegularTime += $row->total;
					}
					echo '<tr><td colspan="9">TOTAL</td>
					<td>' . $totalRegularTime . '</td>';
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