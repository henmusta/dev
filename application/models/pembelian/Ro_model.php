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
		return $pembelian;
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


	public function update($params){
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
				INSERT INTO `pembelian` (`id_pemasok`,`tgl_nota`,`tgl_buat`,`nomor`,`diskon`,`id_cabang`) 
					VALUES ('".$id_pemasok."','".$tgl_nota."','".$tgl_buat."','".$nomor. "','".$diskon."','".$id_cabang."');
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

				/* Record Data Stok Barang */
				$this->db->query("
					INSERT `stok` (`id_produk`, `tgl`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, `transaksi`, `harga`, `qty`)
					VALUES ('".$item['id_produk']."','".$tgl_buat."','". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."','pembelian','pembelian','".$item['harga']."',".$item['qty'].");
				");

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

		unset($pembelian);
		return $result;
		unset($result);
	}
	
}