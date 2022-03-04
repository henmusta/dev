<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Saldo_stok extends GT_Controller {
	private $module = [
		'name' 	=> 'Saldo stok',
		'url'	=> 'inventori/saldo-stok',
	];
	public function __construct(){
		parent::__construct();
		$this->load->model( 'inventori/Saldo_stok_model' );
	}
	public function index(){
		$data = array(
			'module' => $this->module
		);
		$head = array(
			'stylesheets'		=> array(
				'assets/js/plugins/datatables/DataTables-1.10.21/css/dataTables.bootstrap4.min.css',
				'assets/js/plugins/datatables/Select-1.3.1/css/select.bootstrap4.min.css',
				'assets/js/plugins/sweetalert2/sweetalert2.min.css'
			),
			'heading' => array('title'=>$data['module']['name'])
		);
		$foot = array(
			'javascripts'	=> array(
				'assets/js/plugins/datatables/DataTables-1.10.21/js/jquery.dataTables.min.js',
				'assets/js/plugins/datatables/DataTables-1.10.21/js/dataTables.bootstrap4.min.js',
				'assets/js/plugins/datatables/Select-1.3.1/js/dataTables.select.min.js',
				'assets/js/plugins/jquery-validation/jquery.validate.min.js',
				'assets/js/plugins/sweetalert2/sweetalert2.min.js',
				'assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js'
			),
			'scripts' 		=> array(
				$this->load->view($this->module['url'] . '/javascript/datatable', $data, TRUE)
			)
		);

		$this->html_head($head);
		$this->load->view($this->module['url'] . '/html/datatable',$data);
		$this->html_foot($foot);
	}

	public function detail($id_produk = null){
		$data = array(
			'module' => array(
				'id_produk' 	=> $id_produk,
				'name' 	=> 'Saldo Detail',
	        	'url'	=> 'inventori/saldo-stok',
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
	/* API DATA */
	public function api_data($vendor_name = null){

		$response = array(
			'status' 	=> 'error', 
			'message'	=> 'No Action Parameter'
		);

		$vendor_name = strtolower(str_replace("-","_",$vendor_name));
		if( method_exists($this->Saldo_stok_model, $vendor_name) ){
			$params = $this->input->post(NULL, TRUE);
			switch($vendor_name):
				case 'datatable':
					$config = array(
						'params' 	=> $params
					);
				break;

				case 'pembelian':
					$config = array(
						'params' 	=> $params
					);
				break;

				case 'penjualan':
					$config = array(
						'params' 	=> $params
					);
				break;

				case 'opname':
					$config = array(
						'params' 	=> $params
					);
				break;

				case 'retur':
					$config = array(
						'params' 	=> $params
					);
				break;
			endswitch;

			$response 	= $this->Saldo_stok_model->{$vendor_name}($config);

		}

		$this->output
			->set_status_header(200)
			->set_content_type('Application/json')
			->set_output(json_encode($response));
		}

		public function excel($id_produk)
		{
			$spreadsheet = new Spreadsheet(); 
	
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setTitle("Detail Stok Pembelian");
			$row1 = 1;
			$sheet->mergeCells('B1:E'.$row1);
			$row2 = 2;
			$sheet->mergeCells('B2:E'.$row2);
			$sheet->getStyle('B2')->getFont()->setSize(15);
			$sheet->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
			$spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row2, 'Detail Stok Pembelian');
			$row3 = 3;
			// $sheet->mergeCells('B3:E3');
			// $sheet->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
			// $spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$row3, 'PERIODE : '.$start_date.' S/D '.$end_date); 
			$row4 = 4;
			$sheet->setCellValue('A'.$row4, 'No');
			$sheet->setCellValue('B'.$row4, 'Tanggal');
			$sheet->setCellValue('C'.$row4, 'No Nota');
			$sheet->setCellValue('D'.$row4, 'Pemasok');
			$sheet->setCellValue('E'.$row4, 'Qty');
			$sheet->getStyle('A4:E'.$row4)->getFont()->setBold(true);
			$sheet->getStyle('A4:E'.$row4)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	
			for($col = 'B'; $col !== 'E'; $col++){$sheet->getColumnDimension($col)->setAutoSize(true);}
			$list_beli	= $this->Saldo_stok_model->get_excel_beli($id_produk);

			$cell   = $list_beli->num_rows() + 4;
			$no = 1;
			$x = 5;
			foreach($list_beli->result() as $row)
			{
				$sheet->setCellValue('A'.$x, $no++);
				$sheet->setCellValue('B'.$x, $row->tgl);
				$sheet->setCellValue('C'.$x, $row->nomor);
				$sheet->setCellValue('D'.$x, $row->pemasok);
				$sheet->setCellValue('E'.$x, $row->qty);
				$x++;
			}

			$rows = $list_beli->num_rows() + 5;
			$sheet->getColumnDimension('E')->setWidth(20);
			$sheet->getStyle('A5:A'.$cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
			$sheet->getStyle('D5:D'.$cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
			$spreadsheet->setActiveSheetIndex(0)->setCellValue('E'.$rows, '=SUM(E5:E' . $cell . ')');
			$spreadsheet->setActiveSheetIndex(0)->setCellValue('D'.$rows, 'Total');	
			$sheet->getStyle('A4:E'. $cell)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);



			//

			$sheet_2= $spreadsheet->createSheet();
			$sheet_2->setTitle("Detail Stok Penjualan");
			$row1 = 1;
			$sheet_2->mergeCells('B1:E'.$row1);
			$row2 = 2;
			$sheet_2->mergeCells('B2:E'.$row2);
			$sheet_2->getStyle('B2')->getFont()->setSize(15);
			$sheet_2->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
			$spreadsheet->setActiveSheetIndex(1)->setCellValue('B'.$row2, 'Detail Stok Penjualan');
			
			$sheet_2->setCellValue('A'.$row4, 'No');
			$sheet_2->setCellValue('B'.$row4, 'Tanggal');
			$sheet_2->setCellValue('C'.$row4, 'No Nota');
			$sheet_2->setCellValue('D'.$row4, 'Pelanggan');
			$sheet_2->setCellValue('E'.$row4, 'Qty');

			$sheet_2->getStyle('A4:E'.$row4)->getFont()->setBold(true);
			$sheet_2->getStyle('A4:E'.$row4)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			for($col = 'B'; $col !== 'E'; $col++){$sheet_2->getColumnDimension($col)->setAutoSize(true);}

			$list_jual	= $this->Saldo_stok_model->get_excel_jual($id_produk);

			$cell_jual   = $list_jual->num_rows() + 4;
			$no = 1;
			$x = 5;
			foreach($list_jual->result() as $row)
			{
				$sheet_2->setCellValue('A'.$x, $no++);
				$sheet_2->setCellValue('B'.$x, $row->tgl);
				$sheet_2->setCellValue('C'.$x, $row->nomor);
				$sheet_2->setCellValue('D'.$x, $row->pelanggan);
				$sheet_2->setCellValue('E'.$x, $row->qty);
				$x++;
			}

			$rows_jual = $list_jual->num_rows() + 5;
			$sheet_2->getColumnDimension('E')->setWidth(20);
			$sheet_2->getStyle('A5:A'.$cell_jual)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
			$sheet_2->getStyle('D5:D'.$cell_jual)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
			$spreadsheet->setActiveSheetIndex(1)->setCellValue('E'.$rows_jual, '=SUM(E5:E' . $cell_jual . ')');
			$spreadsheet->setActiveSheetIndex(1)->setCellValue('D'.$rows_jual, 'Total');	
			$sheet_2->getStyle('A4:E'. $cell_jual)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
	


			//
			$sheet_3=$spreadsheet->createSheet();
			$sheet_3->setTitle("Detail Stok Retur");
			$row1 = 1;
			$sheet_3->mergeCells('B1:F'.$row1);
			$row2 = 2;
			$sheet_3->mergeCells('B2:F'.$row2);
			$sheet_3->getStyle('B2')->getFont()->setSize(15);
			$sheet_3->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
			$spreadsheet->setActiveSheetIndex(2)->setCellValue('B'.$row2, 'Detail Stok Retur');

			$sheet_3->setCellValue('A'.$row4, 'No');
			$sheet_3->setCellValue('B'.$row4, 'Tanggal');
			$sheet_3->setCellValue('C'.$row4, 'No Nota');
			$sheet_3->setCellValue('D'.$row4, 'Pemasok');
			$sheet_3->setCellValue('E'.$row4, 'Jenis');
			$sheet_3->setCellValue('F'.$row4, 'Qty');
			for($col = 'B'; $col !== 'F'; $col++){$sheet_3->getColumnDimension($col)->setAutoSize(true);}
			$list_retur	= $this->Saldo_stok_model->get_excel_retur($id_produk);
			$cell_retur   = $list_retur->num_rows() + 4;
			$no = 1;
			$x = 5;
			foreach($list_retur->result() as $row)
			{
				$sheet_3->setCellValue('A'.$x, $no++);
				$sheet_3->setCellValue('B'.$x, $row->tgl);
				$sheet_3->setCellValue('C'.$x, $row->nomor);
				$sheet_3->setCellValue('D'.$x, $row->pemasok);
				$sheet_3->setCellValue('E'.$x, $row->jenis);
				$sheet_3->setCellValue('F'.$x, $row->qty);
				$x++;
			}

			$rows_retur = $list_retur->num_rows() + 5;
			$sheet_3->getColumnDimension('E')->setWidth(20);
			$sheet_3->getStyle('A5:A'.$cell_retur)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
			$sheet_3->getStyle('D5:D'.$cell_retur)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
			$spreadsheet->setActiveSheetIndex(1)->setCellValue('F'.$rows_retur, '=SUM(F5:F' . $cell_retur . ')');
			$spreadsheet->setActiveSheetIndex(1)->setCellValue('E'.$rows_retur, 'Total');	
			$sheet_3->getStyle('A4:F'.$cell_retur)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);


			$sheet_4=$spreadsheet->createSheet();
			$sheet_4->setTitle("Detail Stok Opname");
			$row1 = 1;
			$sheet_4->mergeCells('B1:F'.$row1);
			$row2 = 2;
			$sheet_4->mergeCells('B2:F'.$row2);
			$sheet_4->getStyle('B2')->getFont()->setSize(15);
			$sheet_4->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);		
			$spreadsheet->setActiveSheetIndex(3)->setCellValue('B'.$row2, 'Detail Stok Opname');

			$sheet_4->setCellValue('A'.$row4, 'No');
			$sheet_4->setCellValue('B'.$row4, 'Tanggal');
			$sheet_4->setCellValue('C'.$row4, 'No Nota');
			$sheet_4->setCellValue('D'.$row4, 'Pemasok');
			$sheet_4->setCellValue('E'.$row4, 'Jenis');
			$sheet_4->setCellValue('F'.$row4, 'Qty');
			
			for($col = 'B'; $col !== 'F'; $col++){$sheet_4->getColumnDimension($col)->setAutoSize(true);}

			$list_opname	= $this->Saldo_stok_model->get_excel_opname($id_produk);
			$cell_opname   = $list_opname->num_rows() + 4;
			$no = 1;
			$x = 5;
			foreach($list_opname->result() as $row)
			{
				$sheet_4->setCellValue('A'.$x, $no++);
				$sheet_4->setCellValue('B'.$x, $row->tgl);
				$sheet_4->setCellValue('C'.$x, $row->nomor);
				$sheet_4->setCellValue('D'.$x, $row->pemasok);
				$sheet_4->setCellValue('E'.$x, $row->jenis);
				$sheet_4->setCellValue('F'.$x, $row->qty);
				$x++;
			}

			$rows_opname = $list_opname->num_rows() + 5;
			$sheet_4->getColumnDimension('E')->setWidth(20);
			$sheet_4->getStyle('A5:A'.$cell_opname)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
			$sheet_4->getStyle('D5:D'.$cell_opname)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);	
			$spreadsheet->setActiveSheetIndex(1)->setCellValue('F'.$rows_opname, '=SUM(F5:F' . $cell_opname . ')');
			$spreadsheet->setActiveSheetIndex(1)->setCellValue('E'.$rows_opname, 'Total');	
			$sheet_4->getStyle('A4:F'.$cell_opname)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

	
			$writer = new Xlsx($spreadsheet);
			$filename = 'Detail-Stok';		
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
			header('Cache-Control: max-age=0');
			$writer->save('php://output');
		
		}	

}