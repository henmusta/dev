<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Auth extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->auth = (object)$this->session->userdata('auth');
		$this->load->model('Auth_model');
		$this->load->model('pengaturan/Aplikasi_model');
	}
	public function index(){
		if( isset($this->auth->status) && $this->auth->status  == 'logged_in' ){			
			redirect(base_url('backend/dashboard'));
		}
		$data = array(
			'aplikasi'=> $this->Aplikasi_model->get(),
			'form' => array(
				'id' 			=> 'form-login',
				
				'action' 		=> 'auth/sign-in',
				'validation' 	=> array(
					'user_is_exsist_url' => 'auth/user-is-exists'
				),
				'redirect'		=> 'dashboard'
			)
		);
		$this->load->view('auth/login',$data);
	}
	public function user_is_exists(){
		$username 	= trim($this->input->post('username'));
		$result 	= $this->Auth_model->user_is_exists($username);
		$this->output->set_output(
			((isset($result['status']) && $result['status'] == 'success') ? 'true' : 'false')
		);
		unset($username,$result);
	}	

	public function sign_in(){
		$current = date("Y-m-d");
		$this->db->set('chek','1');
		$this->db->where('tgl_giro',$current);
		$this->db->update('rincian_pembayaran');


		$current = date("Y-m-d");
		$this->db->set('chek','1');
		$this->db->where('tgl_giro',$current);
		$this->db->update('rincian_pembayaran_multi');

		$this->output
			->set_content_type('application/json')
			->set_output(
				json_encode($this->Auth_model->sign_in($this->input->post(NULL)))
			);
	}


	public function sign_out(){
		$this->session->unset_userdata(array('auth','user'));
		redirect(base_url('auth'));
	}
}