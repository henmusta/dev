<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Laporan_biaya_lampung extends GT_Controller {
	private $module = [
		'name' 	=> 'Laporan Biaya Lampung',
		'url'	=> 'laporan/laporan-biaya-lampung'
	];
	public function __construct(){
		parent::__construct();
	}
	public function index(){
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan Biaya Lampung',
				'url'	=> 'laporan/laporan-biaya-lampung'
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

	public function print_out(){
		$this->load->model('laporan/Lap_biaya_lampung');
		$params = $this->input->post(NULL, TRUE);
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan Biaya Lampung',
				'url'	=> 'laporan/laporan-biaya-lampung'
			),
			'params'	=> $params,
			'period' => array(
				'start'	=> $params['filter']['start_date'],
				'end'	=> $params['filter']['end_date']
			),
			'data'	=> $this->Lap_biaya_lampung->datatable(
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
		$this->load->model('laporan/Lap_biaya_lampung');
		if( method_exists($this->Lap_biaya_lampung, $vendor_name) ){
			$params = $this->input->post(NULL, TRUE);
			switch($vendor_name):
				case 'datatable':
					$config = array(
						'columns' 	=> array(
							'nomor', 
							'tgl_nota', 
							'id'
						),
						'params' 	=> $params
					);
				break;
			endswitch;

			$response 	= $this->Lap_biaya_lampung->{$vendor_name}($config);

		}

		$this->output
			->set_status_header(200)
			->set_content_type('Application/json')
			->set_output(json_encode($response));
	}

	public function excel($start_date, $end_date, $id_cabang)
	{
		$this->load->model('laporan/Lap_biaya_lampung');
		$spreadsheet = new Spreadsheet();
		$row1 = 1;
		$spreadsheet->getActiveSheet()->mergeCells('B1:K'.$row1);

		$row2 = 2;
		$spreadsheet->getActiveSheet()->mergeCells('B2:K'.$row2);
		$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(15);
		$spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row2, 'LAPORAN BIAYA JAKARTA');
		$row3 = 3;
		$spreadsheet->getActiveSheet()->mergeCells('B3:K3');
		$spreadsheet->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row3, 'PERIODE : '.$start_date.' S/D '.$end_date);
		$row4 = 4;
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A'.$row4, 'No');
		$sheet->setCellValue('B'.$row4, 'Tanggal');
		$sheet->setCellValue('C'.$row4, 'Gaji');
		$sheet->setCellValue('D'.$row4, 'Bulunan');
		$sheet->setCellValue('E'.$row4, 'Listrik');
		$sheet->setCellValue('F'.$row4, 'Angkut');
		$sheet->setCellValue('G'.$row4, 'Angkutan');
		$sheet->setCellValue('H'.$row4, 'Peralatan');
		$sheet->setCellValue('I'.$row4, 'Konsumsi');
		$sheet->setCellValue('J'.$row4, 'Dll');
		$sheet->setCellValue('K'.$row4, 'Total');

		$sheet->getStyle('A4:K'.$row4)->getFont()->setBold(true);
		$sheet->getStyle('A4:K'.$row4)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		for($col = 'B'; $col !== 'K'; $col++){$sheet->getColumnDimension($col)->setAutoSize(true);}
		$list	= $this->Lap_biaya_lampung->get_excel($start_date, $end_date, $id_cabang);
		$cell   = $list->num_rows() + 4;
		$no = 1;
		$x = 5;
		
		foreach($list->result() as $row)
		{
			$sheet->setCellValue('A'.$x, $no++);
			$sheet->setCellValue('B'.$x, $row->tgl);
			$sheet->setCellValue('C'.$x, $row->gaji);
			$sheet->setCellValue('D'.$x, $row->bulanan);
			$sheet->setCellValue('E'.$x, $row->listrik);
			$sheet->setCellValue('F'.$x, $row->angkut);
			$sheet->setCellValue('g'.$x, $row->ekspedisi);
			$sheet->setCellValue('h'.$x, $row->peralatan);
			$sheet->setCellValue('i'.$x, $row->konsumsi);
			$sheet->setCellValue('j'.$x, $row->dll);
			$sheet->setCellValue('k'.$x, $row->total);
			$x++;
		}
		
		$rows = $list->num_rows() + 5;
		$spreadsheet->getActiveSheet()->getStyle('C5:C'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('D5:D'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('E5:E'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('F5:F'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('G5:G'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('H5:H'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('I5:I'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('J5:J'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('K5:K'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');

		$sheet->getColumnDimension('K')->setWidth(20);

		$sheet->getStyle('A4:K'. $cell)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('K'.$rows, '=SUM(K5:K' . $cell . ')');
		$spreadsheet->getActiveSheet()->getStyle('K'.$rows)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('J'.$rows, 'Total');
		
		$writer = new Xlsx($spreadsheet);
		$filename = 'laporan-Biaya-Lampung';		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	
	}	

}