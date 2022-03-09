<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faktur extends GT_Controller {
	private $module = [
		'name' 	=> 'Faktur Penjualan',
		'url'	=> 'penjualan/faktur',
	];
	public function __construct(){
		parent::__construct();
		$this->load->model( 'penjualan/Faktur_model' );
		$this->load->model( 'pengaturan/Aplikasi_model' );
	}
	public function index(){
		$data = array(
			'module' => $this->module
		);
		$head = array(
			'stylesheets'		=> array(
				'assets/js/plugins/datatables/DataTables-1.10.21/css/dataTables.bootstrap4.min.css',
				'assets/js/plugins/datatables/Select-1.3.1/css/select.bootstrap4.min.css',
				'assets/js/plugins/sweetalert2/sweetalert2.min.css',
				'assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css'
			),
			'heading' => array('title'=> $this->module['name'])    
		);
		$foot = array(
			'javascripts'	=> array(
				'assets/js/plugins/datatables/DataTables-1.10.21/js/jquery.dataTables.min.js',
				'assets/js/plugins/datatables/DataTables-1.10.21/js/dataTables.bootstrap4.min.js',
				'assets/js/plugins/datatables/Select-1.3.1/js/dataTables.select.min.js',
				'assets/js/plugins/jquery-validation/jquery.validate.min.js',
				'assets/js/plugins/sweetalert2/sweetalert2.min.js',
				'assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js',
				'assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js'
			),
			'scripts' 		=> array(
				$this->load->view($this->module['url'] . '/javascript/datatable', $data, TRUE)
			)
		);

		$this->html_head($head);
		$this->load->view($this->module['url'] . '/html/datatable',$data);
		$this->html_foot($foot);
	}
	public function insert(){
		$this->module['action'] = $this->module['url'] . '/crud/insert';
		$data = array(
			'module' 		=> $this->module
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
				'assets/autocomplete/bootstrap-typeahead.min.js',
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
				$this->load->view($this->module['url'] . '/javascript/form', $data, TRUE)
			)
		);
		$this->html_head($head);
		$this->load->view($this->module['url'] . '/html/form', $data);
		$this->html_foot($foot);
	}
	public function update($pk = null){
		$this->module['action'] = $this->module['url'] . '/crud/update';
		$data = array(
			'module' 		=> $this->module,
			'data'			=> $this->Faktur_model->single1($pk)
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
				'assets/autocomplete/bootstrap-typeahead.min.js',
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
				$this->load->view($this->module['url'] . '/javascript/form', $data, TRUE)
			)
		);
		$this->html_head($head);
		$this->load->view($this->module['url'] . '/html/form', $data);
		$this->html_foot($foot);
	}
	public function harga_jual($pk = null){
		$this->module['action'] = $this->module['url'] . '/crud/update';
		$data = array(
			'module' 		=> $this->module,
			'penjualan'		=> $this->Faktur_model->single($pk)
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
				$this->load->view($this->module['url'] . '/javascript/harga-jual', $data, TRUE)
			)
		);
		$this->html_head($head);
		$this->load->view($this->module['url'] . '/html/harga-jual', $data);
		$this->html_foot($foot);
	}
	public function single($pk = null){
		$this->module['action'] = $this->module['url'] . '/crud/update';
		$data = array(
			'module' 		=> $this->module,
			'aplikasi'=> $this->Aplikasi_model->get(),
			'penjualan'		=> $this->Faktur_model->single_combine($pk)
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
	/* CRUD */
	public function crud($action = null){
		$action = strtolower($action);
		$response = array(
			'status' 	=> 'error', 
			'message'	=> 'No Action Parameter'
		);

		if( !empty($action) && in_array($action, array('insert','update-harga', 'update', 'delete') ) ){
			$params 	= $this->input->post(NULL, TRUE);
			$response 	= $this->Faktur_model->{str_replace('-','_',$action)}($params);
			$response['status'] = isset($response['status']) && is_bool($response['status']) && $response['status'] === TRUE ? 'success' : 'error';
		}

		$this->output
			->set_status_header(200)
			->set_content_type('Application/json')
			->set_output(json_encode($response));
	}
	/* API DATA */
	public function api_data($vendor_name = null){

		$response = array(
			'status' 	=> 'error', 
			'message'	=> 'No Action Parameter'
		);

		$vendor_name = strtolower(str_replace("-","_",$vendor_name));
		if( method_exists($this->Faktur_model, $vendor_name) ){
			$params = $this->input->post(NULL, TRUE);

			switch($vendor_name):
				case 'datatable':
				case 'datatable_harga':
					$config = array(
						'params' 	=> $params
					);
				break;
				case 'get_kode_produk':
					$config = array(
						'params' 	=> $params
					);
				break;
				case 'select2':
				case 'select2_akun':
				case 'select2_giro':
				case 'select2_produk_retur':
					$config = array(
						'row_per_page' 	=> 50,
						'params' 		=> $params
					);
				break;
				case 'select2_produk':
					$config = array(
						'row_per_page' 	=> 50,
						'params' 		=> $params
					);
				break;
				case 'get_kode_pelanggan_by_nama' :
					$config = array(
						'params' => $params
					);
				break;
				case 'get_kode_pelanggan_by_nama' :
					$config = array(
						'params' => $params
					);
				break;
				case 'select2_pelanggan' :
					$config = array(
						'params' => $params
					);
				break;
			endswitch;

			$response 	= $this->Faktur_model->{$vendor_name}($config);

		}

		$this->output
			->set_status_header(200)
			->set_content_type('Application/json')
			->set_output(json_encode($response));
	}


}