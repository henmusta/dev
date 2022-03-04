<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faktur_model extends CI_Model {
	private $module = [
		'name' 	=> 'Pembelian',
		'url'	=> 'pembelian/faktur',
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
	public function single($pk, $satuan){
		echo $satuan;
		$pembelian = (object)[];
		$pembelian = $this->db->select('*,DATE(tgl_buat) AS tgl_buat')->from('pembelian')->where(['id'=>$pk])->get()->row();
		$pembelian->rincian_pembelian = $this->db->query('select rincian_pembelian.*, produk.*, kode_laba.kode AS kode_laba
			FROM `rincian_pembelian` 
			LEFT JOIN `produk` 
			ON `produk`.`id`=`rincian_pembelian`.`id_produk`
			LEFT JOIN `kode_laba` 
			ON `kode_laba`.`laba`=`produk`.`laba` 
			AND kode_laba.`satuan` = "'.$satuan.'"
			WHERE `rincian_pembelian`.`id_pembelian` = "'.$pk.'" AND (kode_laba.satuan IN ("'.$satuan.'") OR kode_laba.satuan IS NULL)
			GROUP BY rincian_pembelian.`id_produk`')->result();
		$pembelian->pemasok = $this->db->from('pemasok')->where(['id'=>$pembelian->id_pemasok])->get()->row();
		$pembelian->pembayaran 			= $this->db->from('pembayaran')->where(['pembayaran.id_pembelian'=>$pembelian->id,'gabung_faktur'=>1])->get()->row();
		$pembelian->rincian_pembayaran 	= $this->db
			->select('
				rincian_pembayaran.*,
				akun.nama AS nama_akun,
				giro.nomor AS nomor_giro
			')
			->from('rincian_pembayaran')
			->join('akun','akun.id=rincian_pembayaran.id_akun','left')
			->join('giro','giro.id=rincian_pembayaran.id_giro','left')
			->join('pembayaran','rincian_pembayaran.`id_pembayaran` = pembayaran.`id`','left')
			->join('pembelian','pembayaran.`id_pembelian` = pembelian.`id`','left')
			->where(array('pembelian.id'=>$pembelian->id))->get()->result();
		return $pembelian;
	}

	public function single1($pk){
		$pembelian = (object)[];
		$pembelian = $this->db->from('pembelian')->where(['id'=>$pk])->get()->row();
		$pembelian->rincian_pembelian = $this->db->select('rincian_pembelian.*, produk.*')->from('rincian_pembelian')
			->join('produk','produk.id=rincian_pembelian.id_produk','left')
			->join('pemasok', 'pemasok.id=produk.id_pemasok', 'left')
			// ->join('kode_laba','kode_laba.laba=produk.laba','left')
			->where(array('rincian_pembelian.id_pembelian'=>$pembelian->id))->get()->result();

		$pembelian->pemasok = $this->db->from('pemasok')->where(['id'=>$pembelian->id_pemasok])->get()->row();

		$pembelian->pembayaran 			= $this->db->from('pembayaran')->where(['pembayaran.id_pembelian'=>$pembelian->id,'gabung_faktur'=>1])->get()->row();
		$pembelian->rincian_pembayaran 	= $this->db
			->select('
				rincian_pembayaran.*,
				akun.nama AS nama_akun,
				giro.nomor AS nomor_giro
			')
			->from('rincian_pembayaran')
			->join('akun','akun.id=rincian_pembayaran.id_akun','left')
			->join('giro','giro.id=rincian_pembayaran.id_giro','left')
			->where(array('rincian_pembayaran.id_pembayaran'=>$pembelian->pembayaran->id))->get()->result();
		return $pembelian;
	}
	
	public function get_kode_pemasok_by_nama($config){
		extract($config);
		$nama = isset($params['nama']) ? $params['nama']: null;
		$result  = ['status'=>'error','kode_pemasok' => ''];
		if($row = $this->db->select('`kode`')->from('pemasok')->where(array('nama'=>$nama))->get()->row()){
			if(isset($row->kode)){
				$result  = ['status'=>'success','kode_pemasok' => $row->kode];
			}
		}
		return $result;
	}

	public function datatable($config = array()){
		
		extract($config);
	    $columns 			= array('n.id','n.tgl_buat','tgl_nota','nama_pemasok', 'nomor', 'total_rincian', 'diskon', 'total_tagihan', 'total_pembayaran', 'sisa_tagihan', 'id');
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
		)  AND  giro.id_cabang =  ". $params['id_cabang'] ." ";
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




	public function select2_pemasok($config = array()){
		extract($config);

		$row_per_page 	= isset($row_per_page) ? $row_per_page : 10;
		$select_total 	= "SELECT COUNT(DISTINCT(pemasok.`id`)) AS `total` ";
		$select_data	= "SELECT * ";
		$from 			= "FROM `pemasok` left join pembelian on pembelian.id_pemasok = pemasok.id ";
		
		$where 			= "WHERE pemasok.`id` IS NOT NULL ";

		$term = isset($params['term']) ? $this->db->escape_str($params['term']) : (isset($params['q']) ? $this->db->escape_str($params['q']) : null);
		if(isset($term) && !empty($term)){
			$where .= " AND (pemasok.`id` LIKE '%". $term ."%' OR pemasok.`nama` LIKE '%". $term ."%' OR pemasok.`kode` LIKE '%". $term ."%') ";
		}

		if(isset($params['id_cabang']) && !empty($params['id_cabang'])){
			$where .= " AND (`pemasok`.`id_cabang` = " . $params['id_cabang'] . ") ";
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



	public function autocomplete_pemasok($config = array()){
		$this->db->from('pemasok')->like('nama',$config['q']);
		return $this->db->get()->result_array();
	}
	public function autocomplete_produk($config = array()){
		$this->db->from('produk AS p')
			->select('p.*')
			->join('pemasok AS s','s.id=p.id_pemasok','inner')
				->where('s.kode',$config['kode-pemasok'])
				->like('p.nama',$config['q']);
		return $this->db->get()->result_array();
	}
	/* CRUD */
	private function insert_pemasok($data = []){
		if( isset($data['kode']) && !empty($data['kode']) && isset($data['nama']) && !empty($data['nama']) ){
			$pemasok = $this->db->get_where('pemasok',['kode'=>$data['kode'], 'nama' => $data['nama']])->row();
			if(isset($pemasok->id) && !empty($pemasok->id)){
				$id_pemasok = $pemasok->id;
			} else {
				$data['kode'] = trim(strip_tags($data['kode']));
				$data['nama'] = trim(strip_tags($data['nama']));
				if( $this->db->insert('pemasok', $data) ){
					$id_pemasok = $this->db->insert_id();
				}
			}
			return $id_pemasok;
		}
	}
	private function insert_produk($data = []){
		if( isset($data['nama']) && !empty($data['nama']) ){
			$data['nama'] 		= trim(strip_tags($data['nama']));
			$data['id_pemasok'] = (int)$data['id_pemasok'];
			$produk = $this->db->get_where('produk',['nama'=>$data['nama'],'id_pemasok'=>$data['id_pemasok']])->row();
			if(isset($produk->id) && !empty($produk->id)){
				$id_produk = $produk->id;
			} else {
				if( $this->db->insert('produk', $data) ){
					$id_produk = $this->db->insert_id();
				}
			}
			return $id_produk;
		}
	}
	public function insert($params){
		$result = array(
			'status'	=> 'error',
			'message'	=> 'Lengkapi form.',
			'redirect'	=> $this->module['url']
		);

		$data_is_valid = TRUE;
		extract($params);
		$pembelian['id_pemasok'] 	= null;

		if( isset($pemasok['kode']) && !empty($pemasok['kode']) && isset($pemasok['nama']) && !empty($pemasok['nama']) ){
			$pembelian['id_pemasok'] = $this->insert_pemasok($pemasok);
			if( isset($rincian) && is_array($rincian) && count($rincian) > 0 ){
				foreach( $rincian AS $index => $item ){
					$produk[$index]['id_pemasok'] = $pembelian['id_pemasok'];
					$produk[$index]['harga_beli'] = $item['harga'];
					$produk[$index]['id_cabang'] = $item['id_cabang'];
					if(isset($pk) && !empty($pk)){
						foreach($asu as $item => $val){
							$this->db->query("
							UPDATE `produk` SET 
								`id_pemasok`='".$produk[$index]['id_pemasok']."'
							WHERE `id`=".$val['pd_id'].";");
						}
						$rincian[$index]['id_produk'] = $val['pd_id'];
					}else{
						if( isset($produk[$index]['nama']) && !empty($produk[$index]['nama']) ){
							$rincian[$index]['id_produk'] = $this->insert_produk($produk[$index]);
						}
					}
					// if( isset($produk[$index]['nama']) && !empty($produk[$index]['nama']) ){
					// 	$rincian[$index]['id_produk'] = $this->insert_produk($produk[$index]);
					// }
					
				}
			}
		}

		if( isset($pembelian['tgl_nota']) && !empty($pembelian['tgl_nota']) && isset($pembelian['nomor']) && !empty($pembelian['nomor']) ){
			if( isset($pembelian['nomor']) && !empty($pembelian['nomor']) ){
				$pembelian['nomor'] 	= trim(strip_tags($pembelian['nomor']));
				$data_is_valid 	= $this->is_unique_field('nomor', $pembelian['nomor']);
				if( $data_is_valid == FALSE ){
					$result['message'] 	= "Nomor nota pembelian sudah ada.";
				}
			}
		}

		if($data_is_valid === TRUE){
			$this->db->trans_begin();
			extract($pembelian);

			/* Record Data Pembelian */
			$this->db->query("
				INSERT INTO `pembelian` (`id_pemasok`,`tgl_nota`,`tgl_buat`,`nomor`,`diskon`,`id_cabang`, status_ro) 
					VALUES ('".$id_pemasok."','".$tgl_nota."','".$tgl_buat."','".$nomor. "','".$diskon."','".$id_cabang."', '".$status_ro."');
			");
			/* Get ID Pembelian */
			$id_pembelian = (int)$this->db->insert_id();

			/* Build Data Reference */
			$ref = [
				'text' 		=> $nomor,
				'link' 		=> $this->module['url'] .'/single/' . $id_pembelian,
				'pk'		=> $id_pembelian,
				'table'		=> 'pembelian'
			];

			$total_rincian = 0;

			/* Record Data Barang */
			foreach( $rincian AS $index => $item ){
				$item['total'] = $item['harga'] * $item['qty'];

				/* Record Data Barang */
				$this->db->query("
					INSERT INTO `rincian_pembelian` (`id_pembelian`,`id_produk`,`qty`,`harga`,`total`) 
						VALUES ('".$id_pembelian."','".$item['id_produk']."','".$item['qty']."','".$item['harga']."','".$item['total']."');
				");

				if($pembelian['status_ro'] == '1'){
					$sisa_qty = $item['qty'] - $item['qty'];
				   $this->db->query("
					INSERT `stok` (`id_produk`, `tgl`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, `transaksi`, `harga`, `qty`, status_ro)
					VALUES ('".$item['id_produk']."','".$tgl_buat."','". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."','pembelian','pembelian','".$item['harga']."',".$item['qty'].", '".$pembelian['status_ro']."');
				  ");
				  $this->db->query("
					INSERT `receive_order` (`pembelian_id`, `id_produk`, `tgl`, `qty`, qty_diterima, sisa_qty, status_ro)
					VALUES ('".$id_pembelian."','".$item['id_produk']."','".$tgl_buat."',".$item['qty'].",".$item['qty'].", '0','".$pembelian['status_ro']."');
				  ");
				}else{
				   $this->db->query("
					INSERT `receive_order` (`pembelian_id`, `id_produk`, `tgl`, `qty`, qty_diterima, status_ro)
					VALUES ('".$id_pembelian."','".$item['id_produk']."','".$tgl_buat."',".$item['qty'].",'0',".$item['qty'].",'".$pembelian['status_ro']."');
				  ");
				}
			
				/* Update data Produk */
				$this->db->query("
					UPDATE `produk` SET 
						`harga_beli`='".$item['harga']."',
						`laba`=`harga_jual`-'".$item['harga']."'
					WHERE `id`=".$item['id_produk'].";");
				$total_rincian += $item['total'];
			}

			/* Tambah Jurnal Pembelian */
			$this->db->query("
				INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, id_cabang)
					VALUES ( 10,'".$tgl_buat."',". $total_rincian .",0,'". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."' ,'pembelian','".$id_cabang."');
			");

			$total_potongan_dan_diskon 	= isset($diskon) ? (int)$diskon : 0;
			$total_sisa_hutang 			= $total_rincian - $total_potongan_dan_diskon;

			$this->db->query("
				INSERT INTO `pembayaran` (`id_pembelian`, `id_pemasok`,`tgl_bayar`,`nomor`,`gabung_faktur`) 
				VALUES ('".$id_pembelian."','".$id_pemasok."','".$tgl_nota."','".$nomor. "',1);
			");

			$id_pembayaran 		= (int)$this->db->insert_id();
			$total_pembayaran 	= 0;
			if(isset($pembayaran)){
				foreach( $pembayaran AS $index => $transaction ){
					extract($transaction);
					$total_pembayaran 	+= $total;
					$total_sisa_hutang 	-= $total;
					$total_potongan_dan_diskon += $potongan;
					$id_giro 	= isset($id_giro) && !empty($id_giro) ? $id_giro :'NULL';
					$tgl_giro 	= isset($tgl_giro) && !empty($tgl_giro)  ? "'" . $tgl_giro . "'" :'NULL';
					if($id_giro != "NULL"){
						$this->db->query("
						INSERT INTO `rincian_pembayaran` (`id_pembayaran`,`id_akun`,`id_giro`,`tgl_giro`,`metode`,`nominal`,`potongan`,`total`, chek) 
							VALUES ('".$id_pembayaran."','".$id_akun."',".$id_giro.",".$tgl_giro.",'".$metode."','".$nominal."','".$potongan."','".$total."', '1');
					");
					}else{
						$this->db->query("
						INSERT INTO `rincian_pembayaran` (`id_pembayaran`,`id_akun`,`id_giro`,`tgl_giro`,`metode`,`nominal`,`potongan`,`total`) 
							VALUES ('".$id_pembayaran."','".$id_akun."',".$id_giro.",".$tgl_giro.",'".$metode."','".$nominal."','".$potongan."','".$total."');
					");
					}
					 $this->db->query("
						INSERT INTO `jurnal` (`id_akun`, `tgl`, `kredit`, `debit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, metode, id_cabang)
							VALUES ('".$id_akun."','".$tgl_buat."',0,'".$nominal."','". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."','". $ref['table'] ."', '".$metode."','".$id_cabang."');
					");
					unset($id_giro,$tgl_giro);
				}
			}

			/* Tambah Jurnal Pendapatan Potongan Pembelian */
				if($total_potongan_dan_diskon <> 0){
					$this->db->query("
						INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, id_cabang)
						VALUES ( 9,'".$tgl_buat."', 0,". $total_potongan_dan_diskon .", '". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."' ,'". $ref['table'] ."','".$id_cabang."');
					");
				}

			if($total_sisa_hutang > 0){
				/* Tambah Jurnal Hutang Pembelian */
				$this->db->query("
					INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, id_cabang)
						VALUES ( 4,'".$tgl_buat."', 0,". $total_sisa_hutang .", '". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."' ,'". $ref['table'] ."','".$id_cabang."');
				");
				$this->db->query("UPDATE `pembelian` SET `bon`='1' WHERE `id`=". $id_pembelian .";");
			}

			$this->db->query("UPDATE `pembayaran` SET `nominal`=". $total_pembayaran ." WHERE `id`=". $id_pembayaran .";");

			$this->db->query("
			UPDATE `pembelian` 
			    LEFT JOIN (
			        SELECT 
			            `rincian_pembelian`.`id_pembelian` AS `id`,
			            SUM(`rincian_pembelian`.`total`) AS `total_rincian`
			        FROM `rincian_pembelian`
			        WHERE `rincian_pembelian`.`id_pembelian`=". $id_pembelian ."
			        GROUP BY `rincian_pembelian`.`id_pembelian`
			    ) AS `rincian` ON `rincian`.`id`=`pembelian`.`id`
			    LEFT JOIN (
			        SELECT 
			            `pembayaran`.`id_pembelian` AS `id`,
			            SUM(`pembayaran`.`nominal`) AS `total_pembayaran`
			        FROM `pembayaran`
			        WHERE `pembayaran`.`id_pembelian`=". $id_pembelian ."
			        GROUP BY `pembayaran`.`id_pembelian`
			    ) AS `payment` ON `payment`.`id`=`pembelian`.`id`
			SET 
			    `pembelian`.`total_rincian`     = `rincian`.`total_rincian`,
			    `pembelian`.`total_tagihan`     = `rincian`.`total_rincian`-`pembelian`.`diskon`,
			    `pembelian`.`total_pembayaran`  = `payment`.`total_pembayaran`,
			    `pembelian`.`sisa_tagihan`      = `rincian`.`total_rincian`-`pembelian`.`diskon`-`payment`.`total_pembayaran`
			WHERE `pembelian`.`id`=". $id_pembelian .";
			");

			if ($this->db->trans_status() === FALSE){
				$result['message'] 	= $this->db->error();
				$this->db->trans_rollback();
			} else {
				$this->db->trans_commit();
				$result['status'] 	= TRUE;
				$result['message'] 	= 'Data telah disimpan.';
				$result['pk'] 		= $id_pembelian;
			}
		}

		$current = date("Y-m-d");

		$this->db->set('chek','2');
		$this->db->where('tgl_giro <=',$current);
		$this->db->update('rincian_pembayaran');


		// $current = date("Y-m-d");
		// $this->db->set('chek','1');
		// $this->db->where('tgl_giro',$current);
		// $this->db->update('rincian_pembayaran');

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

		

		$id_pembayaran = [];
		if(isset($pk) && !empty($pk)){

			$cicilan = $this->db->select('id')->from('pembayaran')->where('id_pembelian',$pk)->get()->result_array();
			$id_pembayaran = array_column($cicilan, 'id');

			$deleted = $this->delete($params);
			if(isset($deleted['status']) && $deleted['status'] === TRUE){
				$result = $this->insert($params);
				if( isset($result['status']) && $result['status'] === TRUE && isset($result['pk']) && !empty($result['pk']) ){
					$id_pembelian = $result['pk'];
					if(count($id_pembayaran) > 0){
						$this->db->where('gabung_faktur',0)->where_in('id', $id_pembayaran);
						if( $this->db->update('pembayaran', ['id_pembelian' => $id_pembelian]) ){
							$this->db->query("
							UPDATE `pembelian` 
							    LEFT JOIN (
							        SELECT 
							            `rincian_pembelian`.`id_pembelian` AS `id`,
							            SUM(`rincian_pembelian`.`total`) AS `total_rincian`
							        FROM `rincian_pembelian`
							        WHERE `rincian_pembelian`.`id_pembelian`=". $id_pembelian ."
							        GROUP BY `rincian_pembelian`.`id_pembelian`
							    ) AS `rincian` ON `rincian`.`id`=`pembelian`.`id`
							    LEFT JOIN (
							        SELECT 
							            `pembayaran`.`id_pembelian` AS `id`,
							            SUM(`pembayaran`.`nominal`) AS `total_pembayaran`
							        FROM `pembayaran`
							        WHERE `pembayaran`.`id_pembelian`=". $id_pembelian ."
							        GROUP BY `pembayaran`.`id_pembelian`
							    ) AS `payment` ON `payment`.`id`=`pembelian`.`id`
							SET 
							    `pembelian`.`total_rincian`     = `rincian`.`total_rincian`,
							    `pembelian`.`total_tagihan`     = `rincian`.`total_rincian`-`pembelian`.`diskon`,
							    `pembelian`.`total_pembayaran`  = `payment`.`total_pembayaran`,
							    `pembelian`.`sisa_tagihan`      = `rincian`.`total_rincian`-`pembelian`.`diskon`-`payment`.`total_pembayaran`
							WHERE `pembelian`.`id`=". $id_pembelian .";
							");
						}
					}
				}
			}
		}
		return $result;
		unset($result);
	}

	// function kode_laba($laba = 0, $satuan){
	// 	$kode = [
	// 		1 => 'C',
	// 		2 => 'I',
	// 		3 => 'N',
	// 		4 => 'T',
	// 		5 => 'A',
	// 		6 => 'R',
	// 		7 => 'O',
	// 		8 => 'S',
	// 		9 => 'U',
	// 		0 => 'L'
	// 	];
	// 	$satuan     = strtolower(trim($satuan));
	// 	$division   = in_array($satuan, ['lusin', 'kodi']) && $satuan == 'kodi' ? 20 : 12;
	// 	$laba       = ($laba / 1000) * $division;
	// 	$keys       = str_split($laba);
	
	// 	$results = '';
	// 	foreach($keys AS $key){
	// 		$results .= strtoupper($kode[$key]);
	// 	}
	// 	return $results;
	// }


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
			(isset($pk) && !empty($pk))
		){
			if( $data_is_valid == TRUE ){
				if( is_array($produk) && count($produk) > 0 ){
					// $result['message'] 	= "Harga Jual Telah Diubah.";
					$kode = [
						1 => 'C',
						2 => 'I',
						3 => 'N',
						4 => 'T',
						5 => 'o',
						6 => 'R',
						7 => 'O',
						8 => 'S',
						9 => 'U',
						0 => 'L'
					];
					$satuan     = strtolower(trim($satuan));
					$division   = in_array($satuan, ['lusin', 'kodi']) && $satuan == 'kodi' ? 20 : 12;
					$laba       = ($produk['laba'] / 1000) * $division;
					$round      = round($laba);
					$keys       = str_split($round);
					$results = '';
					foreach($keys AS $key){
						$results .= strtoupper($kode[$key]);
					}
					// return $results;
					$cek_kode = $this->db->select('COUNT(`id`) AS `total`, kode AS laba')->from('kode_laba')->where('laba =', $produk['laba'])->get()->row();
					if( $this->db->update('produk', $produk, array('id'=>$pk)) ){
						$kode_laba = $this->db->get_where('kode_laba',['laba'=>$produk['laba'], 'satuan'=>$satuan])->row();
						if($cek_kode->total <= 3){
							$result['status'] 	= TRUE;
							$result['message'] 	= "kode laba telah di simpan";
					    	$result['kode_laba'] 	= isset($kode_laba->kode) ? $kode_laba->kode: '';
							$this->db->insert('kode_laba',  array('kode'=>$results, 'laba'=>$produk['laba'], 'satuan'=>$satuan, 'id_cabang'=>$id_cabang));
						}
						else{
							$result['status'] 	= TRUE;
							$result['message'] 	= 'data telah disimpan';
							$result['kode_laba'] 	= isset($kode_laba->kode) ? $kode_laba->kode: '';
							}
							
						}
				}
			}
		}
		unset($produk);
		// unset($results);
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

			$this->db->query("DELETE FROM `pembayaran`  WHERE `gabung_faktur`=1 AND `id_pembelian`='". $pk ."';");
			$this->db->query("DELETE FROM `pembelian` 	WHERE `id`='". $pk ."'");
			$this->db->query("DELETE FROM `jurnal`  	WHERE `ref_table`='pembelian' AND `ref_pk`='". $pk ."';");
			$this->db->query("DELETE FROM `stok`  		WHERE `ref_table`='pembelian' AND `ref_pk`='". $pk ."';");

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