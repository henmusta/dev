<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stok_opname_model extends CI_Model {
	private $module = [
		'name' 	=> 'Stok opname',
		'url'	=> 'inventori/stok-opname',
	];
	public function __construct(){
		parent::__construct();
	}
	private function is_unique_field($column_name, $value, $pk=NULL){
		$query = "SELECT COUNT(`id`) AS `total` FROM `stok_opname` WHERE `". $column_name ."`='". $this->db->escape_str($value) ."' ";
		if(!empty($pk)){
			$query .= " AND `id`!='" . $pk ."'";
		}
		$result = $this->db->query($query)->row();
		return isset($result->total) && $result->total > 0 ? FALSE : TRUE;
	}
	public function datatable($config = array()){		
		extract($config);
	    $columns 			= array('tgl_opname', 'tgl_buat', 'nomor');

		$select_total 		= "SELECT COUNT(DISTINCT(`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS * ";
		$from 				= "FROM `stok_opname`";
		$where 				= "WHERE `id` IS NOT NULL ";
		$group_by 			= "GROUP BY `id` ";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";

		if( isset($params['filter']['tgl_opname']) && !empty($params['filter']['tgl_opname']) ){
			$value	= $this->db->escape_str(strip_tags($params['filter']['tgl_opname']));
			$where .= " AND `tgl_opname`='" . $value ."' ";
		}

		
		if ( isset( $params["filter"]["id_cabang"] )  && !empty($params["filter"]["id_cabang"]) ) {
			$where .= "  AND  stok_opname.id_cabang =  ". $params["filter"]["id_cabang"] ." ";
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
	public function select2_produk($config = array()){
		extract($config);
		$row_per_page 	= isset($row_per_page) ? $row_per_page : 10;
		$select_total 	= "SELECT COUNT(DISTINCT(`produk`.`id`)) AS `total`";
		$select_data	= "SELECT produk.`id`, produk.`nama` as nama_produk, pemasok.kode as kode, pemasok.nama as nama_pemasok, produk.`harga_jual`,  SUM(IFNULL(`stok`.`qty`,0)) AS `saldo`  ";
		$from 			= "FROM `produk` INNER JOIN stok ON produk.id = stok.id_produk inner join pemasok on pemasok.id = produk.id_pemasok ";
		$where 			= "WHERE produk.id IS NOT NULL ";
		$group_by 		= "GROUP BY produk.id ";
		$order_by 		= "ORDER BY produk.id ASC ";

		$term = isset($params['term']) ? $this->db->escape_str($params['term']) : (isset($params['q']) ? $this->db->escape_str($params['q']) : null);
		if(isset($term) && !empty($term)){
			$where .= " AND (produk.`nama` LIKE '%". $term ."%' OR pemasok.`kode` LIKE '%". $term ."%'  OR pemasok.`nama` LIKE '%". $term ."%') ";
		}

		if(isset($params['id_cabang']) && !empty($params['id_cabang'])){
			$where .= " AND (`produk`.`id_cabang` = " . $params['id_cabang'] . ")";
		}
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
	
	public function insert($params){
		$result = array(
			'status'	=> 'error',
			'message'	=> 'Lengkapi form.',
			'redirect'	=> $this->module['url']
		);

		$data_is_valid = TRUE;
		extract($params);

	    if( isset($stok_opname['tgl_nota']) && !empty($stok_opname['tgl_nota']) && isset($stok_opname['nomor']) && !empty($stok_opname['nomor']) )
			{
				if( isset($stok_opname['nomor']) && !empty($stok_opname['nomor']) ){
					$stok_opname['nomor'] 	= trim(strip_tags($stok_opname['nomor']));
					$data_is_valid 	= $this->is_unique_field('nomor', $stok_opname['nomor']);
					if( $data_is_valid == FALSE ){
						$result['message'] 	= "Nomor nota penjualan sudah ada.";
					}
				}
			}

		if($data_is_valid === TRUE){
				$this->db->trans_begin();
				extract($stok_opname);

				/* Record Data penjualan */
				$this->db->query("
					INSERT INTO `stok_opname` (`tgl_opname`,`nomor`,id_cabang) 
						VALUES ('".$tgl_opname."','".$nomor. "','".$id_cabang."');
				");

				/* Get ID penjualan */
				$stok_opname_id = (int)$this->db->insert_id();

				/* Build Data Reference */
				$ref = [
					'text' 		=> $nomor,
					'link' 		=> $this->module['url'] .'/single/' . $stok_opname_id,
					'pk'		=> $stok_opname_id,
					'table'		=> 'stok_opname'
				];

				$total_rincian = 0;

				/* Record Data Barang */
				foreach( $rincian AS $index => $item ){
					$item['qty_selisih'] = $item['qty_fisik'] -  $item['qty_komputer'];
					$this->db->query("
						INSERT INTO `rincian_stok_opname` (`stok_opname_id`,`id_produk`,`qty_komputer`,`qty_fisik`,`qty_selisih`) 
							VALUES ('".$stok_opname_id."','".$item['id_produk']."','".$item['qty_komputer']."','".$item['qty_fisik']."','".$item['qty_selisih']."');
					");

					$this->db->query("
						INSERT `stok` (`id_produk`, `tgl`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, `transaksi`, `harga`, `qty`)
						VALUES ('".$item['id_produk']."','".$tgl_opname."','". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."','stok_opname','stok_opname','".$item['qty_harga']."',".$item['qty_selisih'].");
					");

					$total_rincian += $item['qty_selisih'];
				}


				if ($this->db->trans_status() === FALSE){
					$result['message'] 	= $this->db->error();
					$this->db->trans_rollback();
				} else {
					$this->db->trans_commit();
					$result['status'] 	= TRUE;
					$result['message'] 	= 'Data telah disimpan.';
					$result['pk'] 		= $stok_opname_id;
				}
			}

			unset($stok_opname);
			return $result;
			unset($result);
	}
	public function single($pk){
		$stok_opname = (object)[];
		$stok_opname = $this->db->from('stok_opname')->where(['id'=>$pk])->get()->row();
		$stok_opname->rincian_stok_opname = $this->db->select('rincian_stok_opname.*, produk.*')->from('rincian_stok_opname')
			->join('produk','produk.id=rincian_stok_opname.id_produk','left')
			->where(array('rincian_stok_opname.stok_opname_id'=>$stok_opname->id))->get()->result();
		return $stok_opname;
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
			$this->db->query("DELETE FROM `stok_opname` WHERE `id`='". $pk ."'");
			$this->db->query("DELETE FROM `stok`  		WHERE `ref_table`='stok_opname' AND `ref_pk`='". $pk ."';");
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
	public function update($params){
		extract($params);

		$result = array(
			'status'	=> 'error',
			'message'	=> 'Lengkapi form.',
			'redirect'	=> $this->module['url']
		);

		$id_stok_opname = [];
		if(isset($pk) && !empty($pk)){
			$cicilan = $this->db->select('id')->from('stok_opname')->where('id',$pk)->get()->result_array();
			$id_stok_opname = array_column($cicilan, 'id');
			$deleted = $this->delete($params);
			if(isset($deleted['status']) && $deleted['status'] === TRUE){
				$result = $this->insert($params);
				if( isset($result['status']) && $result['status'] === TRUE && isset($result['pk']) && !empty($result['pk']) ){
					$id_penjualan = $result['pk'];
				}
			}
		}
		return $result;
		unset($result);
	}

}