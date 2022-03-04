<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Lap_bon extends GT_Controller {
	private $module = [
		'name' 	=> 'Laporan bon',
		'url'	=> 'laporan/lap-pembelian/lap-bon'
	];
	public function __construct(){
		parent::__construct();
		$this->load->model('laporan/lap-pembelian/Lap_bon_model');
	}
	public function index(){
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan bon',
				'url'	=> 'laporan/lap-pembelian/lap-bon'
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

	public function api_data($vendor_name = null){

		$response = array(
			'status' 	=> 'error', 
			'message'	=> 'No Action Parameter'
		);

		$vendor_name = strtolower(str_replace("-","_",$vendor_name));
		if( method_exists($this->Lap_bon_model, $vendor_name) ){
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
				case 'select2_toko':
					$config = array(
						'params' 	=> $params
					);
				break;
			endswitch;

			$response 	= $this->Lap_bon_model->{$vendor_name}($config);

		}

		$this->output
			->set_status_header(200)
			->set_content_type('Application/json')
			->set_output(json_encode($response));
	}

	public function print_out(){
		$params = $this->input->post(NULL, TRUE);
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan bon',
				'url'	=> 'laporan/lap-pembelian/lap-bon'
			),
			'params'	=> $params,
			'period' => array(
				'start'	=> $params['filter']['date_start'],
				'end'	=> $params['filter']['date_end']
			),
			'data'	=> $this->Lap_bon_model->datatable(
				array(
					'params' 	=> $params
				)
			)
		);
		$this->load->view($data['module']['url'] . '/html/print-out', $data);
	}

	public function excel($start_date, $end_date, $id_cabang, $cek_bon,$id_pemasok = null)
	{
		// $this->input->post(NULL, TRUE);
		$spreadsheet = new Spreadsheet();
		$row1 = 1;
		$spreadsheet->getActiveSheet()->mergeCells('B1:F'.$row1);
		// $spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row1, 'Laporan Penjualan');
		$row2 = 2;
		$spreadsheet->getActiveSheet()->mergeCells('B2:F'.$row2);
		$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(15);
		$spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row2, 'LAPORAN BON JAKARTA');
		$row3 = 3;
		$spreadsheet->getActiveSheet()->mergeCells('B3:F3');
		$spreadsheet->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row3, 'PERIODE : '.$start_date.' S/D '.$end_date);
		$row4 = 4;
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A'.$row4, 'No');
		$sheet->setCellValue('B'.$row4, 'Tanggal/Hari');
		$sheet->setCellValue('C'.$row4, 'Toko');
		$sheet->setCellValue('D'.$row4, 'Nomor');
		$sheet->setCellValue('E'.$row4, 'Total');
		$sheet->setCellValue('F'.$row4, 'Jumlah');
		$sheet->getStyle('A4:F'.$row4)->getFont()->setBold(true);
		$sheet->getStyle('A4:F'.$row4)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		
		for($col = 'B'; $col !== 'F'; $col++){$sheet->getColumnDimension($col)->setAutoSize(true);}
  
		$list	= $this->Lap_bon_model->get_excel($start_date, $end_date, $id_cabang, $id_pemasok, $cek_bon);
		$cell   = $list['data']->num_rows() + 4;
		$no = 1;
		$x = 5;
		$a=array();
		$subtotal_plg = $total = 0;
		$result = $list['data']->result_array();
		foreach($result as $key => $row)
		{
			$subtotal_plg += $row['sisa_tagihan'];
			if (@$result[$key+1]['nama'] != $row['nama']) {
			$sheet->setCellValue('F'.$x, $subtotal_plg);
			$subtotal_plg = 0;
			}
	
			$sheet->setCellValue('A'.$x, $no++);
			$sheet->setCellValue('B'.$x, $row['tgl_buat']);
			$sheet->setCellValue('C'.$x, $row['nama']);
			$sheet->setCellValue('D'.$x, $row['nomor']);
			$sheet->setCellValue('E'.$x, $row['sisa_tagihan']);
			$x++;
		}
		$rows = $list['data']->num_rows() + 5;

		$spreadsheet->getActiveSheet()->getStyle('F5:F'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('E5:E'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('E'.$rows)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('E'.$rows, '=SUM(F5:F' . $cell . ')');
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('D'.$rows, 'Total');
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		
		$spreadsheet->getActiveSheet()->getStyle('A5:A'.$cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$spreadsheet->getActiveSheet()->getStyle('C5:C'.$cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
		$spreadsheet->getActiveSheet()->getStyle('D5:D'.$cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle('A4:F'. $cell)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		
		$writer = new Xlsx($spreadsheet);
		$filename = 'laporan-Bon-Jakarta';		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	
	}	
}