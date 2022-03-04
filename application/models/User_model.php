<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class User_model extends CI_Model {
	public function __construct(){
		parent::__construct();
	}
	public function is_unique_username($name, $except_id = 0){
		$username 	= $this->db->escape_str(trim($name));
		$except_id 	= $this->db->escape_str($except_id);
		$query 		= "SELECT COUNT(`id_pengguna`) AS `total` FROM `pengguna` WHERE `username`='". $username ."' AND `id_pengguna`!='". $except_id ."';";
		$result 	= $this->db->query($query)->row();
		if( $result->total > 0 ){
			return FALSE;
		}
		return TRUE;
	}
	public function is_unique_email($name, $except_id = 0){
		$email 		= $this->db->escape_str(trim($name));
		$except_id 	= $this->db->escape_str($except_id);
		$query 		= "SELECT COUNT(`id_pengguna`) AS `total` FROM `pengguna` WHERE `email`='". $email ."' AND `id_pengguna`!='". $except_id ."';";
		$result 	= $this->db->query($query)->row();
		if( $result->total > 0 ){
			return FALSE;
		}
		return TRUE;
	}
	public function single($id = NULL) {
		$id 	= $this->db->escape_str($id);
		$user 	= $this->db->get_where('pengguna', array('id_pengguna' => $id), 1, 0)->row_array();
		return $user;
	}
	public function update($input){
		$result = array(
			'status'			=> 'error',
			'message'			=> 'Please complete the form field requirements.'
		);

		$id_pengguna 			= $this->user->id_pengguna;
		$user  				= $this->input->post('user');

		$user_valid = TRUE;
		if( isset($user['username']) && !empty($user['username']) ){
			$user['username'] = trim(strip_tags($user['username']));
			$user_valid 		= $this->is_unique_username($user['username'],$id_pengguna);
			if( $user_valid == FALSE ){
				$result['message'] 	= "Nama sudah digunakan.";
			}
		}
		if( isset($user['email']) && !empty($user['email']) ){
			$user['email'] = trim(strip_tags($user['email']));
			$user_valid 		= $this->is_unique_email($user['email'],$id_pengguna);
			if( $user_valid == FALSE ){
				$result['message'] 	= "Nama sudah digunakan.";
			}
		}
		if( $user_valid == TRUE ){
			if( is_array($user) && count($user) > 0 ){
				$result['message'] 	= "Data failed to save.";
				$this->db->where('id_pengguna', $id_pengguna);
				if( $this->db->update('pengguna', $user) ){	
					$this->session->set_userdata('user', (object)$this->single($id_pengguna));
					$result = array(
						'status'			=> 'success',
						'message'			=> 'Data has been save.'
					);
				}
			}

		}
		unset($user);
		return $result;
		unset($result);
	}
	public function change_password($input){

		$result = array(
			'status'	=> 'error',
			'message'	=> 'Please complete the form field requirements.',
			'redirect'	=> NULL
		);

		$id_pengguna 	= $this->db->escape_str($this->user->id_pengguna);
		$input['password']	= $this->db->escape_str(password_hash(trim($input['new_password']), PASSWORD_DEFAULT));
		if( is_array($input) && count($input) > 0 ){
			$result['message'] 	= "Data failed to save.";
			$this->db->where('id_pengguna', $id_pengguna);
			if( $this->db->update('pengguna', array('password' => $input['password'])) ){					
				$result = array(
					'status'			=> 'success',
					'message'			=> 'Password has been updated.'
				);
			}
		}
		return $result;
		unset($result);
	}
}