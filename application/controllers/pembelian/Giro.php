<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Giro extends GT_Controller {
	private $module = [
		'name' 	=> 'Pencairan Giro',
		'url'	=> 'pembelian/giro',
	];
	public function __construct(){
		parent::__construct();
		$this->load->model( 'pembelian/Giro_beli_model' );
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
				'assets/toogle/bootstrap-toggle.min.css',
				'assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css'
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
				'assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js',
				'assets/toogle/bootstrap-toggle.min.js',
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

    public function cek_giro($id)
	{
		$data = $this->Giro_beli_model->cek_giro($id);
		echo json_encode($data);
	}

    public function update_active($id)
	{	
		$this->db->set('chek','2');
		$this->db->where('id',$id);
		$this->db->where('metode','giro');
		$this->db->update('rincian_pembayaran');

		$this->db->set('chek','2');
		$this->db->where('id',$id);
		$this->db->where('metode','giro');
		$this->db->update('rincian_pembayaran_multi');
	}

	public function update_not_active($id)
	{	
		$this->db->set('chek','1');
		$this->db->where('id',$id);
		$this->db->where('metode','giro');
		$this->db->update('rincian_pembayaran');

		$this->db->set('chek','1');
		$this->db->where('id',$id);
		$this->db->where('metode','giro');
		$this->db->update('rincian_pembayaran_multi');
	}

	public function api_data($vendor_name = null){
		$response = array(
			'status' 	=> 'error', 
			'message'	=> 'No Action Parameter'
		);
		$vendor_name = strtolower(str_replace("-","_",$vendor_name));
		if( method_exists($this->Giro_beli_model, $vendor_name) ){
			$params = $this->input->post(NULL, TRUE);
			switch($vendor_name):
				case 'datatable':
					$config = array(
						'params' 	=> $params
					);
				break;	
    			
			endswitch;
			$response 	= $this->Giro_beli_model->{$vendor_name}($config);
		}
		$this->output
			->set_status_header(200)
			->set_content_type('Application/json')
			->set_output(json_encode($response));
	}

}