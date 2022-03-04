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
	<title><?= $module['name'] . " Per tanggal " . $start . " s/d " . $end;?></title>
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
			<div style="text-align:center;">
        		<img src="<?php echo base_url() ?>assets\media\photos\logo.jpeg" alt="" style="width: 100px; height: auto;">
    		</div>
				<h1 class="print-out-title"><?= strtoupper($module['name']);?> JAKARTA<br>
					<small>Per Tanggal : <?= $start . ' s/d ' . $end; ?></small>
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
				<th>TAHUN</th>
				<th>ID PELANGGAN</th>
				<th>ID PRODUK</th>
				<th>TOTAL</th>
			</tr>
		</thead>
		<tbody>';
$a=array();
$subtotal_plg = $total = 0;
foreach ($data['data'] as $key => $row)
{
    // $a = (object)$data['data'];
	$subtotal_plg += $row['sisa_tagihan'];
   
	echo '<tr>
			<td>'.$row['tgl_buat'].'</td>
			<td>'.$row['nama_pemasok'].'</td>
			<td>'.$row['nomor'].'</td>
			<td class="right">'.format_ribuan($row['sisa_tagihan']).'</td>
		</tr>';
	if (@$data['data'][$key+1]['nama_pemasok'] != $row['nama_pemasok']) {
			echo '<tr class="subtotal">
				<td></td>
				<td>SUB TOTAL</td>
				<td></td>
				<td class="right">'.format_ribuan($subtotal_plg).'</td>
			</tr>';
			$subtotal_plg = 0;
		}
    $total += $row['sisa_tagihan'];
}

// GRAND TOTAL
echo '<tr class="total">
		<td></td>
		<td>GRAND TOTAL</td>
		<td></td>
		<td class="right"> ' . format_ribuan($total) . '</td>
	</tr>
	</tbody>
</table>';
?>
			</div>
		</div>
	</div>
	<script>
	window.print();
	setTimeout(window.close, 0);
	</script>
</body>
</html>