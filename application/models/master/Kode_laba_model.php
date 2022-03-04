<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kode_laba_model extends CI_Model {
	private $module = [
		'name' 	=> 'Kode Laba',
		'url'	=> 'master/kode-laba',
	];
	public function __construct(){
		parent::__construct();
	}
	private function is_unique_field($column_name, $value, $pk=NULL){
		$query = "SELECT COUNT(`id`) AS `total` FROM `kode_laba` WHERE `". $column_name ."`='". $this->db->escape_str($value) ."' ";
		if(!empty($pk)){
			$query .= " AND `id`!='" . $pk ."'";
		}
		$result = $this->db->query($query)->row();
		return isset($result->total) && $result->total > 0 ? FALSE : TRUE;
	}
	public function single($pk){
		return $this->db->get_where('kode_laba',array('id'=>$pk))->row();
	}
	public function datatable($config = array()){
		
		extract($config);
		$columns 			= array('kode', 'laba', 'id');

		$select_total 		= "SELECT COUNT(`id`) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS * ";
		$from 				= "FROM `kode_laba` ";
		$where 				= "WHERE `id` IS NOT NULL ";
		$group_by 			= "Group by laba ";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";

		if ( isset( $params["filter"]["id_cabang"] )  && !empty($params["filter"]["id_cabang"]) ) {
			$where .= "  AND  kode_laba.satuan =  '". $params["filter"]["id_cabang"] ."' ";
		}

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
		$select_total 	= "SELECT COUNT(`id`) AS `total` ";
		$select_data	= "SELECT *, `id` AS `id`, CONCAT(`id`,' || ',`nama`) AS `text` ";
		$from 			= "FROM `kode_laba` ";
		$where 			= "WHERE `id` IS NOT NULL ";
		$having 		= "";

		$term = isset($params['term']) ? $this->db->escape_str($params['term']) : (isset($params['q']) ? $this->db->escape_str($params['q']) : null);
		if(isset($term) && !empty($term)){
			$where .= " AND (`id` LIKE '%". $term ."%' OR `nama` LIKE '%". $term ."%') ";
		}

		$order_by 		= "ORDER BY `id` ASC ";
		$result_total	= $this->db->query($select_total . $from . $where . $having . ";");
		$total_data 	= $result_total->row()->total;
		$total_page		= ceil((int)$total_data/$row_per_page);
		$page 			= isset($params['page']) ? (int)$params['page'] : 1;
		$offset 		= (($page - 1) * $row_per_page);
		$result_total->free_result();
		$data = $this->db->query($select_data . $from . $where . $having . $order_by . "LIMIT ". $row_per_page ." OFFSET ". $offset .";");
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

		if( isset($kode_laba['kode']) && !empty($kode_laba['kode'])  ){
			
			if( isset($kode_laba['kode']) && !empty($kode_laba['kode']) ){
				$kode_laba['kode'] 	= trim(strip_tags($kode_laba['kode']));
				$data_is_valid 	= $this->is_unique_field('kode', $kode_laba['kode']);
				if($data_is_valid == FALSE){
					$result['message'] 	= "Kode sudah digunakan.";
				}
			}

			if( $data_is_valid == TRUE ){
				$this->db->query("
				INSERT INTO `kode_laba` (`kode`,`laba`,satuan,id_cabang) 
					VALUES ('".$kode_laba['kode']."','".$kode_laba['laba']."','".$kode_laba['satuan']."','".$kode_laba['id_cabang']. "');
				");

					$result['status'] 	= TRUE;
					$result['message'] 	= 'Data telah disimpan.';
				
			}

		}
		unset($kode_laba);
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

		if( isset($kode_laba['kode']) && !empty($kode_laba['kode']) ){
			if( isset($kode_laba['kode']) && !empty($kode_laba['kode']) ){
				$kode_laba['kode'] 	= trim(strip_tags($kode_laba['kode']));
				$data_is_valid 	= $this->is_unique_field('kode', $kode_laba['kode'], $pk);
				if($data_is_valid == FALSE){
					$result['message'] 	= "Kode sudah digunakan.";
				}
			}

			if( $data_is_valid == TRUE ){
				if( is_array($kode_laba) && count($kode_laba) > 0 ){
					$result['message'] 	= "Data dagal disimpan.";
					if( $this->db->update('kode_laba', $kode_laba, array('id'=>$pk)) ){
						$result['status'] 	= TRUE;
						$result['message'] 	= 'Data telah disimpan.';
					}
				}

			}
		}
		unset($kode_laba);
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
			$tables = array('kode_laba');
			$this->db->where('id', $pk);
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