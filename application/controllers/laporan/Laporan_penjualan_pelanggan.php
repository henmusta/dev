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
				'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
				'assets/js/plugins/sweetalert2/sweetalert2.min.css'
			),
			'heading' => array('title'=>$data['module']['name'])
		);
		$foot = array(
			'javascripts'	=> array(
				'assets/js/plugins/flatpickr/flatpickr.min.js',
				'assets/js/plugins/datatables/DataTables-1.10.21/js/jquery.dataTables.min.js',
				'assets/js/plugins/datatables/DataTables-1.10.21/js/dataTables.bootstrap4.min.js',
				'https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js',
				'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js',
				'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js',
				'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js',
				'https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js',
				'https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js',
				'assets/js/plugins/datatables/RowGroup-1.1.2/js/dataTables.rowGroup.min.js',
				'assets/js/plugins/jquery-validation/jquery.validate.min.js',
				'assets/js/plugins/sweetalert2/sweetalert2.min.js',
				'assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js',
				'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
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
        $params = $this->input->post('filter');
		$data =	$this->Lap_penjualan->getPerPelanggan($params['date_start'], $params['date_end'], $params['id_cabang'], isset($params['customer_id']) ? $params['customer_id'] : NULL);
		$this->output
			->set_content_type('application/json')
			->set_status_header(200)
			->set_output(json_encode($data));
	}
	public function getCustomer()
	{
		$q = $this->input->get('q');
		$customer = $this->db
			->select('id as id, nama as text')
			->like('nama', $q)
			->get('pelanggan')->result();
		$this->output
			->set_content_type('application/json')
			->set_status_header(200)
			->set_output(json_encode($customer));
	}

}