<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produk_model extends CI_Model {
	private $module = [
		'name' 	=> 'Produk',
		'url'	=> 'master/produk',
	];
	public function __construct(){
		parent::__construct();
	}
	private function is_unique_field($column_name, $value, $pk=NULL){
		$query = "SELECT COUNT(`id`) AS `total` FROM `produk` WHERE `". $column_name ."`='". $this->db->escape_str($value) ."' ";
		if(!empty($pk)){
			$query .= " AND `id`!='" . $pk ."'";
		}
		$result = $this->db->query($query)->row();
		return isset($result->total) && $result->total > 0 ? FALSE : TRUE;
	}
	public function single($pk){
		$row = $this->db->get_where('produk',array('id'=>$pk))->row();
		if(isset($row->id) && !empty($row->id) ){
			$row->pemasok = $this->db->get_where('pemasok',array('id'=>$row->id_pemasok))->row();
			return $row;
		}
		return (object)[]; 
	}
	public function barcode($kode){
		// $id = array($kode);
		// print_r($kode);
		$barcode = (object)[];
		$barcode = $this->db->from('produk')->where_in('id', explode(',', $kode))->get()->result();
		return $barcode; 
	}
	public function datatable($config = array()){
		extract($config);
		$columns 			= array('produk.kode_produk','produk.id','`pemasok`.`kode`', '`produk`.`kode_produk`' ,'`produk`.`nama`', '`produk`.`harga_beli`', '`produk`.`harga_jual`', '`produk`.`status`', '`produk`.`id`');
		$select_total 		= "SELECT COUNT(`produk`.`id`) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS `produk`.*, `pemasok`.`kode` AS `kode_pemasok`, `pemasok`.`nama` AS `nama_pemasok` ";
		$from 				= "
		FROM `produk` 
			LEFT JOIN `pemasok` ON `pemasok`.`id`=`produk`.`id_pemasok`
		";
		$where 				= "WHERE `produk`.`id` IS NOT NULL 
		 ";
		$group_by 			= "group by produk.id";
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

		if ( isset( $params["filter"]["arsip"] )) {
			$where .= "  AND  produk.status =  '".$params["filter"]["arsip"]."' ";
		}

		if ( isset( $params["start"] ) && $params["length"] != '-1' ) {
			$limit = "LIMIT " . $params["start"] . "," . $params["length"];
		}

		if ( isset( $params["filter"]["id_cabang"] )  && !empty($params["filter"]["id_cabang"]) ) {
			$where .= "  AND  produk.id_cabang =  ". $params["filter"]["id_cabang"] ." ";
		}

	

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
	public function cek_status($id)
	{
		$query = $this->db->query('SELECT * FROM produk where id = '.$id.'');
		return $query->row();
	}
	public function select2($config = array()){
		extract($config);

		$row_per_page 	= isset($row_per_page) ? $row_per_page : 10;
		$select_total 	= "SELECT COUNT(`id`) AS `total` ";
		$select_data	= "SELECT *, `id` AS `id`, CONCAT(`id`,' || ',`nama`) AS `text` ";
		$from 			= "FROM `produk` ";
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
	public function select2_pemasok($config = array()){
		extract($config);

		$row_per_page 	= isset($row_per_page) ? $row_per_page : 10;
		$select_total 	= "SELECT COUNT(`id`) AS `total` ";
		$select_data	= "SELECT *, `nama` AS `text` ";
		$from 			= "FROM `pemasok` ";
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

		if( isset($produk['nama']) && !empty($produk['nama']) ){
			
			if( isset($produk['nama']) && !empty($produk['nama']) ){
				$produk['nama'] 	= trim(strip_tags($produk['nama']));
				$data_is_valid 	= $this->is_unique_field('nama', $produk['nama']);
				if($data_is_valid == FALSE){
					$result['message'] 	= "Nama sudah digunakan.";
				}
			}

			if( $data_is_valid == TRUE ){
				$result['message'] 		= "Data Gagal disimpan.";
				$max = $this->db->select('IF(ISNULL(max(id)), 1,  max(id) + 1) as max_id')->from('produk')->get()->row();
				$date = date('ym');
				$noUrutNext = $date . "-" . str_pad($max->max_id, 1, "0", STR_PAD_LEFT) . "-".$produk['id_pemasok']. "-" . $produk['id_cabang'];
				$produk['kode_produk'] = $noUrutNext;
				if( $this->db->insert('produk', $produk) ){
					$insert_id = $this->db->insert_id();
					$result['status'] 	= TRUE;
					$result['message'] 	= 'Data telah disimpan.';
				}
			}

		}
		unset($produk);
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

		if( isset($produk['nama']) && !empty($produk['nama']) ){
			if( isset($produk['nama']) && !empty($produk['nama']) ){
				// $produk['nama'] 	= trim(strip_tags($produk['nama']));
				// $data_is_valid 	= $this->is_unique_field('nama', $produk['nama'], $pk);
				if($data_is_valid == FALSE){
					$result['message'] 	= "Nama sudah digunakan.";
				}
			}

			if( $data_is_valid == TRUE ){
				if( is_array($produk) && count($produk) > 0 ){
					$result['message'] 	= "Data dagal disimpan.";
					if( $this->db->update('produk', $produk, array('id'=>$pk)) ){
						$result['status'] 	= TRUE;
						$result['message'] 	= 'Data telah disimpan.';
					}
				}

			}
		}
		unset($produk);
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
			$tables = array('produk');
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