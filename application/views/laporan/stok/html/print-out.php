<?php
setlocale(LC_TIME, 'id_ID');
$date = strftime( "%d %B %Y", time());
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
	<base href="<?= base_url('/');?>">
	<title><?= "Laporan Stok - " . $date;?></title>
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
	table tr > th:nth-child(1),
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
	}
	</style>
</head>
<body>
	<div class="container-fluid">
		<div class="print-out">
			<div class="print-out-header">
				<h1 class="print-out-title"><?= strtoupper($module['name']);?></h1>
			</div>
			<div class="print-out-body">
				<table>
					<thead>
						<tr>
							<th>ID Produk</th>
							<th>Nama Produk</th>
							<th>Stok</th>
							<th>Satuan</th>
						</tr>
					</thead>
					<tbody>
					<?php
					$category = ""; 
					foreach($data AS $row) {
						if($category != $row->nama_jenis){
							echo '<tr class="tr-category"><td>'. $row->id_jenis .'</td><td colspan="3"><strong>'. $row->nama_jenis .'</strong></td></tr>';
						}
						echo '
						<tr class="tr-item">
							<td>'. $row->id_jenis .' -- '. $row->id_barang . '</td>
							<td>'. $row->nama_barang .'</td>
							<td>'. $row->saldo_barang .'</td>
							<td>'. $row->nama_satuan .'</td>
						</tr>
						';
						$category = $row->nama_jenis;
					}
					?>
					</tbody>
				</table>
			</div>
			<div class="print-out-footer">
				<div style="display:flex; justify-content: flex-end;">
					<div>
						<p style="margin:0">Jember, <?= $date;?></p>
						<p style="margin:0">Pimpinan</p>
						<br>
						<br>
						<br>
						<p><strong>Vanny Hadiwijaya, S.Kom</strong></p>
					</div>
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