<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pendapatan_model extends CI_Model {
	private $module = [
		'name' 	=> 'Transaksi',
		'url'	=> 'transaksi/pendapatan',
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
				kas.id AS id_kas, kas.nama AS nama_kas, 
				pendapatan.id AS id_pendapatan, pendapatan.nama AS nama_pendapatan 
			')
			->from('transaksi')
			->join('akun AS kas','kas.id=transaksi.id_debit','left')
			->join('akun AS pendapatan','pendapatan.id=transaksi.id_kredit','left')
			->where('transaksi.id',$pk);
		return $this->db->get()->row(); 
	}

	public function datatable($config = array()){		
		extract($config);
		$columns 			= array('`transaksi`.`tgl_nota`', '`kas`.`nama`', '`pendapatan`.`nama`', '`transaksi`.`nominal`', '`transaksi`.`id`');
		$select_total 		= "SELECT COUNT(`transaksi`.`id`) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "
		SELECT SQL_CALC_FOUND_ROWS `transaksi`.*, 
			`kas`.`id` AS `id_kas`, `kas`.`nama` AS `nama_kas`, 
			`pendapatan`.`id` AS `id_pendapatan`, `pendapatan`.`nama` AS `nama_pendapatan` 
		";
		$from 				= "
		FROM `transaksi` 
			LEFT JOIN `akun` AS `kas` ON `kas`.`id`=`transaksi`.`id_debit`
			LEFT JOIN `akun` AS `pendapatan` ON `pendapatan`.`id`=`transaksi`.`id_kredit` 
		";
		$where 				= "WHERE `transaksi`.`id` IS NOT NULL AND `tipe`='pendapatan' ";
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

	public function select2_kas($params = []){
		$this->db->from('akun')->where_in('induk',[1,2]);
		return $this->db->get()->result();
	}
	
	public function select2_pendapatan($params = []){
		$this->db->from('akun')->where_in('id',[5,7]);
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

		$pendapatan = (object)(isset($pendapatan) ? $pendapatan: []);
		if(isset($pendapatan->id_debit) && isset($pendapatan->id_kredit) && isset($pendapatan->nominal) && isset($pendapatan->tgl_nota)){
			$this->db->trans_begin();
			$pendapatan->tipe = 'pendapatan';
			$this->db->insert('transaksi', $pendapatan);
			$id_transaksi = $this->db->insert_id();
			$ref = [
				'text' 		=> 'pendapatan ' . $id_transaksi,
				'link' 		=> $this->module['url'] .'/single/' . $id_transaksi,
				'pk'		=> $id_transaksi,
				'table'		=> 'transaksi'
			];

			$this->db->query("
				INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`)
				VALUES (". $pendapatan->id_debit .",'".$pendapatan->tgl_nota."', ". $pendapatan->nominal .", 0, '". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."' ,'". $ref['table'] ."');
			");
			$this->db->query("
				INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`)
				VALUES (". $pendapatan->id_kredit .",'".$pendapatan->tgl_nota."', 0 , ". $pendapatan->nominal .", '". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."' ,'". $ref['table'] ."');
			");

			// $this->db->query("
			// INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`)
			// VALUES (". $pengeluaran->id_debit .",'".$pengeluaran->tgl_nota."', ". $pengeluaran->nominal .", 0, '". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."' ,'". $ref['table'] ."');
			// ");
			// $this->db->query("
			// INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`)
			// VALUES (". $pengeluaran->id_kredit .",'".$pengeluaran->tgl_nota."', 0 , ". $pengeluaran->nominal .", '". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."' ,'". $ref['table'] ."');
			// ");


			if ($this->db->trans_status() === FALSE){
				$result['message'] 	= $this->db->error();
				$this->db->trans_rollback();
			} else {
				$this->db->trans_commit();
				$result['status'] 	= TRUE;
				$result['message'] 	= 'Data telah disimpan.';
			}
		}

		unset($pendapatan);
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
		$pendapatan 	= (object)(isset($pendapatan) ? $pendapatan: []);
		if(isset($pendapatan->id_debit) && isset($pendapatan->id_kredit) && isset($pendapatan->nominal) && isset($pendapatan->tgl_nota) && !empty($pk)){
			$this->db->trans_begin();

			$this->db->update('transaksi', $pendapatan, array('id'=>$pk));
			$this->db->query("DELETE FROM `jurnal` WHERE `ref_table`='transaksi' AND `ref_pk`='". $pk ."';");

			$ref = [
				'text' 		=> 'pendapatan ' . $id_transaksi,
				'link' 		=> $this->module['url'] .'/single/' . $id_transaksi,
				'pk'		=> $id_transaksi,
				'table'		=> 'transaksi'
			];
			$this->db->query("
				INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`)
				VALUES (". $pendapatan->id_debit .",'".$pendapatan->tgl_nota."', ". $pendapatan->nominal .", 0, '". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."' ,'". $ref['table'] ."');
			");
			$this->db->query("
				INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`)
				VALUES (". $pendapatan->id_kredit .",'".$pendapatan->tgl_nota."', 0 , ". $pendapatan->nominal .", '". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."' ,'". $ref['table'] ."');
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

		unset($pendapatan);
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