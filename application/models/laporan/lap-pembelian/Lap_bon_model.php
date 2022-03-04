<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lap_bon_model extends CI_Model {

	public function __construct(){
		parent::__construct();
	}

	public function datatable($config = array()){		
		extract($config);
		$select_total 		= "SELECT COUNT(DISTINCT(pembelian.`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT pemasok.id as id_pemasok, pemasok.nama as nama_pemasok, pemasok.kode as kode, pembelian.tgl_buat as tgl_buat, pembelian.nomor as nomor, pembelian.sisa_tagihan as sisa_tagihan ";
		$from 				= "
		FROM pemasok 
		LEFT JOIN pembelian
		ON pemasok.id = pembelian.id_pemasok   
		";
		$where 				= "WHERE pembelian.id is not null ";
		$group_by 			= "GROUP by pembelian.id ";
		$having 			= "";
		$order_by 			= "order by pemasok.nama asc";
		$limit 				= "";

		if( isset($params["filter"]["date_start"]) && isset($params["filter"]["date_end"]) ){
			$start		= $this->db->escape_str(trim(strip_tags($params["filter"]["date_start"])));
			$end		= $this->db->escape_str(trim(strip_tags($params["filter"]["date_end"])));
			$where .= " AND (DATE(pembelian.`tgl_buat`) BETWEEN DATE('". $start ."') AND DATE('". $end ."')) ";
		}
        
		if( isset($params["filter"]["id_cabang"]) && !empty($params["filter"]["id_cabang"]) ){
			$cabang       = $this->db->escape_str(trim(strip_tags($params["filter"]["id_cabang"])));
			$where .= "  AND  pembelian.id_cabang =  ". $cabang ." ";
		}

		if( isset($params["filter"]["cek_bon"]) && !empty($params["filter"]["cek_bon"]) ){
			$bon       = $this->db->escape_str(trim(strip_tags($params["filter"]["cek_bon"])));
			$where .= " AND pembelian.bon =  ". $bon ." ";
		}

		if( isset($params["filter"]["id_pemasok"]) && !empty($params["filter"]["id_pemasok"]) ){
			$toko       = $this->db->escape_str(trim(strip_tags($params["filter"]["id_pemasok"])));
			$where .= "  AND  pemasok.id =  ". $toko ." ";
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
		// if( isset($params['order'][0]['column']) ){
		// 	$field 	= $columns[$params["order"][0]["column"]];
		// 	$dir 	= strtoupper($this->db->escape_str($params["order"][0]["dir"]));
		// 	$order_by = " ORDER BY " . $field . " " . $dir . " "; 
		// 	unset($field,$dir);
		// }
		if ( isset( $params["start"] ) && $params["length"] != '-1' ) {
			$limit = "LIMIT " . $params["start"] . "," . $params["length"];
		}

		$totalData 		= $this->db->query($select_total . $from . $where . $order_by .";")->row_array();
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
    
	public function select2_toko($config = array()){
		extract($config);
		$id_cabang = $params['id_cabang'];
		$row_per_page 	= isset($row_per_page) ? $row_per_page : 10;
		$select_total 	= "SELECT COUNT(pemasok.id) AS `total` ";
		$select_data	= "SELECT *, pemasok.`nama` AS `text`, pemasok.id as id  ";
		$from 			= "FROM `pemasok` left join pembelian on pembelian.id_pemasok = pemasok.id ";
		$where 			= "WHERE pemasok.id IS NOT NULL and pembelian.id_cabang = '$id_cabang' group by pemasok.id ";
		$having 		= "";

		$term = isset($params['term']) ? $this->db->escape_str($params['term']) : (isset($params['q']) ? $this->db->escape_str($params['q']) : null);
		if(isset($term) && !empty($term)){
			$where .= " AND (pemasok.id LIKE '%". $term ."%' OR pemasok.`nama` LIKE '%". $term ."%') ";
		}

		$order_by 		= "ORDER BY pemasok.id ASC ";
		$result_total	= $this->db->query($select_total . $from . $where .  $having . ";");
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

	function get_excel($start_date,$end_date, $id_cabang, $id_pemasok, $cek_bon)
    {   
		$start		= $this->db->escape_str(trim(strip_tags($start_date)));
		$end		= $this->db->escape_str(trim(strip_tags($end_date)));
		
		if( isset($id_pemasok) && !empty($id_pemasok) ){
			$toko       = $this->db->escape_str(trim(strip_tags($id_pemasok)));
			$query = $this->db
			->select(' *, pembelian.tgl_buat as tgl_buat ')
			->from('pembelian')
			->join('pembayaran','pembelian.id = pembayaran.id_pembelian ')
			->join('pemasok','pemasok.id = pembelian.id_pemasok','left')
			->where('pembelian.tgl_buat BETWEEN "'. date('Y-m-d', strtotime($start)). '" and "'. date('Y-m-d', strtotime($end)).'"')
			->where('pembelian.id_cabang',$id_cabang)
			->where('pemasok.id',$toko)
			->where('pembelian.bon', $cek_bon)
			->group_by('pembelian.id')
			->order_by('pemasok.nama asc')->get();
			return array(
				"data"				=> $query 
			);

		}
		else{
			$query = $this->db
			->select(' *, pembelian.tgl_buat as tgl_buat ')
			->from('pembelian')
			->join('pembayaran','pembelian.id = pembayaran.id_pembelian ')
			->join('pemasok','pemasok.id = pembelian.id_pemasok','left')
			->where('pembelian.tgl_buat BETWEEN "'. date('Y-m-d', strtotime($start)). '" and "'. date('Y-m-d', strtotime($end)).'"')
			->where('pembelian.id_cabang',$id_cabang)
			->where('pembelian.bon', $cek_bon)
			->where('pembelian.bon', $cek_bon)
			->group_by('pembelian.id')
			->order_by('pemasok.nama asc')->get();
			return array(
				"data"				=> $query 
			);
		}   	
		
    }

}