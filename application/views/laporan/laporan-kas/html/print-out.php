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
			<?php
function format_ribuan ($nilai){
	return number_format ($nilai, 0, ',', '.');
}

// // Ubah hasil query menjadi associative array dan simpan kedalam variabel result
// $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo '<table>
		<thead>
			<tr>
				<th>Tanggal</th>
				<th>Keterangan</th>
				<th>Debit</th>
				<th>Kredit</th>
				<th>Saldo</th>
			</tr>
		</thead>
		<tbody>';
$a=array();
$subtotal_debit = $subtotal_kredit = $debit = $kredit = 0;
foreach ($data['data'] as $key => $row)
{
    // $a = (object)$data['data'];
	$subtotal_debit += $row['debit'];
	$subtotal_kredit += $row['debit'];
   
	echo '<tr>
			<td>'.$row['tanggal'].'</td>
			<td>'.$row['keterangan'].'</td>
			<td class="right">'.format_ribuan($row['debit']).'</td>
			<td class="right">'.format_ribuan($row['kredit']).'</td>
		</tr>';
	if (@$data['data'][$key+1]['tanggal'] != $row['tanggal']) {
			echo '<tr class="subtotal">
				<td></td>
				<td></td>
				<td></td>
				<td>Saldo akhir per tanggal</td>
				<td class="right">'.format_ribuan($row['rumus']).'</td>
			</tr>';
			$subtotal_debit = 0;
			$subtotal_kredit = 0;
		}
    $debit += $row['debit'];
	$kredit += $row['kredit'];
}

// GRAND TOTAL
echo '
	</tbody>
</table>';
?>
		
					
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