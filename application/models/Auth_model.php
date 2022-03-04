<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {
	public function __construct(){
		parent::__construct();
	}
	public function sign_in($input){

		$result = array(
			'status'	=> 'error',
			'message'	=> 'Silahkan masukan username password anda',
			'redirect'	=> base_url('beranda')
		);

		$username 			= $this->db->escape_str($this->security->xss_clean($input['username']));
		$password 			= $this->db->escape_str($this->security->xss_clean($input['password']));

		if(!empty($username) && !empty($password) ){

			$username 	= $this->db->escape_str($this->security->xss_clean($username));
			$sql 		= "SELECT * FROM `pengguna` WHERE `username` = ? AND ( `hak_akses`='Super Admin' OR `id_cabang` IS NOT NULL );";
			$query 		= $this->db->query($sql, array($username));

			$result['message']	= "Akun tidak terdaftar atau cabang telah ditiadakan";
			if( $query->num_rows() > 0 ){
				$user = $query->row();
				$query->free_result();
				unset($query, $username);
				$result['message']	= 'Sorry, your account has been deactivated.';
				if($user->status == TRUE){
					$result['message']	= 'Your password doesn\'t match.';
					if(password_verify($password, $user->password)){
						unset($password, $user->password);
						$query = "SELECT * FROM `pengguna` WHERE `id_pengguna`='". $user->id_pengguna ."' LIMIT 1;";
						$user 	= $this->db->query($query)->row();
						$auth = array(
							'auth' => array(
								'status' 		=> 'logged_in',
								'role'			=> $user->hak_akses
							),
							'user' 				=> $user,
							'cabang'			=> $this->db->get_where('cabang',['id'=>$user->id_cabang])->row()
						);
						$this->session->set_userdata($auth);
						unset($auth);
						$result = array(
							'status'	=> 'success',
							'message'	=> 'Wellcome ' . $user->nama,
							'redirect'	=> base_url('beranda')
						);
					}
				}
			}
		}
		return $result;
		unset($result);
	}
	public function user_is_exists($username = ''){
		$result = array(
			'status'	=> 'error',
			'message'	=> 'User not found',
			'redirect'	=> ''
		);
		if(!empty($username)){
			$username 	= $this->db->escape_str($this->security->xss_clean($username));
			$sql 		= "SELECT `id_pengguna` FROM `pengguna` WHERE username = ?;";
			$query 		= $this->db->query($sql, array($username));
			$result['message']	= 'User doesn\'t exist';
			if($query->num_rows() > 0){
				$query->free_result();
				$result = array(
					'status'	=> 'success',
					'message'	=> '',
					'redirect'	=> ''
				);
			}
		}
		return $result;
		unset($username,$sql,$query,$result);
	}

}