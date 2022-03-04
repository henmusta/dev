<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Aplikasi_model extends CI_Model {
	private $module = [
		'name' 	=> 'Aplikasi',
		'url'	=> 'pengaturan/aplikasi',
	];
	public function __construct(){
		parent::__construct();
	}


	public function datatable($config = array()){
		extract($config);
		$columns 			= array('nama');
		$select_total 		= "SELECT COUNT(`id`) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS *";
		$from 				= "
		FROM `aplikasi` 
		";
		$where 				= "WHERE `aplikasi`.`id` IS NOT NULL 
		 ";
		$group_by 			= "group by aplikasi.id";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";

		if( isset($params["search"]["value"]) && !empty($params["search"]["value"]) ) {
			$q		= $this->db->escape_str(strip_tags($params["search"]["value"]));
			$fields = array();
			foreach( $columns AS $col ){
				array_push($fields, "(".$col." LIKE '%".$q."%')");
			}
			$having = " HAVING " . implode(" OR ",$fields) . " "; 
			unset($fields,$col,$q);
		}

		if( isset($params['order'][0]['column']) ){
			$field 	= $columns[$params["order"][0]["column"]];
			$dir 	= strtoupper($this->db->escape_str($params["order"][0]["dir"]));
			$order_by = " ORDER BY " . $field . " " . $dir . " "; 
			unset($field,$dir);
		}

		if ( isset( $params["start"] ) && $params["length"] != '-1' ) {
			$limit = "LIMIT " . $params["start"] . "," . $params["length"];
		}

		// if ( isset( $params["filter"]["id_cabang"] )  && !empty($params["filter"]["id_cabang"]) ) {
		// 	$where .= "  AND  produk.id_cabang =  ". $params["filter"]["id_cabang"] ." ";
		// }

		$totalData 		= $this->db->query($select_total . $from . $where .  ";")->row_array();
		$results 		= $this->db->query($select . $from . $where . $group_by . $having . $order_by . $limit . ";")->result_array();
		$totalFiltered 	= $this->db->query($select_filtered . ";")->row_array();
		unset($select_filtered, $select_total, $select, $from, $where, $group_by, $having, $order_by, $limit);
		unset($row,$photo,$status,$contact);
		$data = array();
		foreach($results AS $row){
			array_push($data,$row);
		}
		return array(
			"draw" 				=> intval( isset($params['draw']) ? $params['draw'] : 1 ),
			"recordsTotal" 		=> intval( isset($totalData['total']) ? $totalData['total'] : 0 ),
			"recordsFiltered" 	=> intval( isset($totalFiltered['total']) ? $totalFiltered['total'] : 0 ),
			"data"				=> $data 
		); unset($results,$params,$totalData,$totalFiltered,$data);
	}

    public function update($params){
		extract($params);

		$result = array(
			'status'	=> 'error',
			'message'	=> 'Lengkapi form.',
			'redirect'	=> $this->module['url']
		);

		$pk = isset($pk) ? $pk : null;
		$data_is_valid = TRUE;

        if( $data_is_valid == TRUE ){
            if(isset($pk)){
				$config['upload_path']   = './assets/media/photos/';
				$config['allowed_types'] = 'gif|jpg|jpeg|png'; 
				$config['max_size']      = '2097152';
				$config['max_width']     = '5000';
				$config['max_height']    = '2024';
				$config['file_name']     = $nama; 
				$this->upload->initialize($config);
				if($_FILES['imagefile']['size'] >  $config['max_size'] ){
				  echo json_encode(array(   'status'    => 'error',
					  'message'   => 'Ukuran Gambar Terlalu Besar, Masukan Gambar Dibawah 2MB'));
				}else{ 
				if($this->upload->do_upload('imagefile')){
					$gambar = $this->upload->data();
					$save  = array(
						'nama' => $nama,
						'gambar' => $gambar['file_name']
				);
					$g = (object)[];
					$g = $this->db->select('gambar')->from('aplikasi')->where(array('id'=>$pk))->get()->row();
					if ($g->gambar!= null) {
						//hapus gambar yg ada diserver
						unlink('assets/media/photos/'.$g->gambar);
					}
					}else{//Apabila tidak ada gambar yang di upload
						$save  = array('nama' => $nama
						);
					}
				    if( $this->db->update('aplikasi', $save, array('id'=>$pk)) ){
						$result['status'] 	= TRUE;
						$result['message'] 	= 'Data telah disimpan.';
					}
				}


                $result['message'] 	= "Data dagal disimpan.";
            
            }

        }
		unset($akun);
		return $result;
		unset($result);
	}
	public function single($pk){
		return $this->db ->from('aplikasi')->where(array('id'=>$pk))->get()->row();
	}
	public function get(){
		return $this->db ->from('aplikasi')->get()->row();
	}


}