<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Laporan_buku_bank extends GT_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('laporan/Lap_buku_bank');
	}
	public function index(){
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan Buku Bank',
				'url'	=> 'laporan/laporan-buku-bank'
			) 
		);
		$head = array(
			'stylesheets'		=> array(
				'assets/js/plugins/flatpickr/flatpickr.min.css',
				'assets/js/plugins/datatables/DataTables-1.10.21/css/dataTables.bootstrap4.min.css',
				'assets/js/plugins/datatables/RowGroup-1.1.2/css/rowGroup.bootstrap4.min.css',
				'assets/js/plugins/select2/css/select2.min.css',
				'assets/js/plugins/datatables/Select-1.3.1/css/select.bootstrap4.min.css',
				'assets/js/plugins/sweetalert2/sweetalert2.min.css',
				'assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css'
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
				'assets/js/plugins/jsPDF/jsPDF.min.js',
				'assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js'
			),
			'scripts' 		=> array(
				$this->load->view($data['module']['url'] . '/javascript/datatable', $data, TRUE)
			)
		);

		$this->html_head($head);
		$this->load->view($data['module']['url'] . '/html/datatable',$data);
		$this->html_foot($foot);
	}
	public function api_data()
	{
		$params = $this->input->post(NULL, TRUE);
		$start_date = $params['filter']['date_start'];
		$id_cabang = $params['filter']['id_cabang'];
		$end_date   = $params['filter']['date_end'];
		$data = $this->Lap_buku_bank->buku_bank($start_date, $id_cabang, $end_date);
		$this->output
			->set_status_header(200)
			->set_content_type('Application/json')
			->set_output(json_encode($data));
	}
	public function print_out()
	{
		$this->load->model('laporan/Lap_buku_bank');
		$params = $this->input->post(NULL, TRUE);
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan Buku Bank',
				'url'	=> 'laporan/laporan-buku-bank'
			),
			'params'	=> $params,
			'period' => array(
				'start'	=> $params['filter']['date_start'],
				'end'	=> $params['filter']['date_end']
			),
			'data'	=> $this->Lap_buku_bank->print_out($params['filter']['date_start'], $params['filter']['date_end'], $params['filter']['id_cabang']
			)
		);		
		$this->load->view($data['module']['url'] . '/html/print-out', $data);
	}

	public function excel($start_date,$end_date, $id_cabang)
	{
		$spreadsheet = new Spreadsheet();
		$row1 = 1;
		$spreadsheet->getActiveSheet()->mergeCells('B1:H'.$row1);
		// $spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row1, 'Laporan Penjualan');
		$row2 = 2;
		$spreadsheet->getActiveSheet()->mergeCells('B2:H'.$row2);
		// $spreadsheet->getActiveSheet()->mergeCells('A5:A6');
		$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(15);
		$spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row2, 'LAPORAN BUKU BANK');
		$row3 = 3;
		$spreadsheet->getActiveSheet()->mergeCells('B3:H3');
		$spreadsheet->getActiveSheet()->mergeCells('D5:E5');
		$spreadsheet->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row3, 'PERIODE : '.$start_date.' S/D '.$end_date);
		$row5 = 5;
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('D'.$row5, 'Uraian');
		$row6 = 6;
		$sheet = $spreadsheet->getActiveSheet();
		// $sheet->setCellValue('A6', 'No');
		$sheet->setCellValue('A'.$row5, 'No');
		$sheet->setCellValue('B'.$row5, 'Tanggal');
		$sheet->setCellValue('C'.$row5, 'Keterangan');
		$sheet->setCellValue('D'.$row6, 'pemasok');
		$sheet->setCellValue('E'.$row6, 'nomor');
		$sheet->setCellValue('F'.$row5, 'Debit');
		$sheet->setCellValue('G'.$row5, 'Credit');
		$sheet->setCellValue('H'.$row5, 'Saldo');
		$sheet->getStyle('A4:H'.$row5)->getFont()->setBold(true);
		$sheet->getStyle('A4:H'.$row5)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		for($col = 'B'; $col !== 'H'; $col++){$sheet->getColumnDimension($col)->setAutoSize(true);}
		$list	= $this->Lap_buku_bank->print_out($start_date,$end_date, $id_cabang);
		$cell   = $list['laporan_num'] + 6;
		$no = 1;
		$row4 = 4;
		$x = 7;
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('G'.$row4, 'Saldo Awal');
		$sheet->setCellValue('H'.$row4, $list['saldo_awal']);
		$startRow = -1;
		$previousKey = '';
		foreach($list['laporan'] as $index => $value)
		{
			if($startRow == -1){
				$startRow = $x;
				$previousKey = $value['tgl'];
			}


			
			$sheet->setCellValue('A'.$x, $no++);
			$sheet->setCellValue('B'.$x, $value['tgl']);

			if($value['total_kredit'] == 0){
				$sheet->setCellValue('C'.$x, $value['keterangan']);
			    $spreadsheet->getActiveSheet()->getStyle('C'.$x)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);
			}else{
				$sheet->setCellValue('C'.$x, $value['keterangan']);
				$spreadsheet->getActiveSheet()->getStyle('C'.$x)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
			}
			$sheet->setCellValue('D'.$x, $value['uraian']);
			$sheet->setCellValue('E'.$x, $value['nogiro']);
			$sheet->setCellValue('F'.$x, $value['total_debit']);
			$sheet->setCellValue('G'.$x, $value['total_kredit']);
			$sheet->setCellValue('H'.$x, $value['new_saldo']);

			$nextKey = isset($list['laporan'][$index+1]) ? $list['laporan'][$index+1]['tgl'] : null;

			if($x >= $startRow && (($previousKey <> $nextKey) || ($nextKey == null))){
				$cellToMerge = 'B'.$startRow.':B'.$x;
				$spreadsheet->getActiveSheet()->mergeCells($cellToMerge);
				$startRow = -1;
			}
		
			$x++;
		}

		
		
		$spreadsheet->getActiveSheet()->mergeCells('A5:A6');
		$spreadsheet->getActiveSheet()->mergeCells('B5:B6');
		$spreadsheet->getActiveSheet()->mergeCells('C5:C6');


		$spreadsheet->getActiveSheet()->mergeCells('F5:F6');
		$spreadsheet->getActiveSheet()->mergeCells('G5:G6');
		$spreadsheet->getActiveSheet()->mergeCells('H5:H6');

		$rows = $list['laporan_num'] + 7;
		$spreadsheet->getActiveSheet()->getStyle('H5:H'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('H'.$rows)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('F5:F'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('F'.$rows)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('G5:G'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('G'.$rows)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$sheet->getStyle('A5:H'. $cell)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		$spreadsheet->getActiveSheet()->getStyle('B5:B'.$cell)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);	
		$spreadsheet->getActiveSheet()->getStyle('B5:B'.$cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
		$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$writer = new Xlsx($spreadsheet);
		$filename = 'laporan-Buku-Bank';		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');	
	}	
}