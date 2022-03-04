<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lap_kas extends CI_Model {
	public function __construct(){
		parent::__construct();
	}
	public function datatable($config = array()){
		extract($config);
		$select_total 		= "SELECT COUNT(DISTINCT(`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS * ";
		$from 				= "
         FROM transaksi
		";
		$where 				= "";
		$group_by 			= "GROUP BY MONTH(tgl_nota) ";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";

		if( isset($params["filter"]["tgl"]) && !empty($params["filter"]["tgl"])){
			$bulan = date("m", strtotime($params["filter"]["tgl"]));
			$tahun = date("Y", strtotime($params["filter"]["tgl"]));
			// $bulan		= $this->db->escape_str(trim(strip_tags($params["filter"]["tgl"])));
			// $tahun		= $this->db->escape_str(trim(strip_tags($params["filter"]["tgl"])));
			// $where .= " AND (DATE(`t`.`tanggal_transaksi`) BETWEEN DATE('". $start ."') AND DATE('". $end ."')) ";
			$where      .= "WHERE MONTH(tgl_nota) = '". $bulan ."' AND YEAR(tgl_nota) = '". $tahun ."' "; 
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


	public function single($tgl, $cb){
		$kas = (object)[];
		$kas = $this->db->from('transaksi')->where(['month(tgl_nota)'=>$tgl, 'id_cabang'=>$cb])->order_by('tgl_nota', 'DESC')
		->limit('1')->get()->row();
		// $kas->awal = $this->db->select('tgl_nota, rumus AS total')
		// ->from('transaksi')
		// ->where(['id_cabang'=>$cb, 'month(tgl_nota)'=>$tgl])
		
		$kas->rincian = $this->db->select('transaksi.`tgl_nota` as tanggal, akun.`nama` AS keterangan, rincian_transaksi.debit AS debit, rincian_transaksi.`kredit`, transaksi.modal as nominal , transaksi.`biaya` AS biaya')
			->from('transaksi')
			->join('rincian_transaksi','transaksi.`id` = rincian_transaksi.`id_transaksi`','left')
            ->join('akun','akun.`id` = rincian_transaksi.`id_akun`','left')
			->where('MONTH(transaksi.tgl_nota)', $tgl)
			->where('transaksi.id_cabang', $cb)
			->where_in('rincian_transaksi.tipe',['pendapatan','tunai','penjualan','biaya'])
			->order_by('transaksi.tgl_nota ASC')->get()->result();
		return $kas;
	}	



	
	public function detailkas($config = array()){		
		extract($config);
		$select_total 		= "SELECT COUNT(DISTINCT(transaksi.`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT transaksi.`tgl_nota` as tanggal, transaksi.rumus as rumus, transaksi.modal as modal, akun.`nama` AS keterangan, rincian_transaksi.debit AS debit, rincian_transaksi.`kredit`, transaksi.modal as nominal , transaksi.`biaya` AS biaya ";
		$from 				= "
		FROM
  `transaksi` 
  LEFT JOIN `rincian_transaksi` 
    ON transaksi.`id` = rincian_transaksi.`id_transaksi` 
  LEFT JOIN `akun` 
    ON akun.`id` = rincian_transaksi.`id_akun` 
		";
		$where 				= "WHERE `rincian_transaksi`.`tipe` IN (
			'pendapatan',
			'tunai',
			'penjualan',
			'biaya'
		  )  ";
		$group_by 			= "";
		$having 			= "";
		$order_by 			= "order by transaksi.tgl_nota asc ";
		$limit 				= "";

		if( isset($params["filter"]["date_start"]) && isset($params["filter"]["date_start"]) ){
			$start		= $this->db->escape_str(trim(strip_tags($params["filter"]["date_start"])));
			$end		= $this->db->escape_str(trim(strip_tags($params["filter"]["date_end"])));
			$where .= " AND (DATE(transaksi.`tgl_nota`) BETWEEN DATE('". $start ."') AND DATE('". $end ."')) ";
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

	public function get_excel($start_date, $end_date, $id_cabang){
		$month		= date("m", strtotime($start_date));
		$kas = (object)[];
		$kas = $this->db->from('transaksi')->where(['tgl_nota'=>$start_date, 'id_cabang'=>$id_cabang])->order_by('tgl_nota', 'DESC')
		->limit('1')->get()->row();
		$kas->rincian = $this->db->select('transaksi.`tgl_nota` as tanggal, akun.`nama` AS keterangan, rincian_transaksi.debit AS debit, rincian_transaksi.`kredit`, transaksi.modal as nominal , transaksi.`biaya` AS biaya, transaksi.rumus as rumus')
			->from('transaksi')
			->join('rincian_transaksi','transaksi.`id` = rincian_transaksi.`id_transaksi`','left')
            ->join('akun','akun.`id` = rincian_transaksi.`id_akun`','left')
			->where('transaksi.tgl_nota BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"')
			->where('transaksi.id_cabang', $id_cabang)
			->where_in('rincian_transaksi.tipe',['pendapatan','tunai','penjualan','biaya'])
			->order_by('transaksi.tgl_nota ASC')->get();
			return array(
				"data"				=> $kas->rincian,
				"kas_awal"			=>  $kas
			);
	}	

}