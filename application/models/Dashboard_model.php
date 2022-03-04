<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

	public function __construct(){
		parent::__construct();
	}

	public function cards($cabang){

		$query = "
		SELECT ( SELECT COUNT(`id`) FROM `produk` WHERE id_cabang = $cabang ) AS `barang`, 
       ( SELECT COUNT(pelanggan.`id`) FROM `pelanggan` 
         WHERE pelanggan.id_cabang = $cabang ) AS `pelanggan`, 
       ( SELECT COUNT(pemasok.`id`) 
         FROM `pemasok` 
         WHERE pemasok.id_cabang = $cabang ) AS `pemasok`,
       ( SELECT SUM(total_tagihan) 
         FROM `penjualan` 
         WHERE id_cabang = $cabang 
         AND tgl_nota = CURDATE() ) AS `penjualan`
		";
		return $this->db->query($query)->row();

	}


	public function top_pelanggan($cabang){
		$this->db
			->select("`pelanggan`.`kode` AS `kode`, 
			`pelanggan`.`nama` AS `nama`, 
			pelanggan.alamat AS alamat,
			SUM(penjualan.total_tagihan) AS penjualan ")
			->from("pelanggan")
				->join('penjualan','`penjualan`.`id_pelanggan`=`pelanggan`.`id`', 'left')
			->where("`pelanggan`.`id` IS NOT NULL")
			->where('`penjualan`.`id_cabang`',"$cabang")
			->group_by("pelanggan.id")
			->order_by('penjualan','DESC');

		return $this->db->get()->result();
	}


	public function data_barang_habis($cabang){
		$this->db
			->select("produk.harga_beli as harga_beli,
					  produk.harga_jual as harga_jual,
					 `pemasok`.`kode` AS `kode_pemasok`, 
					 `pemasok`.`nama` AS `nama_pemasok`,
					  produk.`nama` AS nama_produk,
					  SUM(IFNULL(`stok`.`qty`,0)) AS `saldo`")
			->from("produk")
				->join('pemasok','`pemasok`.`id`=`produk`.`id_pemasok`', 'left')
				->join('stok', '`stok`.`id_produk`=`produk`.`id`', 'left')
			->where("`produk`.`id` IS NOT NULL")
			->where('`produk`.`id_cabang`',"$cabang")
			->group_by("produk.id")
			->order_by("produk.nama","ASC")
			->having('SUM(stok.`qty`) =','0');
		return $this->db->get()->result();
	}

	function chart($cabang, $tgl){
		$month		= date("m", strtotime($tgl));
		$query = "SELECT `tgl_nota`, SUM(total_tagihan) AS total_tagihan FROM `penjualan` WHERE `id_cabang` = $cabang AND MONTH(tgl_nota) = $month GROUP BY `tgl_nota`";
		$data = $this->db->query($query)->result();

		return array(
			"draw" 				=> intval( isset($params['draw']) ? $params['draw'] : 1 ),
			"data"				=> $data 
		);
	}

}