<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lap_biaya_jakarta extends CI_Model {
	public function __construct(){
		parent::__construct();
	}
	public function datatable($config = array()){
		extract($config);
		$select_total 		= "SELECT COUNT(DISTINCT(transaksi.id)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT * ";
		$from 				= "
        FROM rincian_biaya_jakarta 
		LEFT JOIN transaksi 
		ON rincian_biaya_jakarta.`id_transaksi` = transaksi.`id`     
		";
		$where 				= "WHERE transaksi.`tipe` = 'Transaksi_jakarta' ";
		$group_by 			= "";
		$having 			= "";
		$order_by 			= "order by tgl DESC ";
		$limit 				= "";
		
		if( isset($params["filter"]["start_date"]) && isset($params["filter"]["end_date"]) ){
			$start		= $this->db->escape_str(trim(strip_tags($params["filter"]["start_date"])));
			$end		= $this->db->escape_str(trim(strip_tags($params["filter"]["end_date"])));
			$where .= " AND (DATE(rincian_biaya_jakarta.`tgl`) BETWEEN DATE('". $start ."') AND DATE('". $end ."'))";
		}


		if( isset($params["filter"]["id_cabang"]) && !empty($params["filter"]["id_cabang"]) ){
			$cabang       = $this->db->escape_str(trim(strip_tags($params["filter"]["id_cabang"])));
			$where .= "  AND  transaksi.id_cabang =  ". $cabang ." ";
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

	function get_excel($start_date, $end_date, $id_cabang)
    {   
		$start		= $this->db->escape_str(trim(strip_tags($start_date)));
		$end		= $this->db->escape_str(trim(strip_tags($end_date)));
    	$query = $this->db->select('*')
		->from('rincian_biaya_jakarta')
		->join('transaksi','transaksi.id = rincian_biaya_jakarta.id_transaksi','left')
		->where('rincian_biaya_jakarta.tgl BETWEEN "'. date('Y-m-d', strtotime($start)). '" and "'. date('Y-m-d', strtotime($end)).'"')
		->where('transaksi.id_cabang',$id_cabang)->get();
		return $query;
    }
}