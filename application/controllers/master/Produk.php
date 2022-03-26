<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Produk extends GT_Controller
{

	private $module = [
		'name' 	=> 'Produk',
		'url'	=> 'master/produk',
	];
	public function __construct()
	{
		parent::__construct();
		$this->load->model('master/Produk_model');
	}
	public function index()
	{
		$this->module['action'] = $this->module['url'] . '/barcode_multiple';
		$data = array(
			'module' => $this->module
		);
		$head = array(
			'stylesheets'		=> array(
				'assets/js/plugins/datatables/DataTables-1.10.21/css/dataTables.bootstrap4.min.css',
				'assets/js/plugins/datatables/Select-1.3.1/css/select.bootstrap4.min.css',
				'assets/js/plugins/sweetalert2/sweetalert2.min.css'
			),
			'heading' => array('title' => $data['module']['name'])
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
		$this->load->view($this->module['url'] . '/html/datatable', $data);
		$this->html_foot($foot);
	}
	public function insert()
	{
		$this->module['action'] = $this->module['url'] . '/crud/insert';
		$data = array(
			'module' => $this->module,
		);
		$head = array(
			'stylesheets'		=> array(
				'assets/js/plugins/sweetalert2/sweetalert2.min.css',
				'assets/js/plugins/select2/css/select2.min.css',
				'assets/js/plugins/select2/css/select2-bootstrap4.min.css'
			),
			'heading' => array('title' => $data['module']['name'])
		);

		$foot = array(
			'javascripts'	=> array(
				'assets/js/plugins/select2/js/select2.full.min.js',
				'assets/js/plugins/jquery-validation/jquery.validate.min.js',
				'assets/js/plugins/sweetalert2/sweetalert2.min.js',
				'assets/js/plugins/auto-numeric/auto-numeric.min.js',
				'assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js'
			),
			'scripts' 		=> array(
				$this->load->view($this->module['url'] . '/javascript/form', $data, TRUE)
			)
		);
		$this->html_head($head);
		$this->load->view($this->module['url'] . '/html/form', $data);
		$this->html_foot($foot);
	}


	public function cek_status($id)
	{
		$data = $this->Produk_model->cek_status($id);
		echo json_encode($data);
	}

	public function update_active($id)
	{
		$this->db->set('status', '1');
		$this->db->where('id', $id);
		$this->db->update('produk');
	}

	public function update_non_active($id)
	{
		$this->db->set('status', '0');
		$this->db->where('id', $id);
		$this->db->update('produk');
	}

	public function update($pk = 0)
	{

		$this->module['action'] = $this->module['url'] . '/crud/update';
		$data = array(
			'module' 	=> $this->module,
			'data'		=> $this->Produk_model->single($pk)
		);
		$head = array(
			'stylesheets'		=> array(
				'assets/js/plugins/datatables/DataTables-1.10.21/css/dataTables.bootstrap4.min.css',
				'assets/js/plugins/sweetalert2/sweetalert2.min.css',
				'assets/js/plugins/select2/css/select2.min.css',
				'assets/js/plugins/select2/css/select2-bootstrap4.min.css',
				'assets/js/plugins/flatpickr/flatpickr.min.css'
			),
			'heading' => array('title' => $data['module']['name'])
		);

		$foot = array(
			'javascripts'	=> array(
				'assets/js/plugins/datatables/DataTables-1.10.21/js/jquery.dataTables.min.js',
				'assets/js/plugins/datatables/DataTables-1.10.21/js/dataTables.bootstrap4.min.js',
				'assets/js/plugins/select2/js/select2.full.min.js',
				'assets/js/plugins/jquery-validation/jquery.validate.min.js',
				'assets/js/plugins/sweetalert2/sweetalert2.min.js',
				'assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js',
				'assets/js/plugins/auto-numeric/auto-numeric.min.js',
				'assets/js/plugins/flatpickr/flatpickr.min.js'
			),
			'scripts' 		=> array(
				$this->load->view($this->module['url'] . '/javascript/form', $data, TRUE)
			)
		);
		$this->html_head($head);
		$this->load->view($this->module['url'] . '/html/form', $data);
		$this->html_foot($foot);
	}
	/* CRUD */
	public function crud($action = null)
	{

		$action = strtolower($action);

		$response = array(
			'status' 	=> 'error',
			'message'	=> 'No Action Parameter'
		);

		if (!empty($action) && in_array($action, array('insert', 'update', 'update-status', 'delete'))) {
			$params 	= $this->input->post(NULL, TRUE);
			$response 	= $this->Produk_model->{str_replace('-', '_', $action)}($params);
			$response['status'] = isset($response['status']) && is_bool($response['status']) && $response['status'] === TRUE ? 'success' : 'error';
		}

		$this->output
			->set_status_header(200)
			->set_content_type('Application/json')
			->set_output(json_encode($response));
	}
	/* API DATA */
	public function api_data($vendor_name = null)
	{

		$response = array(
			'status' 	=> 'error',
			'message'	=> 'No Action Parameter'
		);

		$vendor_name = strtolower(str_replace("-", "_", $vendor_name));
		$this->load->model('master/Produk_model');
		if (method_exists($this->Produk_model, $vendor_name)) {
			$params = $this->input->post(NULL, TRUE);

			switch ($vendor_name):
				case 'datatable':
					$config = array(
						'params' 	=> $params
					);
					break;
				case 'select2':
				case 'select2_pemasok':
					$config = array(
						'row_per_page' 	=> 10,
						'params' 		=> $params
					);
					break;
			endswitch;

			$response 	= $this->Produk_model->{$vendor_name}($config);
		}

		$this->output
			->set_status_header(200)
			->set_content_type('Application/json')
			->set_output(json_encode($response));
	}

	public function barcode($pk)
	{

		$data = array(
			'module' 	=> 'Generate Barcode',
			'data'		=> $this->Produk_model->single($pk)
		);

		$head = array(
			'stylesheets'		=> array(
				'assets/js/plugins/select2/css/select2-bootstrap4.min.css'
			),
			'heading' => array('title' => 'Generate Barcode')
		);

		$foot = array(
			'javascripts'	=> array(),
			'scripts' 		=> array(
				$this->load->view($this->module['url'] . '/javascript/barcode', $data, TRUE)
			)
		);
		$generator = new Picqer\Barcode\BarcodeGeneratorHTML();
		// echo $generator->getBarcode('081231723897', $generator::TYPE_CODE_128);
		$this->html_head($head);
		$this->load->view($this->module['url'] . '/html/barcode', $data);
		$this->html_foot($foot);
	}

	public function barcode_multiple()
	{

		$kode = $this->input->get('kode');
		// $kode = 'aaaaaaaaaaa';
		// print_r($kode);
		// die();
		$data = array(
			'module' 	=> 'Genrate Barcode',
			'barcode'		=> $this->Produk_model->barcode($kode),
			'kode' 	=>  $kode,
		);

		// print_r($data['kode']);
		// die();
		$head = array(
			'stylesheets'		=> array(
				'assets/js/plugins/select2/css/select2-bootstrap4.min.css'
			),
			'heading' => array('title' => 'Generate Barcode')
		);

		$foot = array(
			'javascripts'	=> array(),
			'scripts' 		=> array(
				$this->load->view($this->module['url'] . '/javascript/barcode_multiple', $data, TRUE)
			)
		);

		// $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
		// // echo $generator->getBarcode('081231723897', $generator::TYPE_CODE_128);
		$this->html_head($head);
		$this->load->view($this->module['url'] . '/html/barcode_multiple', $data);
		$this->html_foot($foot);
	}

	public function barcode_print()
	{
		$dompdf = new Dompdf\Dompdf;
		$id = $this->input->get('id');
		$tipe = $this->input->get('tipe');
		if ($tipe == 'single') {

			$data = array(
				'module' 	=> 'Generate Barcode Single',
				'barcode'		=> $this->Produk_model->single($id),
				'cabang' => $_SESSION['user']
			);

			$html = $this->load->view($this->module['url'] . '/html/barcode_print/single', $data, true);
		} else {
			$data = array(
				'module' 	=> 'Generate Barcode Multiple',
				'barcode'		=> $this->Produk_model->barcode($id),
				'cabang' => $_SESSION['user']
			);
			$html = $this->load->view($this->module['url'] . '/html/barcode_print/multiple', $data, true);
		}
		$dompdf->loadHtml($html);

		$dompdf->setPaper('A6', 'potrait');

		$dompdf->render();

		$dompdf->stream('barcode_produk', array('Attachment' => 0));
	}
}