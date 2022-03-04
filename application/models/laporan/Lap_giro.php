<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lap_giro extends CI_Model {
	public function __construct(){
		parent::__construct();
	}

	public function datatable($config = array()){		
		extract($config);
		$columns 			= array('g', 'nomor', 'id');
		$columnssearch 			= array('g', 'nomor', 'id');
		$select_total 		= "SELECT COUNT(DISTINCT(`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "
		SELECT  SQL_CALC_FOUND_ROWS *,
		IFNULL(`giro`.`keterangan`, '') AS ket,
		  CONCAT(
			`prefiks`,
			LPAD(25 * FLOOR((`urut` - 0) / 25) + 1, 2, 0)
		  ) AS `awal`,
		  
		  
		  
		  CONCAT(
			`prefiks`,
			LPAD(25 * FLOOR((`urut` - 0) / 25) + 24, 2, 0)
		  ) AS `akhir`,
		
			CONCAT(
				`prefiks`,
				LPAD(25 * FLOOR((`urut`-0)/25) + 1,2,0),
				LPAD('',15,'-'),
				`prefiks`,
				LPAD(25 * FLOOR((`urut`-0)/25) + 24,2,0)
			) AS `g`
		";
		$from 				= "FROM `giro` ";
		$where 				= "WHERE `id` IS NOT NULL ";
		$group_by 			= "GROUP BY `id` ";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";

		if ( isset( $params["filter"]["id_cabang"] )  && !empty($params["filter"]["id_cabang"]) ) {
			$where .= "  AND  giro.id_cabang =  '". $params["filter"]["id_cabang"] ."' ";
		}

		if( isset($params["search"]["value"]) && !empty($params["search"]["value"]) ) {
			$q		= $this->db->escape_str(strip_tags($params["search"]["value"]));
			$fields = array();
			foreach( $columns AS $col ){
				array_push($fields, "(".$col." LIKE '%".$q."%')");
			}
			$having = " HAVING " . implode(" OR ", $fields) . " "; 
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

	public function single($config = array()){
        extract($config);
		$giro = (object)[];
		$giro->awal = $this->db->from('giro')->where(['nomor'=>$params['awal'], 'id_cabang' => $params["id_cabang"]])->get()->row();
		$giro->akhir = $this->db->from('giro')->where(['nomor'=>$params['akhir'], 'id_cabang' => $params["id_cabang"]])->get()->row();
		$giro->rincian_giro 	= $this->db
		->select('
				giro.id as id ,pemasok.nama AS nama, giro.nomor AS nomor,  pemasok.`nama` AS toko, rincian_pembayaran.total AS jumlah, rincian_pembayaran.`tgl_giro` AS tgl_giro, giro.`keterangan` AS keterangan
				')
				->from('giro')
				->join('rincian_pembayaran','giro.id=rincian_pembayaran.id_giro','left')
				->join('pembayaran','rincian_pembayaran.`id_pembayaran` = pembayaran.`id`','left')
				->join('pembelian','pembayaran.`id_pembelian` = pembelian.`id`','left')
				->join('pemasok','pemasok.id = pembelian.id_pemasok', 'left')
				->where('giro.id_cabang =', $params['id_cabang'])
				->where('giro.id >=', $giro->awal->id)
				->where('giro.id <=', $giro->akhir->id)->get()->result();
		return $giro;
	}

	public function get_excel($giroawal,$giroakhir,  $id_cabang){
		$giro = (object)[];
		$giro->awal = $this->db->from('giro')->where(['nomor'=>$giroawal, 'id_cabang' => $id_cabang])->get()->row();
		$giro->akhir = $this->db->from('giro')->where(['nomor'=>$giroakhir, 'id_cabang' => $id_cabang])->get()->row();
		$giro	= $this->db
				->select('
				giro.id as id ,pemasok.nama AS nama, giro.nomor AS nomor,  pemasok.`nama` AS toko, rincian_pembayaran.total AS jumlah, rincian_pembayaran.`tgl_giro` AS tgl_giro, giro.`keterangan` AS keterangan
				')
				->from('giro')
				->join('rincian_pembayaran','giro.id=rincian_pembayaran.id_giro','left')
				->join('pembayaran','rincian_pembayaran.`id_pembayaran` = pembayaran.`id`','left')
				->join('pembelian','pembayaran.`id_pembelian` = pembelian.`id`','left')
				->join('pemasok','pemasok.id = pembelian.id_pemasok', 'left')
				->where('giro.id_cabang =',$id_cabang)
				->where('giro.id >=', $giro->awal->id)
				->where('giro.id <=', $giro->akhir->id)->get();
		return $giro;
	}

	public function update_ket($params){
		extract($params);
		$result = array(
			'status'	=> 'error',
			'message'	=> 'Lengkapi form.',
			'keterangan'	=> ''
		);
		$pk = isset($pk) ? $pk : null;
		$data_is_valid = TRUE;
		if((isset($pk) && !empty($pk)) && 	(isset($produk['keterangan']) && !empty($produk['keterangan'])))
        {
			if( $data_is_valid == TRUE ){			
					if( $this->db->update('giro', $produk, array('id'=>$pk)) ){
						$kode_laba = $this->db->get_where('giro')->row();
							$result['status'] 	= TRUE;
							$result['message'] 	= 'data telah disimpan';
							$result['keterangan'] 	= isset($kode_laba->keterangan) ? $kode_laba->keterangan: '';
						}				
			}
		}
		unset($produk);
		return $result;
		unset($result);
	}

}