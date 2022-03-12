<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Laporan_penjualan_pelanggan extends GT_Controller {
	private $module = [
		'name' 	=> 'Laporan Penjualan Pelanggan',
		'url'	=> 'laporan/laporan-penjualan-pelanggan'
	];
	public function __construct(){
		parent::__construct();
		$this->load->model('laporan/Lap_penjualan');
	}
	public function index(){
		$data = array(
			'module' => array(
				'name' 	=> 'Laporan Penjualan Pelanggan',
				'url'	=> 'laporan/laporan-penjualan-pelanggan'
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
	public function api_data(){
        if($this->input->server('REQUEST_METHOD')=='POST'){
            $this->load->library('Datatables');
            $this->datatables->select('id, tgl_buat, nomor');
            $this->datatables->from('penjualan');
            $this->datatables->add_column(
                'action',
                    '<a class="btn btn-icon btn-sm btn-success mr-1" href="" title="edit">
                    <i class="fa fa-pencil">
                    </i>
                    <a class="btn btn-icon btn-sm btn-danger mr-1" href="" title="delete">
                    <i class="fa fa-trash-o">
                    </i>',
                "id");
                $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output($this->datatables->generate());
            }else{
                $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['message'=>'Cannot serve data.','error'=>'Method not allowed']));
            }
	}

}