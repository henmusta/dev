<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Laporan_pembelian extends GT_Controller {
	private $module = [
		'name' 	=> 'Laporan Pembelian',
		'url'	=> 'laporan/lap-pembelian/laporan-pembelian'
	];
	public function __construct(){
		parent::__construct();
		$this->load->model('laporan/lap-pembelian/Lap_pembelian_model');
	}
	public function index(){
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan Pembelian',
				'url'	=> 'laporan/lap-pembelian/laporan-pembelian'
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

	public function detail(){
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan Pembelian',
				'url'	=> 'laporan/lap-pembelian/laporan-pembelian'
				// 'giro'		=> $this->Lap_pembelian->detailgiro($pk),
				// 'bon'		=> $this->Lap_pembelian->detailbon($pk)
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

	public function single($pk = null){
		$this->module['action'] = $this->module['url'] . '/crud/update';
		$data = array(
			'module' 		=> $this->module,
			'pembelian'		=> $this->Lap_pembelian_model->single($pk)
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


	public function print_out(){
		$params = $this->input->post(NULL, TRUE);
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan Pembelian',
				'url'	=> 'laporan/lap-pembelian/laporan-pembelian'
			),
			'params'	=> $params,
			'period' => array(
				'date_start'	=> $params['filter']['date_start'],
				'date_end'	=> $params['filter']['date_end']
			),
			'data'	=> $this->Lap_pembelian_model->detailtunai(
				array(
					'params' 	=> $params
				)
			),
			'data1'	=> $this->Lap_pembelian_model->detailgiro(
				array(
					'params' 	=> $params
				)
			),
			'data2'	=> $this->Lap_pembelian_model->detailbon(
				array(
					'params' 	=> $params
				)
			)
		);
		$this->load->view($data['module']['url'] . '/html/print-out', $data);
	}
	public function api_data($vendor_name = null){

		$response = array(
			'status' 	=> 'error', 
			'message'	=> 'No Action Parameter'
		);

		$vendor_name = strtolower(str_replace("-","_",$vendor_name));
		if( method_exists($this->Lap_pembelian_model, $vendor_name) ){
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

				case 'detailtunai':
					$config = array(
						'columns' 	=> array(
							'nomor', 
							'tgl_nota', 
							'id'
						),
						'params' 	=> $params
					);
				break;
				case 'detailgiro':
					$config = array(
						'columns' 	=> array(
							'nomor', 
							'tgl_nota', 
							'id'
						),
						'params' 	=> $params
					);
				break;
				case 'detailbon':
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

			$response 	= $this->Lap_pembelian_model->{$vendor_name}($config);

		}

		$this->output
			->set_status_header(200)
			->set_content_type('Application/json')
			->set_output(json_encode($response));
	}


	public function excel($start_date,$end_date, $id_cabang)
	{
		$spreadsheet = new Spreadsheet(); 

		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle("Bon");
		$row1 = 1;
		$sheet->mergeCells('B1:F'.$row1);
		$row2 = 2;
		$sheet->mergeCells('B2:F'.$row2);
		$sheet->getStyle('B2')->getFont()->setSize(15);
		$sheet->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row2, 'LAPORAN PEMBELIAN BON');
		$row3 = 3;
		$sheet->mergeCells('B3:E3');
		$sheet->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row3, 'PERIODE : '.$start_date.' S/D '.$end_date); 
		$row4 = 4;
		$sheet->setCellValue('A'.$row4, 'No');
		$sheet->setCellValue('B'.$row4, 'Nama Toko');
		$sheet->setCellValue('C'.$row4, 'Tgl Buat');
		$sheet->setCellValue('D'.$row4, 'Tgl Nota');
		$sheet->setCellValue('E'.$row4, 'No Nota');
		$sheet->setCellValue('F'.$row4, 'Total');
		$sheet->getStyle('A4:F'.$row4)->getFont()->setBold(true);
		$sheet->getStyle('A4:F'.$row4)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		for($col = 'B'; $col !== 'F'; $col++){$sheet->getColumnDimension($col)->setAutoSize(true);}
		$list_bon	= $this->Lap_pembelian_model->get_excel_bon($start_date,$end_date, $id_cabang);
		$cell   = $list_bon->num_rows() + 4;
		$no = 1;
		$x = 5;
		foreach($list_bon->result() as $row)
		{
			$sheet->setCellValue('A'.$x, $no++);
			$sheet->setCellValue('B'.$x, $row->nama);
			$sheet->setCellValue('C'.$x, $row->tglbuat);
			$sheet->setCellValue('D'.$x, $row->tglnota);
			$sheet->setCellValue('E'.$x, $row->nomor);
			$sheet->setCellValue('F'.$x, $row->sisa_tagihan);
			$x++;
		}

		$rows = $list_bon->num_rows() + 5;
		$sheet->getStyle('F5:F'.$cell)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$sheet->getStyle('F'.$rows)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getStyle('A5:A'.$cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
		$sheet->getStyle('E5:E'.$cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('F'.$rows, '=SUM(F5:F' . $cell . ')');
		$spreadsheet->setActiveSheetIndex(0)->setCellValue('E'.$rows, 'Total');	
		$sheet->getStyle('A4:F'. $cell)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);




		$sheet_2= $spreadsheet->createSheet();
		$sheet_2->setTitle("Giro");
		$row1 = 1;
		$sheet_2->mergeCells('B1:J'.$row1);
		$row2 = 2;
		$sheet_2->mergeCells('B2:J'.$row2);
		$sheet_2->getStyle('B2')->getFont()->setSize(15);
		$sheet_2->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(1)->setCellValue('B'.$row2, 'LAPORAN PEMBELIAN GIRO');
		$row3 = 3;
		$sheet_2->mergeCells('B3:J3');
		$sheet_2->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(1)->setCellValue('B'.$row3, 'PERIODE : '.$start_date.' S/D '.$end_date); 
		$sheet_2->setCellValue('A'.$row4, 'No');
		$sheet_2->setCellValue('B'.$row4, 'Nama Toko');
		$sheet_2->setCellValue('C'.$row4, 'Tgl Buat');
		$sheet_2->setCellValue('D'.$row4, 'Tgl Nota');
		$sheet_2->setCellValue('E'.$row4, 'No Nota');
		$sheet_2->setCellValue('F'.$row4, 'Tgl Giro');
		$sheet_2->setCellValue('G'.$row4, 'No Giro');
		$sheet_2->setCellValue('H'.$row4, 'Jumlah');
		$sheet_2->setCellValue('I'.$row4, 'diskon');
		$sheet_2->setCellValue('J'.$row4, 'Total');
		$sheet_2->getStyle('A4:J'.$row4)->getFont()->setBold(true);
		$sheet_2->getStyle('A4:J'.$row4)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		for($col = 'B'; $col !== 'J'; $col++){$sheet_2->getColumnDimension($col)->setAutoSize(true);}
		$list_giro	= $this->Lap_pembelian_model->get_excel_giro($start_date,$end_date, $id_cabang);
		$cell_giro   = $list_giro->num_rows() + 4;
		$no = 1;
		$x = 5;
		foreach($list_giro->result() as $row)
		{
			$sheet_2->setCellValue('A'.$x, $no++);
			$sheet_2->setCellValue('B'.$x, $row->namap);
			$sheet_2->setCellValue('C'.$x, $row->tgl_buat);
			$sheet_2->setCellValue('D'.$x, $row->tglnota);
			$sheet_2->setCellValue('E'.$x, $row->nomor);
			$sheet_2->setCellValue('F'.$x, $row->tgl_giro);
			$sheet_2->setCellValue('G'.$x, $row->nomor_giro);
			$sheet_2->setCellValue('H'.$x, $row->nominal);
			$sheet_2->setCellValue('I'.$x, $row->diskon);
			$sheet_2->setCellValue('J'.$x, $row->total);
			$x++;
		}

		$rows_giro = $list_giro->num_rows() + 5;
		$sheet_2->getStyle('H5:H'.$cell_giro)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$sheet_2->getStyle('H'.$rows_giro)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$sheet_2->getColumnDimension('H')->setWidth(20);

		$sheet_2->getStyle('I5:I'.$cell_giro)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$sheet_2->getStyle('I'.$rows_giro)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$sheet_2->getColumnDimension('I')->setWidth(20);

		$sheet_2->getStyle('J5:J'.$cell_giro)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$sheet_2->getStyle('J'.$rows_giro)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$sheet_2->getColumnDimension('J')->setWidth(20);
		
		$sheet_2->getStyle('A5:A'.$cell_giro)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
		$sheet_2->getStyle('D5:D'.$cell_giro)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
		$spreadsheet->setActiveSheetIndex(1)->setCellValue('H'.$rows_giro, '=SUM(G5:H' . $cell_giro . ')');
		$spreadsheet->setActiveSheetIndex(1)->setCellValue('I'.$rows_giro, '=SUM(H5:I' . $cell_giro . ')');
		$spreadsheet->setActiveSheetIndex(1)->setCellValue('J'.$rows_giro, '=SUM(I5:J' . $cell_giro . ')');
		$spreadsheet->setActiveSheetIndex(1)->setCellValue('F'.$rows_giro, 'Total');	
		$sheet_2->getStyle('A4:J'. $cell_giro)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

		$sheet_3=$spreadsheet->createSheet();
		$sheet_3->setTitle("Cash Debit");
		$row1 = 1;
		$sheet_3->mergeCells('B1:H'.$row1);
		$row2 = 2;
		$sheet_3->mergeCells('B2:H'.$row2);
		$sheet_3->getStyle('B2')->getFont()->setSize(15);
		$sheet_3->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(2)->setCellValue('B'.$row2, 'LAPORAN PEMBELIAN CASH / DEBIT');
		$row3 = 3;
		$sheet_3->mergeCells('B3:H3');
		$sheet_3->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
		$spreadsheet->setActiveSheetIndex(2)->setCellValue('B'.$row3, 'PERIODE : '.$start_date.' S/D '.$end_date); 
		$sheet_3->setCellValue('A'.$row4, 'No');
		$sheet_3->setCellValue('B'.$row4, 'Nama Toko');
		$sheet_3->setCellValue('C'.$row4, 'Tgl Buat');
		$sheet_3->setCellValue('D'.$row4, 'Tgl Nota');
		$sheet_3->setCellValue('E'.$row4, 'Nomor');
		$sheet_3->setCellValue('F'.$row4, 'Metode');
		$sheet_3->setCellValue('G'.$row4, 'Jumlah');
		$sheet_3->setCellValue('H'.$row4, 'Diskon');
		$sheet_3->setCellValue('I'.$row4, 'Total');
		$sheet_3->getStyle('A4:I'.$row4)->getFont()->setBold(true);
		$sheet_3->getStyle('A4:I'.$row4)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		for($col = 'B'; $col !== 'I'; $col++){$sheet_3->getColumnDimension($col)->setAutoSize(true);}
		$list_tunai	= $this->Lap_pembelian_model->get_excel_tunai($start_date,$end_date, $id_cabang);
		$cell_cash   = $list_tunai->num_rows() + 4;
		$no = 1;
		$x = 5;
		foreach($list_tunai->result() as $row)
		{
			$sheet_3->setCellValue('A'.$x, $no++);
			$sheet_3->setCellValue('B'.$x, $row->namap);
			$sheet_3->setCellValue('C'.$x, $row->tgl_buat);
			$sheet_3->setCellValue('D'.$x, $row->tgl_nota);
			$sheet_3->setCellValue('E'.$x, $row->nomor);
			$sheet_3->setCellValue('F'.$x, $row->metode);
			$sheet_3->setCellValue('G'.$x, $row->nominal);
			$sheet_3->setCellValue('H'.$x, $row->diskon);
			$sheet_3->setCellValue('I'.$x, $row->total);
			$x++;
		}
		$rows_cash = $list_tunai->num_rows() + 5;
		$sheet_3->getStyle('G5:G'.$cell_cash)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$sheet_3->getStyle('G'.$rows_cash)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$sheet_3->getColumnDimension('G')->setWidth(20);

		$sheet_3->getStyle('H5:H'.$cell_cash)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$sheet_3->getStyle('H'.$rows_cash)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$sheet_3->getColumnDimension('H')->setWidth(20);

		$sheet_3->getStyle('I5:I'.$cell_cash)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$sheet_3->getStyle('I'.$rows_cash)->getNumberFormat()->setFormatCode('Rp,#,##0');
		$sheet_3->getColumnDimension('I')->setWidth(20);
		
		$sheet_3->getStyle('A5:A'.$cell_cash)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
		$sheet_3->getStyle('E5:E'.$cell_cash)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
		$spreadsheet->setActiveSheetIndex(2)->setCellValue('G'.$rows_cash, '=SUM(G5:G' . $cell_cash . ')');
		$spreadsheet->setActiveSheetIndex(2)->setCellValue('H'.$rows_cash, '=SUM(H5:H' . $cell_cash . ')');
		$spreadsheet->setActiveSheetIndex(2)->setCellValue('I'.$rows_cash, '=SUM(I5:I' . $cell_cash . ')');
		$spreadsheet->setActiveSheetIndex(2)->setCellValue('F'.$rows_cash, 'Total');	
		$sheet_3->getStyle('A4:I'. $cell_cash)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		
		$writer = new Xlsx($spreadsheet);
		$filename = 'laporan-Pembelian';		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	
	}	
}