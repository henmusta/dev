<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lap_retur_model extends CI_Model {

	public function __construct(){
		parent::__construct();
	}
	public function datatable($config = array()){
		extract($config);
		$select_total 		= "SELECT COUNT(DISTINCT(n.`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS *, r.qty as qty_retur, p.nama as namap ";
		$from 				= "
		FROM `retur_pembelian` AS `n` 
			LEFT JOIN `pemasok` AS `s` 
			ON `s`.`id`=`n`.`id_pemasok`
			LEFT JOIN rincian_retur_pembelian AS r 
			ON r.id_retur_pembelian = n.id
			LEFT JOIN produk AS p
			ON p.id = r.id_produk 
		";
		$where 				= "WHERE `n`.`id` IS NOT NULL ";
		$group_by 			= "GROUP BY `r`.`id` ";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";

		$total_retur        = "SELECT SUM(total) AS nominal FROM rincian_retur_pembelian ";
		$where_retur 		= "WHERE id != '0'";

		if( isset($params["filter"]["date_start"]) && isset($params["filter"]["date_end"]) ){
			$start		= $this->db->escape_str(trim(strip_tags($params["filter"]["date_start"])));
			$end		= $this->db->escape_str(trim(strip_tags($params["filter"]["date_end"])));
			$where .= " AND (DATE(n.`tgl_nota`) BETWEEN DATE('". $start ."') AND DATE('". $end ."')) ";
		}

		if( isset($params["filter"]["id_cabang"]) && !empty($params["filter"]["id_cabang"]) ){
			$toko       = $this->db->escape_str(trim(strip_tags($params["filter"]["id_cabang"])));
			$where .= "  AND  n.id_cabang =  ". $params["filter"]["id_cabang"] ." ";
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
		$totalretur 	= $this->db->query($total_retur . $where_retur . ";")->result_array();
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
			"data"				=> $data,
			"data1"				=> $totalretur
			
		); unset($results,$params,$totalData,$totalFiltered,$data);
	}
	public function printout($config = array()){
		
		extract($config);

		$select 			= "SELECT SQL_CALC_FOUND_ROWS *,
								pembayaran.tgl_bayar,
								pembayaran.nomor,
								pembelian.`id` AS id,
								akun.nama AS nama_akun,
								pemasok.nama AS toko,
								rincian_pembayaran.tgl_giro AS tgl_giro,
								giro.nomor AS nomor_giro ";
		$from 				= "
								FROM pembelian 
								LEFT JOIN pembayaran
								ON pembayaran.`id_pembelian` = pembelian.`id`
								LEFT JOIN rincian_pembayaran
								ON rincian_pembayaran.`id_pembayaran` = pembayaran.`id`
								LEFT JOIN akun
								ON akun.`id` = rincian_pembayaran.`id_akun`
								LEFT JOIN giro
								ON giro.`id` = rincian_pembayaran.`id_giro`
								LEFT JOIN pemasok
								ON pemasok.id = pembelian.id_pemasok    
		";
		$where 				= "WHERE rincian_pembayaran.metode != 'giro' ";
		$total_cash         = "SELECT SUM(rincian_pembayaran.nominal) AS nominal FROM pembelian 
								LEFT JOIN pembayaran
								ON pembayaran.`id_pembelian` = pembelian.`id`
								LEFT JOIN rincian_pembayaran
								ON rincian_pembayaran.`id_pembayaran` = pembayaran.`id` ";
		$where_cash 		= "WHERE rincian_pembayaran.metode != 'giro' ";

		//giro
		$select1 			= "SELECT SQL_CALC_FOUND_ROWS *,
								pembayaran.tgl_bayar,
								pembayaran.nomor,
								pembelian.`id` AS id,
								akun.nama AS nama_akun,
								pemasok.nama AS toko,
								rincian_pembayaran.tgl_giro AS tgl_giro,
								giro.nomor AS nomor_giro ";
		$from1 				= "
								FROM pembelian 
								LEFT JOIN pembayaran
								ON pembayaran.`id_pembelian` = pembelian.`id`
								LEFT JOIN rincian_pembayaran
								ON rincian_pembayaran.`id_pembayaran` = pembayaran.`id`
								LEFT JOIN akun
								ON akun.`id` = rincian_pembayaran.`id_akun`
								LEFT JOIN giro
								ON giro.`id` = rincian_pembayaran.`id_giro`
								LEFT JOIN pemasok
								ON pemasok.id = pembelian.id_pemasok    
		";
		$where1 			= "WHERE rincian_pembayaran.metode = 'giro' ";
		$total_giro         = "SELECT SUM(rincian_pembayaran.nominal) AS nominal FROM pembelian 
								LEFT JOIN pembayaran
								ON pembayaran.`id_pembelian` = pembelian.`id`
								LEFT JOIN rincian_pembayaran
								ON rincian_pembayaran.`id_pembayaran` = pembayaran.`id` ";
		$where_giro 		= "WHERE rincian_pembayaran.metode = 'giro' ";
		//bon
		$select2			= "SELECT SQL_CALC_FOUND_ROWS *,
								pemasok.nama AS toko ";
		$from2				= "
							    FROM pembelian 
							    LEFT JOIN pemasok
							    ON pemasok.id = pembelian.id_pemasok ";
		$where2				= "WHERE pembelian.sisa_tagihan != '0'";
		$total_bon          = "SELECT SUM(pembelian.sisa_tagihan) AS nominal FROM pembelian ";
		$where_bon 			= "WHERE pembelian.sisa_tagihan != '0'";

		if( isset($params["filter"]["date_start"]) && isset($params["filter"]["date_end"]) ){
			$start		= $this->db->escape_str(trim(strip_tags($params["filter"]["date_start"])));
			$end		= $this->db->escape_str(trim(strip_tags($params["filter"]["date_end"])));
			$where .= " AND (DATE(pembelian.`tgl_nota`) BETWEEN DATE('". $start ."') AND DATE('". $end ."')) ";
			$where_cash .= " AND (DATE(pembelian.`tgl_nota`) BETWEEN DATE('". $start ."') AND DATE('". $end ."')) ";
			$where1 .= " AND (DATE(pembelian.`tgl_nota`) BETWEEN DATE('". $start ."') AND DATE('". $end ."')) ";
			$where_giro .= " AND (DATE(pembelian.`tgl_nota`) BETWEEN DATE('". $start ."') AND DATE('". $end ."')) ";
			$where2 .= " AND (DATE(pembelian.`tgl_nota`) BETWEEN DATE('". $start ."') AND DATE('". $end ."')) ";
			$where_bon .= " AND (DATE(pembelian.`tgl_nota`) BETWEEN DATE('". $start ."') AND DATE('". $end ."')) ";
		}

		if( isset($params["filter"]["toko"]) && !empty($params["filter"]["toko"]) ){
			$toko       = $this->db->escape_str(trim(strip_tags($params["filter"]["toko"])));
			$where .= " AND pembelian.id_pemasok = '". $toko ."' ";
			$where_cash .= " AND pembelian.id_pemasok = '". $toko ."' ";
			$where1 .= " AND pembelian.id_pemasok = '". $toko ."' ";
			$where_giro .= " AND pembelian.id_pemasok = '". $toko ."' ";
			$where2 .= " AND pembelian.id_pemasok = '". $toko ."' ";
			$where_bon .= " AND pembelian.id_pemasok = '". $toko ."' ";
		}

		$results 		= $this->db->query($select . $from . $where . ";")->result_array();
		$totalcash 		= $this->db->query($total_cash . $where_cash . ";")->row();
		$results1 		= $this->db->query($select1 . $from1 . $where1 . ";")->result_array();
		$totalgiro 		= $this->db->query($total_giro . $where_giro . ";")->row();
		$results2       = $this->db->query($select2 . $from2 . $where2 . ";")->result_array();
		$totalbon 		= $this->db->query($total_bon . $where_bon . ";")->result_array();
		$totalall		=  $totalcash->nominal + $totalgiro->nominal;
		$data = array();
		foreach($results AS $row){
			array_push($data,$row);
		}

		

		$data1 = array();
		foreach($results1 AS $row){
			array_push($data1,$row);
		}

		

		$data2 = array();
		foreach($results2 AS $row){
			array_push($data2,$row);
		}
		return array(
			"draw" 				=> intval( isset($params['draw']) ? $params['draw'] : 1 ),
			"recordsTotal" 		=> intval( isset($totalData['total']) ? $totalData['total'] : 0 ),
			"recordsFiltered" 	=> intval( isset($totalFiltered['total']) ? $totalFiltered['total'] : 0 ),
			"data"				=> $data,
			"total_cash"		=> $totalcash,
			"data_giro"			=> $data1,
			"total_giro"		=> $totalgiro,
			"data_bon"			=> $data2,
			"total_bon"			=> $totalbon,
			"total_all"			=> $totalall

		); 
	}

	function get_excel($start_date,$end_date, $id_cabang)
    {   
		$start		= $this->db->escape_str(trim(strip_tags($start_date)));
		$end		= $this->db->escape_str(trim(strip_tags($end_date)));
    	$query = $this->db->select('*, r.qty as qty_retur, p.nama as namap ')
		->from('retur_pembelian as n')
		->join('rincian_retur_pembelian as r','n.id = r.id_retur_pembelian','left')
		->join('produk as p','p.id = r.id_produk','left')
		->where('n.tgl_nota BETWEEN "'. date('Y-m-d', strtotime($start)). '" and "'. date('Y-m-d', strtotime($end)).'"')
		->where('n.id_cabang',$id_cabang)
		->group_by('n.id')->get();
		return $query;
    }

}