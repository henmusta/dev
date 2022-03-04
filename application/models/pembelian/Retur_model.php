<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Retur_model extends CI_Model {
	private $module = [
		'name' 	=> 'Pembelian',
		'url'	=> 'pembelian/retur',
	];
	public function __construct(){
		parent::__construct();
	}
	private function is_unique_field($column_name, $value, $pk=NULL){
		$query = "SELECT COUNT(`id`) AS `total` FROM `pembelian` WHERE `". $column_name ."`='". $this->db->escape_str($value) ."' ";
		if(!empty($pk)){
			$query .= " AND `id`!='" . $pk ."'";
		}
		$result = $this->db->query($query)->row();
		return isset($result->total) && $result->total > 0 ? FALSE : TRUE;
	}
	public function single($pk){
		$retur_pembelian = (object)[];
		$retur_pembelian = $this->db->from('retur_pembelian')->where(['id'=>$pk])->get()->row();
		$retur_pembelian->rincian_retur_pembelian = $this->db->select('rincian_retur_pembelian.*, produk.*')->from('rincian_retur_pembelian')
			->join('produk','produk.id=rincian_retur_pembelian.id_produk','left')
			->where(array('rincian_retur_pembelian.id_retur_pembelian'=>$retur_pembelian->id))->get()->result();

		$retur_pembelian->pemasok = $this->db->from('pemasok')->where(['id'=>$retur_pembelian->id_pemasok])->get()->row();
		return $retur_pembelian;
	}
	public function datatable($config = array()){
		extract($config);
		$columns 			= array('tgl_nota', 'nama_pemasok', 'nominal', 'id');
		$select_total 		= "SELECT COUNT(DISTINCT(`n`.`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS `n`.*, `s`.`nama` AS `nama_pemasok` ";
		$from 				= "
		FROM `retur_pembelian` AS `n` 
			LEFT JOIN `pemasok` AS `s` ON `s`.`id`=`n`.`id_pemasok`
		";
		$where 				= "WHERE `n`.`id` IS NOT NULL ";
		$group_by 			= "GROUP BY `n`.`id` ";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";

		if( isset($params['filter']['tgl_nota']) && !empty($params['filter']['tgl_nota']) ){
			$value	= $this->db->escape_str(strip_tags($params['filter']['tgl_nota']));
			$where .= " AND `n`.`tgl_nota`='" . $value ."' ";
		}

		if ( isset( $params["filter"]["id_cabang"] )  && !empty($params["filter"]["id_cabang"]) ) {
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
	public function select2($config = array()){
		extract($config);

		$row_per_page 	= isset($row_per_page) ? $row_per_page : 10;
		$select_total 	= "SELECT COUNT(DISTINCT(`id`)) AS `total` ";
		$select_data	= "SELECT * ";
		$from 			= "FROM `pembelian` ";
		$where 			= "WHERE `id` IS NOT NULL ";

		$term = isset($params['term']) ? $this->db->escape_str($params['term']) : (isset($params['q']) ? $this->db->escape_str($params['q']) : null);
		if(isset($term) && !empty($term)){
			$where .= " AND (`id` LIKE '%". $term ."%' OR `nama` LIKE '%". $term ."%') ";
		}

		$group_by 		= "GROUP BY `id` ";
		$order_by 		= "ORDER BY `id` ASC ";
		$result_total	= $this->db->query($select_total . $from . $where . ";");
		$total_data 	= $result_total->row()->total;
		$total_page		= ceil((int)$total_data/$row_per_page);
		$page 			= isset($params['page']) ? (int)$params['page'] : 1;
		$offset 		= (($page - 1) * $row_per_page);
		$result_total->free_result();
		$data = $this->db->query($select_data . $from . $where . $group_by . $order_by ." LIMIT ". $row_per_page ." OFFSET ". $offset .";");
		return array( 
			'results' 		=> $data->result_array(),
			'pagination' 	=> array('more' => ($page < $total_page)) 
		);
		$data->free_result();
	}
	public function select2_pemasok($config = array()){
		extract($config);

		$row_per_page 	= isset($row_per_page) ? $row_per_page : 10;
		$select_total 	= "SELECT COUNT(DISTINCT(`id`)) AS `total` ";
		$select_data	= "SELECT * ";
		$from 			= "FROM `pemasok` ";
		$where 			= "WHERE `id` IS NOT NULL ";

		$term = isset($params['term']) ? $this->db->escape_str($params['term']) : (isset($params['q']) ? $this->db->escape_str($params['q']) : null);
		if(isset($term) && !empty($term)){
			$where .= " AND (`id` LIKE '%". $term ."%' OR `nama` LIKE '%". $term ."%') ";
		}

		if(isset($params['id_cabang']) && !empty($params['id_cabang'])){
			$where .= " AND (`pemasok`.`id_cabang` = " . $params['id_cabang'] . ") ";
		}

		$group_by 		= "GROUP BY `id` ";
		$order_by 		= "ORDER BY `id` ASC ";
		$result_total	= $this->db->query($select_total . $from . $where . ";");
		$total_data 	= $result_total->row()->total;
		$total_page		= ceil((int)$total_data/$row_per_page);
		$page 			= isset($params['page']) ? (int)$params['page'] : 1;
		$offset 		= (($page - 1) * $row_per_page);
		$result_total->free_result();
		$data = $this->db->query($select_data . $from . $where . $group_by . $order_by ." LIMIT ". $row_per_page ." OFFSET ". $offset .";");
		return array( 
			'results' 		=> $data->result_array(),
			'pagination' 	=> array('more' => ($page < $total_page)) 
		);
		$data->free_result();
	}
	public function select2_produk($config = array()){
		extract($config);
		$id_pemasok = (int)(isset($params['id_pemasok'])?$params['id_pemasok']:0);

		$row_per_page 	= isset($row_per_page) ? $row_per_page : 10;
		$select_total 	= "SELECT COUNT(DISTINCT(`id`)) AS `total` ";
		$select_data	= "SELECT * ";
		$from 			= "FROM `produk` ";
		$where 			= "WHERE `id` IS NOT NULL AND `id_pemasok`=" . $id_pemasok ." ";

		$term = isset($params['term']) ? $this->db->escape_str($params['term']) : (isset($params['q']) ? $this->db->escape_str($params['q']) : null);
		if(isset($term) && !empty($term)){
			$where .= " AND (`id` LIKE '%". $term ."%' OR `nama` LIKE '%". $term ."%') ";
		}
		$group_by 		= "GROUP BY `id` ";
		$order_by 		= "ORDER BY `id` ASC ";
		$result_total	= $this->db->query($select_total . $from . $where . ";");
		$total_data 	= $result_total->row()->total;
		$total_page		= ceil((int)$total_data/$row_per_page);
		$page 			= isset($params['page']) ? (int)$params['page'] : 1;
		$offset 		= (($page - 1) * $row_per_page);
		$result_total->free_result();
		$data = $this->db->query($select_data . $from . $where . $group_by . $order_by ." LIMIT ". $row_per_page ." OFFSET ". $offset .";");
		return array( 
			'results' 		=> $data->result_array(),
			'pagination' 	=> array('more' => ($page < $total_page)) 
		);
		$data->free_result();
	}
	/* CRUD */
	public function insert($params){
		$result = array(
			'status'	=> 'error',
			'message'	=> 'Lengkapi form.',
			'redirect'	=> $this->module['url']
		);

		$data_is_valid = TRUE;
		extract($params);

		if($data_is_valid === TRUE){
			$this->db->trans_begin();
			extract($retur);

			/* Record Data Retur */
			$this->db->query("INSERT INTO `retur_pembelian` (`id_pemasok`,`tgl_nota`, id_cabang) VALUES ('".$id_pemasok."','".$tgl_nota."', '".$id_cabang."');");

			/* Get ID Retur */
			$id_retur_pembelian = (int)$this->db->insert_id();

			/* Build Data Reference */
			$ref = [
				'text' 		=> 'Retur_Pembelian # ' . $id_retur_pembelian,
				'link' 		=> $this->module['url'] .'/single/' . $id_retur_pembelian,
				'pk'		=> $id_retur_pembelian,
				'table'		=> 'retur'
			];

			$nominal = 0;

			/* Record Data Barang */
			foreach( $rincian AS $index => $item ){
				$item['total'] = $item['harga'] * $item['qty'];

				/* Record Data Barang */
				$this->db->query("
					INSERT INTO `rincian_retur_pembelian` (`id_retur_pembelian`,`id_produk`,`qty`,`harga`,`total`) 
					VALUES ('".$id_retur_pembelian."','".$item['id_produk']."','".$item['qty']."','".$item['harga']."','".$item['total']."');
				");

				/* Record Data Stok Barang */
				$this->db->query("
					INSERT `stok` (`id_produk`, `tgl`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, `transaksi`, `harga`, `qty`)
					VALUES ('".$item['id_produk']."','".$tgl_nota."','". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."','".$ref['table']."','pembelian','".$item['harga']."',".($item['qty'] * -1).");
				");

				$nominal += $item['total'];
			}
			$this->db->query("UPDATE `retur_pembelian` SET `nominal`=". $nominal ." WHERE `id`=". $id_retur_pembelian .";");
			if ($this->db->trans_status() === FALSE){
				$result['message'] 	= $this->db->error();
				$this->db->trans_rollback();
			} else {
				$this->db->trans_commit();
				$result['status'] 	= TRUE;
				$result['message'] 	= 'Data telah disimpan.';
			}
		}

		unset($pembelian);
		return $result;
		unset($result);
	}
	public function update($params){
		extract($params);

		$result = array(
			'status'	=> 'error',
			'message'	=> 'Lengkapi form.',
			'redirect'	=> $this->module['url']
		);
		$deleted = $this->delete($params);
		if(isset($deleted['status']) && $deleted['status'] === TRUE){
			$result = $this->insert($params);
		}
		return $result;
		unset($result);
	}
	public function update_harga($params){
		extract($params);

		$result = array(
			'status'	=> 'error',
			'message'	=> 'Lengkapi form.',
			'kode_laba'	=> ''
		);

		$pk = isset($pk) ? $pk : null;
		$data_is_valid = TRUE;

		if( 
			(isset($produk['harga_beli']) && !empty($produk['harga_beli'])) &&
			(isset($produk['harga_jual']) && !empty($produk['harga_jual'])) &&
			(isset($produk['laba']) && !empty($produk['laba'])) &&
			(isset($pk) && !empty($pk))
		){
			if( $data_is_valid == TRUE ){
				if( is_array($produk) && count($produk) > 0 ){
					$result['message'] 	= "Harga Jual Telah Diubah.";
					if( $this->db->update('produk', $produk, array('id'=>$pk)) ){
						$kode_laba = $this->db->get_where('kode_laba',['laba'=>$produk['laba']])->row();
						$result['status'] 		= TRUE;
						$result['message'] 		= 'Data telah disimpan.';
						$result['kode_laba'] 	= isset($kode_laba->kode) ? $kode_laba->kode: '';
					}
				}
			}
		}
		unset($produk);
		return $result;
		unset($result);
	}
	public function delete($params = array()){

		extract($params);

		$result = array(
			'status'	=> 'error',
			'message'	=> 'Please complete data field requirements.'
		);

		if( isset($pk) ){
			$result['message'] 	= "Data couldnt delete.";
			$this->db->trans_begin();

			$this->db->query("DELETE FROM `retur_pembelian` WHERE `id`='". $pk ."'");
			$this->db->query("DELETE FROM `stok` WHERE `ref_table`='retur' AND `ref_pk`='". $pk ."';");

			if ($this->db->trans_status() === FALSE){
				$result['message'] 	= $this->db->error();
				$this->db->trans_rollback();
			} else {
				$this->db->trans_commit();
				$result['status'] 	= TRUE;
				$result['message'] 	= 'Data telah disimpan.';
			}
		}
		return $result;
		unset($result);
	}
}