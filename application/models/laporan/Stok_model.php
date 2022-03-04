<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stok_model extends CI_Model {

	public function __construct(){
		parent::__construct();
	}


	public function report_data($params = array()){
		extract($params);

		$this->db
			->select("`b`.*, `j`.`nama_jenis`, `s`.`nama_satuan`")
			->from("`barang` AS `b`")
				->join("`jenis` AS `j`","`j`.`id_jenis`=`b`.`id_jenis`","left")
				->join("`satuan` AS `s`","`s`.`id_satuan`=`b`.`id_satuan`","left")
			->group_by("`b`.`id_barang` ")
			->order_by("`s`.`nama_satuan`","ASC")
			->order_by("`b`.`nama_barang`","ASC");

		return $this->db->get()->result();

	}

	public function datatable($config = array()){
		
		extract($config);

		$select_total 		= "SELECT COUNT(DISTINCT(`b`.`id_barang`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS `b`.*, `j`.`nama_jenis`, `s`.`nama_satuan` ";
		$from 				= "
		FROM `barang` AS `b`
			LEFT JOIN `jenis`  AS `j` ON `j`.`id_jenis`=`b`.`id_jenis`
			LEFT JOIN `satuan` AS `s` ON `s`.`id_satuan`=`b`.`id_satuan`
		";
		$where 				= "WHERE `b`.`id_barang` IS NOT NULL ";
		$group_by 			= "GROUP BY `b`.`id_barang` ";
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

}