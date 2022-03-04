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
				  WHEN `akun`.`induk` IS NULL 
				  THEN CONCAT(
					`akun`.`nama`,
					' ',
					`jurnal`.`ref_table`
				  ) 
				  ELSE CONCAT(`akun`.`nama`) 
				END AS keterangan,
				CASE
				  WHEN `multi`.`nama` IS NOT NULL 
				  THEN CONCAT(COALESCE(`multi`.`nama`, '')) 
				  ELSE CONCAT(COALESCE(`pembelian`.`nama`, '')) 
				END AS uraian,
				CASE
				  WHEN `pembelian`.`nomor` IS NOT NULL 
				  THEN CONCAT(`pembelian`.`nomor`) 
				  ELSE CONCAT(`multi`.`nomor`) 
				END AS nogiro,
				akun.id AS akun_id,
				akun.`induk` AS induk,
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
				LEFT JOIN 
				  (SELECT 
					jurnal.*,
					giro.nomor AS nomor,
					pemasok.nama AS nama,
					rincian_pembayaran_multi.chek AS chek 
				  FROM
					jurnal 
					INNER JOIN rincian_pembayaran_multi 
					  ON rincian_pembayaran_multi.`gabung_nota` = jurnal.`ref_text` 
					LEFT JOIN pemasok 
					  ON pemasok.`id` = rincian_pembayaran_multi.`id_pemasok` 
					LEFT JOIN giro 
					  ON giro.`id` = rincian_pembayaran_multi.`id_giro` 
				  GROUP BY jurnal.`id`) AS multi 
				  ON multi.ref_text = jurnal.`ref_text` 
				LEFT JOIN 
				  (SELECT 
					jurnal.*,
					giro.nomor AS nomor,
					pemasok.nama AS nama,
					rincian_pembayaran.chek AS chek 
				  FROM
					jurnal 
					INNER JOIN pembelian 
					  ON pembelian.`id` = jurnal.`ref_pk` 
					LEFT JOIN pembayaran 
					  ON pembayaran.`id_pembelian` = pembelian.`id` 
					LEFT JOIN rincian_pembayaran 
					  ON rincian_pembayaran.`id_pembayaran` = pembayaran.`id` 
					LEFT JOIN pemasok 
					  ON pembelian.`id_pemasok` = pemasok.`id` 
					LEFT JOIN giro 
					  ON giro.`id` = rincian_pembayaran.`id_giro` 
				  GROUP BY rincian_pembayaran.`id`) AS pembelian 
				  ON pembelian.`ref_pk` = jurnal.`ref_pk` 
				LEFT JOIN transaksi 
				  ON transaksi.`id` = jurnal.`ref_pk` 
			  WHERE (akun.`induk` IN (1, 2) OR akun.`id` = 12)  
				AND jurnal.`ref_table` NOT IN ('penjualan', 'pelunasan') 
				AND jurnal.`id_cabang` = $id_cabang 
				AND (
					multi.`chek` IN (2, 0) 
					OR multi.`chek` IS NULL OR pembelian.`chek` IN (2, 0) 
					OR pembelian.`chek` IS NULL
				) 
				AND `jurnal`.`tgl` < '$start_date' 
				AND CASE WHEN jurnal.ref_table = 'transaksi_lampung' 
				THEN `jurnal`.`tgl` > '2021-05-01' 
				ELSE `jurnal`.`tgl` > '2021-05-20'  END
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
				" SELECT 
				`jurnal`.`tgl` AS tgl,
				CASE
				  WHEN `jurnal`.`metode` IS NOT NULL 
				  THEN CONCAT(
					`akun`.`nama`,
					' ',
					`jurnal`.`metode`
				  ) 
				  WHEN `akun`.`induk` IS NULL 
				  THEN CONCAT(
					`akun`.`nama`,
					' ',
					`jurnal`.`ref_table`
				  ) 
				  ELSE CONCAT(`akun`.`nama`) 
				END AS keterangan,
				CASE
				  WHEN `multi`.`nama` IS NOT NULL 
				  THEN CONCAT(COALESCE(`multi`.`nama`, '')) 
				  ELSE CONCAT(COALESCE(`pembelian`.`nama`, '')) 
				END AS uraian,
				CASE
				  WHEN `pembelian`.`nomor` IS NOT NULL 
				  THEN CONCAT(`pembelian`.`nomor`) 
				  ELSE CONCAT(`multi`.`nomor`) 
				END AS nogiro,
				akun.id AS akun_id,
				akun.`induk` AS induk,
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
				LEFT JOIN 
				  (SELECT 
					jurnal.*,
					giro.nomor AS nomor,
					pemasok.nama AS nama,
					rincian_pembayaran_multi.chek AS chek 
				  FROM
					jurnal 
					INNER JOIN rincian_pembayaran_multi 
					  ON rincian_pembayaran_multi.`gabung_nota` = jurnal.`ref_text` 
					LEFT JOIN pemasok 
					  ON pemasok.`id` = rincian_pembayaran_multi.`id_pemasok` 
					LEFT JOIN giro 
					  ON giro.`id` = rincian_pembayaran_multi.`id_giro` 
				  GROUP BY jurnal.`id`) AS multi 
				  ON multi.ref_text = jurnal.`ref_text` 
				LEFT JOIN 
				  (SELECT 
					jurnal.*,
					giro.nomor AS nomor,
					pemasok.nama AS nama,
					rincian_pembayaran.chek AS chek 
				  FROM
					jurnal 
					INNER JOIN pembelian 
					  ON pembelian.`id` = jurnal.`ref_pk` 
					LEFT JOIN pembayaran 
					  ON pembayaran.`id_pembelian` = pembelian.`id` 
					LEFT JOIN rincian_pembayaran 
					  ON rincian_pembayaran.`id_pembayaran` = pembayaran.`id` 
					LEFT JOIN pemasok 
					  ON pembelian.`id_pemasok` = pemasok.`id` 
					LEFT JOIN giro 
					  ON giro.`id` = rincian_pembayaran.`id_giro` 
				  GROUP BY rincian_pembayaran.`id`) AS pembelian 
				  ON pembelian.`ref_pk` = jurnal.`ref_pk` 
				LEFT JOIN transaksi 
				  ON transaksi.`id` = jurnal.`ref_pk` 
			  WHERE (akun.`induk` IN (1, 2) OR akun.`id` = 12)  
				AND jurnal.`ref_table` NOT IN ('penjualan', 'pelunasan') 
				AND jurnal.`id_cabang` = $id_cabang
				AND (
					multi.`chek` IN (2, 0) 
					OR multi.`chek` IS NULL OR pembelian.`chek` IN (2, 0) 
					OR pembelian.`chek` IS NULL
				) 
				AND `jurnal`.`tgl` BETWEEN '$start_date' 
				AND '$end_date' 
				AND CASE WHEN jurnal.ref_table = 'transaksi_lampung' 
				THEN `jurnal`.`tgl` > '2021-05-01' 
				ELSE `jurnal`.`tgl` > '2021-05-20'  END
			  GROUP BY jurnal.`id` 
			  ORDER BY jurnal.tgl ASC,
				jurnal.`kredit` DESC,
				jurnal.`metode` ASC ")->result();
					$last_date = null;
			foreach ($laporan as $key => $row) {
					$new_saldo = 0;
			
					if ($key == 0) {
						$new_saldo = $new_saldo + (float)$saldo_awal + (float)$laporan[0]->saldo;
						$laporan[0]->new_saldo = $new_saldo;
						$laporan[0]->akhir_saldo = $saldo_awal;
					}
					else{
						$new_saldo = (float)$laporan[$key-1]->new_saldo + (float)$laporan[$key]->saldo;
						$laporan[$key]->new_saldo = $new_saldo;
						if ( $last_date != $row->tgl ) {
							$laporan[$key]->akhir_saldo = end($laporan[$key-1]);
						}
					}

				
					$last_date = $row->tgl;
				}
		
		return array(
			"data"				=> $laporan,
			"saldo_awal"		=>$saldo_awal,
			// "saldo_akhir"		=>$new_saldo
		);
	}
	public function print_out($start_date, $end_date, $id_cabang)
	{
		$this->db->query("SET @saldo = 0");
		$saldo = $this->db->query(
				"SELECT 
				`jurnal`.`tgl` AS tgl,
				CASE
				  WHEN `jurnal`.`metode` IS NOT NULL 
				  THEN CONCAT(
					`akun`.`nama`,
					' ',
					`jurnal`.`metode`
				  ) 
				  WHEN `akun`.`induk` IS NULL 
				  THEN CONCAT(
					`akun`.`nama`,
					' ',
					`jurnal`.`ref_table`
				  ) 
				  ELSE CONCAT(`akun`.`nama`) 
				END AS keterangan,
				CASE
				  WHEN `multi`.`nama` IS NOT NULL 
				  THEN CONCAT(COALESCE(`multi`.`nama`, '')) 
				  ELSE CONCAT(COALESCE(`pembelian`.`nama`, '')) 
				END AS uraian,
				CASE
				  WHEN `pembelian`.`nomor` IS NOT NULL 
				  THEN CONCAT(`pembelian`.`nomor`) 
				  ELSE CONCAT(`multi`.`nomor`) 
				END AS nogiro,
				akun.id AS akun_id,
				akun.`induk` AS induk,
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
				LEFT JOIN 
				  (SELECT 
					jurnal.*,
					giro.nomor AS nomor,
					pemasok.nama AS nama,
					rincian_pembayaran_multi.chek AS chek 
				  FROM
					jurnal 
					INNER JOIN rincian_pembayaran_multi 
					  ON rincian_pembayaran_multi.`gabung_nota` = jurnal.`ref_text` 
					LEFT JOIN pemasok 
					  ON pemasok.`id` = rincian_pembayaran_multi.`id_pemasok` 
					LEFT JOIN giro 
					  ON giro.`id` = rincian_pembayaran_multi.`id_giro` 
				  GROUP BY jurnal.`id`) AS multi 
				  ON multi.ref_text = jurnal.`ref_text` 
				LEFT JOIN 
				  (SELECT 
					jurnal.*,
					giro.nomor AS nomor,
					pemasok.nama AS nama,
					rincian_pembayaran.chek AS chek 
				  FROM
					jurnal 
					INNER JOIN pembelian 
					  ON pembelian.`id` = jurnal.`ref_pk` 
					LEFT JOIN pembayaran 
					  ON pembayaran.`id_pembelian` = pembelian.`id` 
					LEFT JOIN rincian_pembayaran 
					  ON rincian_pembayaran.`id_pembayaran` = pembayaran.`id` 
					LEFT JOIN pemasok 
					  ON pembelian.`id_pemasok` = pemasok.`id` 
					LEFT JOIN giro 
					  ON giro.`id` = rincian_pembayaran.`id_giro` 
				  GROUP BY rincian_pembayaran.`id`) AS pembelian 
				  ON pembelian.`ref_pk` = jurnal.`ref_pk` 
				LEFT JOIN transaksi 
				  ON transaksi.`id` = jurnal.`ref_pk` 
			  WHERE (akun.`induk` IN (1, 2) OR akun.`id` = 12) 
				AND jurnal.`ref_table` NOT IN ('penjualan', 'pelunasan') 
				AND jurnal.`id_cabang` = $id_cabang
				AND (
					multi.`chek` IN (2, 0) 
					OR multi.`chek` IS NULL OR pembelian.`chek` IN (2, 0) 
					OR pembelian.`chek` IS NULL
				) 
				AND `jurnal`.`tgl` < '$start_date' 
				AND CASE WHEN jurnal.ref_table = 'transaksi_lampung' 
				THEN `jurnal`.`tgl` > '2021-05-01' 
				ELSE `jurnal`.`tgl` > '2021-05-20'  END
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
				  WHEN `akun`.`induk` IS NULL 
				  THEN CONCAT(
					`akun`.`nama`,
					' ',
					`jurnal`.`ref_table`
				  ) 
				  ELSE CONCAT(`akun`.`nama`) 
				END AS keterangan,
				CASE
				  WHEN `multi`.`nama` IS NOT NULL 
				  THEN CONCAT(COALESCE(`multi`.`nama`, '')) 
				  ELSE CONCAT(COALESCE(`pembelian`.`nama`, '')) 
				END AS uraian,
				CASE
				  WHEN `pembelian`.`nomor` IS NOT NULL 
				  THEN CONCAT(`pembelian`.`nomor`) 
				  ELSE CONCAT(`multi`.`nomor`) 
				END AS nogiro,
				akun.id AS akun_id,
				akun.`induk` AS induk,
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
				LEFT JOIN 
				  (SELECT 
					jurnal.*,
					giro.nomor AS nomor,
					pemasok.nama AS nama,
					rincian_pembayaran_multi.chek AS chek 
				  FROM
					jurnal 
					INNER JOIN rincian_pembayaran_multi 
					  ON rincian_pembayaran_multi.`gabung_nota` = jurnal.`ref_text` 
					LEFT JOIN pemasok 
					  ON pemasok.`id` = rincian_pembayaran_multi.`id_pemasok` 
					LEFT JOIN giro 
					  ON giro.`id` = rincian_pembayaran_multi.`id_giro` 
				  GROUP BY jurnal.`id`) AS multi 
				  ON multi.ref_text = jurnal.`ref_text` 
				LEFT JOIN 
				  (SELECT 
					jurnal.*,
					giro.nomor AS nomor,
					pemasok.nama AS nama,
					rincian_pembayaran.chek AS chek 
				  FROM
					jurnal 
					INNER JOIN pembelian 
					  ON pembelian.`id` = jurnal.`ref_pk` 
					LEFT JOIN pembayaran 
					  ON pembayaran.`id_pembelian` = pembelian.`id` 
					LEFT JOIN rincian_pembayaran 
					  ON rincian_pembayaran.`id_pembayaran` = pembayaran.`id` 
					LEFT JOIN pemasok 
					  ON pembelian.`id_pemasok` = pemasok.`id` 
					LEFT JOIN giro 
					  ON giro.`id` = rincian_pembayaran.`id_giro` 
				  GROUP BY rincian_pembayaran.`id`) AS pembelian 
				  ON pembelian.`ref_pk` = jurnal.`ref_pk` 
				LEFT JOIN transaksi 
				  ON transaksi.`id` = jurnal.`ref_pk` 
			  WHERE (akun.`induk` IN (1, 2) OR akun.`id` = 12)  
				AND jurnal.`ref_table` NOT IN ('penjualan', 'pelunasan') 
				AND jurnal.`id_cabang` = $id_cabang
								AND (
					multi.`chek` IN (2, 0) 
					OR multi.`chek` IS NULL OR pembelian.`chek` IN (2, 0) 
					OR pembelian.`chek` IS NULL
				) 
				AND `jurnal`.`tgl` BETWEEN '$start_date' 
				AND '$end_date' 
				AND CASE WHEN jurnal.ref_table = 'transaksi_lampung' 
				THEN `jurnal`.`tgl` > '2021-05-01' 
				ELSE `jurnal`.`tgl` > '2021-05-20'  END
			  GROUP BY jurnal.`id` 
			  ORDER BY jurnal.tgl ASC,
				jurnal.`kredit` DESC,
				jurnal.`metode` ASC  ");
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