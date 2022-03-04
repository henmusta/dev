<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lap_pembelian_model extends CI_Model {

	public function __construct(){
		parent::__construct();
	}
	public function datatable($config = array()){		
		extract($config);
		$select_total 		= "SELECT COUNT(DISTINCT(p.`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS * ";
		$from 				= "
								FROM pembelian AS p  
		";
		$where 				= "WHERE p.`nomor` IS NOT NULL ";
		$group_by 			= "GROUP BY `p`.`tgl_buat`";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";

		if( isset($params["filter"]["id_cabang"]) && !empty($params["filter"]["id_cabang"]) ){
			$toko       = $this->db->escape_str(trim(strip_tags($params["filter"]["id_cabang"])));
			$where .= "AND  p.id_cabang =  ". $params["filter"]["id_cabang"] ." ";
		}

		if( isset($params["filter"]["date_start"]) && isset($params["filter"]["date_end"])){
			$start		= $this->db->escape_str(trim(strip_tags($params["filter"]["date_start"])));
			$end		= $this->db->escape_str(trim(strip_tags($params["filter"]["date_end"])));
			$where .= " AND (DATE(p.`tgl_buat`) BETWEEN DATE('". $start ."') AND DATE('". $end ."')) ";
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
	public function datatableretur($config = array()){
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
		$group_by 			= "GROUP BY `n`.`id` ";
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

		$select 			= "SELECT SQL_CALC_FOUND_ROWS
								rincian_pembayaran.*,
								pembelian.*, 
								pembelian.diskon as diskon,
								pembayaran.tgl_bayar,
								pembayaran.nomor,
								akun.nama AS nama_akun,
								pemasok.nama AS namap,
								CASE
								WHEN `rincian_pembayaran`.`metode` IS NOT NULL 
								THEN CONCAT(`rincian_pembayaran`.`metode`) 
								ELSE GROUP_CONCAT(
								`multi`.`metode`
								) 
								END AS metode_beli,
								giro.nomor AS nomor_giro ";
		$from 				= "
								FROM
								pembelian 
								LEFT JOIN pembayaran 
								ON pembayaran.`id_pembelian` = pembelian.`id` 
								LEFT JOIN rincian_pembayaran 
								ON rincian_pembayaran.`id_pembayaran` = pembayaran.`id` 
								LEFT JOIN (select * from rincian_pembayaran_multi ) as multi
								on multi.gabung_nota = pembayaran.gabung_nota
								LEFT JOIN akun 
								ON akun.`id` = rincian_pembayaran.`id_akun` 
								LEFT JOIN giro 
								ON giro.`id` = rincian_pembayaran.`id_giro` 
								LEFT JOIN pemasok 
								ON pemasok.id = pembelian.id_pemasok    
		";
		$where 				= " WHERE pembayaran.id is not null and (rincian_pembayaran.metode IN('tunai', 'debit') or multi.metode in ('tunai','debit') ) Group by pembayaran.id ";
		$total_cash         = "SELECT SUM(pembayaran.nominal) AS nominal FROM pembelian 
								LEFT JOIN pembayaran
								ON pembayaran.`id_pembelian` = pembelian.`id`
								LEFT JOIN rincian_pembayaran
								ON rincian_pembayaran.`id_pembayaran` = pembayaran.`id` ";
		$where_cash 		= "WHERE pembayaran.id is not null and (rincian_pembayaran.metode IN('tunai', 'debit') or multi.metode in ('tunai','debit') ) Group by pembayaran.id ";

		//giro
		$select1 			= "SELECT SQL_CALC_FOUND_ROWS
								rincian_pembayaran.*,
								pembelian.*, 
								pembelian.diskon as diskon,
								pembayaran.tgl_bayar as tgl,
								pembayaran.nomor,
								akun.nama AS nama_akun,
								pemasok.nama AS nama_pemasok,
								CASE
								WHEN `giro`.`nomor` IS NOT NULL 
								THEN CONCAT(`giro`.`nomor`) 
								ELSE GROUP_CONCAT(DISTINCT `multi`.`nomor`) 
								END AS nogiro, 
								CASE
								WHEN `rincian_pembayaran`.`tgl_giro` IS NOT NULL 
								THEN CONCAT(`rincian_pembayaran`.`tgl_giro`) 
								ELSE GROUP_CONCAT(DISTINCT `multi`.`tgl_giro`) 
								END AS tgl_giro ";
		$from1 				= "
								FROM
								pembelian 
								LEFT JOIN pembayaran 
								ON pembayaran.`id_pembelian` = pembelian.`id` 
								LEFT JOIN rincian_pembayaran 
								ON rincian_pembayaran.`id_pembayaran` = pembayaran.`id` 
								LEFT JOIN (select rincian_pembayaran_multi.*, giro.nomor as nomor  from rincian_pembayaran_multi inner join giro 
								on giro.id = rincian_pembayaran_multi.id_giro ) as multi
								on multi.gabung_nota = pembayaran.gabung_nota
								LEFT JOIN akun 
								ON akun.`id` = rincian_pembayaran.`id_akun` 
								LEFT JOIN giro 
								ON giro.`id` = rincian_pembayaran.`id_giro` 
								LEFT JOIN pemasok 
								ON pemasok.id = pembelian.id_pemasok        
		";
		$where1 			= "WHERE pembayaran.id is not null and (rincian_pembayaran.metode = 'giro' or multi.metode = 'giro') ";
		$total_giro         = "SELECT SUM(pembayaran.nominal) AS nominal FROM pembelian 
								LEFT JOIN pembayaran
								ON pembayaran.`id_pembelian` = pembelian.`id`
								LEFT JOIN rincian_pembayaran
								ON rincian_pembayaran.`id_pembayaran` = pembayaran.`id` ";
		$where_giro 		= "WHERE pembayaran.id is not null and (rincian_pembayaran.metode = 'giro' or multi.metode = 'giro') ";
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
	public function detailtunai($config = array()){		
		extract($config);
		$select_total 		= "SELECT COUNT(DISTINCT(pembelian.`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS
								rincian_pembayaran.*,
								pembelian.*, 
								pembelian.diskon as diskon,
								pembayaran.tgl_bayar,
								pembayaran.nomor,
								akun.nama AS nama_akun,
								pemasok.nama AS namap,
								CASE
								WHEN `rincian_pembayaran`.`metode` IS NOT NULL 
								THEN CONCAT(`rincian_pembayaran`.`metode`) 
								ELSE GROUP_CONCAT(
								`multi`.`metode`
								) 
						    	END AS metode_beli,
								giro.nomor AS nomor_giro ";
		$from 				= "
								FROM
								pembelian 
								LEFT JOIN pembayaran 
								ON pembayaran.`id_pembelian` = pembelian.`id` 
								LEFT JOIN rincian_pembayaran 
								ON rincian_pembayaran.`id_pembayaran` = pembayaran.`id` 
								LEFT JOIN (select rincian_pembayaran_multi.*, giro.nomor as nomor  from rincian_pembayaran_multi inner join giro 
								on giro.id = rincian_pembayaran_multi.id_giro ) as multi
								on multi.gabung_nota = pembayaran.gabung_nota
								LEFT JOIN akun 
								ON akun.`id` = rincian_pembayaran.`id_akun` 
								LEFT JOIN giro 
								ON giro.`id` = rincian_pembayaran.`id_giro` 
								LEFT JOIN pemasok 
								ON pemasok.id = pembelian.id_pemasok    
		";
		$where 				= "WHERE pembayaran.id is not null and (rincian_pembayaran.metode IN('tunai', 'debit') or multi.metode in ('tunai','debit') )";
		$group_by 			= "group by pembayaran.id ";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";


		if( isset($params["filter"]["id_cabang"]) && !empty($params["filter"]["id_cabang"]) ){
			$cabang       = $this->db->escape_str(trim(strip_tags($params["filter"]["id_cabang"])));
			$where .= "  AND  pembelian.id_cabang =  ". $cabang ." ";
		}

		if( isset($params["filter"]["date_start"]) && isset($params["filter"]["date_end"]) ){
			$start		= $this->db->escape_str(trim(strip_tags($params["filter"]["date_start"])));
			$end		= $this->db->escape_str(trim(strip_tags($params["filter"]["date_end"])));
			$where .= " AND (DATE(pembelian.`tgl_buat`) BETWEEN DATE('". $start ."') AND DATE('". $end ."'))  ";
		}

		if( isset($params["filter"]["id_pemasok"]) && !empty($params["filter"]["id_pemasok"]) ){
			$toko       = $this->db->escape_str(trim(strip_tags($params["filter"]["id_pemasok"])));
			$where .= "  AND  pembelian.id_pemasok =  ". $params["filter"]["id_pemasok"] ." ";
		}

		// if( isset($params["filter"]["toko"]) && !empty($params["filter"]["toko"]) ){
		// 	$toko       = $this->db->escape_str(trim(strip_tags($params["filter"]["toko"])));
		// 	$where .= "  AND  pembelian.id_cabang =  ". $params["filter"]["toko"] ." ";
		// }

		// if( isset($params["filter"]["id_barang"]) && !empty($params["filter"]["id_barang"]) ){
		// 	$toko       = $this->db->escape_str(trim(strip_tags($params["filter"]["id_barang"])));
		// 	$where .= "  AND  rincian_pembelian.id_produk =  ". $params["filter"]["id_produk"] ." ";
		// }

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

		$totalData 		= $this->db->query($select_total . $from . $where .  $group_by . ";")->row_array();
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
	public function detailgiro($config = array()){		
		extract($config);
		$select_total 		= "SELECT COUNT(DISTINCT(pembelian.`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS
								rincian_pembayaran.*,
								pembelian.*, 
								pembelian.diskon as diskon,
								pembayaran.tgl_bayar as tgl,
								pembayaran.nomor,
								akun.nama AS nama_akun,
								pemasok.nama AS nama_pemasok,
								CASE
								WHEN `giro`.`nomor` IS NOT NULL 
								THEN CONCAT(`giro`.`nomor`) 
								ELSE GROUP_CONCAT(DISTINCT `multi`.`nomor`) 
	  							END AS nogiro, 
								CASE
								WHEN `rincian_pembayaran`.`tgl_giro` IS NOT NULL 
								THEN CONCAT(`rincian_pembayaran`.`tgl_giro`) 
								ELSE GROUP_CONCAT(DISTINCT `multi`.`tgl_giro`) 
	  							END AS tgl_giro ";
		$from 				= "
								FROM
								pembelian 
								LEFT JOIN pembayaran 
								ON pembayaran.`id_pembelian` = pembelian.`id` 
								LEFT JOIN rincian_pembayaran 
								ON rincian_pembayaran.`id_pembayaran` = pembayaran.`id` 
								LEFT JOIN (select rincian_pembayaran_multi.*, giro.nomor as nomor  from rincian_pembayaran_multi inner join giro 
								on giro.id = rincian_pembayaran_multi.id_giro ) as multi
								on multi.gabung_nota = pembayaran.gabung_nota
								LEFT JOIN akun 
								ON akun.`id` = rincian_pembayaran.`id_akun` 
								LEFT JOIN giro 
								ON giro.`id` = rincian_pembayaran.`id_giro` 
								LEFT JOIN pemasok 
								ON pemasok.id = pembelian.id_pemasok  
		";
		$where 				= "WHERE pembayaran.id is not null and (rincian_pembayaran.metode = 'giro' or multi.metode = 'giro') ";
		$group_by 			= "group by pembayaran.id";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";

		if( isset($params["filter"]["tgl"])  && !empty($params["filter"]["tgl"])){
			$tgl		= $this->db->escape_str(trim(strip_tags($params["filter"]["tgl"])));
			$where .= " AND pembelian.`tgl_buat` = '". $tgl ."' ";
		}

		if( isset($params["filter"]["id_cabang"]) && !empty($params["filter"]["id_cabang"]) ){
			$cabang       = $this->db->escape_str(trim(strip_tags($params["filter"]["id_cabang"])));
			$where .= "  AND  pembelian.id_cabang =  ". $cabang ." ";
		}

		if( isset($params["filter"]["date_start"]) && isset($params["filter"]["date_end"]) ){
			$start		= $this->db->escape_str(trim(strip_tags($params["filter"]["date_start"])));
			$end		= $this->db->escape_str(trim(strip_tags($params["filter"]["date_end"])));
			$where .= " AND (DATE(pembelian.`tgl_buat`) BETWEEN DATE('". $start ."') AND DATE('". $end ."'))";
		}

		if( isset($params["filter"]["id_pemasok"]) && !empty($params["filter"]["id_pemasok"]) ){
			$toko       = $this->db->escape_str(trim(strip_tags($params["filter"]["id_pemasok"])));
			$where .= "  AND  pembelian.id_pemasok =  ". $params["filter"]["id_pemasok"] ." ";
		}

		// if( isset($params["filter"]["toko"]) && !empty($params["filter"]["toko"]) ){
		// 	$toko       = $this->db->escape_str(trim(strip_tags($params["filter"]["toko"])));
		// 	$where .= "  AND  pembelian.id_cabang =  ". $params["filter"]["toko"] ." ";
		// }

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
	public function detailbon($config = array()){		
		extract($config);
		$select_total 		= "SELECT COUNT(DISTINCT(pembelian.`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS * ";
		$from 				= "
		FROM pembelian 
		LEFT JOIN pemasok
		ON pemasok.id = pembelian.id_pemasok   
		";
		$where 				= "WHERE pembelian.sisa_tagihan > '0' ";
		$group_by 			= "";
		$having 			= "";
		$order_by 			= "";
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

		if( isset($params["filter"]["id_pemasok"]) && !empty($params["filter"]["id_pemasok"]) ){
			$toko       = $this->db->escape_str(trim(strip_tags($params["filter"]["id_pemasok"])));
			$where .= "  AND  pembelian.id_pemasok =  ". $params["filter"]["id_pemasok"] ." ";
		}
		
		// if( isset($params["filter"]["toko"]) && !empty($params["filter"]["toko"]) ){
		// 	$toko       = $this->db->escape_str(trim(strip_tags($params["filter"]["toko"])));
		// 	$where .= "  AND  pembelian.id_cabang =  ". $params["filter"]["toko"] ." ";
		// }

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
	public function select2_barang($config = array()){
		extract($config);
		$id_cabang = $params['id_cabang'];
		$row_per_page 	= isset($row_per_page) ? $row_per_page : 10;
		$select_total 	= "SELECT COUNT(id) AS `total` ";
		$select_data	= "SELECT *, `nama` AS `text` ";
		$from 			= "FROM `produk` ";
		$where 			= "WHERE id IS NOT NULL and id_cabang = '$id_cabang' ";
		$having 		= "";

		$term = isset($params['term']) ? $this->db->escape_str($params['term']) : (isset($params['q']) ? $this->db->escape_str($params['q']) : null);
		if(isset($term) && !empty($term)){
			$where .= " AND (id LIKE '%". $term ."%' OR `nama` LIKE '%". $term ."%') ";
		}

		if( isset($params["id_pemasok"]) && !empty($params["id_pemasok"]) ){
			$toko       = $this->db->escape_str(trim(strip_tags($params["id_pemasok"])));
			$where .= "  AND  produk.id_pemasok =  ". $params["id_pemasok"] ." ";
		}

		$order_by 		= "ORDER BY id ASC ";
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
	
	function get_excel_tunai($start_date,$end_date, $id_cabang)
    {   
		$start		= $this->db->escape_str(trim(strip_tags($start_date)));
		$end		= $this->db->escape_str(trim(strip_tags($end_date)));
    	$query = $this->db
		->select(' rincian_pembayaran.*,
				   pembelian.*, 
				   pembayaran.tgl_bayar,
				   pembayaran.nomor,
				   akun.nama AS nama_akun,
				   pemasok.nama AS namap,
				   giro.nomor AS nomor_giro ')
		->from('pembelian')
		->join('pembayaran','pembelian.id = pembayaran.id_pembelian ')
		->join('rincian_pembayaran','rincian_pembayaran.id_pembayaran = pembayaran.id','left')
		->join('akun','akun.id = rincian_pembayaran.id_akun','left')
		->join('giro','giro.id = rincian_pembayaran.id_giro','left')
		->join('pemasok','pemasok.id = pembelian.id_pemasok','left')
		->where('pembelian.tgl_buat BETWEEN "'. date('Y-m-d', strtotime($start)). '" and "'. date('Y-m-d', strtotime($end)).'"')
		->where('pembelian.id_cabang',$id_cabang)
		->where('rincian_pembayaran.metode !=', 'giro')->get();
		return $query;
    }

	function get_excel_giro($start_date,$end_date, $id_cabang)
    {   
		$start		= $this->db->escape_str(trim(strip_tags($start_date)));
		$end		= $this->db->escape_str(trim(strip_tags($end_date)));
    	$query = $this->db
		->select(' rincian_pembayaran.*,
		pembelian.*, 
		pembayaran.tgl_bayar,
		pembayaran.nomor,
		akun.nama AS nama_akun,
		pemasok.nama AS namap,
		giro.nomor AS nomor_giro ')
		->from('pembelian')
		->join('pembayaran','pembelian.id = pembayaran.id_pembelian ')
		->join('rincian_pembayaran','rincian_pembayaran.id_pembayaran = pembayaran.id','left')
		->join('akun','akun.id = rincian_pembayaran.id_akun','left')
		->join('giro','giro.id = rincian_pembayaran.id_giro','left')
		->join('pemasok','pemasok.id = pembelian.id_pemasok','left')
		->where('pembelian.tgl_nota BETWEEN "'. date('Y-m-d', strtotime($start)). '" and "'. date('Y-m-d', strtotime($end)).'"')
		->where('pembelian.id_cabang',$id_cabang)
		->where('rincian_pembayaran.metode =', 'giro')->get();
		return $query;
    }

	function get_excel_bon($start_date,$end_date, $id_cabang)
    {   
		$start		= $this->db->escape_str(trim(strip_tags($start_date)));
		$end		= $this->db->escape_str(trim(strip_tags($end_date)));
    	$query = $this->db
		->select(' *, pembelian.tgl_buat as tglbuat, pembelian.tgl_nota as tglnota ')
		->from('pembelian')
		->join('pembayaran','pembelian.id = pembayaran.id_pembelian ')
		->join('pemasok','pemasok.id = pembelian.id_pemasok','left')
		->where('pembelian.tgl_buat BETWEEN "'. date('Y-m-d', strtotime($start)). '" and "'. date('Y-m-d', strtotime($end)).'"')
		->where('pembelian.id_cabang',$id_cabang)
		->where('pembelian.sisa_tagihan >', '0')->get();
		return $query;
    }

	function get_excel_retur($start_date,$end_date, $id_cabang)
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