<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Laporan_kas extends GT_Controller {
	private $module = [
		'name' 	=> 'Laporan kas',
		'url'	=> 'laporan/laporan-kas'
	];
	public function __construct(){
		parent::__construct();
		$this->load->model('laporan/Lap_kas');
	}
	public function index(){
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan kas',
				'url'	=> 'laporan/laporan-kas'
			) 
		);
		$head = array(
			'stylesheets'		=> array(
				'assets/js/plugins/flatpickr/flatpickr.min.css',
				'assets/js/plugins/datatables/DataTables-1.10.21/css/dataTables.bootstrap4.min.css',
				'assets/js/plugins/datatables/RowGroup-1.1.2/css/rowGroup.bootstrap4.min.css',
				'assets/js/plugins/select2/css/select2.min.css',
				'assets/js/plugins/datatables/Select-1.3.1/css/select.bootstrap4.min.css',
				'assets/js/plugins/sweetalert2/sweetalert2.min.css'
			),
			'heading' => array('title'=>$data['module']['name'])
		);
		$foot = array(
			'javascripts'	=> array(
				'assets/js/plugins/flatpickr/flatpickr.min.js',
				'assets/js/plugins/datatables/DataTables-1.10.21/js/jquery.dataTables.min.js',
				'assets/js/plugins/datatables/DataTables-1.10.21/js/dataTables.bootstrap4.min.js',
				'assets/js/plugins/datatables/RowGroup-1.1.2/js/dataTables.rowGroup.min.js',
				'assets/js/plugins/datatables/Select-1.3.1/js/dataTables.select.min.js',
				'assets/js/plugins/select2/js/select2.full.min.js',
				'assets/js/plugins/jquery-validation/jquery.validate.min.js',
				'assets/js/plugins/sweetalert2/sweetalert2.min.js',
				'assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js',
				'assets/js/plugins/jsPDF/jsPDF.min.js'
			),
			'scripts' 		=> array(
				$this->load->view($data['module']['url'] . '/javascript/datatable', $data, TRUE)
			)
		);
		$this->html_head($head);
		$this->load->view($data['module']['url'] . '/html/datatable',$data);
		$this->html_foot($foot);
	}


	public function print_out(){
		$params = $this->input->post(NULL, TRUE);
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan Kas',
				'url'	=> 'laporan/laporan-kas'
			),
			'params'	=> $params,
			'period' => array(
				'date_start'	=> $params['filter']['date_start'],
				'date_end'	=> $params['filter']['date_end']
			),
			'data'	=> $this->Lap_kas->detailkas(
				array(
					'params' 	=> $params
				)
			)
		);
		$this->load->view($data['module']['url'] . '/html/print-out', $data);
	}

	

	/* API DATA */
	public function api_data($vendor_name = null){
		$response = array(
			'status' 	=> 'error', 
			'message'	=> 'No Action Parameter'
		);
		$vendor_name = strtolower(str_replace("-","_",$vendor_name));
		$this->load->model('laporan/Lap_kas');
		if( method_exists($this->Lap_kas, $vendor_name) ){
			$params = $this->input->post(NULL, TRUE);
			switch($vendor_name):
				case 'datatable':
					$config = array(
						'params' 	=> $params
					);
				break;
				case 'detailkas':
					$config = array(
						'params' 	=> $params
					);
				break;
			endswitch;

			$response 	= $this->Lap_kas->{$vendor_name}($config);

		}
		$this->output
			->set_status_header(200)
			->set_content_type('Application/json')
			->set_output(json_encode($response));
	}
	
	public function excel($start_date, $end_date, $id_cabang)
	{
		$spreadsheet = new Spreadsheet();
		$row1 = 1;
		$spreadsheet->getActiveSheet()->mergeCells('B1:G'.$row1);
		// $spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row1, 'Laporan Penjualan');
		$row2 = 2;
		$spreadsheet->getActiveSheet()->mergeCells('B2:G'.$row2);
		$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(15);
		$spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row2, 'LAPORAN 	KAS');
		$row3 = 3;
		$spreadsheet->getActiveSheet()->mergeCells('B3:G3');
		$spreadsheet->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		// $spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row3, 'PERIODE : '.$start_date.' S/D '.$end_date);
		$row4 = 4;
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A'.$row4, 'No');
		$sheet->setCellValue('B'.$row4, 'Tanggal');
		$sheet->setCellValue('C'.$row4, 'Keterangan');
		$sheet->setCellValue('D'.$row4, 'Debit');
		$sheet->setCellValue('E'.$row4, 'Kredit');
		$sheet->setCellValue('F'.$row4, 'Saldo awal');
		$sheet->setCellValue('G'.$row4, 'Saldo akhir');
		$sheet->getStyle('A4:G'.$row4)->getFont()->setBold(true);
		$sheet->getStyle('A4:G'.$row4)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		for($col = 'B'; $col !== 'G'; $col++){$sheet->getColumnDimension($col)->setAutoSize(true);}
		$list	= $this->Lap_kas->get_excel($start_date,$end_date, $id_cabang);
		$cell   = $list['data']->num_rows() + 4;
		$result = $list['data']->result_array();
		$kas_awal = $list['kas_awal']->rumus;
		$no = 1;
		$row = 5;
		$startRow = -1;
		$previousKey = '';
		$totalkredit = 0;
		$totaldebit = 0;
		$saldoakhir = 0;
		foreach($result as $index => $value){
			$saldoawal = $value['nominal'];
			$totalkredit += $value['kredit'];
			$totaldebit += $value['debit'];
			$saldoakhir = ($saldoawal + $totaldebit) - $totalkredit ;
			if($startRow == -1){
				$startRow = $row;
				$previousKey = $value['tanggal'];
			}
			if (@$result[$index-1]['tanggal'] != $value['tanggal']) {
				$sheet->setCellValue('F'.$row, $value['nominal']);
			}
			if (@$result[$index+1]['tanggal'] != $value['tanggal']) {
				$sheet->setCellValue('G'.$row, $value['rumus']);
			}
			$sheet->setCellValue('A'.$row, $no++);
			$sheet->setCellValue('B'.$row, $value['tanggal']);
			$sheet->setCellValue('C'.$row, $value['keterangan']);
			$sheet->setCellValue('D'.$row, $value['debit']);
			$sheet->setCellValue('E'.$row, $value['kredit']);
			// $x++;
			$nextKey = isset($result[$index+1]) ? $result[$index+1]['tanggal'] : null;

			if($row >= $startRow && (($previousKey <> $nextKey) || ($nextKey == null))){
				$cellToMerge = 'B'.$startRow.':B'.$row;
				$spreadsheet->getActiveSheet()->mergeCells($cellToMerge);
				$startRow = -1;
			}
		
			$row++;
		}
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(20);
		$rows = $list['data']->num_rows() + 5;
		
		$spreadsheet->getActiveSheet()->getStyle('E5:E'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('E'.$rows)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('D5:D'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('D'.$rows)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('F5:F'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('F'.$rows)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('G5:G'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('G'.$rows)->getNumberFormat()->setFormatCode('Rp,#,##0');

		$sheet->getStyle('A4:G'. $cell)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		$spreadsheet->getActiveSheet()->getStyle('A5:A'.$cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
		$spreadsheet->getActiveSheet()->getStyle('B5:B'.$cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('C5:C'.$cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
		$spreadsheet->getActiveSheet()->getStyle('B5:B'.$cell)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);	
		$spreadsheet->getActiveSheet()->getStyle('B5:B'.$cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
		$writer = new Xlsx($spreadsheet);
		$filename = 'laporan-Buku-Kas';		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');	
	}
}