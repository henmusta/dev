<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ro_model extends CI_Model {
	private $module = [
		'name' 	=> 'Pembelian',
		'url'	=> 'pembelian/ro',
	];
	public function __construct(){
		parent::__construct();
	}


	public function datatable($config = array()){		
		extract($config);
	    $columns 			= array('n.id','n.tgl_buat','tgl_nota','nama_pemasok', 'nomor', 'status_ro', 'id');
		$select_total 		= "SELECT COUNT(DISTINCT(`n`.`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS `n`.*, DATE(`n`.`tgl_buat`) AS `tgl_buat`, `s`.`nama` AS `nama_pemasok` ";
		$from 				= "
		FROM `pembelian` AS `n` 
			LEFT JOIN `pemasok` AS `s` ON `s`.`id`=`n`.`id_pemasok`
		";
		$where 				= "WHERE `n`.`id` IS NOT NULL  ";
		$group_by 			= "GROUP BY `n`.`id` ";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";

		if( isset($params['filter']['tgl_buat']) && !empty($params['filter']['tgl_buat']) ){
			$value	= $this->db->escape_str(strip_tags($params['filter']['tgl_buat']));
			$where .= " AND DATE(`n`.`tgl_buat`)=DATE('" . $value ."') ";
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

		if ( isset( $params["filter"]["id_cabang"] )  && !empty($params["filter"]["id_cabang"]) ) {
			$where .= "  AND  n.id_cabang =  ". $params["filter"]["id_cabang"] ." ";
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

	public function single1($pk){
		$pembelian = (object)[];
		$pembelian = $this->db->from('pembelian')->where(['id'=>$pk])->get()->row();
		$pembelian->rincian_pembelian = $this->db->select('receive_order.*, produk.*')->from('receive_order')
			->join('produk','produk.id=receive_order.id_produk','left')
			->join('pemasok', 'pemasok.id=produk.id_pemasok', 'left')
			->where(array('receive_order.pembelian_id'=>$pembelian->id))->get()->result();
		$pembelian->pemasok = $this->db->from('pemasok')->where(['id'=>$pembelian->id_pemasok])->get()->row();
		return $pembelian;
	}
	

	// public function update($params){
	// 	$result = array(
	// 		'status'	=> 'error',
	// 		'message'	=> 'Lengkapi form.',
	// 		'redirect'	=> $this->module['url']
	// 	);

	// 	// print_r($params);

	
	// 	return $result;
	// 	unset($result);
	// }
	public function update($params){
		extract($params);

		$result = array(
			'status'	=> 'error',
			'message'	=> 'Lengkapi form.',
			'redirect'	=> $this->module['url']
		);

		$pk = isset($pk) ? $pk : null;
		$data_is_valid = TRUE;

		if( $data_is_valid == TRUE ){
			$this->db->trans_begin();
			$ref = [
				'text' 		=> $pembelian['nomor'],
				'link' 		=> $this->module['url'] .'/single/' . $pk,
				'pk'		=> $pk,
				'table'		=> 'pembelian'
			];
			$sum_qty = 0;
			$sum_qtyditerima = 0;
			foreach( $rincian AS $index => $item ){
				$sum_qty += $item['qty'];
				// print_r($pembelian['status_ro']);
				// die();
					$this->db->where('pembelian_id', $pk)->update('receive_order', 
						[
						'id_produk'    => $item['pd_id'],
						'tgl' => $pembelian['tgl_buat'],
						'qty' => $item['qty'],
						'qty_diterima' => $item['qty_diterima'],
						'status_ro'    => $pembelian['status_ro'],
						]
			 		  );
					   $this->db->where('id', $pk)->update('pembelian', 
					   [
					   'status_ro'    => $pembelian['status_ro'],
					   ]
					  );
					if($pembelian['status_ro'] == '1'){
						$this->db->query("
							INSERT `stok` (`id_produk`, `tgl`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, `transaksi`, `harga`, `qty`, status_ro, id_cabang)
							VALUES ('".$item['pd_id']."','".$pembelian['tgl_buat']."','". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."','pembelian','pembelian','".$item['harga']."',".$item['qty_diterima'].", '".$pembelian['status_ro']."', '".$this->user->id_cabang."');
						");	
					}
				
				
			}
			// if($sum_qty == $sum_qtyditerima){
			// // 	$this->db->where('pembelian_id', $pk)->update('pembelian', 
			// // 	[
			// // 	'status_ro'    => 1,
			// // 	]
			// //    );
			// //    $this->db->where('pembelian_id', $pk)->update('stok', 
			// // 	[
			// // 	'ref_pk'    => 1,
			// // 	]
			// //    );
			// }
			if ($this->db->trans_status() === FALSE){
				$result['message'] 	= $this->db->error();
				$this->db->trans_rollback();
			} else {
				$this->db->trans_commit();
				$result['status'] 	= TRUE;
				$result['message'] 	= 'Data telah disimpan.';
				$result['pk'] 		= $pk;
			}
		}
		unset($produk);
		return $result;
		unset($result);
	}
	
}