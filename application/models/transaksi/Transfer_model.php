<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transfer_model extends CI_Model {
	private $module = [
		'name' 	=> 'Transaksi',
		'url'	=> 'transaksi/transfer',
	];
	public function __construct(){
		parent::__construct();
	}
	private function is_unique_field($column_name, $value, $pk=NULL){
		$query = "SELECT COUNT(`id`) AS `total` FROM `transaksi` WHERE `". $column_name ."`='". $this->db->escape_str($value) ."' ";
		if(!empty($pk)){
			$query .= " AND `id`!='" . $pk ."'";
		}
		$result = $this->db->query($query)->row();
		return isset($result->total) && $result->total > 0 ? FALSE : TRUE;
	}
	public function single($pk){
		$this->db
			->select('
				transaksi.*, 
				dari.id AS id_dari, dari.nama AS nama_dari, 
				ke.id AS id_ke, ke.nama AS nama_ke 
			')
			->from('transaksi')
			->join('akun AS dari','dari.id=transaksi.id_kredit','left')
			->join('akun AS ke','ke.id=transaksi.id_debit','left')
			->where('transaksi.id',$pk);
		return $this->db->get()->row(); 
	}
	public function datatable($config = array()){
		
		extract($config);
		$columns 			= array('`transaksi`.`tgl_nota`', '`dari`.`nama`', '`ke`.`nama`', '`transaksi`.`nominal`', '`transaksi`.`id`');

		$select_total 		= "SELECT COUNT(`transaksi`.`id`) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "
		SELECT SQL_CALC_FOUND_ROWS `transaksi`.*, 
			`dari`.`id` AS `id_dari`, `dari`.`nama` AS `nama_dari`, 
			`ke`.`id` AS `id_ke`, `ke`.`nama` AS `nama_ke` 
		";
		$from 				= "
		FROM `transaksi` 
			LEFT JOIN `akun` AS `dari` ON `dari`.`id`=`transaksi`.`id_kredit`
			LEFT JOIN `akun` AS `ke` ON `ke`.`id`=`transaksi`.`id_debit` 
		";
		$where 				= "WHERE `transaksi`.`id` IS NOT NULL AND `tipe`='transfer' ";
		$group_by 			= "";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";

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
		$select_total 	= "SELECT COUNT(`id`) AS `total` ";
		$select_data	= "SELECT *, `id` AS `id`, CONCAT(`id`,' || ',`nama`) AS `text` ";
		$from 			= "FROM `transaksi` ";
		$where 			= "WHERE `id` IS NOT NULL ";
		$having 		= "";

		$term = isset($params['term']) ? $this->db->escape_str($params['term']) : (isset($params['q']) ? $this->db->escape_str($params['q']) : null);
		if(isset($term) && !empty($term)){
			$where .= " AND (`id` LIKE '%". $term ."%' OR `nama` LIKE '%". $term ."%') ";
		}

		$order_by 		= "ORDER BY `id` ASC ";
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
	public function select2_pemasok($config = array()){
		extract($config);

		$row_per_page 	= isset($row_per_page) ? $row_per_page : 10;
		$select_total 	= "SELECT COUNT(`id`) AS `total` ";
		$select_data	= "SELECT *, `nama` AS `text` ";
		$from 			= "FROM `pemasok` ";
		$where 			= "WHERE `id` IS NOT NULL ";
		$having 		= "";

		$term = isset($params['term']) ? $this->db->escape_str($params['term']) : (isset($params['q']) ? $this->db->escape_str($params['q']) : null);
		if(isset($term) && !empty($term)){
			$where .= " AND (`id` LIKE '%". $term ."%' OR `nama` LIKE '%". $term ."%') ";
		}

		$order_by 		= "ORDER BY `id` ASC ";
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
	public function select2_dari($params = []){
		$this->db->from('akun')->where_in('induk',[1,2]);
		return $this->db->get()->result();
	}
	public function select2_ke($params = []){
		$this->db->from('akun')->where_in('induk',[1,2]);
		return $this->db->get()->result();
	}
	/* CRUD */
	public function insert($params){

		extract($params);

		$result = array(
			'status'	=> 'error',
			'message'	=> 'Lengkapi form.',
			'redirect'	=> $this->module['url']
		);

		$transfer = (object)(isset($transfer) ? $transfer: []);
		if(isset($transfer->id_debit) && isset($transfer->id_kredit) && isset($transfer->nominal) && isset($transfer->tgl_nota)){
			$this->db->trans_begin();
			$transfer->tipe = 'transfer';
			$this->db->insert('transaksi', $transfer);
			$id_transaksi = $this->db->insert_id();
			$ref = [
				'text' 		=> 'transfer ' . $id_transaksi,
				'link' 		=> $this->module['url'] .'/single/' . $id_transaksi,
				'pk'		=> $id_transaksi,
				'table'		=> 'transaksi'
			];

			$this->db->query("
				INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`)
				VALUES (". $transfer->id_debit .",'".$transfer->tgl_nota."', ". $transfer->nominal .", 0, '". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."' ,'". $ref['table'] ."');
			");
			$this->db->query("
				INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`)
				VALUES (". $transfer->id_kredit .",'".$transfer->tgl_nota."', 0 , ". $transfer->nominal .", '". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."' ,'". $ref['table'] ."');
			");

			if ($this->db->trans_status() === FALSE){
				$result['message'] 	= $this->db->error();
				$this->db->trans_rollback();
			} else {
				$this->db->trans_commit();
				$result['status'] 	= TRUE;
				$result['message'] 	= 'Data telah disimpan.';
			}
		}

		unset($transfer);
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

		$id_transaksi 	= isset($pk) ? $pk : null;
		$transfer 	= (object)(isset($transfer) ? $transfer: []);
		if(isset($transfer->id_debit) && isset($transfer->id_kredit) && isset($transfer->nominal) && isset($transfer->tgl_nota) && !empty($pk)){
			$this->db->trans_begin();

			$this->db->update('transaksi', $transfer, array('id'=>$pk));
			$this->db->query("DELETE FROM `jurnal` WHERE `ref_table`='transaksi' AND `ref_pk`='". $pk ."';");

			$ref = [
				'text' 		=> 'Transfer ' . $id_transaksi,
				'link' 		=> $this->module['url'] .'/single/' . $id_transaksi,
				'pk'		=> $id_transaksi,
				'table'		=> 'transaksi'
			];
			$this->db->query("
				INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`)
				VALUES (". $transfer->id_debit .",'".$transfer->tgl_nota."', ". $transfer->nominal .", 0, '". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."' ,'". $ref['table'] ."');
			");
			$this->db->query("
				INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`)
				VALUES (". $transfer->id_kredit .",'".$transfer->tgl_nota."', 0 , ". $transfer->nominal .", '". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."' ,'". $ref['table'] ."');
			");
			if ($this->db->trans_status() === FALSE){
				$result['message'] 	= $this->db->error();
				$this->db->trans_rollback();
			} else {
				$this->db->trans_commit();
				$result['status'] 	= TRUE;
				$result['message'] 	= 'Data telah disimpan.';
			}
		}

		unset($transfer);
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
			$this->db->trans_begin();
			$this->db->query("DELETE FROM `transaksi`  	WHERE `id`='". $pk ."';");
			$this->db->query("DELETE FROM `jurnal`  	WHERE `ref_table`='transaksi' AND `ref_pk`='". $pk ."';");
			
			$result['message'] 	= "Data couldnt delete.";

			if ($this->db->trans_status() === FALSE){
				$result['message'] 	= $this->db->error();
				$this->db->trans_rollback();
			} else {
				$this->db->trans_commit();
				$result['status'] 	= TRUE;
				$result['message'] 	= 'Data telah disimpan.';
			}
			unset($data);			
		}
		return $result;
		unset($result);
	}

}