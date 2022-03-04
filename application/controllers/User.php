<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends GT_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('User_model');
	}
	public function index(){
		$header = array(
			'stylesheets'	=> array(),
			'user'	=> $this->user,
		);
		$data = array(
			'user'	=> (array)$this->user,
			'form_update' => array(
				'id' 		=> 'form-update',
				'action'	=> 'user/update',
				'method'	=> 'POST'
			),
			'form_change_password' => array(
				'id' 		=> 'form-change-password',
				'action'	=> 'user/change-password',
				'method'	=> 'POST'
			),
			'validation' => array(
				'check-username' 		=> 'user/check-username',
				'check-email' 			=> 'user/check-email',
				'check-old-password' 	=> 'user/check-old-password'
			)
		);
		$footer = array(
			'javascripts' => array(
				'assets/js/plugins/jquery-validation/jquery.validate.min.js',
				'assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js'
			),
			'scripts'	=> array(
				$this->load->view('user/js/form', $data, TRUE)
			)
		);
		$this->html_head($header);
		$this->load->view('user/html/form', $data);
		$this->html_foot($footer);
	}
	public function check_username(){
		$username 	= $this->input->post('username');
		$this->output->set_output(
			($this->User_model->is_unique_username($username) ? 'true' : 'false')
		);
	}
	public function check_email(){
		$email 	= $this->input->post('email');
		$this->output->set_output(
			($this->User_model->is_unique_email($email) ? 'true' : 'false')
		);
	}
	public function check_old_password(){
		$password 	= $this->input->post('old_password');
		$this->output->set_output(
			(password_verify($this->db->escape_str($password), $this->user->password) ? 'true' : 'false')
		);
	}
	public function change_password(){
		$response = $this->User_model->change_password($this->input->post(NULL));
		$this->output->set_content_type('application/json')->set_output( json_encode( $response ) );
	}
	public function update(){
		$response = $this->User_model->update($this->input->post(NULL));
		$this->output->set_content_type('application/json')->set_output( json_encode( $response ) );
	}
}