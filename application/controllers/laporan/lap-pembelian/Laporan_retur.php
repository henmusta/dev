<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Laporan_retur extends GT_Controller {
	private $module = [
		'name' 	=> 'Laporan Retur Pembelian',
		'url'	=> 'laporan/lap-pembelian/laporan-retur'
	];
	public function __construct(){
		parent::__construct();
		$this->load->model('laporan/lap-pembelian/Lap_retur_model');
	}
	public function index(){
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan Retur Pembelian',
				'url'	=> 'laporan/lap-pembelian/laporan-retur'
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

	public function api_data($vendor_name = null){

		$response = array(
			'status' 	=> 'error', 
			'message'	=> 'No Action Parameter'
		);

		$vendor_name = strtolower(str_replace("-","_",$vendor_name));
		if( method_exists($this->Lap_retur_model, $vendor_name) ){
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
				case 'select2_barang':
						$config = array(
							'params' 	=> $params
						);
			endswitch;

			$response 	= $this->Lap_retur_model->{$vendor_name}($config);

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
				'name' 	=> 'Laporan Retur Pembelian',
				'url'	=> 'laporan/lap-pembelian/laporan-retur'
			),
			'params'	=> $params,
			'period' => array(
				'start'	=> $params['filter']['date_start'],
				'end'	=> $params['filter']['date_end']
			),
			'data'	=> $this->Lap_retur_model->datatable(
				array(
					'params' 	=> $params
				)
			)
		);
		$this->load->view($data['module']['url'] . '/html/print-out-retur', $data);
	}

	public function excel($start_date,$end_date, $id_cabang)
	{
		$spreadsheet = new Spreadsheet();
		$row1 = 1;
		$spreadsheet->getActiveSheet()->mergeCells('B1:E'.$row1);
		// $spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row1, 'Laporan Penjualan');
		$row2 = 2;
		$spreadsheet->getActiveSheet()->mergeCells('B2:E'.$row2);
		$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(15);
		$spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row2, 'LAPORAN RETUR PEMBELIAN');
		$row3 = 3;
		$spreadsheet->getActiveSheet()->mergeCells('B3:E3');
		$spreadsheet->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row3, 'PERIODE : '.$start_date.' S/D '.$end_date);
		$row4 = 4;
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A'.$row4, 'No');
		$sheet->setCellValue('B'.$row4, 'Qty');
		$sheet->setCellValue('C'.$row4, 'Nama Barang');
		$sheet->setCellValue('D'.$row4, 'Harga Satuan');
		$sheet->setCellValue('E'.$row4, 'Jumlah');
		$sheet->getStyle('A4:E'.$row4)->getFont()->setBold(true);
		$sheet->getStyle('A4:E'.$row4)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		for($col = 'B'; $col !== 'E'; $col++){$sheet->getColumnDimension($col)->setAutoSize(true);}
  
		$list	= $this->Lap_retur_model->get_excel($start_date,$end_date, $id_cabang);
		$cell   = $list->num_rows() + 4;
		$no = 1;
		$x = 5;
		foreach($list->result() as $row)
		{
			$sheet->setCellValue('A'.$x, $no++);
			$sheet->setCellValue('B'.$x, $row->qty_retur);
			$sheet->setCellValue('C'.$x, $row->namap);
			$sheet->setCellValue('D'.$x, $row->harga);
			$sheet->setCellValue('E'.$x, $row->total);
			$x++;
		}
		$rows = $list->num_rows() + 5;
		$spreadsheet->getActiveSheet()->getStyle('D5:D'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('D'.$rows)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('E5:E'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('E'.$rows)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('E'.$rows, '=SUM(E5:E' . $cell . ')');
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('D'.$rows, 'Total');
		//
		// $spreadsheet->getActiveSheet()->mergeCells('B5:B'.$cell);
		$sheet->getStyle('A4:E'. $cell)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		
		$writer = new Xlsx($spreadsheet);
		$filename = 'laporan-retur-Pembelian';		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	
	}	
}