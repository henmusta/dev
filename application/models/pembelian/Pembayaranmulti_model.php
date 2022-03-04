<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembayaranmulti_model extends CI_Model {
	private $module = [
		'name' 	=> 'Pembayaran Pembelian Multi Faktur',
		'url'	=> 'pembelian/pembayaranmulti',
	];
	public function __construct(){
		parent::__construct();
	}
	private function is_unique_field($column_name, $value, $pk=NULL){
		$query = "SELECT COUNT(`id`) AS `total` FROM `pembayaran` WHERE `". $column_name ."`='". $this->db->escape_str($value) ."' ";
		if(!empty($pk)){
			$query .= " AND `id`!='" . $pk ."'";
		}
		$result = $this->db->query($query)->row();
		return isset($result->total) && $result->total > 0 ? FALSE : TRUE;
	}

	public function single($pk){
		$row = $this->db->get_where('pembayaran',array('gabung_nota'=>$pk))->row();
		if(isset($row->id) && !empty($row->id) ){
			$row->pembelian = $this->db->get_where('pembelian',array('id'=>$row->id_pembelian))->row();
			$row->multi = $this->db->get_where('rincian_pembayaran_multi',array('gabung_nota'=>$pk))->row();
			$row->nota =  $this->db->select('*, pembelian.tgl_nota as tgl_nota, pembayaran.nominal as sisa_tagihan')
									->from('pembayaran')
									->join('pembelian','pembelian.id=pembayaran.id_pembelian','left')
									->where(array('gabung_nota'=>$pk))->get()->result();
			$row->pemasok 	= $this->db->get_where('pemasok',array('id'=>$row->id_pemasok))->row();
			$row->rincian 	= $this->db
				->select('rincian_pembayaran_multi.*,
					rincian_pembayaran_multi.total as total,
					rincian_pembayaran_multi.nominal as nominal,
					akun.nama AS nama_akun,
					giro.nomor AS nomor_giro
				')
				->from('rincian_pembayaran_multi')
				->join('akun','akun.id=rincian_pembayaran_multi.id_akun','left')
				->join('giro','giro.id=rincian_pembayaran_multi.id_giro','left')
				->join('pembayaran','pembayaran.gabung_nota=rincian_pembayaran_multi.gabung_nota','left')
				->where(['rincian_pembayaran_multi.gabung_nota'=>$pk])
				->group_by('rincian_pembayaran_multi.id')->get()->result();
			return $row;
		}
		return (object)[]; 
	}

	// public function single($pk){
	// 	$row = $this->db->get_where('pembayaran',array('gabung_nota'=>$pk))->row();
	// 	if(isset($row->id) && !empty($row->id) ){
	// 		$row->pembelian = $this->db->get_where('pembelian',array('id'=>$row->id_pembelian))->row();
	// 		$row->nota =  $this->db->select('*, pembelian.tgl_nota as tgl_nota, pembayaran.nominal as sisa_tagihan')
	// 								->from('pembayaran')
	// 								->join('pembelian','pembelian.id=pembayaran.id_pembelian','left')
	// 								->where(array('gabung_nota'=>$pk))->get()->result();
	// 		$row->pemasok 	= $this->db->get_where('pemasok',array('id'=>$row->id_pemasok))->row();
	// 		$row->rincian 	= $this->db
	// 			->select('rincian_pembayaran.*,
	// 				sum(rincian_pembayaran.total) as total,
	// 				sum(rincian_pembayaran.total) as nominal,
	// 				akun.nama AS nama_akun,
	// 				giro.nomor AS nomor_giro
	// 			')
	// 			->from('rincian_pembayaran')
	// 			->join('akun','akun.id=rincian_pembayaran.id_akun','left')
	// 			->join('giro','giro.id=rincian_pembayaran.id_giro','left')
	// 			->join('pembayaran','pembayaran.id=rincian_pembayaran.id_pembayaran','left')
	// 			->where(['rincian_pembayaran.gabung_nota'=>$pk])->get()->result();
	// 		return $row;
	// 	}
	// 	return (object)[]; 
	// }

	public function get_pembelian($config = []){
		extract($config);
		$pk = isset($params['pk']) ? (int)$params['pk']:0;

		$pembelian = (object)[
			'total_tagihan'=>0,
			'total_pembayaran'=>0,
			'sisa_tagihan'=>0
		];
		if($result = $this->db
		->select('*,
					pembayaran.id as id_pembayaran
				')
		->from('pembelian')
		->join('pembayaran','pembayaran.id_pembelian=pembelian.id','left')
		->where(['pembelian.id'=>$pk])->get()){
			return $result->row();
		}
		return $pembelian;
	} 

	// public function datatable($config = array()){		
	// 	extract($config);
	// 	$columns 			= array('`pembayaran`.`tgl_bayar`', '`pemasok`.`nama`', '`pembayaran`.`nomor`', '`pembelian`.`nomor`', '`pembayaran`.`nominal`', '`pembayaran`.`id`');
	// 	$select_total 		= "SELECT COUNT(`pembayaran`.`id`) AS `total` ";
	// 	$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
	// 	$select 			= "
	// 	SELECT SQL_CALC_FOUND_ROWS `pembayaran`.*, 
	// 		`pemasok`.`kode` AS `kode_pemasok`, 
	// 		`pemasok`.`nama` AS `nama_pemasok`,
	// 		 sum(pembayaran.tgl_bayar) AS 'tgl_bayar',
	// 		 sum(pembayaran.nominal) AS 'nominal_gabung',
	// 		`pembayaran`.`gabung_nota` AS `nomor_gabung`,
	// 		 rincian_pembayaran.`metode` AS metode_bayar
	// 	";
	// 	$from 				= "
	// 	FROM rincian_pembayaran 
	// 	    LEFT JOIN  pembayaran ON pembayaran.gabung_nota = rincian_pembayaran.`gabung_nota`
	// 		LEFT JOIN `pemasok` ON `pemasok`.`id`=`pembayaran`.`id_pemasok`
	// 		LEFT JOIN `pembelian` ON `pembelian`.`id`= `pembayaran`.`id_pembelian`
	// 	";
	// 	$where 				= "WHERE `pembayaran`.`id` IS NOT NULL AND `gabung_faktur`=0 and pembayaran.gabung_nota is not null ";
	// 	$group_by 			= "group by rincian_pembayaran.gabung_nota ";
	// 	$having 			= "";
	// 	$order_by 			= "";
	// 	$limit 				= "";

	// 	if( isset($params["search"]["value"]) && !empty($params["search"]["value"]) ) {
	// 		$q		= $this->db->escape_str(strip_tags($params["search"]["value"]));
	// 		$fields = array();
	// 		foreach( $columns AS $col ){
	// 			array_push($fields, "(".$col." LIKE '%".$q."%')");
	// 		}
	// 		$having = " HAVING " . implode(" OR ",$fields) . " "; 
	// 		unset($fields,$col,$q);
	// 	}
	// 	if( isset($params['order'][0]['column']) ){
	// 		$field 	= $columns[$params["order"][0]["column"]];
	// 		$dir 	= strtoupper($this->db->escape_str($params["order"][0]["dir"]));
	// 		$order_by = " ORDER BY " . $field . " " . $dir . " "; 
	// 		unset($field,$dir);
	// 	}
	// 	if ( isset( $params["start"] ) && $params["length"] != '-1' ) {
	// 		$limit = "LIMIT " . $params["start"] . "," . $params["length"];
	// 	}
	// 	if ( isset( $params["filter"]["id_cabang"] )  && !empty($params["filter"]["id_cabang"]) ) {
	// 		$where .= "  AND  pembelian.id_cabang =  ". $params["filter"]["id_cabang"] ." ";
	// 	}
	// 	$totalData 		= $this->db->query($select_total . $from . $where . ";")->row_array();
	// 	$results 		= $this->db->query($select . $from . $where . $group_by . $having . $order_by . $limit . ";")->result_array();
	// 	$totalFiltered 	= $this->db->query($select_filtered . ";")->row_array();
	// 	unset($select_filtered, $select_total, $select, $from, $where, $group_by, $having, $order_by, $limit);
	// 	unset($row,$photo,$status,$contact);
	// 	$data = array();
	// 	foreach($results AS $row){
	// 		array_push($data,$row);
	// 	}
	// 	return array(
	// 		"draw" 				=> intval( isset($params['draw']) ? $params['draw'] : 1 ),
	// 		"recordsTotal" 		=> intval( isset($totalData['total']) ? $totalData['total'] : 0 ),
	// 		"recordsFiltered" 	=> intval( isset($totalFiltered['total']) ? $totalFiltered['total'] : 0 ),
	// 		"data"				=> $data 
	// 	); unset($results,$params,$totalData,$totalFiltered,$data);
	// }
	public function datatable($config = array()){		
		extract($config);
		$columns 			= array('rincian_pembayaran_multi.id', 'rincian_pembayaran_multi.gabung_nota', 'rincian_pembayaran_multi.nominal', 'rincian_pembayaran_multi.metode', 'rincian_pembayaran_multi.tgl_bayar');
		$select_total 		= "SELECT COUNT(`rincian_pembayaran_multi`.`id`) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "
		SELECT SQL_CALC_FOUND_ROWS `rincian_pembayaran_multi`.*, 
			`pemasok`.`kode` AS `kode_pemasok`, 
			`pemasok`.`nama` AS `nama_pemasok`,
			 sum(rincian_pembayaran_multi.tgl_bayar) AS 'tgl_bayar',
			 sum(rincian_pembayaran_multi.nominal) AS 'nominal_gabung',
			`rincian_pembayaran_multi`.`gabung_nota` AS `nomor_gabung`,
			 rincian_pembayaran_multi.`metode` AS metode_bayar
		";
		$from 				= "
		FROM rincian_pembayaran_multi 
			LEFT JOIN `pemasok` ON `pemasok`.`id`=`rincian_pembayaran_multi`.`id_pemasok`
		";
		$where 				= "WHERE rincian_pembayaran_multi.id IS NOT NULL and rincian_pembayaran_multi.gabung_nota is not null ";
		$group_by 			= "group by rincian_pembayaran_multi.gabung_nota ";
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
			$where .= "  AND  rincian_pembayaran_multi.id_cabang =  ". $params["filter"]["id_cabang"] ." ";
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
		$from 			= "FROM `pembayaran` ";
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
		$select_total 	= "SELECT COUNT(DISTINCT(pemasok.`id`)) AS `total` ";
		$select_data	= "SELECT *, pemasok.id as ids ";
		$from 			= "FROM `pemasok` left join pembelian on pembelian.id_pemasok = pemasok.id ";
		
		$where 			= "WHERE pemasok.`id` IS NOT NULL ";

		$term = isset($params['term']) ? $this->db->escape_str($params['term']) : (isset($params['q']) ? $this->db->escape_str($params['q']) : null);
		if(isset($term) && !empty($term)){
			$where .= " AND (pemasok.`id` LIKE '%". $term ."%' OR pemasok.`nama` LIKE '%". $term ."%' OR pemasok.`kode` LIKE '%". $term ."%') ";
		}

		if(isset($params['id_cabang']) && !empty($params['id_cabang'])){
			$where .= " AND (`pembelian`.`id_cabang` = " . $params['id_cabang'] . ") ";
		}

		$group_by 		= "group by pemasok.id  ";
		$order_by 		= "ORDER by pemasok.id ASC ";
		$result_total	= $this->db->query($select_total . $from . $where .  $group_by . ";");
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
	public function select2_akun($params = []){
		extract($params);
		$group = isset($params['metode']) ? strtolower($params['metode']) : '';
		$response = [];
		switch($group){
			case 'giro':
			case 'debit':
				$response = $this->db->get_where('akun', ['induk'=>2])->result();
			break;
			case 'tunai':
			default :
				$response = $this->db->get_where('akun', ['induk'=>1])->result();
		}
		return $response;
	}
	public function select2_giro($config = array()){
		extract($config);

		$row_per_page 	= isset($row_per_page) ? $row_per_page : 10;
		$select_total 	= "SELECT COUNT(DISTINCT(`id`)) AS `total` ";
		$select_data	= "SELECT * ";
		$from 			= "FROM `giro` ";
		$where 			= "WHERE `id` NOT IN (
			SELECT `id_giro` FROM `rincian_pembayaran` WHERE `id_giro` IS NOT NULL OR `id_giro` != 0
		) AND  giro.id_cabang =  ". $params['id_cabang'] ." ";

		$term = isset($params['term']) ? $this->db->escape_str($params['term']) : (isset($params['q']) ? $this->db->escape_str($params['q']) : null);
		if(isset($term) && !empty($term)){
			$where .= " AND (`id` LIKE '%". $term ."%' OR `nomor` LIKE '%". $term ."%') ";
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

	public function select2_pembelian($config = array()){
		extract($config);

		$row_per_page 	= isset($row_per_page) ? $row_per_page : 10;
		$select_total 	= "SELECT COUNT(DISTINCT(`id`)) AS `total` ";
		$select_data	= "SELECT *, `nomor` AS `text` ";
		$from 			= "FROM `pembelian` ";
		$where 			= "WHERE `id` IS NOT NULL AND `sisa_tagihan` > 0  AND (`id_pemasok` = ". (int)$params['id_pemasok']  .") ";
		$term = isset($params['term']) ? $this->db->escape_str($params['term']) : (isset($params['q']) ? $this->db->escape_str($params['q']) : null);
		if(isset($term) && !empty($term)){
			$where .= " AND (`id` LIKE '%". $term ."%' OR `nomor` LIKE '%". $term ."%') ";
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

		extract($params);

		$result = array(
			'status'	=> 'error',
			'message'	=> 'Lengkapi form.',
			'redirect'	=> $this->module['url']
		);

		$data_is_valid = TRUE;

		if( isset($pembayaran['id_pemasok']) && !empty($pembayaran['id_pemasok']) ){
			if( $data_is_valid == TRUE ){
				$result['message'] 		= "Data Gagal disimpan.";
				$this->db->trans_begin();
				extract($pembayaran);

				$refr = [
					'text' 		=> $gabung_nota,
					'link' 		=> $this->module['url'] .'/single/' . $gabung_nota,
					'table'		=> 'pembayaran'
				];

				if(isset($rincian)){
					foreach( $rincian AS $index => $transaction ){
						extract($transaction);
							$id_giro 	= isset($id_giro) && !empty($id_giro) ? $id_giro :'NULL';
							$tgl_giro 	= isset($tgl_giro) && !empty($tgl_giro)  ? "'" . $tgl_giro . "'" :'NULL';
							if($id_giro != "NULL"){
								$this->db->query("
								 	INSERT INTO `rincian_pembayaran_multi` (`id_akun`,`id_giro`,`tgl_giro`,`metode`,`nominal`,`total`,gabung_nota, chek, id_cabang, id_pemasok, tgl_bayar, diskon) 
								 	VALUES ('".$id_akun."',".$id_giro.",".$tgl_giro.",'".$metode."','".$total."','".$total."', '".$gabung_nota."', '1', '".$id_cabang."' ,'".$id_pemasok."',  '".$tgl_bayar."', '".$diskon."');
							");
							}else{
								$this->db->query("
								 	INSERT INTO `rincian_pembayaran_multi` (`id_akun`,`id_giro`,`tgl_giro`,`metode`,`nominal`,`total`,gabung_nota,  id_cabang, id_pemasok, tgl_bayar, diskon) 
								 	VALUES ('".$id_akun."',".$id_giro.",".$tgl_giro.",'".$metode."','".$total."','".$total."', '".$gabung_nota."', '".$id_cabang."' ,'".$id_pemasok."',  '".$tgl_bayar."', '".$diskon."');
							");
							}	
							$id_pembayaran_multi 		= (int)$this->db->insert_id();	
						
						$this->db->query("
							INSERT INTO `jurnal` (`id_akun`, `tgl`, `kredit`, `debit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, metode, id_cabang)
							VALUES ('".$id_akun."','".$tgl_bayar."',0,'".$total."','". $refr['text'] ."','". $refr['link'] ."','". 	$id_pembayaran_multi ."','". $refr['table'] ."', '".$metode."','".$id_cabang."');
						");
						unset($id_giro,$tgl_giro);
					}

			$total_potongan_dan_diskon 	= isset($diskon) ? (int)$diskon : 0;

				if($total_potongan_dan_diskon <> 0){
					$this->db->query("
						INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, id_cabang)
						VALUES ( 9,'".$tgl_bayar."', 0,". $total_potongan_dan_diskon .", '". $refr['text'] ."','". $refr['link'] ."','". $id_pembayaran_multi ."' ,'". $refr['table'] ."','".$id_cabang."');
					");
				}
				}

		

				foreach( $nota AS $index => $akun ){
					$this->db->query("
						INSERT INTO `pembayaran` (`id_pemasok`,`nomor`,`id_pembelian`,`tgl_bayar`, `gabung_nota`) 
							VALUES ('".$id_pemasok."','".$akun['nomor']."','".$akun['id']."','".$tgl_bayar."', '".$gabung_nota."');
					");

					$id_pembayaran 		= (int)$this->db->insert_id();
					
					$hitung = $tambah_diskon / count($nota);
					
					// $bagi_diskon = $value['total'];
					// $this->db->query("UPDATE `pembelian` SET `diskon`= ".$count_pembayaran." WHERE `id`=". $id_pembayaran .";");

					$ref = [
						'text' 		=> $gabung_nota,
						'link' 		=> $this->module['url'] .'/single/' . $id_pembayaran,
						'pk'		=> $akun['id'],
						'table'		=> 'pembayaran'
					];

					if( isset($hitung) && !empty($hitung)){
						$tagihan = $akun['total_rincian'] - ($akun['diskon'] + $hitung);
					}else{
						$tagihan = $akun['total_rincian'] -  $akun['diskon'];
					}
	
					$this->db->query("UPDATE `pembayaran` SET `nominal`=".$tagihan." WHERE `id`=". $id_pembayaran .";");
					$this->db->query("
					UPDATE `pembelian` 
						LEFT JOIN (
							SELECT 
								`rincian_pembelian`.`id_pembelian` AS `id`,
								SUM(`rincian_pembelian`.`total`) AS `total_rincian`
							FROM `rincian_pembelian`
							WHERE `rincian_pembelian`.`id_pembelian`=".$akun['id']."
							GROUP BY `rincian_pembelian`.`id_pembelian`
						) AS `rincian` ON `rincian`.`id`=`pembelian`.`id`
						LEFT JOIN (
							SELECT 
								`pembayaran`.`id_pembelian` AS `id`,
								SUM(`pembayaran`.`nominal`) AS `total_pembayaran`
							FROM `pembayaran`
							WHERE `pembayaran`.`id_pembelian`=".$akun['id']."
							GROUP BY `pembayaran`.`id_pembelian`
						) AS `payment` ON `payment`.`id`=`pembelian`.`id`
					SET 
					    `pembelian`.`bon`     = 2,   
						`pembelian`.`diskon`     = `pembelian`.`diskon` + ".$hitung.",      
						`pembelian`.`total_rincian`     = `rincian`.`total_rincian`,
						`pembelian`.`total_tagihan`     = `rincian`.`total_rincian`-`pembelian`.`diskon`,
						`pembelian`.`total_pembayaran`  = `payment`.`total_pembayaran`,
						`pembelian`.`sisa_tagihan`      = `rincian`.`total_rincian`-`payment`.`total_pembayaran`- `pembelian`.`diskon` 
					WHERE `pembelian`.`id`=".$akun['id'].";
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
		
			
			}
		}

		$current = date("Y-m-d");
		$this->db->set('chek','2');
		$this->db->where('tgl_giro <=',$current);
		$this->db->update('rincian_pembayaran_multi');

		unset($pembayaran);
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
		$deleted = $this->delete_update($params);
		if(isset($deleted['status']) && $deleted['status'] === TRUE){
			$result = $this->insert($params);
		}
		return $result;
		unset($result);
	}

	public function delete_update($params = array()){
		extract($params);

		$result = array(
			'status'	=> 'error',
			'message'	=> 'Please complete data field requirements.'
		);

		if( isset($pk) ){
			$result['message'] 	= "Data couldnt delete.";
			$pembayaran = $this->db->where(['gabung_nota'=>$pk])->get('pembayaran')->result_array();
			$this->db->trans_begin();
			$this->db->query("DELETE FROM `pembayaran`  WHERE `gabung_faktur`=0 AND `gabung_nota`='". $pk  ."';");
			$this->db->query("DELETE FROM `rincian_pembayaran_multi`  WHERE `gabung_nota`='". $pk  ."';");
			$this->db->query("DELETE FROM `jurnal`  	WHERE `ref_table`='pembayaran' AND `ref_text`='". $pk ."';");
			foreach ($pembayaran  as $key => $value) {

			
				if(isset($value['id_pembelian'])){



					$this->db->query("
					UPDATE `pembelian` 
						LEFT JOIN (
							SELECT 
								`rincian_pembelian`.`id_pembelian` AS `id`,
								SUM(`rincian_pembelian`.`total`) AS `total_rincian`
							FROM `rincian_pembelian`
							WHERE `rincian_pembelian`.`id_pembelian`=". $value['id_pembelian'] ."
							GROUP BY `rincian_pembelian`.`id_pembelian`
						) AS `rincian` ON `rincian`.`id`=`pembelian`.`id`
						LEFT JOIN (
							SELECT 
								`pembayaran`.`id_pembelian` AS `id`,
								SUM(`pembayaran`.`nominal`) AS `total_pembayaran`
							FROM `pembayaran`
							WHERE `pembayaran`.`id_pembelian`=". $value['id_pembelian'] ."
							GROUP BY `pembayaran`.`id_pembelian`
						) AS `payment` ON `payment`.`id`=`pembelian`.`id`
					SET 
					    `pembelian`.`bon`     = 2,
						`pembelian`.`total_rincian`     = `rincian`.`total_rincian`,
						`pembelian`.`total_tagihan`     = `rincian`.`total_rincian`-`pembelian`.`diskon`,
						`pembelian`.`total_pembayaran`  = `payment`.`total_pembayaran`,
						`pembelian`.`sisa_tagihan`      = `rincian`.`total_rincian`-`pembelian`.`diskon`-`payment`.`total_pembayaran`
					WHERE `pembelian`.`id`=". $value['id_pembelian'] .";
					");
				}

			}
		

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


	public function delete($params = array()){

		extract($params);

		$result = array(
			'status'	=> 'error',
			'message'	=> 'Please complete data field requirements.'
		);

		if( isset($pk) ){
			$result['message'] 	= "Data couldnt delete.";
			$pembayaran = $this->db->where(['gabung_nota'=>$pk])->get('pembayaran')->result_array();
			$this->db->trans_begin();
			$this->db->query("DELETE FROM `pembayaran`  WHERE `gabung_faktur`=0 AND `gabung_nota`='". $pk  ."';");
				$this->db->query("DELETE FROM `rincian_pembayaran_multi`  WHERE `gabung_nota`='". $pk  ."';");
			$this->db->query("DELETE FROM `jurnal`  	WHERE `ref_table`='pembayaran' AND `ref_text`='". $pk ."';");
			foreach ($pembayaran  as $key => $value) {
				if(isset($value['id_pembelian'])){
	
					$this->db->query("
					UPDATE `pembelian` 
						LEFT JOIN (
							SELECT 
								`rincian_pembelian`.`id_pembelian` AS `id`,
								SUM(`rincian_pembelian`.`total`) AS `total_rincian`
							FROM `rincian_pembelian`
							WHERE `rincian_pembelian`.`id_pembelian`=".  $value['id_pembelian'] ."
							GROUP BY `rincian_pembelian`.`id_pembelian`
						) AS `rincian` ON `rincian`.`id`=`pembelian`.`id`
						LEFT JOIN (
							SELECT 
								`pembayaran`.`id_pembelian` AS `id`,
								SUM(`pembayaran`.`nominal`) AS `total_pembayaran`
							FROM `pembayaran`
							WHERE `pembayaran`.`id_pembelian`=".  $value['id_pembelian'] ."
							GROUP BY `pembayaran`.`id_pembelian`
						) AS `payment` ON `payment`.`id`=`pembelian`.`id`
					SET 
						`pembelian`.`total_rincian`     = `rincian`.`total_rincian`,
						`pembelian`.`total_tagihan`     = `rincian`.`total_rincian`-`pembelian`.`diskon`,
						`pembelian`.`total_pembayaran`  = `payment`.`total_pembayaran`,
						`pembelian`.`sisa_tagihan`      = `rincian`.`total_rincian`-`pembelian`.`diskon`-`payment`.`total_pembayaran`
					WHERE `pembelian`.`id`=". $value['id_pembelian'] .";
					");
				}
	
				if ($this->db->trans_status() === FALSE){
					$result['message'] 	= $this->db->error();
					$this->db->trans_rollback();
				} else {
					$this->db->trans_commit();
					$result['status'] 	= TRUE;
					$result['message'] 	= 'Data telah disimpan.';
				}
			
			}

		
		}
		return $result;
		unset($result);
	}

}