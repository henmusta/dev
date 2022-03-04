<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Giro_model extends CI_Model {
	private $module = [
		'name' 	=> 'Pencairan Giro',
		'url'	=> 'transaksi/giro',
	];
	public function __construct(){
		parent::__construct();
	}

	public function datatable($config = array()){
		
		extract($config);
		$columns 			= array('tgl_bayar','n.id');

		$select_total 		= "SELECT COUNT(DISTINCT(`n`.`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS `n`.*, SUM(`n`.`nominal`) AS `nominal`, n.tgl_bayar as tgl_bayar, rincian_pembayaran.tgl_giro as tgl_giro, giro.nomor as nomor_giro, rincian_pembayaran.chek as cek";
		$from 				= "
		FROM
            `pembayaran` AS `n` 
             LEFT JOIN rincian_pembayaran 
             ON n.id = rincian_pembayaran.id_pembayaran 
             LEFT JOIN giro
             ON giro.id = rincian_pembayaran.id_giro
		";
		$where 				= "WHERE `rincian_pembayaran`.`metode` = 'giro'";
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

	public function insert($params){
		$result = array(
			'status'	=> 'error',
			'message'	=> 'Lengkapi form.',
			'redirect'	=> $this->module['url']
		);

		$data_is_valid = TRUE;

		if($data_is_valid === TRUE){
			extract($params);
			$this->db->trans_begin();
			extract($tf_jakarta);

			/* Record Data penjualan */
			$this->db->query("
				INSERT INTO `transaksi` (`id_cabang`,`tipe`,`tgl_nota`,`biaya`) 
					VALUES ('".$id_cabang."','transaksi_jakarta','".$tgl_nota."',".$nominal.");
			");

			/* Get ID penjualan */
			$id_transaksi = (int)$this->db->insert_id();

			/* Build Data Reference */
			$ref = [
				'link' 		=> $this->module['url'] .'/single/' . $id_transaksi,
				'pk'		=> $id_transaksi,
				'table'		=> 'transaksi_jakarta'
			];
			/* Record Data Barang */

			$this->db->query("
			INSERT INTO `rincian_biaya_jakarta` (`id_transaksi`,tgl,mingguan, pln, pam, internettv, atk, telepon, peralatan, iuranrumah,plastik, tiket, kuli, dll, total) 
				VALUES ('".$id_transaksi."','".$tgl_nota."','".$rincian[0]['total']."','".$rincian[1]['total']."' ,'".$rincian[2]['total']."' ,'".$rincian[3]['total']."' ,'".$rincian[4]['total']."' ,'".$rincian[5]['total']."' ,'".$rincian[6]['total']."' ,'".$rincian[7]['total']."' ,'".$rincian[8]['total']."' ,'".$rincian[9]['total']."','".$rincian[10]['total']."','".$rincian[11]['total']."', '".$nominal."');
			");
		
			foreach( $rincian AS $index => $akun ){
				/* Record Data Barang */
				$this->db->query("
					INSERT INTO `rincian_transaksi` (`id_transaksi`,`id_akun`, kredit,`total`) 
						VALUES ('".$id_transaksi."','".$akun['id_akun']."','".$akun['total']."','".$akun['total']."');
				");
				// $this->db->query("
				// 	INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_link`, `ref_pk`, `ref_table`)
				// 	VALUES ('14','".$tgl_nota."', ". $nominal .", 0,'". $ref['link'] ."','". $ref['pk'] ."' ,'". $ref['table'] ."');
				// ");
				// $this->db->query("
				// 	INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_link`, `ref_pk`, `ref_table`)
				// 	VALUES (".$akun['id_akun'].",'".$tgl_nota."', 0 , ". $nominal .",'". $ref['link'] ."','". $ref['pk'] ."' ,'". $ref['table'] ."');
				// ");
			}

			if ($this->db->trans_status() === FALSE){
				$result['message'] 	= $this->db->error();
				$this->db->trans_rollback();
			} else {
				$this->db->trans_commit();
				$result['status'] 	= TRUE;
				$result['message'] 	= 'Data telah disimpan.';
				$result['pk'] 		= $id_transaksi;
			}
		}

		unset($penjualan);
		return $result;
		unset($result);
	}

}
