<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Laporan_penjualan extends GT_Controller {
	private $module = [
		'name' 	=> 'Laporan Penjualan',
		'url'	=> 'laporan/laporan-penjualan'
	];
	public function __construct(){
		parent::__construct();
		$this->load->model('laporan/Lap_penjualan');
	}
	public function index(){
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan Penjualan',
				'url'	=> 'laporan/laporan-penjualan'
			) 
		);
		$head = array(
			'stylesheets'		=> array(
				'assets/js/plugins/flatpickr/flatpickr.min.css',
				'assets/js/plugins/datatables/DataTables-1.10.21/css/dataTables.bootstrap4.min.css',
				'assets/js/plugins/datatables/RowGroup-1.1.2/css/rowGroup.bootstrap4.min.css',
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

	public function single($pk = null){
        $this->load->model('laporan/Lap_penjualan');
		$this->module['action'] = $this->module['url'] . '/crud/update';
		$data = array(
			'module' 		=> $this->module,
			'penjualan'		=> $this->Lap_penjualan->single_combine($pk)
		);
		$head = array(
			'stylesheets'		=> array(),
			'heading' => array('title'=>$this->module['name'])
		);

		$foot = array(
			'javascripts'	=> array(),
			'scripts' 		=> array()
		);
		$this->html_head($head);
		$this->load->view($this->module['url'] . '/html/single', $data);
		$this->html_foot($foot);
	}

	public function detail(){
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan penjualan',
				'url'	=> 'laporan/laporan-penjualan'
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
				$this->load->view($data['module']['url'] . '/javascript/detail', $data, TRUE)
			)
		);

		$this->html_head($head);
		$this->load->view($data['module']['url'] . '/html/detail',$data);
		$this->html_foot($foot);
	}

	public function print_out(){

		$this->load->model('pengaturan/Aplikasi_model');
		$params = $this->input->post(NULL, TRUE);
		$data = array(
			'aplikasi'=> $this->Aplikasi_model->get(),
			'module' => array(
				'name' 	=> 'Laporan Penjualan',
			
				'url'	=> 'laporan/laporan-penjualan'
			),
			'params'	=> $params,
			'period' => array(
				'date_start'  => $params['filter']['date_start'],
				'date_end'	  => $params['filter']['date_end'],
				'id_cabang'	  => $params['filter']['id_cabang']
			),
			'data'	=> $this->Lap_penjualan->detailjual(
				array(
					'params' 	=> $params
				)
			)
		);
		$this->load->view($data['module']['url'] . '/html/print-out', $data);
	}

	public function print_out_retur(){
		$this->load->model('laporan/Lap_penjualan');
		$params = $this->input->post(NULL, TRUE);
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan Retur Penjualan',
				'url'	=> 'laporan/laporan-penjualan'
			),
			'params'	=> $params,
			'period' => array(
				'start'	=> $params['filter']['date_start'],
				'end'	=> $params['filter']['date_end']
			),
			'data'	=> $this->Lap_penjualan->datatableretur(
				array(
					'params' 	=> $params
				)
			)
		);
		$this->load->view($data['module']['url'] . '/html/print-out-retur', $data);
	}
	/* API DATA */
	public function api_data($vendor_name = null){

		$response = array(
			'status' 	=> 'error', 
			'message'	=> 'No Action Parameter'
		);

		$vendor_name = strtolower(str_replace("-","_",$vendor_name));
		$this->load->model('laporan/Lap_penjualan');
		if( method_exists($this->Lap_penjualan, $vendor_name) ){
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
				case 'detailjual':
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

			$response 	= $this->Lap_penjualan->{$vendor_name}($config);

		}

		$this->output
			->set_status_header(200)
			->set_content_type('Application/json')
			->set_output(json_encode($response));
	}

	public function api_data_retur($vendor_name = null){

		$response = array(
			'status' 	=> 'error', 
			'message'	=> 'No Action Parameter'
		);

		$vendor_name = strtolower(str_replace("-","_",$vendor_name));
		$this->load->model('laporan/Lap_penjualan');
		if( method_exists($this->Lap_penjualan, $vendor_name) ){
			$params = $this->input->post(NULL, TRUE);
			switch($vendor_name):
				case 'datatableretur':
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

			$response 	= $this->Lap_penjualan->{$vendor_name}($config);

		}

		$this->output
			->set_status_header(200)
			->set_content_type('Application/json')
			->set_output(json_encode($response));
	}

	public function excel($start_date,$end_date, $id_cabang)
	{
		$this->load->model('laporan/Lap_penjualan');
		$spreadsheet = new Spreadsheet();
		$row1 = 1;
		$spreadsheet->getActiveSheet()->mergeCells('B1:H'.$row1);
		// $spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row1, 'Laporan Penjualan');
		$row2 = 2;
		$spreadsheet->getActiveSheet()->mergeCells('B2:H'.$row2);
		$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(15);
		$spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row2, 'LAPORAN PENJUALAN');
		$row3 = 3;
		$spreadsheet->getActiveSheet()->mergeCells('B3:H3');
		$spreadsheet->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row3, 'PERIODE : '.$start_date.' S/D '.$end_date);
		$row4 = 4;
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A'.$row4, 'No');
		$sheet->setCellValue('B'.$row4, 'Tanggal/Hari');
		$sheet->setCellValue('C'.$row4, 'Nama');
		$sheet->setCellValue('D'.$row4, 'Alamat');
		$sheet->setCellValue('E'.$row4, 'No Nota');
		$sheet->setCellValue('F'.$row4, 'Jumlah Penjualan');
		$sheet->setCellValue('G'.$row4, 'Diskon');
		$sheet->setCellValue('H'.$row4, 'Laba Penjualan');
		$sheet->getStyle('A4:H'.$row4)->getFont()->setBold(true);
		$sheet->getStyle('A4:H'.$row4)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		for($col = 'B'; $col !== 'I'; $col++){$sheet->getColumnDimension($col)->setAutoSize(true);}
  
		$list	= $this->Lap_penjualan->get_excel($start_date,$end_date, $id_cabang);
		$cell   = $list->num_rows() + 4;
		$no = 1;
		$x = 5;

		
		foreach($list->result() as $row)
		{
			$sheet->setCellValue('A'.$x, $no++);
			$sheet->setCellValue('B'.$x, $row->tgl_nota);
			$sheet->setCellValue('C'.$x, $row->nama);
			$sheet->setCellValue('D'.$x, $row->alamat);
			$sheet->setCellValue('E'.$x, $row->nomor);
			$sheet->setCellValue('F'.$x, $row->total);
			$sheet->setCellValue('g'.$x, $row->diskon);
			$sheet->setCellValue('H'.$x, $row->laba);
			$x++;
		}
		$rows = $list->num_rows() + 5;
		$spreadsheet->getActiveSheet()->getStyle('F5:F'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('F'.$rows)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('G'.$rows)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('H'.$rows)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('F'.$rows, '=SUM(F5:F' . $cell . ')');
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('G'.$rows, '=SUM(G5:G' . $cell . ')');
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('H'.$rows, '=SUM(H5:H' . $cell . ')');
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('E'.$rows, 'Total');
		//
		$spreadsheet->getActiveSheet()->getStyle('G5:G'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		// $spreadsheet->getActiveSheet()->mergeCells('B5:B'.$cell);
		$sheet->getStyle('A4:H'. $cell)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		$spreadsheet->getActiveSheet()->getStyle('B5:B'.$cell)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);	
		$spreadsheet->getActiveSheet()->getStyle('B5:B'.$cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
		// $Cash_debit = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'My Data');

		// // Attach the "My Data" worksheet as the first worksheet in the Spreadsheet object
		// $spreadsheet->addSheet($Cash_debit, 1);
		
		$writer = new Xlsx($spreadsheet);
		$filename = 'laporan-Penjualan';		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	
	}	
}