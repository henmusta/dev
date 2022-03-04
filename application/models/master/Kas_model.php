<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kas_model extends CI_Model {
	private $module = [
		'name' 	=> 'kas',
		'url'	=> 'master/kas',
	];
	public function __construct(){
		parent::__construct();
	}
	private function is_unique_field($column_name, $value, $pk=NULL){
		$query = "SELECT COUNT(`kode_kas`) AS `total` FROM `kas` WHERE `". $column_name ."`='". $this->db->escape_str($value) ."' ";
		if(!empty($pk)){
			$query .= " AND `kode_kas`!='" . $pk ."'";
		}
		$result = $this->db->query($query)->row();
		return isset($result->total) && $result->total > 0 ? FALSE : TRUE;
	}
	public function single($pk){
		return $this->db ->from('kas')->where(array('kode_kas'=>$pk))->get()->row();
	}
	public function datatable($config = array()){
		
		extract($config);
		$columns 			= array('jenis_kas', 'kode_kas', 'nama_kas', 'telp_kas', 'email_kas', 'alamat_kas', 'kode_kas');

		$select_total 		= "SELECT COUNT(DISTINCT(`kode_kas`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS * ";
		$from 				= "FROM `kas` AS `b` ";
		$where 				= "WHERE `kode_kas` IS NOT NULL ";
		$group_by 			= "GROUP BY `kode_kas` ";
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

		$totalData 		= $this->db->query($select_total . $from . $where . ";")->row_array();
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
	public function select2($config = array()){
		extract($config);

		$row_per_page 	= isset($row_per_page) ? $row_per_page : 10;
		$select_total 	= "SELECT COUNT(DISTINCT(`kode_kas`)) AS `total` ";
		$select_data	= "SELECT * ";
		$from 			= "FROM `kas` ";
		$where 			= "WHERE `kode_kas` IS NOT NULL ";

		$term = isset($params['term']) ? $this->db->escape_str($params['term']) : (isset($params['q']) ? $this->db->escape_str($params['q']) : null);
		if(isset($term) && !empty($term)){
			$where .= " AND (`kode_kas` LIKE '%". $term ."%' OR `nama_kas` LIKE '%". $term ."%') ";
		}

		$group_by 		= "GROUP BY `kode_kas` ";
		$order_by 		= "ORDER BY `kode_kas` ASC ";
		$result_total	= $this->db->query($select_total . $from . $where . ";");
		$total_data 	= $result_total->row()->total;
		$total_page		= ceil((int)$total_data/$row_per_page);
		$page 			= isset($params['page']) ? (int)$params['page'] : 1;
		$offset 		= (($page - 1) * $row_per_page);
		$result_total->free_result();
		$data = $this->db->query($select_data . $from . $where . $group_by . $order_by ." LIMIT ". $row_per_page ." OFFSET ". $offset .";");
		return array( 
			'results' 		=> $data->result_array(),
			'pagination' 	=> array('more' => ($page < $total_page)) 
		);
		$data->free_result();
	}
	/* CRUD */
	public function insert($params){

		extract($params);

		$result = array(
			'status'	=> 'error',
			'message'	=> 'Lengkapi form.',
			'redirect'	=> $this->module['url']
		);

		$data_is_valid = TRUE;

		if( isset($kas['kode_kas']) && !empty($kas['kode_kas']) && isset($kas['nama_kas']) && !empty($kas['nama_kas']) ){
			
			if( isset($kas['kode_kas']) && !empty($kas['kode_kas']) ){
				$kas['kode_kas'] 	= trim(strip_tags($kas['kode_kas']));
				$data_is_valid 	= $this->is_unique_field('kode_kas', $kas['kode_kas']);
				if($data_is_valid == FALSE){
					$result['message'] 	= "Kode sudah digunakan.";
				} else {
					if( isset($kas['nama_kas']) && !empty($kas['nama_kas']) ){
						$kas['nama_kas'] 	= trim(strip_tags($kas['nama_kas']));
						$data_is_valid 	= $this->is_unique_field('nama_kas', $kas['nama_kas']);
						if($data_is_valid == FALSE){
							$result['message'] 	= "Nama sudah digunakan.";
						}
					}
				}
			}

			if( $data_is_valid == TRUE ){
				$result['message'] 		= "Data Gagal disimpan.";
				$kas['kode_induk'] 	= $this->kode_kantor_pusat;
				if( $this->db->insert('kas', $kas) ){
					//$insert_id = $this->db->insert_id();
					$result['status'] 	= TRUE;
					$result['message'] 	= 'Data telah disimpan.';
				}
			}

		}
		unset($kas);
		return $result;
		unset($result);
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

		if( isset($kas['kode_kas']) && !empty($kas['kode_kas']) && isset($kas['nama_kas']) && !empty($kas['nama_kas']) ){
			if( isset($kas['kode_kas']) && !empty($kas['kode_kas']) ){
				$kas['kode_kas'] 	= trim(strip_tags($kas['kode_kas']));
				$data_is_valid 	= $this->is_unique_field('kode_kas', $kas['kode_kas'], $pk);
				if($data_is_valid == FALSE){
					$result['message'] 	= "Kode sudah digunakan.";
				} else {
					if( isset($kas['nama_kas']) && !empty($kas['nama_kas']) ){
						$kas['nama_kas'] 	= trim(strip_tags($kas['nama_kas']));
						$data_is_valid 	= $this->is_unique_field('nama_kas', $kas['nama_kas'], $pk);
						if($data_is_valid == FALSE){
							$result['message'] 	= "Nama sudah digunakan.";
						}
					}
				}
			}

			if( $data_is_valid == TRUE ){
				if( is_array($kas) && count($kas) > 0 ){
					$result['message'] 	= "Data dagal disimpan.";
					if( $this->db->update('kas', $kas, array('kode_kas'=>$pk)) ){
						$result['status'] 	= TRUE;
						$result['message'] 	= 'Data telah disimpan.';
					}
				}

			}
		}
		unset($kas);
		return $result;
		unset($result);
	}
	public function delete($params = array()){

		extract($params);

		$result = array(
			'status'	=> 'error',
			'message'	=> 'Please complete data field requirements.'
		);

		if( isset($pk) ){
			$result['message'] 	= "Data couldnt delete.";
			$tables = array('kas');
			$this->db->where('kode_kas', $pk);
			$this->db->delete($tables);
			if( $this->db->affected_rows() > 0 ){
				$result = array(
					'status'	=> TRUE,
					'message'	=> 'Data has been deleted.'
				);
			}
			unset($data);			
		}
		return $result;
		unset($result);
	}
}