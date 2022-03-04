<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengguna extends GT_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('pengaturan/Pengguna_model');
	}
	public function index(){
		$head = array(
			'stylesheets'		=> array(
				'assets/js/plugins/datatables/dataTables.bootstrap4.css',
				'assets/js/plugins/sweetalert2/sweetalert2.min.css'
			),
			'heading' 		=> array(
				'title' 		=> 'Pengguna',
				'subtitle' 		=> '',
			)
		);
		$datatables = array(
			'id'			=> 'table-pengguna',
			'title'			=> 'Daftar pengguna',
			'subtitle'		=> '',
			'btn_add_new' 	=> 'pengaturan/pengguna/add',
			'thead'			=> trim('
				<thead>
					<tr>
						<th>Nama</th>
						<th>Username</th>
						<th>Hak Akses</th>
						<th>Status</th>
						<th>Aksi</th>
					</tr>
				</thead>
			'),
			'tfoot'				=> trim(''),
			'source_url' 		=> base_url('pengaturan/pengguna/datatables'),
			'delete_url' 		=> base_url('pengaturan/pengguna/delete'),
			'display_length' 	=> 10,
			'order_column'		=> array('number'=>0,'dir'=>'asc'),
			'columns' => array(
				'{ data : "nama" },',
				'{ data : "username" },',
				'{ data : "hak_akses" },',
				'{ 
					data : "status",
					render:function(data, type, row, meta){
						return data == 1 ? `<label class="badge badge-success">Aktif</label>` : `<label class="badge badge-secondary">Non Aktif</label>`;
					}
				},',
				'{ 
					data: "id_pengguna", 
					className:"text-center", 
					width:"50px",
					render:function(data, type, row, meta){
						return \'<div class="dropdown">\'+
                                \'<button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></button>\'+
                                \'<div class="dropdown-menu dropdown-menu-right font-size-sm" aria-labelledby="dropdown-align-outline-primary" x-placement="top-end">\'+
                                    \'<a class="dropdown-item" href="pengaturan/pengguna/edit/\'+ data +\'">Edit</a>\'+
                                    \'<a class="dropdown-item btn-delete" data-pk="\'+ data +\'"href="javascript:void(0)">Hapus</a>\'+
                                \'</div>\'+
                            \'</div>\';
					}
				}'
			)
		);
		$foot = array(
			'javascripts' => array(
				'assets/js/plugins/datatables/jquery.dataTables.min.js',
				'assets/js/plugins/datatables/dataTables.bootstrap4.min.js',
				'assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js',
				'assets/js/plugins/sweetalert2/sweetalert2.min.js'
			),
			'scripts'	=> array(
				$this->load->view('pengaturan/pengguna/js/datatables', $datatables, TRUE)
			)
		);
		$this->html_head($head);
		$this->load->view('pengaturan/pengguna/html/datatables', $head);
		$this->html_foot($foot);
	}
	public function add(){
		$head = array(
			'stylesheets'	=> array(
				'assets/js/plugins/select2/css/select2.min.css',
				'assets/js/plugins/select2/css/select2-bootstrap4.min.css'
			),
			'heading' 		=> array(
				'title' 		=> 'Pengguna',
				'subtitle' 		=> ''
			)
		);
		$form = array(
			'id'			=> 'form-pengguna',
			'title'			=> 'form tambah pengguna',
			'subtitle'		=> '',
			'method'		=> 'POST',
			'action'		=> base_url('pengaturan/pengguna/insert'),
			'redirect'		=> 'pengaturan/pengguna',
			'validation'	=> array(
				'check_username' => array(
					'url' 	=> base_url('pengaturan/pengguna/is-unique'),
					'field'	=> 'username'
				),
				'check_email' => array(
					'url' 	=> base_url('pengaturan/pengguna/is-unique'),
					'field'	=> 'email'
				),
				'check_nama' => array(
					'url' 	=> base_url('pengaturan/pengguna/is-unique'),
					'field'	=> 'nama'
				)
			)
		);
		$foot = array(
			'javascripts' => array(
				'assets/js/plugins/select2/js/select2.full.min.js',
				'assets/js/plugins/jquery-validation/jquery.validate.min.js',
				'assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js'
			),
			'scripts'	=> array(
				$this->load->view('pengaturan/pengguna/js/form', $form, TRUE)
			)
		);
		$this->html_head($head);
		$this->load->view('pengaturan/pengguna/html/form', $form);
		$this->html_foot($foot);
	}
	public function edit($pk){
		$single_data = $this->Pengguna_model->single($pk);
		$prev_data = isset($single_data['results']) ? $single_data['results'] : array();
		$head = array(
			'stylesheets'	=> array(
				'assets/js/plugins/select2/css/select2.min.css',
				'assets/js/plugins/select2/css/select2-bootstrap4.min.css'
			),
			'heading' 		=> array(
				'title' 		=> 'Pengguna',
				'subtitle' 		=> ''
			)
		);
		$form = array(
			'id'			=> 'form-pengguna',
			'title'			=> 'form ubah pengguna',
			'subtitle'		=> '',
			'method'		=> 'POST',
			'action'		=> base_url('pengaturan/pengguna/update'),
			'redirect'		=> 'pengaturan/pengguna',
			'validation'	=> array(
				'check_username' => array(
					'url' 	=> base_url('pengaturan/pengguna/is-unique'),
					'field'	=> 'username'
				),
				'check_email' => array(
					'url' 	=> base_url('pengaturan/pengguna/is-unique'),
					'field'	=> 'email'
				),
				'check_nama' => array(
					'url' 	=> base_url('pengaturan/pengguna/is-unique'),
					'field'	=> 'nama'
				)
			),
			'prev_data' => $prev_data
		);
		$foot = array(
			'javascripts' => array(
				'assets/js/plugins/select2/js/select2.full.min.js',
				'assets/js/plugins/jquery-validation/jquery.validate.min.js',
				'assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js'	
			),
			'scripts'	=> array(
				$this->load->view('pengaturan/pengguna/js/form', $form, TRUE)
			)
		);
		$this->html_head($head);
		$this->load->view('pengaturan/pengguna/html/form', $form);
		$this->html_foot($foot);
	}

	public function insert(){
		$response = $this->Pengguna_model->insert($this->input->post(NULL));
		$this->output
			->set_status_header($response['status_code'])
			->set_content_type('application/json')
			->set_output(json_encode($response));
		unset($response);
	}
	public function update(){
		$response = $this->Pengguna_model->update($this->input->post(NULL));
		$this->output
			->set_status_header($response['status_code'])
			->set_content_type('application/json')
			->set_output(json_encode($response));
		unset($response);
	}

	public function delete(){
		$response = $this->Pengguna_model->delete($this->input->post(NULL));
		$this->output
			->set_status_header($response['status_code'])
			->set_content_type('application/json')
			->set_output(json_encode($response));
		unset($response);
	}

	public function datatables(){
		$response = $this->Pengguna_model->datatables($this->input->post(NULL));
		$this->output
			->set_status_header($response['status_code'])
			->set_content_type('application/json')
			->set_output(json_encode($response));
		unset($response);
	}
	
	public function select2(){
		$response = $this->Pengguna_model->select2($this->input->post(NULL));
		$this->output
			->set_status_header($response['status_code'])
			->set_content_type('application/json')
			->set_output(json_encode($response));
		unset($response);
	}
	public function select2_cabang(){
		$response = $this->Pengguna_model->select2_cabang($this->input->post(NULL));
		$this->output
			->set_status_header(200)
			->set_content_type('application/json')
			->set_output(json_encode($response));
		unset($response);
	}
	public function select2_hak_akses(){
		$response = $this->Pengguna_model->select2_hak_akses($this->input->post(NULL));
		$this->output
			->set_status_header(200)
			->set_content_type('application/json')
			->set_output(json_encode($response));
		unset($response);
	}

	public function is_unique(){
		$filedname 		= $this->input->post('fieldname');
		$unique_term 	= $this->input->post('term');
		$except_pk 		= $this->input->post('pk');
		$response = $this->Pengguna_model->is_unique($filedname, $unique_term, $except_pk);
		$this->output
			->set_status_header(200)
			->set_content_type('application/json')
			->set_output(json_encode($response));
		unset($field, $term, $except_id, $response);
	}

}