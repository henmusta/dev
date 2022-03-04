<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Giro_beli_model extends CI_Model {
	private $module = [
		'name' 	=> 'Pencairan Giro',
		'url'	=> 'penjualan/giro',
	];
	public function __construct(){
		parent::__construct();
	}

	public function datatable($config = array()){
		extract($config);
		$columns 			= array('jurnal.id','CASE
		WHEN `pembelian`.`tgl_bayar` IS NOT NULL 
		THEN `pembelian`.`tgl_bayar` 
		ELSE `multi`.`tgl_bayar` 
	  END','CASE
	  WHEN `pembelian`.`tgl_giro` IS NOT NULL 
	  THEN `pembelian`.`tgl_giro` 
	  ELSE `multi`.`tgl_giro` 
      END','CASE
		WHEN `pembelian`.`nomor` IS NOT NULL 
		THEN `pembelian`.`nomor` 
		ELSE `multi`.`nomor` 
	  END', 'cek', ' CASE
		WHEN `pembelian`.`total` IS NOT NULL 
		THEN `pembelian`.`total` 
		ELSE `multi`.`total` 
	  END', 'cek');


	  $columnsearch			= array('jurnal.id','nogiro','id_bayar','tgl_bayar', 'tgl_giro', 'cek', 'total_bayar');


		$select_total 		= "SELECT COUNT(DISTINCT(`jurnal`.`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS CASE
		WHEN `pembelian`.`nomor` IS NOT NULL 
		THEN CONCAT(`pembelian`.`nomor`) 
		ELSE CONCAT(`multi`.`nomor`) 
	  END AS nogiro ,
	  CASE
		WHEN `pembelian`.`id_bayar` IS NOT NULL 
		THEN CONCAT(`pembelian`.`id_bayar`) 
		ELSE CONCAT(`multi`.`id_bayar`) 
	  END AS id_bayar, 
	   CASE
		WHEN `pembelian`.`tgl_bayar` IS NOT NULL 
		THEN CONCAT(`pembelian`.`tgl_bayar`) 
		ELSE CONCAT(`multi`.`tgl_bayar`) 
	  END AS tgl_bayar, 
	  CASE
		WHEN `pembelian`.`tgl_giro` IS NOT NULL 
		THEN CONCAT(`pembelian`.`tgl_giro`) 
		ELSE CONCAT(`multi`.`tgl_giro`) 
	  END AS tgl_giro, 
	  CASE
		WHEN `pembelian`.`chek` IS NOT NULL 
		THEN CONCAT(`pembelian`.`chek`) 
		ELSE CONCAT(`multi`.`chek`) 
	  END AS cek, 
	  CASE
		WHEN `pembelian`.`total` IS NOT NULL 
		THEN CONCAT(`pembelian`.`total`) 
		ELSE CONCAT(`multi`.`total`) 
	  END AS total_bayar  ";
		$from 				= "
		FROM
  jurnal
  LEFT JOIN akun 
    ON akun.`id` = jurnal.`id_akun`  
  LEFT JOIN 
    (SELECT 
      jurnal.*,
      giro.nomor AS nomor,
      pemasok.nama AS nama,
      rincian_pembayaran_multi.chek AS chek, 
       rincian_pembayaran_multi.tgl_giro AS tgl_giro,
       jurnal.debit AS total,  
	   rincian_pembayaran_multi.id AS id_bayar,  
      rincian_pembayaran_multi.tgl_bayar AS tgl_bayar  
    FROM
      jurnal 
      INNER JOIN rincian_pembayaran_multi 
        ON rincian_pembayaran_multi.`id` = jurnal.`ref_pk` 
      LEFT JOIN pemasok 
        ON pemasok.`id` = rincian_pembayaran_multi.`id_pemasok` 
      LEFT JOIN giro 
        ON giro.`id` = rincian_pembayaran_multi.`id_giro` 
    GROUP BY jurnal.`id`) AS multi 
    ON multi.ref_pk = jurnal.`ref_pk` 
  LEFT JOIN 
    (SELECT 
      jurnal.*,
      giro.nomor AS nomor,
      pemasok.nama AS nama,
      rincian_pembayaran.tgl_giro AS tgl_giro, 
      rincian_pembayaran.total AS total, 
	  rincian_pembayaran.id AS id_bayar,  
      rincian_pembayaran.chek AS chek, 
      pembayaran.tgl_bayar AS tgl_bayar  
    FROM
      jurnal 
      INNER JOIN pembelian 
        ON pembelian.`id` = jurnal.`ref_pk` 
      LEFT JOIN pembayaran 
        ON pembayaran.`id_pembelian` = pembelian.`id` 
      LEFT JOIN rincian_pembayaran 
        ON rincian_pembayaran.`id_pembayaran` = pembayaran.`id` 
      LEFT JOIN pemasok 
        ON pembelian.`id_pemasok` = pemasok.`id` 
      LEFT JOIN giro 
        ON giro.`id` = rincian_pembayaran.`id_giro` 
    GROUP BY rincian_pembayaran.`id`) AS pembelian 
    ON pembelian.`ref_pk` = jurnal.`ref_pk` 
		";
		$where 				= "WHERE akun.`induk` IN (1, 2) and `jurnal`.`metode` = 'giro' ";
		$group_by 			= "GROUP BY `jurnal`.`id` ";
		$having 			= "";
		$order_by 			= "order by CASE
		WHEN `pembelian`.`tgl_bayar` IS NOT NULL 
		THEN `pembelian`.`tgl_bayar` 
		ELSE `multi`.`tgl_bayar END DESC ";
		$limit 				= "";

		if( isset($params['filter']['tgl_nota']) && !empty($params['filter']['tgl_nota']) ){
			$value	= $this->db->escape_str(strip_tags($params['filter']['tgl_nota']));
			$where .= " AND `jurnal`.`tgl`='" . $value ."' ";
		}

		if( isset($params["filter"]["id_cabang"]) && !empty($params["filter"]["id_cabang"]) ){
			$toko       = $this->db->escape_str(trim(strip_tags($params["filter"]["id_cabang"])));
			$where .= "  AND  jurnal.id_cabang =  ". $params["filter"]["id_cabang"] ." ";
		}

		if( isset($params["search"]["value"]) && !empty($params["search"]["value"]) ) {
			$q		= $this->db->escape_str(strip_tags($params["search"]["value"]));
			$fields = array();
			foreach( $columnsearch AS $col ){
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
		$query = $this->db->query('SELECT 
		CASE
		  WHEN `pembelian`.`chek` IS NOT NULL 
		  THEN CONCAT(`pembelian`.`chek`) 
		  ELSE CONCAT(`multi`.`chek`) 
		END AS cek,
		CASE
		  WHEN `pembelian`.`id_beli` IS NOT NULL 
		  THEN CONCAT(`pembelian`.`id_beli`) 
		  ELSE CONCAT(`multi`.`chek`) 
		END AS id  
	  FROM
		jurnal 
		LEFT JOIN akun 
		  ON akun.`id` = jurnal.`id_akun` 
		LEFT JOIN 
		  (SELECT 
			jurnal.*,
			rincian_pembayaran_multi.id AS id_beli,
			rincian_pembayaran_multi.chek AS chek 
		  FROM
			jurnal 
			INNER JOIN rincian_pembayaran_multi 
			  ON rincian_pembayaran_multi.`gabung_nota` = jurnal.`ref_text` 
		  GROUP BY jurnal.`id`) AS multi 
		  ON multi.ref_text = jurnal.`ref_text` 
		LEFT JOIN 
		  (SELECT 
			jurnal.*,
			rincian_pembayaran.id AS id_beli, 
			rincian_pembayaran.chek AS chek 
		  FROM
			jurnal 
			INNER JOIN pembelian 
			  ON pembelian.`id` = jurnal.`ref_pk` 
			LEFT JOIN pembayaran 
			  ON pembayaran.`id_pembelian` = pembelian.`id` 
			LEFT JOIN rincian_pembayaran 
			  ON rincian_pembayaran.`id_pembayaran` = pembayaran.`id` 
		  GROUP BY rincian_pembayaran.`id`) AS pembelian 
		  ON pembelian.`ref_pk` = jurnal.`ref_pk` 
	  WHERE akun.`induk` IN (1, 2) AND (multi.id_beli = '.$id.' OR pembelian.id_beli = '.$id.') 
	  GROUP BY jurnal.`id` ');
		return $query->row();
	}
}
