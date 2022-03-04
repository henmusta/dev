<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barang_masuk_model extends CI_Model {

	public function __construct(){
		parent::__construct();
	}
	public function datatable($config = array()){
		
		extract($config);

		$select_total 		= "SELECT COUNT(DISTINCT(`i`.`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS *, `u`.`nama` AS `nama_penerima` ";
		$from 				= "
		FROM `transaksi_barang_masuk` 		AS `i`
			LEFT JOIN `transaksi_masuk` 	AS `t` ON `t`.`id_transaksi`=`i`.`id_transaksi`
			LEFT JOIN `barang` 				AS `b` ON `b`.`id_barang`=`i`.`id_barang`
			LEFT JOIN `jenis`  				AS `j` ON `j`.`id_jenis`=`b`.`id_jenis`
			LEFT JOIN `satuan` 				AS `s` ON `s`.`id_satuan`=`b`.`id_satuan`			
			LEFT JOIN `pengguna`  			AS `u` ON `u`.`id_pengguna`=`t`.`id_pengguna`
			LEFT JOIN `pemasok` 			AS `p` ON `p`.`id_pemasok`=`t`.`id_pemasok`
		";
		$where 				= "WHERE `b`.`id_barang` IS NOT NULL ";
		$group_by 			= "";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";

		if( isset($params["filter"]["date_start"]) && isset($params["filter"]["date_end"]) ){
			$start		= $this->db->escape_str(trim(strip_tags($params["filter"]["date_start"])));
			$end		= $this->db->escape_str(trim(strip_tags($params["filter"]["date_end"])));
			$where .=" AND (DATE(`t`.`tanggal_transaksi`) BETWEEN DATE('". $start ."') AND DATE('". $end ."')) ";
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
/*		unset($select_filtered, $select_total, $select, $from, $where, $group_by, $having, $order_by, $limit);*/
		unset($row,$photo,$status,$contact);
		$data = array();
		foreach($results AS $row){
			array_push($data,$row);
		}
		return array(
			"draw" 				=> intval( isset($params['draw']) ? $params['draw'] : 1 ),
			"recordsTotal" 		=> intval( isset($totalData['total']) ? $totalData['total'] : 0 ),
			"recordsFiltered" 	=> intval( isset($totalFiltered['total']) ? $totalFiltered['total'] : 0 ),
			"data"				=> $data,
			"params"			=> $params,
			"query"				=> $select . $from . $where . $group_by . $having . $order_by . $limit
		); unset($results,$params,$totalData,$totalFiltered,$data);
	}

}