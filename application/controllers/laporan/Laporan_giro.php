<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Laporan_giro extends GT_Controller {
	private $module = [
		'name' 	=> 'Laporan Giro',
		'url'	=> 'laporan/laporan-giro'
	];
	public function __construct(){
		parent::__construct();
		$this->load->model( 'laporan/Lap_giro' );
	}
	public function index(){
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan giro',
				'url'	=> 'laporan/laporan-giro'
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

	public function crud($action = null){

		$action = strtolower($action);

		$response = array(
			'status' 	=> 'error', 
			'message'	=> 'No Action Parameter'
		);

		if( !empty($action) && in_array($action, array('update','update-ket') ) ){
			$params 	= $this->input->post(NULL, TRUE);
			$response 	= $this->Lap_giro->{str_replace('-','_',$action)}($params);
			$response['status'] = isset($response['status']) && is_bool($response['status']) && $response['status'] === TRUE ? 'success' : 'error';
		}

		$this->output
			->set_status_header(200)
			->set_content_type('Application/json')
			->set_output(json_encode($response));
	}

    public function api_data($vendor_name = null){

		$response = array(
			'status' 	=> 'error', 
			'message'	=> 'No Action Parameter'
		);

		$vendor_name = strtolower(str_replace("-","_",$vendor_name));
		$this->load->model('laporan/Lap_giro');
		if( method_exists($this->Lap_giro, $vendor_name) ){
			$params = $this->input->post(NULL, TRUE);
			switch($vendor_name):
				case 'select2_giro':
					$config = array(
						'params' 	=> $params
					);
				break;
				case 'datatable':
					$config = array(
						'params' 	=> $params
					);
				break;
			endswitch;
			$response 	= $this->Lap_giro->{$vendor_name}($config);
		}
		$this->output
			->set_status_header(200)
			->set_content_type('Application/json')
			->set_output(json_encode($response));
	}
    public function single(){
		// $params 	= $this->input->post(NULL, TRUE)
		$awal =  $this->uri->segment(4);
		$akhir =  $this->uri->segment(5);
		$id_cabang =  $this->uri->segment(6);
		$this->module['action'] = $this->module['url'] . '/crud/update';
		$this->load->model('laporan/Lap_giro');

		$params = array(
		   'awal' =>	$awal,
		   'akhir' => $akhir,
		   'id_cabang' =>$id_cabang
		);

		$nama = 'LAPORAN GIRO';

		$data = array(
			'nama' 		=> $nama,
			'awal' 		=> $awal,
			'akhir' 	=> $akhir,
			'id_cabang' 		=> $id_cabang,
			'module' 		=> $this->module,
			'giro'		=> $this->Lap_giro->single(
				array(
				'params' 	=> $params
			    )
			)
		);
		$head = array(
			'stylesheets'		=> array(
				'assets/js/plugins/datatables/DataTables-1.10.21/css/dataTables.bootstrap4.min.css',
				'assets/js/plugins/datatables/Select-1.3.1/css/select.bootstrap4.min.css',
				'assets/js/plugins/sweetalert2/sweetalert2.min.css',
				'assets/js/plugins/select2/css/select2.min.css',
				'assets/js/plugins/select2/css/select2-bootstrap4.min.css',
				'assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css'
			),
			'heading' => array('title'=>$this->module['name'])
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
				'assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js'
			),
			'scripts' 		=> array(
				$this->load->view($this->module['url'] . '/javascript/detail', $data, TRUE)
			)
		);
		$this->html_head($head);
		$this->load->view($this->module['url'] . '/html/detail', $data);
		$this->html_foot($foot);
	}

	public function excel($giroawal,$giroakhir, $id_cabang)
	{
		// $this->load->model('laporan/Lap_penjualan');
		$nama = 'LAPORAN GIRO';
		$spreadsheet = new Spreadsheet();
		$row1 = 1;
		$spreadsheet->getActiveSheet()->mergeCells('B1:G'.$row1);
		// $spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row1, 'Laporan Penjualan');
		$row2 = 2;
		$spreadsheet->getActiveSheet()->mergeCells('B2:F'.$row2);
		$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(15);
		$spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row2, $nama);
		$row3 = 3;
		$spreadsheet->getActiveSheet()->mergeCells('B3:F3');
		$spreadsheet->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		// $spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row3, 'PERIODE : '.$start_date.' S/D '.$end_date);
		$row4 = 4;
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A'.$row4, 'No');
		$sheet->setCellValue('B'.$row4, 'No Giro');
		$sheet->setCellValue('C'.$row4, 'Nama Toko');
		$sheet->setCellValue('D'.$row4, 'Tgl Giro');
		$sheet->setCellValue('E'.$row4, 'Jumlah');
		$sheet->setCellValue('F'.$row4, 'Keterangan');
		// $sheet->setCellValue('G'.$row4, 'Laba Penjualan');
		$sheet->getStyle('A4:F'.$row4)->getFont()->setBold(true);
		$sheet->getStyle('A4:F'.$row4)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		for($col = 'B'; $col !== 'F'; $col++){$sheet->getColumnDimension($col)->setAutoSize(true);}
  
		$list	= $this->Lap_giro->get_excel($giroawal,$giroakhir, $id_cabang);
		$cell   = $list->num_rows() + 4;
		$no = 1;
		$x = 5;
		
		foreach($list->result() as $row)
		{
			$sheet->setCellValue('A'.$x, $no++);
			$sheet->setCellValue('B'.$x, $row->nomor);
			$sheet->setCellValue('C'.$x, $row->toko);
			$sheet->setCellValue('D'.$x, $row->tgl_giro);
			$sheet->setCellValue('E'.$x, $row->jumlah);
			$sheet->setCellValue('F'.$x, $row->keterangan);
			$x++;
		}
		$rows = $list->num_rows() + 5;
		$spreadsheet->getActiveSheet()->getStyle('E5:E'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->getActiveSheet()->getStyle('E'.$rows)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('E'.$rows, '=SUM(E5:E' . $cell . ')');
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('D'.$rows, 'Total');
		$sheet->getColumnDimension('F')->setWidth(30);
		// $spreadsheet->getActiveSheet()->mergeCells('B5:B'.$cell);
		$sheet->getStyle('A4:F'. $cell)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		$spreadsheet->getActiveSheet()->getStyle('A5:A'.$cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
	
		
		$writer = new Xlsx($spreadsheet);
		$filename = $nama;		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	
	}	
}