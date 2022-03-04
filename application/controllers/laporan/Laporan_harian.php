<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Laporan_harian extends GT_Controller {
	public function __construct(){
		parent::__construct();
		$this->auth 	= (object)$this->session->userdata('auth');
		$this->user 	= (object)$this->session->userdata('user');
		$this->cabang 	= (object)$this->session->userdata('cabang');
		$this->load->model('laporan/lap_harian');
	}
	public function index(){
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan Harian/Bulanan/Tahunan',
				'url'	=> 'laporan/laporan-harian'
			) 
		);
		$head = array(
			'stylesheets'		=> array(
				'assets/js/plugins/datatables/DataTables-1.10.21/css/dataTables.bootstrap4.min.css',
				'assets/js/plugins/datatables/Select-1.3.1/css/select.bootstrap4.min.css',
				'assets/js/plugins/sweetalert2/sweetalert2.min.css',
				'assets/js/plugins/select2/css/select2.min.css',
				'assets/js/plugins/select2/css/select2-bootstrap4.min.css',
				'assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css',
				'assets/js/plugins/EasyAutocomplete-1.3.5/easy-autocomplete.min.css'
			),
			'heading' => array('title'=>$data['module']['name'])
		);
		$foot = array(
			'javascripts'	=> array(
				'assets/js/plugins/select2/js/select2.full.min.js',
				'assets/js/plugins/jquery-validation/jquery.validate.min.js',
				'assets/js/plugins/sweetalert2/sweetalert2.min.js',
				'assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js',
				'assets/js/plugins/datatables/DataTables-1.10.21/js/jquery.dataTables.min.js',
				'assets/js/plugins/datatables/DataTables-1.10.21/js/dataTables.bootstrap4.min.js',
				'assets/js/plugins/auto-numeric/AutoNumeric.min.js',
				'assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js',
				'assets/js/plugins/EasyAutocomplete-1.3.5/jquery.easy-autocomplete.min.js'
			),
			'scripts' 		=> array(
				$this->load->view($data['module']['url'] . '/javascript/datatable', $data, TRUE)
			)
		);

		$this->html_head($head);
		$this->load->view($data['module']['url'] . '/html/generate',$data);
		$this->html_foot($foot);
	}
	public function print_out_hari(){
		$this->load->model('laporan/lap_harian');
		$params = $this->input->post(NULL, TRUE);
		$cabang = $params['filter']['id_cabang'];
		if ($cabang == 4)
		{
			$nama = 'TOKO AHSANA ABG';
		}
		else if ($cabang == 5)
		{
			$nama = 'TOKO AHSANA KID"S';
		}
		else
		{
			$nama = 'TOKO AHSANA HIJAB';
		}

		// $params = $this->input->post(NULL, TRUE);
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan Harian',
				'cabang' 	=> $nama,
				'laporan' => 'LAPORAN HARIAN',
				'tanggal' => date('Y-m-d', strtotime($params['filter']['tglhari'])),
				'url'	=> 'laporan/laporan-harian'
			),
			'params'	=> $params,
			'period' => array(
				'tgl'	=> $params['filter']['tglhari'],
			),
			'print'	=> $this->lap_harian->printhari(
				array(
					'params' 	=> $params
				)
			)
		);
		$result = $this->load->view($data['module']['url'] . '/html/print-out', $data);
	}

	public function print_out_bulan(){
		$this->load->model('laporan/lap_harian');

		$params = $this->input->post(NULL, TRUE);
		$cabang = $params['filter']['id_cabang'];
		if ($cabang == 4)
		{
			$nama = 'TOKO AHSANA ABG';
		}
		else if ($cabang == 5)
		{
			$nama = 'TOKO AHSANA KID';
		}
		else
		{
			$nama = 'TOKO AHSANA HIJAB';
		}
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan Harian',
				'cabang' 	=> $nama,
				'laporan' => 'LAPORAN BULANAN',
				'tanggal' => date('Y-m', strtotime($params['filter']['tglbulan'])),
				'url'	=> 'laporan/laporan-harian'
			),
			'params'	=> $params,
			'period' => array(
				'tgl'	=> $params['filter']['tglbulan']
			),
			'print'	=> $this->lap_harian->printbulan(
				array(
					'params' 	=> $params
				)
			)
		);

		$this->load->view($data['module']['url'] . '/html/print-out', $data);
	}

	public function print_out_tahun(){


		$params = $this->input->post(NULL, TRUE);
		$cabang = $params['filter']['id_cabang'];
		if ($cabang == 4)
		{
			$nama = 'TOKO AHSANA ABG';
		}
		else if ($cabang == 5)
		{
			$nama = 'TOKO AHSANA  KID"S';
		}
		else
		{
			$nama = 'TOKO AHSANA HIJAB';
		}
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan Harian',
				'cabang' 	=> $nama,
				'tanggal' => date('Y', strtotime($params['filter']['tgltahun'])),
				'laporan' => 'LAPORAN TAHUNAN',
				'url'	=> 'laporan/laporan-harian'
			),
			'params'	=> $params,
			'period' => array(
				'tgl'	=> $params['filter']['tgltahun']
			),
			'print'	=> $this->lap_harian->printtahun(
				array(
					'params' 	=> $params
				)
			)
		);

		$this->load->view($data['module']['url'] . '/html/print-out', $data);
	}

	public function excel_hari($tgl, $id_cabang)
	{
		$params = $this->input->post(NULL, TRUE);
		
		$params = array(
			'filter' => array(
				'tglhari' 	=> $tgl,
				'id_cabang'	=> $id_cabang
			) 
		);
		$cabang = $params['filter']['id_cabang'];
		if ($cabang == 4)
		{
			$nama = 'TOKO AHSANA ABG';
		}
		else if ($cabang == 5)
		{
			$nama = 'TOKO AHSANA KID"S';
		}
		else
		{
			$nama = 'TOKO AHSANA HIJAB';
		}

		$spreadsheet = new Spreadsheet();
		
		$row1 = 1;
		$spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('A'.$row1, 'LAPORAN HARIAN');

		$row2 = 2;
		$spreadsheet->getActiveSheet()->mergeCells('B2:E2');
		$spreadsheet->getActiveSheet()->getStyle('B2:E3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row2, 'LAPORAN HARIAN PERIODE : '.$tgl);
		$spreadsheet->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		

		$row3 = 3;
		$spreadsheet->getActiveSheet()->mergeCells('B3:E3');
		$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(15);
		$spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row3, $nama);
	
		
		// $row4 = 4;
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A6', 'PERSEDIAN AWAL');
		$sheet->setCellValue('A7', 'PEMBELIAN');
		$sheet->setCellValue('A8', 'RETUR PEMBELIAN');
		$sheet->setCellValue('A9', 'PEMBELIAN BERSIH');
		$sheet->setCellValue('A10', 'TOTAL STOK AWAL');

		$sheet->setCellValue('A12', 'PENJUALAN TUNAI');
		$sheet->setCellValue('A13', 'PENJUALAN KREDIT');
		$sheet->setCellValue('A14', 'TOTAL PENJUALAN');
		$sheet->setCellValue('A15', 'LABA PENJUALAN');
		$sheet->setCellValue('A16', 'TOTAL PENJUALAN TUNAI');
		$sheet->setCellValue('A17', 'HPP');
		$sheet->setCellValue('A18', 'RETUR PENJUALAN');
		$sheet->setCellValue('A19', 'LABA RETUR');
		$sheet->setCellValue('A20', 'HPP RETUR PENJUALAN');
		$sheet->setCellValue('A21', 'PERSEDIAN AKHIR');
		$sheet->setCellValue('A22', 'OMSET');

		$sheet->setCellValue('A24', 'KAS AWAL');
		$sheet->setCellValue('A25', 'PENJUALAN TUNAI');
		$sheet->setCellValue('A26', 'PENERIMAAN PIUTANG');
		$sheet->setCellValue('A27', 'KU, VIA BANK');
		$sheet->setCellValue('A28', 'TOTAL PENDAPATAN');
		$sheet->setCellValue('A29', 'TOTAL BIAYA PEMBELIAN');
		$sheet->setCellValue('A30', 'SETORAN TUNAI');
		$sheet->setCellValue('A31', 'BRI');
		$sheet->setCellValue('A32', 'BCA');
		$sheet->setCellValue('A33', 'BNI');
		$sheet->setCellValue('A34', 'MANDIRI');
		$sheet->setCellValue('A35', 'JUMLAH SETORAN');
		$sheet->setCellValue('A36', 'SALDO AKHIR');

		$sheet->setCellValue('A38', 'LABA BORUTO');

		$sheet->setCellValue('A40', 'BEBAN GAJI KARYAWAN');
		$sheet->setCellValue('A41', 'BEBAN SEWA TOKO/ BULANAN');
		$sheet->setCellValue('A42', 'BEBAN JAKARTA');
		$sheet->setCellValue('A43', 'BEBAN LISTRIK');
		$sheet->setCellValue('A44', 'BEBAN ANGKUT');
		$sheet->setCellValue('A45', 'BEBAN PERALATAN');
		$sheet->setCellValue('A46', 'BEBAN EKSPEDISI');
		$sheet->setCellValue('A47', 'BEBAN KONSUMSI');
		$sheet->setCellValue('A48', 'BEBAN LAIN-LAIN');
		$sheet->setCellValue('A49', 'TOTAL BIAYA');
		$sheet->setCellValue('A50', 'TOTAL LABA OPERASIONAL');
		
		$sheet->getStyle('A1')->getFont()->setUnderline(true);
		$sheet->getColumnDimension('A')->setWidth(30);
		$sheet->getStyle('A')->getFont()->setBold(true);
		$sheet->getColumnDimension('B')->setWidth(20);
		$sheet->getColumnDimension('C')->setWidth(20);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);


		$data = array(
			'print'	=> $this->lap_harian->printhari(
				array(
					'params' 	=> $params
				)
			)
		);


		$sheet->setCellValue('C6',isset($data['print']->stok->persediaan)  ? $data['print']->stok->persediaan : '');
		$sheet->setCellValue('B7',isset($data['print']->rincian_pembelian->persediaan)  ? $data['print']->rincian_pembelian->persediaan : '');
			$sheet->setCellValue('C8',isset($data['print']->retur_pembelian->totalretur)  ? $data['print']->retur_pembelian->totalretur : '');
				$sheet->setCellValue('C9',isset($data['print']->pembelian_bersih)  ? $data['print']->pembelian_bersih : '');
				$sheet->setCellValue('D10',isset($data['print']->total_stok_awal)  ? $data['print']->total_stok_awal : '');
					$sheet->setCellValue('B12',isset($data['print']->total_penjualan_tunai)  ? $data['print']->total_penjualan_tunai : '');
					$sheet->setCellValue('B15',isset($data['print']->laba_penjualan)  ? $data['print']->laba_penjualan : '');
					$sheet->setCellValue('D16',isset($data['print']->total_penjualan_tunai)  ? $data['print']->total_penjualan_tunai : '');
					$sheet->setCellValue('E17',isset($data['print']->hpp)  ? $data['print']->hpp : '');
			$sheet->setCellValue('C18',isset($data['print']->retur_penjualan->nominal)  ? $data['print']->retur_penjualan->nominal : '');
			$sheet->setCellValue('C19',isset($data['print']->laba_retur_penjualan->laba)  ? $data['print']->laba_retur_penjualan->laba : '');
				$sheet->setCellValue('D20',isset($data['print']->hppretur)  ? $data['print']->hppretur : '');
					$sheet->setCellValue('E21',isset($data['print']->persediaanakhir)  ? $data['print']->persediaanakhir : '');
					$sheet->setCellValue('E21',isset($data['print']->rincian_penjualan_omset->omset)  ? $data['print']->rincian_penjualan_omset->omset : '');

		$sheet->setCellValue('B24',isset($data['print']->kasir->modal)  ? $data['print']->kasir->modal : '');
		$sheet->setCellValue('B25',isset($data['print']->penjualan_tunai_kasir)  ? $data['print']->penjualan_tunai_kasir : '');
		$sheet->setCellValue('C26',isset($data['print']->rincian_piutang->nominal)  ? $data['print']->rincian_piutang->nominal : '');
		$sheet->setCellValue('E28',isset($data['print']->total_pendapatan)  ? $data['print']->total_pendapatan : '');
		$sheet->setCellValue('D29',isset($data['print']->kasir->biaya)  ? $data['print']->kasir->biaya : '');
		$sheet->setCellValue('B30',isset($data['print']->rincian_kasir_tunai->total)  ? $data['print']->rincian_kasir_tunai->total : '');
		$sheet->setCellValue('B31',isset($data['print']->rincian_kasir_bri->total)  ? $data['print']->rincian_kasir_bri->total : '');
		$sheet->setCellValue('B32',isset($data['print']->rincian_kasir_bca->total)  ? $data['print']->rincian_kasir_bca->total : '');
		$sheet->setCellValue('B33',isset($data['print']->rincian_kasir_bni->total)  ? $data['print']->rincian_kasir_bni->total : '');
		$sheet->setCellValue('B34',isset($data['print']->rincian_kasir_mandiri->total)  ? $data['print']->rincian_kasir_mandiri->total : '');
				$sheet->setCellValue('D35',isset($data['print']->kasir->setoran)  ? $data['print']->kasir->setoran : '');
					$sheet->setCellValue('E36',isset($data['print']->kasir->setoran)  ? $data['print']->kasir->setoran : '');
				$sheet->setCellValue('D38',isset($data['print']->bruto)  ? $data['print']->bruto : '');
		$sheet->setCellValue('B40',isset($data['print']->rincian_biaya->gaji)  ? $data['print']->rincian_biaya->gaji : '');
		$sheet->setCellValue('B41',isset($data['print']->rincian_biaya->bulanan)  ? $data['print']->rincian_biaya->bulanan : '');
		$sheet->setCellValue('B42',isset($data['print']->rincian_biaya_jakarta->total)  ? $data['print']->rincian_biaya_jakarta->total : '');
		$sheet->setCellValue('B43',isset($data['print']->rincian_biaya->listrik)  ? $data['print']->rincian_biaya->listrik : '');
		$sheet->setCellValue('B44',isset($data['print']->rincian_biaya->angkut)  ? $data['print']->rincian_biaya->angkut : '');
		$sheet->setCellValue('B45',isset($data['print']->rincian_biaya->peralatan)  ? $data['print']->rincian_biaya->peralatan : '');
		$sheet->setCellValue('B46',isset($data['print']->rincian_biaya->ekspedisi)  ? $data['print']->rincian_biaya->ekspedisi : '');
		$sheet->setCellValue('B47',isset($data['print']->rincian_biaya->konsumsi)  ? $data['print']->rincian_biaya->konsumsi : '');
		$sheet->setCellValue('B48',isset($data['print']->rincian_biaya->dll)  ? $data['print']->rincian_biaya->dll : '');
				$sheet->setCellValue('D49',isset($data['print']->totalbiaya)  ? $data['print']->totalbiaya : '');
					$sheet->setCellValue('E50',isset($data['print']->labaoperasional)  ? $data['print']->labaoperasional : '');

		$spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode('Rp,#,##0');
		
		$writer = new Xlsx($spreadsheet);
		$filename = 'laporan-Harian';		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	
	}	

	public function excel_bulan($tgl, $id_cabang)
	{
		$params = $this->input->post(NULL, TRUE);
		
		$params = array(
			'filter' => array(
				'tglbulan' 	=> $tgl,
				'id_cabang'	=> $id_cabang
			) 
		);
		$cabang = $params['filter']['id_cabang'];
		if ($cabang == 4)
		{
			$nama = 'TOKO AHSANA ABG';
		}
		else if ($cabang == 5)
		{
			$nama = 'TOKO AHSANA  KID"S';
		}
		else
		{
			$nama = 'TOKO AHSANA HIJAB';
		}

		$spreadsheet = new Spreadsheet();
		
		$row1 = 1;
		$spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('A'.$row1, 'LAPORAN HARIAN');

		$row2 = 2;
		$spreadsheet->getActiveSheet()->mergeCells('B2:E2');
		$spreadsheet->getActiveSheet()->getStyle('B2:E3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row2, 'LAPORAN HARIAN PERIODE : '.$tgl);
		$spreadsheet->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		

		$row3 = 3;
		$spreadsheet->getActiveSheet()->mergeCells('B3:E3');
		$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(15);
		$spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row3, $nama);
	
		
		// $row4 = 4;
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A6', 'PERSEDIAN AWAL');
		$sheet->setCellValue('A7', 'PEMBELIAN');
		$sheet->setCellValue('A8', 'RETUR PEMBELIAN');
		$sheet->setCellValue('A9', 'PEMBELIAN BERSIH');
		$sheet->setCellValue('A10', 'TOTAL STOK AWAL');

		$sheet->setCellValue('A12', 'PENJUALAN TUNAI');
		$sheet->setCellValue('A13', 'PENJUALAN KREDIT');
		$sheet->setCellValue('A14', 'TOTAL PENJUALAN');
		$sheet->setCellValue('A15', 'LABA PENJUALAN');
		$sheet->setCellValue('A16', 'TOTAL PENJUALAN TUNAI');
		$sheet->setCellValue('A17', 'HPP');
		$sheet->setCellValue('A18', 'RETUR PENJUALAN');
		$sheet->setCellValue('A19', 'LABA RETUR');
		$sheet->setCellValue('A20', 'HPP RETUR PENJUALAN');
		$sheet->setCellValue('A21', 'PERSEDIAN AKHIR');
		$sheet->setCellValue('A22', 'OMSET');

		$sheet->setCellValue('A24', 'KAS AWAL');
		$sheet->setCellValue('A25', 'PENJUALAN TUNAI');
		$sheet->setCellValue('A26', 'PENERIMAAN PIUTANG');
		$sheet->setCellValue('A27', 'KU, VIA BANK');
		$sheet->setCellValue('A28', 'TOTAL PENDAPATAN');
		$sheet->setCellValue('A29', 'TOTAL BIAYA PEMBELIAN');
		$sheet->setCellValue('A30', 'SETORAN TUNAI');
		$sheet->setCellValue('A31', 'BRI');
		$sheet->setCellValue('A32', 'BCA');
		$sheet->setCellValue('A33', 'BNI');
		$sheet->setCellValue('A34', 'MANDIRI');
		$sheet->setCellValue('A35', 'JUMLAH SETORAN');
		$sheet->setCellValue('A36', 'SALDO AKHIR');

		$sheet->setCellValue('A38', 'LABA BORUTO');

		$sheet->setCellValue('A40', 'BEBAN GAJI KARYAWAN');
		$sheet->setCellValue('A41', 'BEBAN SEWA TOKO/ BULANAN');
		$sheet->setCellValue('A42', 'BEBAN JAKARTA');
		$sheet->setCellValue('A43', 'BEBAN LISTRIK');
		$sheet->setCellValue('A44', 'BEBAN ANGKUT');
		$sheet->setCellValue('A45', 'BEBAN PERALATAN');
		$sheet->setCellValue('A46', 'BEBAN EKSPEDISI');
		$sheet->setCellValue('A47', 'BEBAN KONSUMSI');
		$sheet->setCellValue('A48', 'BEBAN LAIN-LAIN');
		$sheet->setCellValue('A49', 'TOTAL BIAYA');
		$sheet->setCellValue('A50', 'TOTAL LABA OPERASIONAL');
		
		$sheet->getStyle('A1')->getFont()->setUnderline(true);
		$sheet->getColumnDimension('A')->setWidth(30);
		$sheet->getStyle('A')->getFont()->setBold(true);
		$sheet->getColumnDimension('B')->setWidth(20);
		$sheet->getColumnDimension('C')->setWidth(20);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);

		$data = array(
			'print'	=> $this->lap_harian->printbulan(
				array(
					'params' 	=> $params
				)
			)
		);

		
		$sheet->setCellValue('C6',isset($data['print']->stok->persediaan)  ? $data['print']->stok->persediaan : '');
		$sheet->setCellValue('B7',isset($data['print']->rincian_pembelian->persediaan)  ? $data['print']->rincian_pembelian->persediaan : '');
			$sheet->setCellValue('C8',isset($data['print']->retur_pembelian->totalretur)  ? $data['print']->retur_pembelian->totalretur : '');
				$sheet->setCellValue('C9',isset($data['print']->pembelian_bersih)  ? $data['print']->pembelian_bersih : '');
				$sheet->setCellValue('D10',isset($data['print']->total_stok_awal)  ? $data['print']->total_stok_awal : '');
					$sheet->setCellValue('B12',isset($data['print']->total_penjualan_tunai)  ? $data['print']->total_penjualan_tunai : '');
					$sheet->setCellValue('B15',isset($data['print']->laba_penjualan)  ? $data['print']->laba_penjualan : '');
					$sheet->setCellValue('D16',isset($data['print']->total_penjualan_tunai)  ? $data['print']->total_penjualan_tunai : '');
					$sheet->setCellValue('E17',isset($data['print']->hpp)  ? $data['print']->hpp : '');
			$sheet->setCellValue('C18',isset($data['print']->retur_penjualan->nominal)  ? $data['print']->retur_penjualan->nominal : '');
			$sheet->setCellValue('C19',isset($data['print']->laba_retur_penjualan->laba)  ? $data['print']->laba_retur_penjualan->laba : '');
				$sheet->setCellValue('D20',isset($data['print']->hppretur)  ? $data['print']->hppretur : '');
					$sheet->setCellValue('E21',isset($data['print']->persediaanakhir)  ? $data['print']->persediaanakhir : '');
					$sheet->setCellValue('E21',isset($data['print']->rincian_penjualan_omset->omset)  ? $data['print']->rincian_penjualan_omset->omset : '');

		$sheet->setCellValue('B24',isset($data['print']->kasir->modal)  ? $data['print']->kasir->modal : '');
		$sheet->setCellValue('B25',isset($data['print']->penjualan_tunai_kasir)  ? $data['print']->penjualan_tunai_kasir : '');
		$sheet->setCellValue('C26',isset($data['print']->rincian_piutang->nominal)  ? $data['print']->rincian_piutang->nominal : '');
		$sheet->setCellValue('E28',isset($data['print']->total_pendapatan)  ? $data['print']->total_pendapatan : '');
		$sheet->setCellValue('D29',isset($data['print']->kasir->biaya)  ? $data['print']->kasir->biaya : '');
		$sheet->setCellValue('B30',isset($data['print']->rincian_kasir_tunai->total)  ? $data['print']->rincian_kasir_tunai->total : '');
		$sheet->setCellValue('B31',isset($data['print']->rincian_kasir_bri->total)  ? $data['print']->rincian_kasir_bri->total : '');
		$sheet->setCellValue('B32',isset($data['print']->rincian_kasir_bca->total)  ? $data['print']->rincian_kasir_bca->total : '');
		$sheet->setCellValue('B33',isset($data['print']->rincian_kasir_bni->total)  ? $data['print']->rincian_kasir_bni->total : '');
		$sheet->setCellValue('B34',isset($data['print']->rincian_kasir_mandiri->total)  ? $data['print']->rincian_kasir_mandiri->total : '');
				$sheet->setCellValue('D35',isset($data['print']->kasir->setoran)  ? $data['print']->kasir->setoran : '');
					$sheet->setCellValue('E36',isset($data['print']->kasir->setoran)  ? $data['print']->kasir->setoran : '');
				$sheet->setCellValue('D38',isset($data['print']->bruto)  ? $data['print']->bruto : '');
		$sheet->setCellValue('B40',isset($data['print']->rincian_biaya->gaji)  ? $data['print']->rincian_biaya->gaji : '');
		$sheet->setCellValue('B41',isset($data['print']->rincian_biaya->bulanan)  ? $data['print']->rincian_biaya->bulanan : '');
		$sheet->setCellValue('B42',isset($data['print']->rincian_biaya_jakarta->total)  ? $data['print']->rincian_biaya_jakarta->total : '');
		$sheet->setCellValue('B43',isset($data['print']->rincian_biaya->listrik)  ? $data['print']->rincian_biaya->listrik : '');
		$sheet->setCellValue('B44',isset($data['print']->rincian_biaya->angkut)  ? $data['print']->rincian_biaya->angkut : '');
		$sheet->setCellValue('B45',isset($data['print']->rincian_biaya->peralatan)  ? $data['print']->rincian_biaya->peralatan : '');
		$sheet->setCellValue('B46',isset($data['print']->rincian_biaya->ekspedisi)  ? $data['print']->rincian_biaya->ekspedisi : '');
		$sheet->setCellValue('B47',isset($data['print']->rincian_biaya->konsumsi)  ? $data['print']->rincian_biaya->konsumsi : '');
		$sheet->setCellValue('B48',isset($data['print']->rincian_biaya->dll)  ? $data['print']->rincian_biaya->dll : '');
				$sheet->setCellValue('D49',isset($data['print']->totalbiaya)  ? $data['print']->totalbiaya : '');
					$sheet->setCellValue('E50',isset($data['print']->labaoperasional)  ? $data['print']->labaoperasional : '');

		$spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode('Rp,#,##0');
	
		$writer = new Xlsx($spreadsheet);
		$filename = 'laporan-Bulanan';		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}	

	public function excel_tahun($tgl, $id_cabang)
	{
		$params = $this->input->post(NULL, TRUE);
		
		$params = array(
			'filter' => array(
				'tgltahun' 	=> $tgl,
				'id_cabang'	=> $id_cabang
			) 
		);
		$cabang = $params['filter']['id_cabang'];
		if ($cabang == 4)
		{
			$nama = 'TOKO AHSANA ABG';
		}
		else if ($cabang == 5)
		{
			$nama = 'TOKO AHSANA  KID"S';
		}
		else
		{
			$nama = 'TOKO AHSANA HIJAB';
		}

		$spreadsheet = new Spreadsheet();
		
		$row1 = 1;
		$spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('A'.$row1, 'LAPORAN HARIAN');

		$row2 = 2;
		$spreadsheet->getActiveSheet()->mergeCells('B2:E2');
		$spreadsheet->getActiveSheet()->getStyle('B2:E3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row2, 'LAPORAN HARIAN PERIODE : '.$tgl);
		$spreadsheet->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		

		$row3 = 3;
		$spreadsheet->getActiveSheet()->mergeCells('B3:E3');
		$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(15);
		$spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row3, $nama);
	
		
		// $row4 = 4;
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A6', 'PERSEDIAN AWAL');
		$sheet->setCellValue('A7', 'PEMBELIAN');
		$sheet->setCellValue('A8', 'RETUR PEMBELIAN');
		$sheet->setCellValue('A9', 'PEMBELIAN BERSIH');
		$sheet->setCellValue('A10', 'TOTAL STOK AWAL');

		$sheet->setCellValue('A12', 'PENJUALAN TUNAI');
		$sheet->setCellValue('A13', 'PENJUALAN KREDIT');
		$sheet->setCellValue('A14', 'TOTAL PENJUALAN');
		$sheet->setCellValue('A15', 'LABA PENJUALAN');
		$sheet->setCellValue('A16', 'TOTAL PENJUALAN TUNAI');
		$sheet->setCellValue('A17', 'HPP');
		$sheet->setCellValue('A18', 'RETUR PENJUALAN');
		$sheet->setCellValue('A19', 'LABA RETUR');
		$sheet->setCellValue('A20', 'HPP RETUR PENJUALAN');
		$sheet->setCellValue('A21', 'PERSEDIAN AKHIR');
		$sheet->setCellValue('A22', 'OMSET');

		$sheet->setCellValue('A24', 'KAS AWAL');
		$sheet->setCellValue('A25', 'PENJUALAN TUNAI');
		$sheet->setCellValue('A26', 'PENERIMAAN PIUTANG');
		$sheet->setCellValue('A27', 'KU, VIA BANK');
		$sheet->setCellValue('A28', 'TOTAL PENDAPATAN');
		$sheet->setCellValue('A29', 'TOTAL BIAYA PEMBELIAN');
		$sheet->setCellValue('A30', 'SETORAN TUNAI');
		$sheet->setCellValue('A31', 'BRI');
		$sheet->setCellValue('A32', 'BCA');
		$sheet->setCellValue('A33', 'BNI');
		$sheet->setCellValue('A34', 'MANDIRI');
		$sheet->setCellValue('A35', 'JUMLAH SETORAN');
		$sheet->setCellValue('A36', 'SALDO AKHIR');

		$sheet->setCellValue('A38', 'LABA BORUTO');

		$sheet->setCellValue('A40', 'BEBAN GAJI KARYAWAN');
		$sheet->setCellValue('A41', 'BEBAN SEWA TOKO/ BULANAN');
		$sheet->setCellValue('A42', 'BEBAN JAKARTA');
		$sheet->setCellValue('A43', 'BEBAN LISTRIK');
		$sheet->setCellValue('A44', 'BEBAN ANGKUT');
		$sheet->setCellValue('A45', 'BEBAN PERALATAN');
		$sheet->setCellValue('A46', 'BEBAN EKSPEDISI');
		$sheet->setCellValue('A47', 'BEBAN KONSUMSI');
		$sheet->setCellValue('A48', 'BEBAN LAIN-LAIN');
		$sheet->setCellValue('A49', 'TOTAL BIAYA');
		$sheet->setCellValue('A50', 'TOTAL LABA OPERASIONAL');
		
		$sheet->getStyle('A1')->getFont()->setUnderline(true);
		$sheet->getColumnDimension('A')->setWidth(30);
		$sheet->getStyle('A')->getFont()->setBold(true);
		$sheet->getColumnDimension('B')->setWidth(20);
		$sheet->getColumnDimension('C')->setWidth(20);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);


		$data = array(
			'print'	=> $this->lap_harian->printtahun(
				array(
					'params' 	=> $params
				)
			)
		);

		$sheet->setCellValue('C6',isset($data['print']->stok->persediaan)  ? $data['print']->stok->persediaan : '');
		$sheet->setCellValue('B7',isset($data['print']->rincian_pembelian->persediaan)  ? $data['print']->rincian_pembelian->persediaan : '');
			$sheet->setCellValue('C8',isset($data['print']->retur_pembelian->totalretur)  ? $data['print']->retur_pembelian->totalretur : '');
				$sheet->setCellValue('C9',isset($data['print']->pembelian_bersih)  ? $data['print']->pembelian_bersih : '');
				$sheet->setCellValue('D10',isset($data['print']->total_stok_awal)  ? $data['print']->total_stok_awal : '');
					$sheet->setCellValue('B12',isset($data['print']->total_penjualan_tunai)  ? $data['print']->total_penjualan_tunai : '');
					$sheet->setCellValue('B15',isset($data['print']->laba_penjualan)  ? $data['print']->laba_penjualan : '');
					$sheet->setCellValue('D16',isset($data['print']->total_penjualan_tunai)  ? $data['print']->total_penjualan_tunai : '');
					$sheet->setCellValue('E17',isset($data['print']->hpp)  ? $data['print']->hpp : '');
			$sheet->setCellValue('C18',isset($data['print']->retur_penjualan->nominal)  ? $data['print']->retur_penjualan->nominal : '');
			$sheet->setCellValue('C19',isset($data['print']->laba_retur_penjualan->laba)  ? $data['print']->laba_retur_penjualan->laba : '');
				$sheet->setCellValue('D20',isset($data['print']->hppretur)  ? $data['print']->hppretur : '');
					$sheet->setCellValue('E21',isset($data['print']->persediaanakhir)  ? $data['print']->persediaanakhir : '');
					$sheet->setCellValue('E21',isset($data['print']->rincian_penjualan_omset->omset)  ? $data['print']->rincian_penjualan_omset->omset : '');

		$sheet->setCellValue('B24',isset($data['print']->kasir->modal)  ? $data['print']->kasir->modal : '');
		$sheet->setCellValue('B25',isset($data['print']->penjualan_tunai_kasir)  ? $data['print']->penjualan_tunai_kasir : '');
		$sheet->setCellValue('C26',isset($data['print']->rincian_piutang->nominal)  ? $data['print']->rincian_piutang->nominal : '');
		$sheet->setCellValue('E28',isset($data['print']->total_pendapatan)  ? $data['print']->total_pendapatan : '');
		$sheet->setCellValue('D29',isset($data['print']->kasir->biaya)  ? $data['print']->kasir->biaya : '');
		$sheet->setCellValue('B30',isset($data['print']->rincian_kasir_tunai->total)  ? $data['print']->rincian_kasir_tunai->total : '');
		$sheet->setCellValue('B31',isset($data['print']->rincian_kasir_bri->total)  ? $data['print']->rincian_kasir_bri->total : '');
		$sheet->setCellValue('B32',isset($data['print']->rincian_kasir_bca->total)  ? $data['print']->rincian_kasir_bca->total : '');
		$sheet->setCellValue('B33',isset($data['print']->rincian_kasir_bni->total)  ? $data['print']->rincian_kasir_bni->total : '');
		$sheet->setCellValue('B34',isset($data['print']->rincian_kasir_mandiri->total)  ? $data['print']->rincian_kasir_mandiri->total : '');
				$sheet->setCellValue('D35',isset($data['print']->kasir->setoran)  ? $data['print']->kasir->setoran : '');
					$sheet->setCellValue('E36',isset($data['print']->kasir->setoran)  ? $data['print']->kasir->setoran : '');
				$sheet->setCellValue('D38',isset($data['print']->bruto)  ? $data['print']->bruto : '');
		$sheet->setCellValue('B40',isset($data['print']->rincian_biaya->gaji)  ? $data['print']->rincian_biaya->gaji : '');
		$sheet->setCellValue('B41',isset($data['print']->rincian_biaya->bulanan)  ? $data['print']->rincian_biaya->bulanan : '');
		$sheet->setCellValue('B42',isset($data['print']->rincian_biaya_jakarta->total)  ? $data['print']->rincian_biaya_jakarta->total : '');
		$sheet->setCellValue('B43',isset($data['print']->rincian_biaya->listrik)  ? $data['print']->rincian_biaya->listrik : '');
		$sheet->setCellValue('B44',isset($data['print']->rincian_biaya->angkut)  ? $data['print']->rincian_biaya->angkut : '');
		$sheet->setCellValue('B45',isset($data['print']->rincian_biaya->peralatan)  ? $data['print']->rincian_biaya->peralatan : '');
		$sheet->setCellValue('B46',isset($data['print']->rincian_biaya->ekspedisi)  ? $data['print']->rincian_biaya->ekspedisi : '');
		$sheet->setCellValue('B47',isset($data['print']->rincian_biaya->konsumsi)  ? $data['print']->rincian_biaya->konsumsi : '');
		$sheet->setCellValue('B48',isset($data['print']->rincian_biaya->dll)  ? $data['print']->rincian_biaya->dll : '');
				$sheet->setCellValue('D49',isset($data['print']->totalbiaya)  ? $data['print']->totalbiaya : '');
					$sheet->setCellValue('E50',isset($data['print']->labaoperasional)  ? $data['print']->labaoperasional : '');

		$spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode('Rp,#,##0');
		
		$writer = new Xlsx($spreadsheet);
		$filename = 'laporan-Tahunan';		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	
	}	




}