<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Beranda extends GT_Controller {
	public function __construct(){
		parent::__construct();
		$this->auth 	= (object)$this->session->userdata('auth');
		$this->user 	= (object)$this->session->userdata('user');
		$this->cabang 	= (object)$this->session->userdata('cabang');
	}
	public function index(){
		$this->load->model('Dashboard_model');
		$cabang = $this->user->id_cabang;
		$data = array(
			'cards' 	=> $this->Dashboard_model->cards($cabang),
			'barang' 	=> $this->Dashboard_model->data_barang_habis($cabang),
			'pelanggan' => $this->Dashboard_model->top_pelanggan($cabang),
			// 'chart'     =>$this->Dashboard_model->chart($cabang)
		);
		$head = array(
			'stylesheets'		=> array(
				'assets/chart/css/morris.css',
				'assets/js/plugins/datatables/DataTables-1.10.21/css/dataTables.bootstrap4.min.css',
				'assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css'
			)
		);
		$foot = array(
			'javascripts'	=> array(
				// 'assets/chart/js/jquery.min.js',
				'assets/chart/js/raphael-min.js',
				'assets/chart/js/morris.min.js',
				'assets/js/plugins/datatables/DataTables-1.10.21/js/jquery.dataTables.min.js',
				'assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js',
				'assets/js/plugins/datatables/DataTables-1.10.21/js/dataTables.bootstrap4.min.js'
			),
			'scripts' 		=> array(
				$this->load->view('dashboard/javascript/dashboard1', $data, TRUE)
			)
		);
		$this->html_head($head);
		$this->load->view('dashboard/html/dashboard', $data);
		$this->html_foot($foot);
		
	}

	public function chart()
	{
		$this->load->model('Dashboard_model');
		$tgl =  $this->input->post('tgl');
		$cabang = $this->user->id_cabang;
		$data = $this->Dashboard_model->chart($cabang, $tgl);
		echo json_encode($data);
	}

}

