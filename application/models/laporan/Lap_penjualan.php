<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lap_penjualan extends CI_Model {

	public function __construct(){
		parent::__construct();
	}
	public function datatable($config = array()){
		
		extract($config);
	
		$select_total 		= "SELECT COUNT(DISTINCT(p.`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS * ";
		$from 				= "
								FROM penjualan AS p  
		";
		$where 				= "WHERE p.`nomor` IS NOT NULL ";
		$group_by 			= "GROUP BY `p`.`tgl_nota` ";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";

		if( isset($params["filter"]["date_start"]) && isset($params["filter"]["date_end"]) ){
			$start		= $this->db->escape_str(trim(strip_tags($params["filter"]["date_start"])));
			$end		= $this->db->escape_str(trim(strip_tags($params["filter"]["date_end"])));
			$where .= " AND (DATE(p.`tgl_nota`) BETWEEN DATE('". $start ."') AND DATE('". $end ."')) ";
		}
		if( isset($params["filter"]["id_cabang"]) && !empty($params["filter"]["id_cabang"]) ){
			$toko       = $this->db->escape_str(trim(strip_tags($params["filter"]["id_cabang"])));
			$where .= "  AND  p.id_cabang =  ". $params["filter"]["id_cabang"] ." ";
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


	public function detailjual($config = array()){	
		extract($config);
		$columns 			= array(	'nama_p', 'tgl_nota', 'nomor', 'jumlah', 'diskon', 'chek', 'notaretur', 'laba', 'laba_retur', 'total', 'laba_akhir');
		$satuan = $params["filter"]["id_cabang"];
		$columns 			= array('penjualan.id', 'pelanggan.nama', 'penjualan.tgl_nota', 'penjualan.nomor');
		$select_total 		= "SELECT COUNT(DISTINCT(rincian_penjualan.id)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS *, 
		IFNULL(retur.laba,0) AS laba_retur,
		pelanggan.`alamat` AS alamat,
		pelanggan.nama AS nama_p,
		penjualan.`nomor` AS nomor,
		penjualan.`total_rincian` AS jumlah,
		penjualan.`diskon` AS diskon,
		penjualan.`chek` AS chek,
		(penjualan.laba_akhir) AS laba,
		(penjualan.laba_akhir - penjualan.diskon) AS laba_akhir,
		penjualan.`total_tagihan` AS total ";
		$from 				= "FROM rincian_penjualan
LEFT JOIN penjualan ON penjualan.id = rincian_penjualan.id_penjualan 
LEFT JOIN pelanggan ON pelanggan.id = penjualan.id_pelanggan
LEFT JOIN produk ON produk.id=rincian_penjualan.id_produk  
LEFT JOIN (SELECT SUM(produk.`laba` * rincian_retur_penjualan.qty) AS laba,
	penjualan.`id` AS id
	FROM penjualan
	LEFT JOIN retur_penjualan
	ON retur_penjualan.id_penjualan = penjualan.`id`
	LEFT JOIN rincian_retur_penjualan
	ON rincian_retur_penjualan.`id_retur_penjualan` = retur_penjualan.`id`
	LEFT JOIN produk
	ON produk.id = rincian_retur_penjualan.`id_produk`
	WHERE penjualan.`tgl_nota` = '".$params["filter"]["date_start"]."' 
	AND penjualan.id_cabang = '".$params["filter"]["id_cabang"]."' GROUP BY penjualan.`id`) AS retur ON retur.id = penjualan.id
 
		";
		$where 				= "where rincian_penjualan.id is not null ";
		$group_by 			= "GROUP BY penjualan.id ";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";

		// if( isset($params["filter"]["tgl"])  && !empty($params["filter"]["tgl"])){
		// 	$tgl		= $this->db->escape_str(trim(strip_tags($params["filter"]["tgl"])));
		// 	$where .= " AND penjualan.`tgl_nota` = '". $tgl ."' ";
		// }

		
		if( isset($params["filter"]["date_start"]) && isset($params["filter"]["date_end"]) ){
			$start		= $this->db->escape_str(trim(strip_tags($params["filter"]["date_start"])));
			$end		= $this->db->escape_str(trim(strip_tags($params["filter"]["date_end"])));
			$where .= " AND (DATE(penjualan.`tgl_nota`) BETWEEN DATE('". $start ."') AND DATE('". $end ."')) ";
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


	public function single_combine($pk){
		$penjualan = (object)[];
		$penjualan = $this->db->from('penjualan')->where(['tgl_nota'=>$pk])->get()->row();
		$penjualan->rincian_penjualan = $this->db->select('
		pelanggan.`alamat` AS alamat,
		pelanggan.nama AS nama_p,
		penjualan.`nomor` AS nomor,
		penjualan.`total_rincian` AS jumlah,
		penjualan.`diskon` AS potong,
		kode_laba.kode AS kode_laba, 
		kode_laba.laba  AS laba, 
		penjualan.`total_tagihan` AS total ')
			->from('rincian_penjualan')
            ->join('penjualan','penjualan.id = rincian_penjualan.id_penjualan', 'left')
			->join('pelanggan','pelanggan.id = penjualan.id_pelanggan', 'left')
			->join('produk','produk.id=rincian_penjualan.id_produk','left')
			->join('kode_laba','kode_laba.laba=produk.laba','left')
			->where('penjualan.tgl_nota', $pk)
			->group_by('penjualan.id')->get()->result();
		$penjualan->pelanggan = $this->db->from('pelanggan')->where(['id'=>$penjualan->id_pelanggan])->get()->row();

		return $penjualan;
	}

	function get_excel($start_date,$end_date, $id_cabang)
    {   
		$start		= $this->db->escape_str(trim(strip_tags($start_date)));
		$end		= $this->db->escape_str(trim(strip_tags($end_date)));
    	$query = $this->db->select(' penjualan.tgl_nota as tgl_nota, 
		pelanggan.alamat as alamat, 
		pelanggan.nama as nama, 
		penjualan.nomor as nomor, 
		penjualan.diskon as diskon, 
		penjualan.total_tagihan as total, 
		(penjualan.laba_akhir - penjualan.diskon) as laba ')
		->from('rincian_penjualan')
		->join('penjualan','penjualan.id = rincian_penjualan.id_penjualan ')
		->join('pelanggan','pelanggan.id = penjualan.id_pelanggan','left')
		->where('penjualan.tgl_nota BETWEEN "'. date('Y-m-d', strtotime($start)). '" and "'. date('Y-m-d', strtotime($end)).'"')
		->where('penjualan.id_cabang',$id_cabang)
		->group_by('penjualan.id')->get();
		return $query;
    }

	function get_excel_retur($start_date,$end_date, $id_cabang)
    {   
		$start		= $this->db->escape_str(trim(strip_tags($start_date)));
		$end		= $this->db->escape_str(trim(strip_tags($end_date)));
    	$query = $this->db->select('*, r.qty as qty_retur, p.nama as namap ')
		->from('retur_penjualan as n')
		->join('rincian_retur_penjualan as r','n.id = r.id_retur_penjualan','left')
		->join('produk as p','p.id = r.id_produk','left')
		->where('n.tgl_nota BETWEEN "'. date('Y-m-d', strtotime($start)). '" and "'. date('Y-m-d', strtotime($end)).'"')
		->where('n.id_cabang',$id_cabang)
		->group_by('n.id')->get();
		return $query;
    }

	public function datatableretur($config = array()){
		extract($config);
		$select_total 		= "SELECT COUNT(DISTINCT(n.`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS *, r.qty as qty_retur, p.nama as namap ";
		$from 				= "
		FROM `retur_penjualan` AS `n` 
			LEFT JOIN rincian_retur_penjualan AS r
			ON r.id_retur_penjualan = n.id 
			LEFT JOIN produk AS p
			ON p.id = r.id_produk 
		";
		$where 				= "WHERE `n`.`id` IS NOT NULL ";
		$group_by 			= "GROUP BY `n`.`id` ";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";

		$total_retur        = "SELECT SUM(total) AS nominal FROM rincian_retur_penjualan ";
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
		$totalFiltered 	= $this->db->query($select_filtered . ";")->row_array();
		$totalretur 	= $this->db->query($total_retur . $where_retur . ";")->result_array();
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
	public function getPerPelanggan($date_start, $date_end, $cabang_id, $customer_id = NULL)
	{
		$query = "
		SELECT SQL_CALC_FOUND_ROWS *, 
				IFNULL(retur.laba,0) AS laba_retur, 
				pelanggan.`alamat` AS alamat, 
				pelanggan.nama AS nama_p, 
				penjualan.`nomor` AS nomor, 
				penjualan.`total_rincian` AS jumlah, 
				penjualan.`diskon` AS diskon, 
				penjualan.`chek` AS chek, 
				(penjualan.laba_akhir) AS laba, 
				(penjualan.laba_akhir - penjualan.diskon) AS laba_akhir, 
				penjualan.`total_tagihan` AS total 
		FROM rincian_penjualan 
		LEFT JOIN penjualan ON penjualan.id = rincian_penjualan.id_penjualan 
		LEFT JOIN pelanggan ON pelanggan.id = penjualan.id_pelanggan 
		LEFT JOIN produk ON produk.id=rincian_penjualan.id_produk 
		LEFT JOIN (SELECT SUM(produk.`laba` * rincian_retur_penjualan.qty) AS laba, 
		penjualan.`id` AS id FROM penjualan 
		LEFT JOIN retur_penjualan ON retur_penjualan.id_penjualan = penjualan.`id` 
		LEFT JOIN rincian_retur_penjualan ON rincian_retur_penjualan.`id_retur_penjualan` = retur_penjualan.`id` 
		LEFT JOIN produk ON produk.id = rincian_retur_penjualan.`id_produk` 
		WHERE penjualan.`tgl_nota` = '".$date_start."' AND penjualan.id_cabang = '5' 
		GROUP BY penjualan.`id`) AS retur ON retur.id = penjualan.id 
		WHERE rincian_penjualan.id is not null AND (DATE(penjualan.`tgl_nota`) BETWEEN DATE('".$date_start."') AND DATE('".$date_end."')) 
		AND penjualan.id_cabang = '".$cabang_id."' 
		";
		if ($customer_id != NULL) {
			$query .= "AND penjualan.id_pelanggan = '".$customer_id."'";
		}
		$query .= "GROUP BY penjualan.id; ";
		return $this->db->query($query)->result();
	}
	

}