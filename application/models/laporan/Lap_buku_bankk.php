<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lap_buku_bank extends CI_Model {
	private $module = [
		'name' 	=> 'laporan buku-bank',
		'url'	=> 'laporan/laporan-buku-bank',
	];
	public function __construct(){
		parent::__construct();
	}
	public function buku_bank($start_date, $id_cabang, $end_date)
	{
		$this->db->query("SET @saldo = 0");
		// $this->db->query("SET @saldo_awal = '0'");
		$saldo = $this->db->query(
				" SELECT 
				`jurnal`.`tgl` AS tgl,
				CASE
				  WHEN `jurnal`.`metode` IS NOT NULL 
				  THEN CONCAT(
					`akun`.`nama`,
					' ',
					`jurnal`.`metode`
				  ) 
				  ELSE CONCAT(
					`akun`.`nama`,
					' ',
					`jurnal`.`ref_table`
				  ) 
				END AS keterangan,
				CONCAT(
					COALESCE(`pemasok`.`nama`, '')
				) AS uraian,
				CASE
				  WHEN `k1`.`nomor` IS NOT NULL 
				  THEN CONCAT(
					`k1`.`nomor`
				  ) 
				  ELSE CONCAT(
					`k2`.`nomor`
				  ) 
				END AS nogiro,
				akun.id AS akun_id,
				akun.`induk` AS induk,
				rincian_pembayaran.`chek` AS cekbeli,
				rincian_pelunasan.`chek` AS cekjual,
				jurnal.`metode` AS metode,
				jurnal.`debit` AS total_debit,
				jurnal.`kredit` AS total_kredit,
				@saldo_awal := (
				  IFNULL(`jurnal`.`kredit`, 0) - IFNULL(`jurnal`.`debit`, 0)
				) AS `saldo` 
			  FROM
				jurnal 
				LEFT JOIN akun 
				  ON akun.`id` = jurnal.`id_akun` 
				LEFT JOIN pembelian 
				  ON pembelian.`id` = jurnal.`ref_pk` 
				LEFT JOIN pembayaran 
				  ON pembayaran.`id_pembelian` = pembelian.`id` 
				  AND pembayaran.`nominal` != 0
				LEFT JOIN rincian_pembayaran 
				  ON pembayaran.id = rincian_pembayaran.`id_pembayaran` 
				LEFT JOIN penjualan 
				  ON penjualan.`id` = jurnal.`ref_pk` 
				LEFT JOIN pelunasan 
				  ON pelunasan.`id_penjualan` = penjualan.`id` 
				  AND pelunasan.`nominal` != 0
				LEFT JOIN rincian_pelunasan 
				  ON rincian_pelunasan.`id_pelunasan` = pelunasan.`id` 
				LEFT JOIN giro AS k1
				  ON k1.`id` = rincian_pembayaran.`id_giro` 
				LEFT JOIN giro AS k2
				  ON k2.`id` = rincian_pelunasan.`id_giro` 
				LEFT JOIN `pemasok` 
				  ON `pemasok`.`id` = `pembelian`.`id_pemasok` 
				LEFT JOIN transaksi 
				  ON transaksi.`id` = jurnal.`ref_pk` 
			  WHERE akun.`induk` IN (1, 2) 
			  AND jurnal.`id_cabang` = '$id_cabang' 
				AND (
				  rincian_pembayaran.`chek` IN (2, 0) 
				  OR rincian_pembayaran.`chek` IS NULL
				) 
				AND (
				  rincian_pelunasan.`chek` IN (2, 0) 
				  OR rincian_pelunasan.`chek` IS NULL
				) 
				AND `jurnal`.`tgl` < '$start_date' 
				AND `jurnal`.`tgl` > '2021-05-20' 
				OR jurnal.`id_akun` = 12 
				AND jurnal.`id_cabang` = '$id_cabang' 
			  GROUP BY jurnal.`id` 
			  ORDER BY jurnal.tgl ASC,
			  jurnal.`kredit` DESC,
			  jurnal.metode ASC ")->result();
		foreach ($saldo as $key => $row) {
			$new_saldo = 0;
			if ($key == 0) {
				$new_saldo = $new_saldo + (float)$saldo[0]->saldo;
				$saldo[0]->new_saldo = $new_saldo;

			}
			else{
				$new_saldo = (float)$saldo[$key-1]->new_saldo + (float)$saldo[$key]->saldo;
				$saldo[$key]->new_saldo = $new_saldo;
				$saldo[$key]->saldo_awal = $new_saldo;
			}
		}

		if (empty(end($saldo)->new_saldo)) {
			$saldo_awal = 0;
		}
		else{
			$saldo_awal = end($saldo)->new_saldo;
		}
	
		$this->db->query("SET @saldo_awal = '".$saldo_awal."'");
		$laporan = $this->db->query(
				"SELECT 
				`jurnal`.`tgl` AS tgl,
				CASE
				  WHEN `jurnal`.`metode` IS NOT NULL 
				  THEN CONCAT(
					`akun`.`nama`,
					' ',
					`jurnal`.`metode`
				  ) 
				  ELSE CONCAT(
					`akun`.`nama`,
					' ',
					`jurnal`.`ref_table`
				  ) 
				END AS keterangan,
				CONCAT(
					COALESCE(`pemasok`.`nama`, '')
				) AS uraian,
				CASE
				  WHEN `k1`.`nomor` IS NOT NULL 
				  THEN CONCAT(
					`k1`.`nomor`
				  ) 
				  ELSE CONCAT(
					`k2`.`nomor`
				  ) 
				END AS nogiro,
				akun.id AS akun_id,
				akun.`induk` AS induk,
				rincian_pembayaran.`chek` AS cekbeli,
				rincian_pelunasan.`chek` AS cekjual,
				jurnal.`metode` AS metode,
				jurnal.`debit` AS total_debit,
				jurnal.`kredit` AS total_kredit,
				@saldo_awal := (
				  IFNULL(`jurnal`.`kredit`, 0) - IFNULL(`jurnal`.`debit`, 0)
				) AS `saldo` 
			  FROM
				jurnal 
				LEFT JOIN akun 
				  ON akun.`id` = jurnal.`id_akun` 
				LEFT JOIN pembelian 
				  ON pembelian.`id` = jurnal.`ref_pk` 
				LEFT JOIN pembayaran 
				  ON pembayaran.`id_pembelian` = pembelian.`id` 
				  AND pembayaran.`nominal` != 0
				LEFT JOIN rincian_pembayaran 
				  ON pembayaran.id = rincian_pembayaran.`id_pembayaran` 
				LEFT JOIN penjualan 
				  ON penjualan.`id` = jurnal.`ref_pk` 
				LEFT JOIN pelunasan 
				  ON pelunasan.`id_penjualan` = penjualan.`id` 
				  AND pelunasan.`nominal` != 0
				LEFT JOIN rincian_pelunasan 
				  ON rincian_pelunasan.`id_pelunasan` = pelunasan.`id` 
				LEFT JOIN giro as k1
				  ON k1.`id` = rincian_pembayaran.`id_giro` 
				LEFT JOIN giro as k2
				  ON k2.`id` = rincian_pelunasan.`id_giro` 
				LEFT JOIN `pemasok` 
				  ON `pemasok`.`id` = `pembelian`.`id_pemasok` 
				LEFT JOIN transaksi 
				  ON transaksi.`id` = jurnal.`ref_pk` 
			  WHERE akun.`induk` IN (1, 2) 
			  AND jurnal.`id_cabang` = '$id_cabang' 
				AND (
				  rincian_pembayaran.`chek` IN (2, 0) 
				  OR rincian_pembayaran.`chek` IS NULL
				) 
				AND (
				  rincian_pelunasan.`chek` IN (2, 0) 
				  OR rincian_pelunasan.`chek` IS NULL
				) 
				AND `jurnal`.`tgl` BETWEEN '$start_date' 
				AND '$end_date' 
				AND `jurnal`.`tgl` > '2021-05-20' 
				OR jurnal.`id_akun` = 12 
			  GROUP BY jurnal.`id` 
			  ORDER BY jurnal.tgl ASC,
			  jurnal.`kredit` DESC, 
			  jurnal.`metode` ASC")->result();
			foreach ($laporan as $key => $row) {
					$new_saldo = 0;
					if ($key == 0) {
						$new_saldo = $new_saldo + (float)$saldo_awal + (float)$laporan[0]->saldo;
						$laporan[0]->new_saldo = $new_saldo;
					}
					else{
						$new_saldo = (float)$laporan[$key-1]->new_saldo + (float)$laporan[$key]->saldo;
						$laporan[$key]->new_saldo = $new_saldo;
					}
				}
		
		return array(
			"data"				=> $laporan,
			"saldo_awal"		=>$saldo_awal
		);
	}
	public function print_out($start_date, $end_date, $id_cabang)
	{
		$this->db->query("SET @saldo = 0");
		$saldo = $this->db->query(
				" SELECT 
				`jurnal`.`tgl` AS tgl,
				CASE
				  WHEN `jurnal`.`metode` IS NOT NULL 
				  THEN CONCAT(
					`akun`.`nama`,
					' ',
					`jurnal`.`metode`
				  ) 
				  ELSE CONCAT(
					`akun`.`nama`,
					' ',
					`jurnal`.`ref_table`
				  ) 
				END AS keterangan,
				CONCAT(
					COALESCE(`pemasok`.`nama`, '')
				) AS uraian,
				CASE
				  WHEN `k1`.`nomor` IS NOT NULL 
				  THEN GROUP_CONCAT(
					`k1`.`nomor`
				  ) 
				  ELSE CONCAT(
					`k2`.`nomor`
				  ) 
				END AS nogiro,
				akun.id AS akun_id,
				akun.`induk` AS induk,
				rincian_pembayaran.`chek` AS cekbeli,
				rincian_pelunasan.`chek` AS cekjual,
				jurnal.`metode` AS metode,
				jurnal.`debit` AS total_debit,
				jurnal.`kredit` AS total_kredit,
				@saldo_awal := (
				  IFNULL(`jurnal`.`kredit`, 0) - IFNULL(`jurnal`.`debit`, 0)
				) AS `saldo` 
			  FROM
				jurnal 
				LEFT JOIN akun 
				  ON akun.`id` = jurnal.`id_akun` 
				LEFT JOIN pembelian 
				  ON pembelian.`id` = jurnal.`ref_pk` 
				LEFT JOIN pembayaran 
				  ON pembayaran.`id_pembelian` = pembelian.`id` 
				  AND pembayaran.`nominal` != 0
				LEFT JOIN rincian_pembayaran 
				  ON pembayaran.id = rincian_pembayaran.`id_pembayaran` 
				LEFT JOIN penjualan 
				  ON penjualan.`id` = jurnal.`ref_pk` 
				LEFT JOIN pelunasan 
				  ON pelunasan.`id_penjualan` = penjualan.`id` 
				  AND pelunasan.`nominal` != 0
				LEFT JOIN rincian_pelunasan 
				  ON rincian_pelunasan.`id_pelunasan` = pelunasan.`id` 
				LEFT JOIN giro AS k1
				  ON k1.`id` = rincian_pembayaran.`id_giro` 
				LEFT JOIN giro AS k2
				  ON k2.`id` = rincian_pelunasan.`id_giro` 
				LEFT JOIN `pemasok` 
				  ON `pemasok`.`id` = `pembelian`.`id_pemasok` 
				LEFT JOIN transaksi 
				  ON transaksi.`id` = jurnal.`ref_pk` 
			  WHERE akun.`induk` IN (1, 2) 
			  AND jurnal.`id_cabang` = '$id_cabang' 
				AND (
				  rincian_pembayaran.`chek` IN (2, 0) 
				  OR rincian_pembayaran.`chek` IS NULL
				) 
				AND (
				  rincian_pelunasan.`chek` IN (2, 0) 
				  OR rincian_pelunasan.`chek` IS NULL
				) 
				AND `jurnal`.`tgl` < '$start_date' 
				AND `jurnal`.`tgl` > '2021-05-20' 
				OR jurnal.`id_akun` = 12 
			  GROUP BY jurnal.`id` 
			  ORDER BY jurnal.tgl ASC,
			  jurnal.`kredit` DESC,
			  jurnal.`metode` ASC ")->result();
				foreach ($saldo as $key => $row) {
					$new_saldo = 0;
					if ($key == 0) {
						$new_saldo = $new_saldo + (float)$saldo[0]->saldo;
						$saldo[0]->new_saldo = $new_saldo;
		
					}
					else{
						$new_saldo = (float)$saldo[$key-1]->new_saldo + (float)$saldo[$key]->saldo;
						$saldo[$key]->new_saldo = $new_saldo;
						$saldo[$key]->saldo_awal = $new_saldo;
					}
				}
		
				if (empty(end($saldo)->new_saldo)) {
					$saldo_awal = 0;
				}
				else{
					$saldo_awal = end($saldo)->new_saldo;
				}
			
		$this->db->query("SET @saldo_awal = '".$saldo_awal."'");
		$laporan = $this->db->query(
				"SELECT 
				`jurnal`.`tgl` AS tgl,
				CASE
				  WHEN `jurnal`.`metode` IS NOT NULL 
				  THEN CONCAT(
					`akun`.`nama`,
					' ',
					`jurnal`.`metode`
				  ) 
				  ELSE CONCAT(
					`akun`.`nama`,
					' ',
					`jurnal`.`ref_table`
				  ) 
				END AS keterangan,
				CONCAT(
					COALESCE(`pemasok`.`nama`, '')
				) AS uraian,
				CASE
				  WHEN `k1`.`nomor` IS NOT NULL 
				  THEN CONCAT(
					`k1`.`nomor`
				  ) 
				  ELSE CONCAT(
					`k2`.`nomor`
				  ) 
				END AS nogiro,
				akun.id AS akun_id,
				akun.`induk` AS induk,
				rincian_pembayaran.`chek` AS cekbeli,
				rincian_pelunasan.`chek` AS cekjual,
				jurnal.`metode` AS metode,
				jurnal.`debit` AS total_debit,
				jurnal.`kredit` AS total_kredit,
				@saldo_awal := (
				  IFNULL(`jurnal`.`kredit`, 0) - IFNULL(`jurnal`.`debit`, 0)
				) AS `saldo` 
			  FROM
				jurnal 
				LEFT JOIN akun 
				  ON akun.`id` = jurnal.`id_akun` 
				LEFT JOIN pembelian 
				  ON pembelian.`id` = jurnal.`ref_pk` 
				LEFT JOIN pembayaran 
				  ON pembayaran.`id_pembelian` = pembelian.`id` 
				  AND pembayaran.`nominal` != 0
				LEFT JOIN rincian_pembayaran 
				  ON pembayaran.id = rincian_pembayaran.`id_pembayaran` 
				LEFT JOIN penjualan 
				  ON penjualan.`id` = jurnal.`ref_pk` 
				LEFT JOIN pelunasan 
				  ON pelunasan.`id_penjualan` = penjualan.`id` 
				  AND pelunasan.`nominal` != 0
				LEFT JOIN rincian_pelunasan 
				  ON rincian_pelunasan.`id_pelunasan` = pelunasan.`id` 
				LEFT JOIN giro as k1
				  ON k1.`id` = rincian_pembayaran.`id_giro` 
				LEFT JOIN giro as k2
				  ON k2.`id` = rincian_pelunasan.`id_giro` 
				LEFT JOIN `pemasok` 
				  ON `pemasok`.`id` = `pembelian`.`id_pemasok` 
				LEFT JOIN transaksi 
				  ON transaksi.`id` = jurnal.`ref_pk` 
			  WHERE akun.`induk` IN (1, 2) 
			  AND jurnal.`id_cabang` = '$id_cabang' 
				AND (
				  rincian_pembayaran.`chek` IN (2, 0) 
				  OR rincian_pembayaran.`chek` IS NULL
				) 
				AND (
				  rincian_pelunasan.`chek` IN (2, 0) 
				  OR rincian_pelunasan.`chek` IS NULL
				) 
				AND `jurnal`.`tgl` BETWEEN '$start_date' 
				AND '$end_date' 
				AND `jurnal`.`tgl` > '2021-05-20' 
				OR jurnal.`id_akun` = 12 
			  GROUP BY jurnal.`id` 
			  ORDER BY jurnal.tgl ASC,
			  jurnal.`kredit` DESC, 
			  jurnal.`metode` ASC ");
				$laporan_result = $laporan->result();
		foreach ($laporan_result as $key => $row) {
			$new_saldo = 0;
			if ($key == 0) {
				$new_saldo = $new_saldo + (float)$laporan_result[0]->saldo;
				$laporan_result[0]->new_saldo = $new_saldo;
			}
			else{
				$new_saldo = (float)$laporan_result[$key-1]->new_saldo + (float)$laporan_result[$key]->saldo;
				$laporan_result[$key]->new_saldo = $new_saldo;
			}
		}
		return array(
			"laporan" => $laporan->result_array(),
			"laporan_num" => $laporan->num_rows(),
			"saldo_awal" =>$saldo_awal
		);
	}

}