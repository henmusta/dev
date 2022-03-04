<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Giro_jual_model extends CI_Model {
	private $module = [
		'name' 	=> 'Pencairan Giro',
		'url'	=> 'penjualan/giro',
	];
	public function __construct(){
		parent::__construct();
	}

	public function datatable($config = array()){
		
		extract($config);
		$columns 			= array('tgl_bayar','n.id');

		$select_total 		= "SELECT COUNT(DISTINCT(`n`.`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS `n`.*, SUM(`n`.`nominal`) AS `nominal`, n.tgl_bayar as tgl_bayar, rincian_pelunasan.tgl_giro as tgl_giro, giro.nomor as nomor_giro, rincian_pelunasan.chek as cek";
		$from 				= "
		FROM
            `pelunasan` AS `n` 
			 left join penjualan
			 on penjualan.id = n.id_penjualan 
             LEFT JOIN rincian_pelunasan 
             ON n.id = rincian_pelunasan.id_pelunasan 
             LEFT JOIN giro
             ON giro.id = rincian_pelunasan.id_giro
		";
		$where 				= "WHERE `rincian_pelunasan`.`metode` = 'giro'";
		$group_by 			= "GROUP BY `n`.`id` ";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";

		if( isset($params['filter']['tgl_nota']) && !empty($params['filter']['tgl_nota']) ){
			$value	= $this->db->escape_str(strip_tags($params['filter']['tgl_nota']));
			$where .= " AND `n`.`tgl_bayar`='" . $value ."' ";
		}

		
		if( isset($params["filter"]["id_cabang"]) && !empty($params["filter"]["id_cabang"]) ){
			$toko       = $this->db->escape_str(trim(strip_tags($params["filter"]["id_cabang"])));
			$where .= "  AND  penjualan.id_cabang =  ". $params["filter"]["id_cabang"] ." ";
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

    public function cek_giro($id)
	{
		$query = $this->db->from('rincian_pelunasan')
		                  ->where('id',$id)->get();
		return $query->row();
	}

}
