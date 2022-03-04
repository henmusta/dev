<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pelunasan_model extends CI_Model {
	private $module = [
		'name' 	=> 'Pelunasan Penjualan',
		'url'	=> 'penjualan/pelunasan',
	];
	public function __construct(){
		parent::__construct();
	}
	private function is_unique_field($column_name, $value, $pk=NULL){
		$query = "SELECT COUNT(`id`) AS `total` FROM `pelunasan` WHERE `". $column_name ."`='". $this->db->escape_str($value) ."' ";
		if(!empty($pk)){
			$query .= " AND `id`!='" . $pk ."'";
		}
		$result = $this->db->query($query)->row();
		return isset($result->total) && $result->total > 0 ? FALSE : TRUE;
	}
	public function single($pk){
		$row = $this->db->get_where('pelunasan',array('id'=>$pk))->row();
		if(isset($row->id) && !empty($row->id) ){
			$row->penjualan 	= $this->db->get_where('penjualan',array('id'=>$row->id_penjualan))->row();
			$row->pelanggan 	= $this->db->get_where('pelanggan',array('id'=>$row->id_pelanggan))->row();
			$row->nota =  $this->db->select('*, penjualan.tgl_nota as tgl_input, pelanggan.nama as pelanggan, penjualan.total_tagihan as total, penjualan.total_pelunasan, penjualan.sisa_tagihan, penjualan.nomor as nota, pelunasan.nomor as nota_pelunasan')
			->from('pelunasan')
			->join('penjualan','penjualan.id=pelunasan.id_penjualan','left')
			->join('rincian_pelunasan','pelunasan.id=rincian_pelunasan.id_pelunasan','left')
			->join('pelanggan','pelanggan.id=penjualan.id_pelanggan','left')
			->group_by('penjualan.id')
			->where(array('pelunasan.id_penjualan'=>$row->penjualan->id))->get()->result();
			$row->rincian 	= $this->db
				->select('
					rincian_pelunasan.*,
					akun.nama AS nama_akun,
					giro.nomor AS nomor_giro
				')
				->from('rincian_pelunasan')
				->join('akun','akun.id=rincian_pelunasan.id_akun','left')
				->join('giro','giro.id=rincian_pelunasan.id_giro','left')
				->where(['rincian_pelunasan.id_pelunasan'=>$row->id])->get()->result();
			return $row;
		}
		return (object)[]; 
	}
	public function get_penjualan($config = []){
		extract($config);
		$pk = isset($params['pk']) ? (int)$params['pk']:0;

		$penjualan = (object)[
			'total_tagihan'=>0,
			'total_pelunasan'=>0,
			'sisa_tagihan'=>0
		];
		if($result = $this->db->from('penjualan')->where(['id'=>$pk])->get()){
			return $result->row();
		}
		return $penjualan;
	} 
	public function datatable($config = array()){
		
		extract($config);
		$columns 			= array('`pelunasan`.`tgl_bayar`', '`pelanggan`.`nama`', '`pelunasan`.`nomor`', '`penjualan`.`nomor`', '`pelunasan`.`nominal`', '`pelunasan`.`id`');

		$select_total 		= "SELECT COUNT(`pelunasan`.`id`) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "
		SELECT SQL_CALC_FOUND_ROWS `pelunasan`.*, 
			`pelanggan`.`kode` AS `kode_pelanggan`, 
			`pelanggan`.`nama` AS `nama_pelanggan`,
			`penjualan`.`nomor` AS `nomor_penjualan`
		";
		$from 				= "
		FROM `pelunasan` 
			LEFT JOIN `pelanggan` ON `pelanggan`.`id`=`pelunasan`.`id_pelanggan`
			LEFT JOIN `penjualan` ON `penjualan`.`id`= `pelunasan`.`id_penjualan`
		";
		$where 				= "WHERE `pelunasan`.`id` IS NOT NULL AND `gabung_faktur`=0 ";
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
			$where .= "  AND  penjualan.id_cabang =  ". $params["filter"]["id_cabang"] ." ";
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
		$from 			= "FROM `pelunasan` ";
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
	public function select2_pelanggan($config = array()){
		extract($config);

		$row_per_page 	= isset($row_per_page) ? $row_per_page : 10;
		$select_total 	= "SELECT COUNT(DISTINCT(pelanggan.`id`)) AS `total` ";
		$select_data	= "SELECT pelanggan.`id`, pelanggan.`nama` AS nama, pelanggan.`alamat` AS alamat ";
		$from 			= "FROM `pelanggan` left join penjualan on penjualan.id_pelanggan = pelanggan.id ";
		
		$where 			= "WHERE pelanggan.`id` IS NOT NULL ";

		$term = isset($params['term']) ? $this->db->escape_str($params['term']) : (isset($params['q']) ? $this->db->escape_str($params['q']) : null);
		if(isset($term) && !empty($term)){
			$where .= " AND (pelanggan.`id` LIKE '%". $term ."%' OR pelanggan.`nama` LIKE '%". $term ."%' OR pelanggan.`alamat` LIKE '%". $term ."%') ";
		}

		if(isset($params['id_cabang']) && !empty($params['id_cabang'])){
			$where .= " AND (`penjualan`.`id_cabang` = " . $params['id_cabang'] . ") ";
		}

		$group_by 		= "group by pelanggan.id  ";
		$order_by 		= "ORDER by pelanggan.id ASC ";
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
			SELECT `id_giro` FROM `rincian_pelunasan` WHERE `id_giro` IS NOT NULL OR `id_giro` != 0
		) ";

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
	public function select2_penjualan($config = array()){
		extract($config);

		$row_per_page 	= isset($row_per_page) ? $row_per_page : 10;
		$select_total 	= "SELECT COUNT(DISTINCT(`id`)) AS `total` ";
		$select_data	= "SELECT *, `nomor` AS `text` ";
		$from 			= "FROM `penjualan` ";
		$where 			= "WHERE `id` IS NOT NULL AND `sisa_tagihan` > 0 ";
		if(isset($params['id_pelanggan']) && !empty($params['id_pelanggan'])){
			$where .= " AND (`id_pelanggan` = ". (int)$params['id_pelanggan']  .") ";
		}
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

		if( isset($pelunasan['nomor']) && !empty($pelunasan['nomor']) ){
			
			if( isset($pelunasan['nomor']) && !empty($pelunasan['nomor']) ){
				$pelunasan['nomor'] 	= trim(strip_tags($pelunasan['nomor']));
				$data_is_valid 	= $this->is_unique_field('nomor', $pelunasan['nomor']);
				if($data_is_valid == FALSE){
					$result['message'] 	= "Nomor sudah digunakan.";
				}
			}

			if( $data_is_valid == TRUE ){
				$result['message'] 		= "Data Gagal disimpan.";
				
				$this->db->trans_begin();
				extract($pelunasan);
				$this->db->query("
					INSERT INTO `pelunasan` (`id_pelanggan`,`id_penjualan`,`tgl_bayar`,`nomor`) 
					VALUES ('".$id_pelanggan."','".$id_penjualan."','".$tgl_bayar."','".$nomor. "');
				");

				$id_pelunasan 		= (int)$this->db->insert_id();

				/* Build Data Reference */
				$ref = [
					'text' 		=> $nomor,
					'link' 		=> $this->module['url'] .'/single/' . $id_pelunasan,
					'pk'		=> $id_pelunasan,
					'table'		=> 'pelunasan'
				];

				$total_potongan = $total_pelunasan = 0;

				if(isset($rincian)){
					foreach( $rincian AS $index => $transaction ){
						extract($transaction);
						$total_pelunasan 	+= $total;
						$total_potongan 	+= $potongan;
						$id_giro 	= isset($id_giro) && !empty($id_giro) ? $id_giro :'NULL';
						$tgl_giro 	= isset($tgl_giro) && !empty($tgl_giro)  ? "'" . $tgl_giro . "'" :'NULL';
						$this->db->query("
							INSERT INTO `rincian_pelunasan` (`id_pelunasan`,`id_akun`,`id_giro`,`tgl_giro`,`metode`,`nominal`,`potongan`,`total`) 
							VALUES ('".$id_pelunasan."','".$id_akun."',".$id_giro.",".$tgl_giro.",'".$metode."','".$nominal."','".$potongan."','".$total."');
						");
						$this->db->query("
							INSERT INTO `jurnal` (`id_akun`, `tgl`, `kredit`, `debit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, metode, id_cabang)
							VALUES ('".$id_akun."','".$tgl_bayar."', '".$nominal."', 0,'". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."','". $ref['table'] ."', '".$metode."','".$id_cabang."');
						");
						unset($id_giro,$tgl_giro);
					}
				}

				/* Tambah Jurnal Pendapatan Potongan penjualan */
				if($total_potongan <> 0){	
					$this->db->query("
						INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, id_cabang)
						VALUES ( 11,'".$tgl_bayar."', 0,". $total_potongan .", '". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."' ,'". $ref['table'] ."','".$id_cabang."');
					");
				}

				$this->db->query("
					INSERT INTO `jurnal` (`id_akun`, `tgl`, `kredit`, `debit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, id_cabang)
					VALUES ( 3,'".$tgl_bayar."', 0, ". $total_pelunasan .", '". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."' ,'". $ref['table'] ."','".$id_cabang."');
				");

				$this->db->query("UPDATE `pelunasan` SET `nominal`=". $total_pelunasan ." WHERE `id`=". $id_pelunasan .";");
				$this->db->query("
				UPDATE `penjualan` 
				    LEFT JOIN (
				        SELECT 
				            `rincian_penjualan`.`id_penjualan` AS `id`,
				            SUM(`rincian_penjualan`.`total`) AS `total_rincian`
				        FROM `rincian_penjualan`
				        WHERE `rincian_penjualan`.`id_penjualan`=". $id_penjualan ."
				        GROUP BY `rincian_penjualan`.`id_penjualan`
				    ) AS `rincian` ON `rincian`.`id`=`penjualan`.`id`
				    LEFT JOIN (
				        SELECT 
				            `pelunasan`.`id_penjualan` AS `id`,
				            SUM(`pelunasan`.`nominal`) AS `total_pelunasan`
				        FROM `pelunasan`
				        WHERE `pelunasan`.`id_penjualan`=". $id_penjualan ."
				        GROUP BY `pelunasan`.`id_penjualan`
				    ) AS `payment` ON `payment`.`id`=`penjualan`.`id`
				SET 
				    `penjualan`.`total_rincian`     = `rincian`.`total_rincian`,
				    `penjualan`.`total_tagihan`     = `rincian`.`total_rincian`-`penjualan`.`diskon`,
				    `penjualan`.`total_pelunasan`  = `payment`.`total_pelunasan`,
				    `penjualan`.`sisa_tagihan`      = `rincian`.`total_rincian`-`penjualan`.`diskon`-`payment`.`total_pelunasan`
				WHERE `penjualan`.`id`=". $id_penjualan .";
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
		unset($pelunasan);
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
	public function delete($params = array()){

		extract($params);

		$result = array(
			'status'	=> 'error',
			'message'	=> 'Please complete data field requirements.'
		);

		if( isset($pk) ){
			$result['message'] 	= "Data couldnt delete.";
			$pelunasan = $this->db->where(['id'=>$pk])->get('pelunasan')->row();

			$this->db->trans_begin();
			$this->db->query("DELETE FROM `pelunasan`  WHERE `gabung_faktur`=0 AND `id`='". $pk ."';");
			$this->db->query("DELETE FROM `jurnal`  	WHERE `ref_table`='pelunasan' AND `ref_pk`='". $pk ."';");
			if(isset($pelunasan->id_penjualan)){

				$this->db->query("
				UPDATE `penjualan` 
				    LEFT JOIN (
				        SELECT 
				            `rincian_penjualan`.`id_penjualan` AS `id`,
				            SUM(`rincian_penjualan`.`total`) AS `total_rincian`
				        FROM `rincian_penjualan`
				        WHERE `rincian_penjualan`.`id_penjualan`=". $pelunasan->id_penjualan ."
				        GROUP BY `rincian_penjualan`.`id_penjualan`
				    ) AS `rincian` ON `rincian`.`id`=`penjualan`.`id`
				    LEFT JOIN (
				        SELECT 
				            `pelunasan`.`id_penjualan` AS `id`,
				            SUM(`pelunasan`.`nominal`) AS `total_pelunasan`
				        FROM `pelunasan`
				        WHERE `pelunasan`.`id_penjualan`=". $pelunasan->id_penjualan ."
				        GROUP BY `pelunasan`.`id_penjualan`
				    ) AS `payment` ON `payment`.`id`=`penjualan`.`id`
				SET 
				    `penjualan`.`total_rincian`     = `rincian`.`total_rincian`,
				    `penjualan`.`total_tagihan`     = `rincian`.`total_rincian`-`penjualan`.`diskon`,
				    `penjualan`.`total_pelunasan`  = `payment`.`total_pelunasan`,
				    `penjualan`.`sisa_tagihan`      = `rincian`.`total_rincian`-`penjualan`.`diskon`-`payment`.`total_pelunasan`
				WHERE `penjualan`.`id`=". $pelunasan->id_penjualan .";
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

		return $result;
		unset($result);
	}

}