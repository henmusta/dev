<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Kasir_model extends CI_Model {
	private $module = [
		'name' 	=> 'Transaksi',
		'url'	=> 'transaksi/kasir',
	];	
	public function __construct(){
		parent::__construct();
	}
	
	// public function get_penjualan($config){
	// 	extract($config);
	// 	$pk = isset($params['pk']) ? (int)$params['pk']:null;
	// 	// $result  = ['status'=>'error','total' => ''];
	// 	if($row = $this->db
	// 	->select('tgl_nota, SUM(total_tagihan) AS total')
	// 	->from('penjualan')
	// 	->where(['tgl_nota'=>$pk])
	// 	->group_by('tgl_nota')->get()->row()){
	// 		// if(isset($row->total)){
	// 			$result  = ['status'=>'success','total' => $row->total];
	// 		// }
	// 	}
	// 	return $result;
	// }

	public function get_penjualan($config = []){
		extract($config);
		// $pk = isset($params['pk']);
		$pk	= $this->db->escape_str(strip_tags($params['pk']));
		$cb	= $this->db->escape_str(strip_tags($params['cb']));
		$penjualan = (object)[
			'total'=>0
		];
		if($result = $this->db
		->select('tgl_nota, SUM(total_tagihan) AS total 
				')
		->from('penjualan')
		->where(['tgl_nota'=>$pk, 'id_cabang'=>$cb])->get()){
			return $result->row();
		}
		return $penjualan;
	} 

	public function get_saldo($config = []){
		extract($config);
		$pk	= $this->db->escape_str(strip_tags($params['pk']));

		// $yesterday = date('Y-m-d', strtotime('-1 day', strtotime($pk)));
		$cb	= $this->db->escape_str(strip_tags($params['cb']));
		$penjualan = (object)[
			'total'=>0
		];
		if($result = $this->db
		->select('tgl_nota, rumus AS total 
				')
		->from('transaksi')
		->where(['id_cabang'=>$cb])
		->order_by('tgl_nota', 'DESC')
		->limit('1')->get()){
			return $result->row();
		}
		return $penjualan;
	} 



	public function get_akun($config = []){
		extract($config);
		// $pk = isset($params['pk']);
		$pk	= $this->db->escape_str(strip_tags($params['pk']));
		$cb	= $this->db->escape_str(strip_tags($params['cb']));
		$penjualan = (object)[
			'total'=>0
		];

		$penjualan->rincian_pelunasan 	= $this->db
		->select('
		  SUM(rincian_pelunasan.`nominal`) AS nominal, 
		  akun.`nama` AS akun,
		  rincian_pelunasan.id_akun as id_akun  
		')
		->from('penjualan')
		->join('pelunasan','penjualan.`id` = pelunasan.`id_penjualan`','left')
		->join('rincian_pelunasan','pelunasan.`id` = rincian_pelunasan.`id_pelunasan`','left')
		->join('akun','akun.`id` = rincian_pelunasan.`id_akun`','left')
		->where(array('penjualan.tgl_nota'=>$pk, 'akun.induk'=>'2'))
		->group_by('akun.nama')->get()->result();
		return $penjualan;
	} 

	public function get_rincian($config = []){
		extract($config);
		// $pk = isset($params['pk']);
		$pk	= $this->db->escape_str(strip_tags($params['pk']));
		$penjualan = (object)[
			'total'=>0
		];
		if($result = $this->db
		->select('SUM(rincian_pelunasan.`total`) AS total, akun.`nama` AS akun, akun.`id` AS id_akun 
				')
		->from('penjualan')
		->join('pelunasan','pelunasan.`id_penjualan` = penjualan.`id`','left')
		->join('rincian_pelunasan','pelunasan.id = rincian_pelunasan.`id_pelunasan`','left')
		->join('akun','akun.`id` = rincian_pelunasan.`id_akun`','left')
		->where('akun.induk', '2')
		->where(['penjualan.tgl_nota'=>$pk])
		->group_by('akun.nama')->get()){
			return $result->result_array();
		}
		return $penjualan;
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
		$transaksi = (object)[];
		$transaksi = $this->db->from('transaksi')->where(['id'=>$pk])->get()->row();
		$transaksi->rincian_transaksi = $this->db->select('akun.id AS id_akun, akun.nama AS akun, rincian_transaksi.total AS total')->from('rincian_transaksi')
			->join('akun','akun.id=rincian_transaksi.id_akun','left')
			->where(array('rincian_transaksi.id_transaksi'=>$transaksi->id, 'rincian_transaksi.tipe'=>'pendapatan'))->get()->result();
		$transaksi->rincian_transaksi_biaya = $this->db->select('akun.id AS id_akun, akun.nama AS akun, rincian_transaksi.total AS total')
		    ->from('rincian_transaksi')
			->join('akun','akun.id=rincian_transaksi.id_akun','left')
			->where(array('rincian_transaksi.id_transaksi'=>$transaksi->id, 'rincian_transaksi.tipe'=>'pengeluaran' ))->get()->result();
		return $transaksi;
	}

	public function single_up($pk){
		$transaksi = (object)[];
		$transaksi = $this->db->from('transaksi')->where(['tgl_nota'=>$pk])->get()->row();
		$transaksi->rincian_transaksi = $this->db->select('akun.id AS id_akun, akun.nama AS akun, rincian_transaksi.total AS total')->from('rincian_transaksi')
			->join('akun','akun.id=rincian_transaksi.id_akun','left')
			->where(array('rincian_transaksi.id_transaksi'=>$transaksi->id, 'rincian_transaksi.tipe'=>'pendapatan'))->get()->result();
		$transaksi->rincian_transaksi_biaya = $this->db->select('akun.id AS id_akun, akun.nama AS akun, rincian_transaksi.total AS total')
		    ->from('rincian_transaksi')
			->join('akun','akun.id=rincian_transaksi.id_akun','left')
			->where(array('rincian_transaksi.id_transaksi'=>$transaksi->id, 'rincian_transaksi.tipe'=>'pengeluaran' ))->get()->result();
		return $transaksi;
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
		FROM transaksi as transaksi 
			LEFT JOIN `akun` AS `kas` ON `kas`.`id`=`transaksi`.`id_debit`
			LEFT JOIN `akun` AS `pendapatan` ON `pendapatan`.`id`=`transaksi`.`id_kredit` 
		";
		$where 				= "WHERE `transaksi`.`id` IS NOT NULL";
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

		if ( isset( $params["filter"]["id_cabang"] )  && !empty($params["filter"]["id_cabang"]) ) {
			$where .= "  AND  transaksi.id_cabang =  ". $params["filter"]["id_cabang"] ." ";
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
		$this->db->from('akun')
		->where_in('induk',[1,2]);
		return $this->db->get()->result_array();
	}
	public function select2_biaya($params = []){
		$this->db->from('akun')
		->where('kelompok', 'biaya_lampung')
		->where_in('induk',[12]);
		return $this->db->get()->result_array();
	}
	public function select2_pendapatan($params = []){
		$this->db->from('akun')->where_in('id',[5,7]);
		return $this->db->get()->result();
	}

	public function single_pendapatan($pk, $cb){
		$transaksi = (object)[];
		$date      = isset($pk) ? $pk : null;
		// $penjualan = $this->db->from('penjualan')->where(['tgl_nota'=>$pk])->get()->row();
		// $penjualan->rincian_penjualan = $this->db->select('rincian_penjualan.*, produk.*, pemasok.kode AS kode_pemasok ,kode_laba.kode AS kode_laba')->from('rincian_penjualan')
		// 	->join('produk','produk.id=rincian_penjualan.id_produk','left')
		// 	->join('pemasok','produk.id_pemasok=pemasok.id','left')
		// 	->join('kode_laba','kode_laba.laba=produk.laba','left')
		// 	->where(array('rincian_penjualan.id_penjualan'=>$penjualan->id))->get()->result();
		// $penjualan->pelanggan = $this->db->from('pelanggan')->where(['id'=>$penjualan->id_pelanggan])->get()->row();
		// $penjualan->pelunasan 			= $this->db->from('pelunasan')->where(['pelunasan.id_penjualan'=>$penjualan->id,'gabung_faktur'=>1])->get()->row();
		$transaksi->rincian_transaksi 	= $this->db
			->select('
			  SUM(rincian_pelunasan.`nominal`) AS total, 
			  akun.`nama` AS akun,
			  rincian_pelunasan.id_akun AS id_akun  
			')
			->from('penjualan')
			->join('pelunasan','penjualan.`id` = pelunasan.`id_penjualan`','left')
			->join('rincian_pelunasan','pelunasan.`id` = rincian_pelunasan.`id_pelunasan`','left')
			->join('akun','akun.`id` = rincian_pelunasan.`id_akun`','left')
			->where(array('penjualan.tgl_nota'=>$date, 'akun.induk'=>'2', 'penjualan.id_cabang' => $cb))
			->group_by('akun.nama')->get()->result();
	$transaksi->rincian_transaksi_biaya = $this->db
			->select('akun.id AS id_akun, akun.nama AS akun')
			->from('akun')
			->where(array('akun.kelompok'=>'Biaya_lampung'))
			->order_by('akun.id', 'ASC')->get()->result();
	// $transaksi->rincian_transaksi_biaya = $this->db->select('akun.id AS id_akun, akun.nama AS akun, rincian_transaksi.total AS total')
	// ->from('rincian_transaksi')
	// ->join('akun','akun.id=rincian_transaksi.id_akun','left')
	// ->where(array('rincian_transaksi.tipe'=>'pengeluaran' ))->get()->result();
		return $transaksi;
	}
	/* CRUD */
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
			extract($kasir);
			// extract($modal);
			$this->db->query("
				INSERT INTO transaksi (`id_cabang`,`tipe`,`tgl_nota`,penjualan,`setoran`, modal, biaya, nominal , rumus, register) 
					VALUES ('".$id_cabang."','transaksi_lampung','".$tgl_nota."',".$penjualan.",".$setoran.",".$modal.",".$totalbiaya.",".$kasakhir.", ".$rumus." ,".$register.");
			");
		
			$id_transaksi = (int)$this->db->insert_id();
			
			$ref = [
				'text' 		=> 'pendapatan ' . $id_transaksi,
				'text1' 	=> 'pengeluaran_kasir' . $id_transaksi,
				'text2' 	=> 'modal ' . $id_transaksi,
				'link' 		=> $this->module['url'] .'/single/' . $id_transaksi,
				'pk'		=> $id_transaksi,
				'table'		=> 'transaksi_lampung'
			];
			$this->db->query("
			INSERT INTO `rincian_transaksi` (`id_transaksi`,kredit, id_akun, total, tipe) 
				VALUES ('".$id_transaksi."','".$totalbiaya."','12','".$totalbiaya."', 'biaya');
			");
			
			// $this->db->query("
			// INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, id_cabang)
			// VALUES ('12','".$tgl_nota."', ". $totalbiaya .", 0, '". $ref['text1'] ."','". $ref['link'] ."','". $ref['pk'] ."' ,'". $ref['table'] ."', '". $id_cabang ."');
		    // ");

			$this->db->query("
					INSERT INTO `rincian_biaya_lampung` (`id_transaksi`,tgl,`gaji`,`bulanan`, listrik, angkut, ekspedisi, peralatan, konsumsi, dll, total) 
						VALUES ('".$id_transaksi."','".$tgl_nota."' ,'".$biaya[0]['total']."','".$biaya[1]['total']."' , '".$biaya[2]['total']."' , '".$biaya[3]['total']."' , '".$biaya[4]['total']."' , '".$biaya[5]['total']."' , '".$biaya[6]['total']."' , '".$biaya[7]['total']."' , '".$totalbiaya."');
				");

			$total_rincian = 0;
			foreach( $rincian AS $index => $akun ){
				$this->db->query("
					INSERT INTO `rincian_transaksi` (`id_transaksi`,kredit,`id_akun`,`total`, tipe) 
						VALUES ('".$id_transaksi."','".$akun['nominal']."','".$akun['id_akun']."','".$akun['nominal']."', 'pendapatan');
				");

			$this->db->query("
				INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, id_cabang)
				VALUES ('".$akun['id_akun']."','".$tgl_nota."', 0, '".$akun['nominal']."', '". $ref['text'] ."','". $ref['link'] ."','". $id_transaksi ."' ,'". $ref['table'] ."', '". $id_cabang ."');
		    ");
				
			}
			foreach( $biaya AS $index => $akun ){
				$this->db->query("
					INSERT INTO `rincian_transaksi` (`id_transaksi`,`id_akun`,`total`, tipe) 
						VALUES ('".$id_transaksi."','".$akun['id_akun']."','".$akun['total']."', 'pengeluaran');
				");
			}
			// foreach( $biaya AS $index => $akun ){
		
			// }
			$this->db->query("
			INSERT INTO `rincian_transaksi` (`id_transaksi`,kredit, id_akun, total, tipe) 
				VALUES ('".$id_transaksi."','".$id_kredit."', '".$id_kredit."','".$modal."', 'modal');
			");

			$this->db->query("
				INSERT INTO `jurnal` (`id_akun`, `tgl`, `kredit`, `debit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`)
				VALUES ('5','".$tgl_nota."', ". $kasakhir .", 0, '". $ref['text2'] ."','". $ref['link'] ."','". $id_transaksi ."' ,'". $ref['table'] ."');
		    ");

			$this->db->query("
			INSERT INTO `rincian_transaksi` (`id_transaksi`,debit, id_akun, total, tipe) 
				VALUES ('".$id_transaksi."','".$penjualan."', '8','".$penjualan."', 'tunai');
			");

			// $this->db->query("
			// INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`)
			// VALUES ('".$id_transaksi."','".$tgl_nota."', ". $pendapatan->nominal .", 0, '". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."' ,'". $ref['table'] ."');
		    // ");

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
	// public function insert($params){
	// 	extract($params);
	// 	$result = array(
	// 		'status'	=> 'error',
	// 		'message'	=> 'Lengkapi form.',
	// 		'redirect'	=> $this->module['url']
	// 	);

	// 	$pendapatan = (object)(isset($pendapatan) ? $pendapatan: []);
	// 	if(isset($pendapatan->id_kredit)){
	// 		$this->db->trans_begin();

	// 		extract($pendapatan);

	// 		// $pendapatan->tipe = 'pendapatan';

	// 		$id_transaksi = $this->db->insert_id();

	// 		$ref = [
	// 			'text' 		=> 'kasirlampung' . $id_transaksi,
	// 			'link' 		=> $this->module['url'] .'/single/' . $id_transaksi,
	// 			'pk'		=> $id_transaksi,
	// 			'table'		=> 'transaksi'
	// 		];

	// 		$this->db->query("
	// 		INSERT INTO `transaksi` (`id_cabang`,`tipe`,`tgl_nota`,`nominal`) 
	// 			VALUES ('2','transaksi_lampung','".$tgl_nota."',".$nominal.");
	// 		");

	// 		if ($this->db->trans_status() === FALSE){
	// 			$result['message'] 	= $this->db->error();
	// 			$this->db->trans_rollback();
	// 		} else {
	// 			$this->db->trans_commit();
	// 			$result['status'] 	= TRUE;
	// 			$result['message'] 	= 'Data telah disimpan.';
	// 		}
	// 	}

	// 	unset($pendapatan);
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
		if(isset($pk) && !empty($pk)){
			$deleted = $this->delete($params);
			if(isset($deleted['status']) && $deleted['status'] === TRUE){
				$result = $this->insert($params);
			}
		}
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
			$this->db->query("DELETE FROM `jurnal`  	WHERE `ref_table`='transaksi_lampung' AND `ref_pk`='". $pk ."';");
			
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

