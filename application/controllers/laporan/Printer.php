<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//buat class
class Printer extends CI_Controller 
{

	function __construct()
	{
		parent::__construct ();
		$this->load->model('penjualan/faktur_model','penjualan');
		$this->load->model('pengaturan/Aplikasi_model');
	}
 


	function cetak($pk)
	{
	  $list = $this->penjualan->single_combine($pk);
	  $user = $_SESSION['user'];
	  $num_rows = 1;
      $no = 1;
	  $metode = "";
	  foreach ($list->rincian_pelunasan as $row) {
		  $metode .= $row->metode;
	  }
      $products = array_chunk($list->rincian_penjualan, 20);
      $last = sizeof($products) - 1;
	  if($num_rows > 0) // jika data ada di database
	  {
        $pdf = new FPDF('L', 'pt', array(595.28, 421.45));
        $pdf->AliasNbPages();
        $pdf->AddPage();
        foreach ($products as $key => $rows) {

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(50, 10, $list->cabang->nama, 0, 0);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(350, 15, '');
            $pdf->Cell(0, 10, 'Bandar Lampung , '. date('d M Y', strtotime($list->tgl_nota)), 0, 1);
            $pdf->Cell(72, 10, 'Komplek Pertokoan Pasar Tengah', 0, 0);
            $pdf->Cell(328, 15, '');
            $pdf->Cell(0, 10, 'Kepada Yth : ', 0, 1);
            $pdf->Cell(72, 10, $list->cabang->alamat, 0, 0);
            $pdf->Cell(328, 15, '');
            $pdf->Cell(0, 10, $list->pelanggan->nama, 0, 1);
            $pdf->Cell(72, 10, 'Telp/Hp : '.$list->cabang->telp, 0, 0);
            $pdf->Cell(328, 15, '');
            $pdf->Cell(0, 10, $list->pelanggan->alamat, 0, 1);
            $pdf->Cell(72, 10, 'Wa         : '.$list->cabang->wa, 0, 1);
    
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(282, 10, 'FAKTUR JUAL', 0, 0, 'R');
            $pdf->Cell(118);
            $pdf->Cell(50, 10, '', 0, 0, 'L');
            $pdf->Cell(100, 10, '', 0, 1, 'L');
            $pdf->Cell(290, 5, '----------------------------', 0, 0, 'R');
            $pdf->Cell(110);
            $pdf->Cell(50, 10, '', 0, 0, 'L');
            $pdf->Cell(100, 10, '', 0, 1, 'L');
            $pdf->Cell(210, 5, 'Jenis Pembayaran : '.$metode, 0,0);
            $pdf->Cell(80, 5, 'No : '.$list->nomor, 0, 0, 'C');
            $pdf->Cell(110);
            $pdf->Cell(50, 10, '', 0, 0, 'L');
            $pdf->Cell(100, 10, '', 0, 1, 'L');
            
            if ($key == 0) {
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(189, 20, '================================================================================================================', 0, 1);
                $pdf->Cell(50, 0, 'No.', 0,0,'C');
                $pdf->Cell(100, 0, 'Banyak', 0,0,'L');
                $pdf->Cell(200, 0, 'Nama Barang', 0,0,'L');
                $pdf->Cell(90, 0, 'Harga', 0,0,'R');
                $pdf->Cell(90, 0, 'Jumlah', 0,0,'R');
                $pdf->Ln(0);
                $pdf->Cell(189, 20, '================================================================================================================', 0, 1);
                $pdf->Ln(5);
            }else{
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(189, 20, '================================================================================================================', 0, 1);
                $pdf->Cell(50, 0, '', 0,0,'C');
                $pdf->Cell(100, 0, '', 0,0,'L');
                $pdf->Cell(200, 0, '', 0,0,'L');
                $pdf->Cell(90, 0, '', 0,0,'R');
                $pdf->Cell(90, 0, '', 0,0,'R');
                $pdf->Ln(0);
                $pdf->Cell(189, 20, '', 0, 1);
                $pdf->Ln(5);
            }
            
            
            $total_barang = sizeof($rows);
            $total_qty = 0;
            $total_harga = 0;
            
            foreach ($rows as $row) {
                $total_qty += $row->qty;
                $total_harga += $row->harga;
                $pdf->Cell(50, 5, $no++, 0,0,'C');
                $pdf->Cell(100, 5, $row->qty, 0,0,'L');
                $pdf->Cell(200, 5, $row->nama, 0,0,'L');
                $pdf->Cell(90, 5, number_format($row->harga), 0,0,'R');
                $pdf->Cell(90, 5, number_format($row->total), 0,1,'R');
                $pdf->Ln(5);
            }
            if ($total_barang < 10) {
                for ($i=0; $i < (10-$total_barang); $i++) { 
                    $pdf->Cell(50, 10, '***', 0,1,'C');
                }
            }
            $pdf->Cell(189, 5, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1);
            
            if ($key == $last) {
                $pdf->SetFont('Arial', '', 8);
                if ($total_barang > 10) {
                    $pdf->Ln($total_barang + 60);
                }
                $pdf->Cell(50, 20, 'Total :', 0, 0);
                $pdf->Cell(350, 20, number_format($total_qty), 0, 0);
                $pdf->Cell(29, 20, 'Sub Total           :', 0, 0);
                $pdf->Cell(100, 20, 'Rp. '.number_format($total_harga), 0, 1, 'R');
                $pdf->Cell(400, 10, 'Barang2 telah diterima baik dan cukup', 0, 0);
                $pdf->Cell(29, 10, 'Total Retur         :', 0, 0);
                $pdf->Cell(100, 10, 'Rp. '.number_format($list->retur->nominal), 0, 1, 'R');
                $pdf->Cell(400, 10, 'Retur Barang Rijek Max 15 hari', 0, 0);
                $pdf->Cell(29, 10, 'Cek Nota            :', 0, 0);
                $pdf->Cell(100, 10, 'Rp. '.number_format($list->chek), 0, 1, 'R');
                $pdf->Cell(400, 10, 'Retur Barang Rijek Harus Ada Barcode Barang', 0, 0);
                $pdf->Cell(29, 10, 'Diskon                :', 0, 0);
                $pdf->Cell(100, 10, 'Rp. '.number_format($list->diskon), 0, 1, 'R');
                $pdf->Cell(400, 10, '', 0, 0);
                $pdf->Cell(29, 10, 'Grand Total        :', 0, 0);
                $pdf->Cell(100, 10, 'Rp. '.number_format($list->total_rincian - $list->retur->nominal - $list->diskon + $list->chek), 0, 1, 'R');
                $pdf->Cell(400, 10, '**'.preg_replace('/\s+/', ' ', terbilang($list->total_tagihan)).'Rupiah **', 0, 0);
                $pdf->Cell(29, 10, 'Total Pelunasan :', 0, 0);
                $pdf->Cell(100, 10, 'Rp. '. number_format($list->pelunasan->nominal), 0, 1, 'R');
                $pdf->Cell(400, 10, '', 0, 0);
                $pdf->Cell(29, 10, 'Sisa Tagihan      :', 0, 0);
                $pdf->Cell(100, 10, 'Rp. '.number_format($list->sisa_tagihan), 0, 1, 'R');
                $pdf->Ln(10);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(132, 15, 'Penerima, ', 0, 0, 'C');
                $pdf->Cell(132, 15, '', 0, 0, 'C');
                $pdf->Cell(132, 15, '', 0, 0, 'C');
                $pdf->Cell(132, 15, 'Hormat Kami,', 0, 1, 'C');
            }
    
        }
        $pdf->Output('NOTA PENJUALAN CASH '.$pk.'.pdf','I');		
	  }
	  else // jika data kosong
	  {
		redirect('report');
	  }
  
	  exit();
	}


// 	function cetak($pk)
// 	{
// 	//   $kodebarang = "A001";
  
// 	  // ambil data dengan memanggil fungsi di model
// 	  $list = $this->penjualan->single_combine($pk);
// 	  $num_rows = 1;
  
// 	  if($num_rows > 0) // jika data ada di database
// 	  {
		  
// 		// memanggil (instantiasi) class reportProduct di file print_rekap_helper.php
// 		$a=new reportProduct();
// 		$a->setKriteria("transaksi");
// 		// judul report
// 		$a->setNama("Faktur Penjualan ".$pk);
// 		// buat halaman
// 		$a->AliasNbPages();
// 		// Potrait ukuran A4
// 		$a->AddPage("P","A4");
// 		$image = $this->Aplikasi_model->get();
// 	   //$img = file_get_contents('http://example.com/wp-content/themes/example/map_image_leasing.php/?city=Calgary&suit_type=&min_area=&max_area=');
		
//         $a->image(base_url().'assets/media/photos/'.$image->gambar,13,17,33,20);
// 		$a->SetFont('Arial','B',9);
// 		$a->Cell(1);
// 		$a->Cell(6,10,'',0,0,'L');
// 		$a->Ln(5);


//         $a->SetFont('Arial','B',9);
//         $a->Cell(37);
//         $a->Cell(15 ,10,'AHSANA LAMPUNG',0,0);
// 		$a->SetFont('Arial','',9);
//         $a->Cell(75);
//         $a->Cell(0 ,10,'LAMPUNG, '.longdate_indo(date('Y-m-d')),0,0);
// 		$list = $this->penjualan->single_combine($pk);

// 		$a->Ln(5);
// 		$a->SetFont('Arial','',9);
// 		$a->Cell(37);
// 		$a->Cell(15,10,'Jl. Padang No. 04 Pasar Tengah Tanjung Karang',0,0,'L');
//         $a->Cell(75);
//         $a->Cell(0 ,10,'Kepada Yth : '.$list->pelanggan->nama,0,0);

	
		
// 		$a->Ln(5);
// 		$a->SetFont('Arial','',9);
// 		$a->Cell(37);
// 		$a->Cell(15,10,'Telp : (0721) 264711');
// 		$a->Cell(75);
//         $a->Cell(0 ,10,'Di - ' . $list->pelanggan->alamat,0,0);


//         $a->Ln(5);
// 		$a->SetFont('Arial','',9);
// 		$a->Cell(37);
// 		$a->Cell(15,10,'WA   : 0821 7986 8578 / 0812 6697 2868');
// 		$a->Cell(75);
//         $a->Cell(0 ,10,'Telp - ' . $list->pelanggan->telp,0,0);
//         $a->Ln(5);

// 		$metode = 0;
// 		foreach($list->rincian_pelunasan as $baris)
// 		{
// 			$metode = $baris->metode;
// 		}
	
//         $a->SetFont('Arial','',8);
// 		$a->Cell(10);
// 		$a->Cell(55,10,'',0,0,'C');
//         $a->Cell(12);
// 		$a->SetFont('Arial','U',8);
// 		$a->Cell(55,10,'         FAKTUR JUAL         ',0,0,'C');
//         $a->Cell(12);
// 		$a->SetFont('Arial','',8);
// 		$a->Cell(55,10,'',0,0,'C');
// 		$a->Ln(5);

//         $a->SetFont('Arial','',8);
// 		$a->Cell(3);
// 		$a->Cell(62,8,'Jenis Payment : '.$metode,0,0,'L');
//         $a->Cell(12);
// 		$a->Cell(55,8,'No : '. $list->nomor,0,0,'C');
//         $a->Cell(12);
// 		// $a->Cell(55,8,'Alamat :',0,0,'C');
		
// 		$a->Ln(5);
  
	
// 		$a->SetFont('Arial','',10);
// 		$a->SetWidths(array(10,20,99,30,30));
// 		$a->SetFont('Arial','B',10);
// 		$a->Ln(2);
// 		$a->Cell(10,6,'No.','LBT',0,'C');	
// 		$a->Cell(20,6,'Banyak','LBT',0,'C');			
// 		$a->Cell(99,6,'Produk','LBT',0,'C');
// 		$a->Cell(30,6,'Harga','LBT',0,'C');
// 		$a->Cell(30,6,'Jumlah','LBRT',0,'C');	
// 		$a->Ln();
// 		$a->SetFont('Arial','',8);
// 		$a->SetAligns(array('C','C','L','R','R'));
// 		$n=0;
// 		foreach($list->rincian_penjualan as $r)
// 		{
// 		  $n++;
// 		  $a->Row(array(($n), intval($r->qty), $r->nama, number_format($r->harga,0,',','.'), number_format($r->total,0,',','.')));
// 		  $a->Ln(0);
// 		}
		
// 		$a->Cell(110,5,'Terbilang : **'.preg_replace('/\s+/', ' ', terbilang($list->total_tagihan)).'Rupiah **','LT',0,'L');
// 		$a->Cell(34,5,'Sub Total           :','T',0,'L');
// 		$a->Cell(45,5,'Rp. '.number_format($list->total_rincian),'TR',0,'R');
// 		$a->Ln(5);

// 		$a->Cell(110,5,'Barang yang sudah dibeli tidak dapat','L',0,'L');
// 		$a->Cell(34,5,'Total Retur         :','',0,'L');
// 		$a->Cell(45,5,'Rp. '.number_format($list->retur->nominal),'R',0,'R');
// 		$a->Ln(5);
// 		$a->Cell(110,5,'ditukar / dikembalikan kecuali ada','L',0,'L');
// 		$a->Cell(34,5,'Cek Nota            :','',0,'L');
// 		$a->Cell(45,5,'Rp. '.number_format($list->chek),'R',0,'R');
// 		// $a->Cell(1,5,'','',0,'L');
// 		// $a->Cell(74,5,'Cek Nota        :','R',0,'L');
// 		$a->Ln(5);
// 		$a->Cell(110,5,'perjanjian sebelumnya','L',0,'L');
// 		$a->Cell(34,5,'Diskon                :','',0,'L');
// 		$a->Cell(45,5,'Rp. '.number_format($list->diskon),'R',0,'R');
// 		// $a->Cell(1,5,'','',0,'L');
// 		// $a->Cell(74,5,'Diskon          :','R',0,'L');
// 		$a->Ln(5);
// 		$a->Cell(110,5,'Memo : ------------------------------','L',0,'L');
// 		$a->Cell(34.5,5,'Grand Total        :','',0,'L');
// 		$a->Cell(44.5,5,'Rp. '.number_format($list->total_rincian - $list->retur->nominal - $list->diskon + $list->chek),'R',0,'R');

// 		$a->Ln(5);
// 		$a->Cell(110,5,'-------------------------------------','L',0,'L');
// 		$a->Cell(34.5,5,'Total Pelunasan :','',0,'L');
// 		$a->Cell(44.5,5,'Rp. '. number_format($list->pelunasan->nominal),'R',0,'R');
// 		// $a->Cell(1,5,'','',0,'L');
// 		// $a->Cell(74,5,'Total Pelunasan :','R',0,'L');
// 		$a->Ln(5);
// 		$a->Cell(110,5,'-------------------------------------','BL',0,'L');
// 		// $a->Cell(1,5,'','B',0,'L');
// 		// $a->Cell(74,5,'Sisa Tagihan    :','BR',0,'L');
// 		$a->Cell(34.5,5,'Sisa Tagihan      :','B',0,'L');
// 		$a->Cell(44.5,5,'Rp. '.number_format($list->sisa_tagihan),'BR',0,'R');
// 		// $a->Cell(190,6,'Terbilang :'.preg_replace('/\s+/', ' ', terbilang($list->total_tagihan)).'Rupiah','LBRT',0,'R');
// 		$a->Ln();
// 		$a->Cell(189.5,27,'','B',0,'C');
// 		$a->Ln(0);
// 		$a->SetFont('Arial','',9);
// 		$a->Cell(20);
// 		$a->Cell(28,10,'Admin,',0,0,'C');
		
// 		$a->Ln(0);		
// 		$a->SetFont('Arial','',9);
// 		$a->Cell(157);
// 		$a->Cell(25,10,'Konsumen,',0,0,'C');
		
// 		$a->Ln(15);
// 		$a->SetFont('Arial','BU',12);
// 		$a->Cell(20);
// 		$a->Cell(28,10,'(                         )',10,0,'C');
		
// 		$a->SetFont('Arial','',12);
// 		$a->Cell(34);
// 		$a->Cell(25,10,'                           ',10,0,'C');
		
// 		$a->SetFont('Arial','BU',12);
// 		$a->Cell(50);
// 		$a->Cell(25,10,'(                         )',10,0,'C');
		
//         // $a->AutoPrint(true);
// 		$a->Output('NOTA PENJUALAN CASH '.$pk.'.pdf','I');		
// 	  }
// 	  else // jika data kosong
// 	  {
// 		redirect('report');
// 	  }
  
// 	  exit();
// 	}
  }


	






	function terbilang($nilai) 
    {
        $nilai = abs($nilai);
        $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " ". $huruf[$nilai];
        } else if ($nilai <20) {
            $temp = terbilang($nilai - 10)." Belas ";
        } else if ($nilai < 100) {
            $temp = terbilang($nilai/10)." Puluh".terbilang($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " Seratus". terbilang($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = terbilang($nilai/100)." Ratus".terbilang($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " Seribu". terbilangt($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = terbilang($nilai/1000)." Ribu".terbilang($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = terbilang($nilai/1000000)." Juta".terbilang($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = terbilang($nilai/1000000000)." Milyar".terbilang(fmod($nilai,1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = terbilang($nilai/1000000000000)." Triliun".terbilang(fmod($nilai,1000000000000));
        }     
          return $temp;
    }
?>