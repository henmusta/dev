<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lap_harian extends CI_Model {
	private $module = [
		'name' 	=> 'laporan harian',
		'url'	=> 'laporan/laporan-harian',
	];
	public function __construct(){
		parent::__construct();
	}
	public function printhari($config = array()){
        extract($config);
		$tgl = isset($params['filter']['tglhari']) ? $params['filter']['tglhari']: null;
		$cabang = isset($params['filter']['id_cabang']) ? $params['filter']['id_cabang']: null;
		$akun = array(1, 2);
		$print = (object)[];
		$print->stok 	= $this->db->query('SELECT *,SUM(produk.harga_beli * stok.qty) AS persediaan, CONCAT(('.$tgl.') - INTERVAL 1 DAY ) AS asu
		FROM
		  stok 
		LEFT JOIN produk
		ON produk.`id` = stok.`id_produk` 
		WHERE `stok`.`tgl` >= "2021-05-20" and (stok.`tgl` < "'.$tgl.'" 
		  AND ref_table = "pembelian")
		  AND (produk.`id_cabang` = '.$cabang.'
		  OR ref_table != "pembelian" 
		  AND stok.`tgl` <  "'.$tgl.'" - INTERVAL 1 DAY) 
		  HAVING(SUM(stok.`qty`) > 0) ')->row();
		$print->rincian_pembelian 	= $this->db
			->select(' *, sum(pembelian.total_rincian) as persediaan, sum(pembelian.total_pembayaran) as bersih ')
			->from('pembelian')
			->where(array('pembelian.tgl_buat'=>$tgl, 'pembelian.id_cabang' => $cabang))->get()->row();
		$print->retur_pembelian 	= $this->db
			->select(' *, sum(retur_pembelian.nominal) as totalretur ')
			->from('retur_pembelian')
			->where(array('retur_pembelian.tgl_nota'=>$tgl, 'retur_pembelian.id_cabang' => $cabang))->get()->row();
		$print->rincian_biaya_jakarta 	= $this->db
		->select(' sum(rincian_biaya_jakarta.total) as total ')
			->from('rincian_biaya_jakarta')
			->join('transaksi','transaksi.id=rincian_biaya_jakarta.id_transaksi','left')
			->where(array('rincian_biaya_jakarta.tgl'=>$tgl, 'transaksi.id_cabang' => $cabang))->get()->row();
		$print->rincian_piutang 	= $this->db
			->select(' *, sum(jurnal.kredit) as nominal')
			->from('jurnal')
			->join('akun','akun.id = jurnal.id_akun', 'left')
			->where_in('akun.induk', $akun)
			->where(array('jurnal.tgl'=>$tgl, 'jurnal.id_cabang' => $cabang, 'jurnal.ref_table' => 'pelunasan'))->get()->row();
		$print->rincian_penjualan 	= $this->db
			->select(' *, sum(penjualan.total_tagihan) as persediaan, sum(penjualan.total_pelunasan) as bersih')
			->from('penjualan')
			->where(array('penjualan.tgl_nota'=>$tgl, 'penjualan.id_cabang' => $cabang))->get()->row();
		$print->rincian_penjualan_omset 	= $this->db
			->select(' *, sum(penjualan.total_rincian) as persediaan, sum(penjualan.total_pelunasan) as omset')
			->from('penjualan')
			->where(array('penjualan.tgl_nota <= '=>$tgl, 'penjualan.id_cabang' => $cabang))->get()->row();
		$print->rincian_biaya 	= $this->db
			->select(' * ')
			->from('rincian_biaya_lampung')
			->join('transaksi','transaksi.id=rincian_biaya_lampung.id_transaksi','left')
			->where(array('rincian_biaya_lampung.tgl'=>$tgl, 'transaksi.id_cabang' => $cabang))->get()->row();
		$print->kasir = $this->db
			->select(' * ')
			->from('transaksi')
			->where(array('transaksi.tgl_nota'=>$tgl, 'transaksi.id_cabang' => $cabang))->get()->row();
		$print->rincian_kasir_tunai = $this->db->select('akun.id AS id_akun, akun.nama AS akun, rincian_transaksi.total AS total')->from('rincian_transaksi')
			->join('akun','akun.id=rincian_transaksi.id_akun','left')
			->join('transaksi','transaksi.id = rincian_transaksi.id_transaksi','left')
			->where(array('transaksi.tgl_nota'=>$tgl, 'rincian_transaksi.tipe'=>'pendapatan', 'rincian_transaksi.id_akun'=> 13 ,'transaksi.id_cabang' => $cabang))->get()->row();
	
		$print->rincian_kasir_bri = $this->db->select('akun.id AS id_akun, akun.nama AS akun, rincian_transaksi.total AS total')->from('rincian_transaksi')
			->join('akun','akun.id=rincian_transaksi.id_akun','left')
			->join('transaksi','transaksi.id = rincian_transaksi.id_transaksi','left')
			->where(array('transaksi.tgl_nota'=>$tgl, 'rincian_transaksi.tipe'=>'pendapatan', 'rincian_transaksi.id_akun'=> 15,'transaksi.id_cabang' => $cabang ))->get()->row();

		$print->rincian_kasir_bca = $this->db->select('akun.id AS id_akun, akun.nama AS akun, rincian_transaksi.total AS total')->from('rincian_transaksi')
			->join('akun','akun.id=rincian_transaksi.id_akun','left')
			->join('transaksi','transaksi.id = rincian_transaksi.id_transaksi','left')
			->where(array('transaksi.tgl_nota'=>$tgl, 'rincian_transaksi.tipe'=>'pendapatan', 'rincian_transaksi.id_akun'=> 16 ,'transaksi.id_cabang' => $cabang))->get()->row();

		$print->rincian_kasir_bni = $this->db->select('akun.id AS id_akun, akun.nama AS akun, rincian_transaksi.total AS total')->from('rincian_transaksi')
			->join('akun','akun.id=rincian_transaksi.id_akun','left')
			->join('transaksi','transaksi.id = rincian_transaksi.id_transaksi','left')
			->where(array('transaksi.tgl_nota'=>$tgl, 'rincian_transaksi.tipe'=>'pendapatan', 'rincian_transaksi.id_akun'=> 20 ,'transaksi.id_cabang' => $cabang))->get()->row();

		$print->rincian_kasir_mandiri = $this->db->select('akun.id AS id_akun, akun.nama AS akun, rincian_transaksi.total AS total')->from('rincian_transaksi')
			->join('akun','akun.id=rincian_transaksi.id_akun','left')
			->join('transaksi','transaksi.id = rincian_transaksi.id_transaksi','left')
			->where(array('transaksi.tgl_nota'=>$tgl, 'rincian_transaksi.tipe'=>'pendapatan', 'rincian_transaksi.id_akun'=> 19 ,'transaksi.id_cabang' => $cabang))->get()->row();

		$print->laba_penjualan 	= $this->db->select('
		sum(penjualan.laba_akhir - penjualan.diskon) as laba ')
		->from('penjualan')
		->where(array('penjualan.tgl_nota'=>$tgl, 'penjualan.id_cabang' => $cabang))->get()->row();
		$print->retur_penjualan 	= $this->db
			->select(' *, sum(retur_penjualan.nominal) as nominal')
			->from('retur_penjualan')
			->where(array('DATE(retur_penjualan.tgl_nota)'=>$tgl, 'retur_penjualan.id_cabang' => $cabang))->get()->row();
		$print->laba_retur_penjualan 	= $this->db
			->select(' *, sum(produk.laba) as laba ')
			->from('retur_penjualan')
			->join('rincian_retur_penjualan','rincian_retur_penjualan.id_retur_penjualan = retur_penjualan.id', 'left')
			->join('produk','rincian_retur_penjualan.id_produk = produk.id','left')
			->where(array('DATE(retur_penjualan.tgl_nota)'=>$tgl, 'retur_penjualan.id_cabang' => $cabang))->get()->row();

		$saldo_awal = isset($print->kasir->modal_tahun) ? $print->kasir->modal_tahun : NULL;
		$piutang = isset($print->rincian_piutang->nominal) ? $print->rincian_piutang->nominal : NULL;
		$biaya_kasir = isset($print->kasir->biaya) ? $print->kasir->biaya : NULL;
		$persediaan_awal = isset($print->stok->persediaan) ? $print->stok->persediaan : NULL;
		$pembelian = isset($print->rincian_pembelian->persediaan) ? $print->rincian_pembelian->persediaan : NULL;
		$total_retur_pembelian = isset($print->retur_pembelian->totalretur) ? $print->retur_pembelian->totalretur : NULL;
		$print->pembelian_bersih = $pembelian - $total_retur_pembelian;
		$print->total_stok_awal = $persediaan_awal + $print->pembelian_bersih;
		$laba_penjualan = isset($print->laba_penjualan->laba) ? $print->laba_penjualan->laba : NULL;
		$total_retur_penjualan = isset($print->retur_penjualan->totalretur) ? $print->retur_penjualan->totalretur : NULL;
		$total_retur = isset($print->retur_penjualan->nominal) ? $print->retur_penjualan->nominal : NULL;

		$laba_retur_penjualan = isset($print->laba_retur_penjualan->laba) ? $print->laba_retur_penjualan->laba : NULL;
		$register = isset($print->kasir->register) ? $print->kasir->register : NULL;

		$total_retur_penjualan = isset($print->retur_penjualan->nominal) ? $print->retur_penjualan->nominal : NULL;

		$penjualan_bersih = isset($print->rincian_penjualan->bersih) ? $print->rincian_penjualan->bersih : NULL;

		$print->total_penjualan_tunai = $penjualan_bersih + $total_retur_penjualan + $register;

		$print->laba_penjualan = $laba_penjualan + $laba_retur_penjualan;

		$print->hpp = $print->total_stok_awal - $print->total_penjualan_tunai;

		$print->penjualan_tunai_kasir = $penjualan_bersih - $total_retur;
	
		$print->total_pendapatan = $saldo_awal + $print->penjualan_tunai_kasir + $piutang;


		$total_biaya_lampung = isset($print->rincian_biaya->total) ? $print->rincian_biaya->total : NULL;
		$total_biaya_jakarta = isset($print->rincian_biaya_jakarta->total) ? $print->rincian_biaya_jakarta->total : NULL;
		$print->hppretur = $total_retur - $laba_retur_penjualan;
		$print->persediaanakhir = $print->hpp + $print->hppretur;
		$print->bruto = $print->laba_penjualan - $laba_retur_penjualan;
		$print->totalbiaya = $total_biaya_lampung + $total_biaya_jakarta;

		$print->labaoperasional = $print->bruto - $print->totalbiaya;
		
		return $print;
	}

	public function printbulan($config = array()){
        extract($config);
		$tgl = isset($params['filter']['tglbulan']) ? $params['filter']['tglbulan']: null;
		$cabang = isset($params['filter']['id_cabang']) ? $params['filter']['id_cabang']: null;
		$akun = array(1, 2);
		$year		=  date('Y', strtotime($tgl));
		$month		= date("m", strtotime($tgl));
		$print = (object)[];

		$print->stok 	= $this->db->query('SELECT *,SUM(produk.harga_beli * stok.qty) AS persediaan, CONCAT(('.$tgl.') - INTERVAL 1 DAY ) AS asu
		FROM
		  stok 
		LEFT JOIN produk
		ON produk.`id` = stok.`id_produk` 
		WHERE `stok`.`tgl` >= "2021-05-20" and (MONTH(stok.`tgl`) < "'.$month.'" 
		  AND YEAR(stok.tgl) =  "'.$year.'" 
		  AND ref_table = "pembelian")
		  AND (produk.`id_cabang` = '.$cabang.'
		  OR ref_table != "pembelian" 
		  AND MONTH(stok.`tgl`) < "'.$month.'"  - INTERVAL 1 DAY AND YEAR(stok.tgl) =  "'.$year.'" ) 
		  HAVING(SUM(stok.`qty`) > 0)')->row();


		$print->rincian_pembelian 	= $this->db
		->select(' *, sum(pembelian.total_rincian) as persediaan, sum(pembelian.total_pembayaran) as bersih ')
		->from('pembelian')
		->where(array('MONTH(pembelian.tgl_buat)'=>$month, 'YEAR(pembelian.tgl_buat)'=>$year,   'pembelian.id_cabang' => $cabang))->get()->row();
		$print->retur_pembelian 	= $this->db
			->select(' *, sum(retur_pembelian.nominal) as totalretur ')
			->from('retur_pembelian')
			->where(array('MONTH(retur_pembelian.tgl_nota)'=>$month, 'YEAR(retur_pembelian.tgl_nota)'=>$year, 'retur_pembelian.id_cabang' => $cabang))->get()->row();
		$print->rincian_biaya_jakarta 	= $this->db
		->select(' sum(rincian_biaya_jakarta.total) as total ')
			->from('rincian_biaya_jakarta')
			->join('transaksi','transaksi.id=rincian_biaya_jakarta.id_transaksi','left')
			->where(array('MONTH(rincian_biaya_jakarta.tgl)'=>$month, '	YEAR(rincian_biaya_jakarta.tgl)'=>$year, 'transaksi.id_cabang' => $cabang))->get()->row();
			$print->rincian_piutang 	= $this->db
			->select(' *, sum(jurnal.kredit) as nominal')
			->from('jurnal')
			->join('akun','akun.id = jurnal.id_akun', 'left')
			->where_in('akun.induk', $akun)
			->where(array('MONTH(jurnal.tgl)'=>$month, 'YEAR(jurnal.tgl)'=>$year, 'jurnal.id_cabang' => $cabang, 'jurnal.ref_table' => 'pelunasan'))->get()->row();
		
		$print->rincian_penjualan_omset 	= $this->db
			->select(' *, sum(penjualan.total_rincian) as persediaan, sum(penjualan.total_pelunasan) as omset')
			->from('penjualan')
			->where(array('MONTH(penjualan.tgl_nota) <= '=>$month, 'YEAR(penjualan.tgl_nota)'=>$year, 'penjualan.id_cabang' => $cabang))->get()->row();
		
		$print->rincian_penjualan_omset 	= $this->db
			->select(' *, sum(penjualan.total_rincian) as persediaan, sum(penjualan.total_pelunasan) as omset')
			->from('penjualan')
			->where(array('YEAR(penjualan.tgl_nota) = '=>$tgl, 'penjualan.id_cabang' => $cabang))->get()->row();
			
		$print->rincian_penjualan 	= $this->db
			->select(' *, sum(penjualan.total_tagihan) as persediaan, sum(penjualan.total_pelunasan) as bersih')
			->from('penjualan')
			->where(array('MONTH(penjualan.tgl_nota)'=>$month, 'YEAR(penjualan.tgl_nota)'=>$year, 'penjualan.id_cabang' => $cabang))->get()->row();
		$print->rincian_biaya 	= $this->db
    		->select('r.tgl as tgl, sum(r.gaji) as gaji, sum(r.bulanan) as bulanan, sum(r.listrik) as listrik, sum(r.angkut) as angkut, sum(r.ekspedisi) as ekspedisi, sum(r.peralatan) as peralatan, sum(r.konsumsi) as konsumsi, sum(r.dll) as dll, sum(r.total) as total ')
			->from('rincian_biaya_lampung as r')
			->join('transaksi','transaksi.id=r.id_transaksi','left')
			->where(array('MONTH(transaksi.tgl_nota)'=>$month, 'YEAR(transaksi.tgl_nota)'=>$year,   'transaksi.id_cabang' => $cabang))->get()->row();
			
		$print->kasir = $this->db
			->select('sum(transaksi.modal) as modal, sum(transaksi.biaya) as biaya ')
			->from('transaksi')
			->where(array('MONTH(transaksi.tgl_nota)'=>$month, 'YEAR(transaksi.tgl_nota)'=>$year, 'transaksi.id_cabang' => $cabang))->get()->row();

		$print->rincian_kasir_tunai = $this->db->select('akun.id AS id_akun, akun.nama AS akun, sum(rincian_transaksi.total) AS total')->from('rincian_transaksi')
			->join('akun','akun.id=rincian_transaksi.id_akun','left')
			->join('transaksi','transaksi.id = rincian_transaksi.id_transaksi','left')
			->where(array('MONTH(transaksi.tgl_nota)'=>$month, 'YEAR(transaksi.tgl_nota)'=>$year, 'rincian_transaksi.tipe'=>'pendapatan', 'rincian_transaksi.id_akun'=> 13, 'transaksi.id_cabang' => $cabang ))->get()->row();
	
		$print->rincian_kasir_bri = $this->db->select('akun.id AS id_akun, akun.nama AS akun, sum(rincian_transaksi.total) AS total')->from('rincian_transaksi')
			->join('akun','akun.id=rincian_transaksi.id_akun','left')
			->join('transaksi','transaksi.id = rincian_transaksi.id_transaksi','left')
			->where(array('MONTH(transaksi.tgl_nota)'=>$month, 'YEAR(transaksi.tgl_nota)'=>$year, 'rincian_transaksi.tipe'=>'pendapatan', 'rincian_transaksi.id_akun'=> 15,'transaksi.id_cabang' => $cabang ))->get()->row();

		$print->rincian_kasir_bca = $this->db->select('akun.id AS id_akun, akun.nama AS akun, sum(rincian_transaksi.total) AS total')->from('rincian_transaksi')
			->join('akun','akun.id=rincian_transaksi.id_akun','left')
			->join('transaksi','transaksi.id = rincian_transaksi.id_transaksi','left')
			->where(array('MONTH(transaksi.tgl_nota)'=>$month, 'YEAR(transaksi.tgl_nota)'=>$year, 'rincian_transaksi.tipe'=>'pendapatan', 'rincian_transaksi.id_akun'=> 16,'transaksi.id_cabang' => $cabang ))->get()->row();

		$print->rincian_kasir_bni = $this->db->select('akun.id AS id_akun, akun.nama AS akun, sum(rincian_transaksi.total) AS total')->from('rincian_transaksi')
			->join('akun','akun.id=rincian_transaksi.id_akun','left')
			->join('transaksi','transaksi.id = rincian_transaksi.id_transaksi','left')
			->where(array('MONTH(transaksi.tgl_nota)'=>$month, 'YEAR(transaksi.tgl_nota)'=>$year, 'rincian_transaksi.tipe'=>'pendapatan', 'rincian_transaksi.id_akun'=> 20,'transaksi.id_cabang' => $cabang))->get()->row();

		$print->rincian_kasir_mandiri = $this->db->select('akun.id AS id_akun, akun.nama AS akun, sum(rincian_transaksi.total) AS total')->from('rincian_transaksi')
			->join('akun','akun.id=rincian_transaksi.id_akun','left')
			->join('transaksi','transaksi.id = rincian_transaksi.id_transaksi','left')
			->where(array('MONTH(transaksi.tgl_nota)'=>$month,  'YEAR(transaksi.tgl_nota)'=>$year,  'rincian_transaksi.tipe'=>'pendapatan', 'rincian_transaksi.id_akun'=> 19,'transaksi.id_cabang' => $cabang ))->get()->row();

		$print->laba_penjualan 	= $this->db->select('
				sum(penjualan.laba_akhir - penjualan.diskon) as laba ')
				->from('penjualan')
			->where(array('MONTH(penjualan.tgl_nota)'=>$month, 'YEAR(penjualan.tgl_nota)'=>$year, 'penjualan.id_cabang' => $cabang))->get()->row();
		$print->retur_penjualan 	= $this->db
			->select(' *, sum(retur_penjualan.nominal) as nominal')
			->from('retur_penjualan')
			->where(array('MONTH(DATE(retur_penjualan.tgl_nota))'=>$month, 'YEAR(DATE(retur_penjualan.tgl_nota))'=>$year, 'retur_penjualan.id_cabang' => $cabang))->get()->row();
		$print->laba_retur_penjualan 	= $this->db
			->select(' *, sum(produk.laba) as laba ')
			->from('retur_penjualan')
			->join('rincian_retur_penjualan','rincian_retur_penjualan.id_retur_penjualan = retur_penjualan.id', 'left')
			->join('produk','rincian_retur_penjualan.id_produk = produk.id','left')
			->where(array('MONTH(DATE(retur_penjualan.tgl_nota))'=>$month, 'YEAR(DATE(retur_penjualan.tgl_nota))'=>$year, 'retur_penjualan.id_cabang' => $cabang))->get()->row();
		
			$saldo_awal = isset($print->kasir->modal) ? $print->kasir->modal : NULL;
			$piutang = isset($print->rincian_piutang->nominal) ? $print->rincian_piutang->nominal : NULL;
			$biaya_kasir = isset($print->kasir->biaya) ? $print->kasir->biaya : NULL;
			$persediaan_awal = isset($print->stok->persediaan) ? $print->stok->persediaan : NULL;
			$pembelian = isset($print->rincian_pembelian->persediaan) ? $print->rincian_pembelian->persediaan : NULL;
			$total_retur_pembelian = isset($print->retur_pembelian->totalretur) ? $print->retur_pembelian->totalretur : NULL;
			$print->pembelian_bersih = $pembelian - $total_retur_pembelian;
			$print->total_stok_awal = $persediaan_awal + $print->pembelian_bersih;
			$laba_penjualan = isset($print->laba_penjualan->laba) ? $print->laba_penjualan->laba : NULL;
			$total_retur_penjualan = isset($print->retur_penjualan->totalretur) ? $print->retur_penjualan->totalretur : NULL;
			$total_retur = isset($print->retur_penjualan->nominal) ? $print->retur_penjualan->nominal : NULL;
	
			$laba_retur_penjualan = isset($print->laba_retur_penjualan->laba) ? $print->laba_retur_penjualan->laba : NULL;
			$register = isset($print->kasir->register) ? $print->kasir->register : NULL;
	
			$total_retur_penjualan = isset($print->retur_penjualan->nominal) ? $print->retur_penjualan->nominal : NULL;
	
			$penjualan_bersih = isset($print->rincian_penjualan->bersih) ? $print->rincian_penjualan->bersih : NULL;
	
			$print->total_penjualan_tunai = $penjualan_bersih + $total_retur_penjualan + $register;
	
			$print->laba_penjualan = $laba_penjualan + $laba_retur_penjualan;
	
			$print->hpp = $print->total_stok_awal - $print->total_penjualan_tunai;
	
			$print->penjualan_tunai_kasir = $penjualan_bersih - $total_retur;
		
			$print->total_pendapatan = $saldo_awal + $print->penjualan_tunai_kasir + $piutang;
	
		
	
			$total_biaya_lampung = isset($print->rincian_biaya->total) ? $print->rincian_biaya->total : NULL;
			// $total_biaya_jakarta = isset($print->rincian_biaya_jakarta->total) ? $print->rincian_biaya_jakarta->total : NULL;
			$print->hppretur = $total_retur - $laba_retur_penjualan;

			$print->persediaanakhir = $print->hpp + $print->hppretur;
			
			$print->bruto = $print->laba_penjualan - $laba_retur_penjualan;
			$print->totalbiaya = $total_biaya_lampung;
	
			$print->labaoperasional = $print->bruto - $print->totalbiaya;
			
			return $print;
	}
	public function printtahun($config = array()){
        extract($config);
		$tgl = isset($params['filter']['tgltahun']) ? $params['filter']['tgltahun']: null;
		$cabang = isset($params['filter']['id_cabang']) ? $params['filter']['id_cabang']: null;
		$akun = array(1, 2);
		// $years		=  date('Y', strtotime($tgl));
		$print = (object)[];
		$print->stok 	= $this->db->query('SELECT *,SUM(produk.harga_beli * stok.qty) AS persediaan, CONCAT(('.$tgl.') - INTERVAL 1 DAY ) AS asu
		FROM
		  stok 
		LEFT JOIN produk
		ON produk.`id` = stok.`id_produk` 
		WHERE `stok`.`tgl` >= "2021-05-20" and (YEAR(stok.`tgl`) < "'.$tgl.'" 
		  AND ref_table = "pembelian")
		  AND (produk.`id_cabang` = '.$cabang.'
		  OR ref_table != "pembelian" 
		  AND YEAR(stok.`tgl`) < "'.$tgl.'"  - INTERVAL 1 DAY) 
		  HAVING(SUM(stok.`qty`) > 0)')->row();
		$print->rincian_pembelian 	= $this->db
			->select(' *, sum(pembelian.total_rincian) as persediaan, sum(pembelian.total_pembayaran) as bersih ')
			->from('pembelian')
			->where(array('YEAR(pembelian.tgl_buat)'=>$tgl, 'pembelian.id_cabang' => $cabang))->get()->row();
		$print->retur_pembelian 	= $this->db
			->select(' *, sum(retur_pembelian.nominal) as totalretur ')
			->from('retur_pembelian')
			->where(array('YEAR(retur_pembelian.tgl_nota)'=>$tgl, 'retur_pembelian.id_cabang' => $cabang))->get()->row();
		$print->rincian_biaya_jakarta 	= $this->db
			->select(' sum(rincian_biaya_jakarta.total) as total ')
			->from('rincian_biaya_jakarta')
			->join('transaksi','transaksi.id=rincian_biaya_jakarta.id_transaksi','left')
			->where(array('YEAR(rincian_biaya_jakarta.tgl)'=>$tgl, 'transaksi.id_cabang' => $cabang))->get()->row();
		$print->rincian_piutang 	= $this->db
			->select(' *, sum(jurnal.kredit) as nominal')
			->from('jurnal')
			->join('akun','akun.id = jurnal.id_akun', 'left')
			->where_in('akun.induk', $akun)
			->where(array('YEAR(jurnal.tgl)'=>$tgl, 'jurnal.id_cabang' => $cabang, 'jurnal.ref_table' => 'pelunasan'))->get()->row();
		$print->rincian_penjualan 	= $this->db
			->select(' *, sum(penjualan.total_tagihan) as persediaan, sum(penjualan.total_pelunasan) as bersih')
			->from('penjualan')
			->where(array('YEAR(penjualan.tgl_nota)'=>$tgl, 'penjualan.id_cabang' => $cabang))->get()->row();
		$print->rincian_biaya 	= $this->db
			->select('r.tgl as tgl, sum(r.gaji) as gaji, sum(r.bulanan) as bulanan, sum(r.listrik) as listrik, sum(r.angkut) as angkut, sum(r.ekspedisi) as ekspedisi, sum(r.peralatan) as peralatan, sum(r.konsumsi) as konsumsi, sum(r.dll) as dll, sum(r.total) as total ')
			->from('rincian_biaya_lampung as r')
			->join('transaksi','transaksi.id=r.id_transaksi','left')
			->where(array('YEAR(r.tgl)'=>$tgl, 'transaksi.id_cabang' => $cabang))->get()->row();
			
		$print->kasir = $this->db
			->select(' sum(transaksi.modal) as modal, sum(transaksi.biaya) as biaya ')
			->from('transaksi')
			->where(array('YEAR(transaksi.tgl_nota)'=>$tgl, 'transaksi.id_cabang' => $cabang))->get()->row();

		$print->rincian_kasir_tunai = $this->db->select('akun.id AS id_akun, akun.nama AS akun, sum(rincian_transaksi.total) AS total')->from('rincian_transaksi')
			->join('akun','akun.id=rincian_transaksi.id_akun','left')
			->join('transaksi','transaksi.id = rincian_transaksi.id_transaksi','left')
			->where(array('YEAR(transaksi.tgl_nota)'=>$tgl, 'rincian_transaksi.tipe'=>'pendapatan', 'rincian_transaksi.id_akun'=> 13 ,'transaksi.id_cabang' => $cabang))->get()->row();
	
		$print->rincian_kasir_bri = $this->db->select('akun.id AS id_akun, akun.nama AS akun, sum(rincian_transaksi.total) AS total')->from('rincian_transaksi')
			->join('akun','akun.id=rincian_transaksi.id_akun','left')
			->join('transaksi','transaksi.id = rincian_transaksi.id_transaksi','left')
			->where(array('YEAR(transaksi.tgl_nota)'=>$tgl, 'rincian_transaksi.tipe'=>'pendapatan', 'rincian_transaksi.id_akun'=> 15,'transaksi.id_cabang' => $cabang ))->get()->row();

		$print->rincian_kasir_bca = $this->db->select('akun.id AS id_akun, akun.nama AS akun, sum(rincian_transaksi.total) AS total')->from('rincian_transaksi')
			->join('akun','akun.id=rincian_transaksi.id_akun','left')
			->join('transaksi','transaksi.id = rincian_transaksi.id_transaksi','left')
			->where(array('YEAR(transaksi.tgl_nota)'=>$tgl, 'rincian_transaksi.tipe'=>'pendapatan', 'rincian_transaksi.id_akun'=> 16,'transaksi.id_cabang' => $cabang ))->get()->row();

		$print->rincian_kasir_bni = $this->db->select('akun.id AS id_akun, akun.nama AS akun, sum(rincian_transaksi.total) AS total')->from('rincian_transaksi')
			->join('akun','akun.id=rincian_transaksi.id_akun','left')
			->join('transaksi','transaksi.id = rincian_transaksi.id_transaksi','left')
			->where(array('YEAR(transaksi.tgl_nota)'=>$tgl, 'rincian_transaksi.tipe'=>'pendapatan', 'rincian_transaksi.id_akun'=> 20,'transaksi.id_cabang' => $cabang ))->get()->row();

		$print->rincian_kasir_mandiri = $this->db->select('akun.id AS id_akun, akun.nama AS akun, sum(rincian_transaksi.total) AS total')->from('rincian_transaksi')
			->join('akun','akun.id=rincian_transaksi.id_akun','left')
			->join('transaksi','transaksi.id = rincian_transaksi.id_transaksi','left')
			->where(array('YEAR(transaksi.tgl_nota)'=>$tgl, 'rincian_transaksi.tipe'=>'pendapatan', 'rincian_transaksi.id_akun'=> 19,'transaksi.id_cabang' => $cabang ))->get()->row();

		$print->laba_penjualan 	= $this->db->select('
		sum(penjualan.laba_akhir - penjualan.diskon) as laba ')
		->from('penjualan')
			->where(array('YEAR(penjualan.tgl_nota)'=>$tgl, 'penjualan.id_cabang' => $cabang))->get()->row();
		$print->retur_penjualan 	= $this->db
			->select(' *, sum(retur_penjualan.nominal) as nominal')
			->from('retur_penjualan')
			->where(array('YEAR(DATE(retur_penjualan.tgl_nota))'=>$tgl, 'retur_penjualan.id_cabang' => $cabang))->get()->row();
		$print->laba_retur_penjualan 	= $this->db
			->select(' *, sum(produk.laba) as laba ')
			->from('retur_penjualan')
			->join('rincian_retur_penjualan','rincian_retur_penjualan.id_retur_penjualan = retur_penjualan.id', 'left')
			->join('produk','rincian_retur_penjualan.id_produk = produk.id','left')
			->where(array('YEAR(DATE(retur_penjualan.tgl_nota))'=>$tgl, 'retur_penjualan.id_cabang' => $cabang))->get()->row();
		
		
			$saldo_awal = isset($print->kasir->modal) ? $print->kasir->modal : NULL;
			$piutang = isset($print->rincian_piutang->nominal) ? $print->rincian_piutang->nominal : NULL;
			$biaya_kasir = isset($print->kasir->biaya) ? $print->kasir->biaya : NULL;
			$persediaan_awal = isset($print->stok->persediaan) ? $print->stok->persediaan : NULL;
			$pembelian = isset($print->rincian_pembelian->persediaan) ? $print->rincian_pembelian->persediaan : NULL;
			$total_retur_pembelian = isset($print->retur_pembelian->totalretur) ? $print->retur_pembelian->totalretur : NULL;
			$print->pembelian_bersih = $pembelian - $total_retur_pembelian;
			$print->total_stok_awal = $persediaan_awal + $print->pembelian_bersih;
			$laba_penjualan = isset($print->laba_penjualan->laba) ? $print->laba_penjualan->laba : NULL;
			$total_retur_penjualan = isset($print->retur_penjualan->totalretur) ? $print->retur_penjualan->totalretur : NULL;
			$total_retur = isset($print->retur_penjualan->nominal) ? $print->retur_penjualan->nominal : NULL;
	
			$laba_retur_penjualan = isset($print->laba_retur_penjualan->laba) ? $print->laba_retur_penjualan->laba : NULL;
			$register = isset($print->kasir->register) ? $print->kasir->register : NULL;
	
			$total_retur_penjualan = isset($print->retur_penjualan->nominal) ? $print->retur_penjualan->nominal : NULL;
	
			$penjualan_bersih = isset($print->rincian_penjualan->bersih) ? $print->rincian_penjualan->bersih : NULL;
	
			$print->total_penjualan_tunai = $penjualan_bersih + $total_retur_penjualan + $register;
	
			$print->laba_penjualan = $laba_penjualan + $laba_retur_penjualan;
	
			$print->hpp = $print->total_stok_awal - $print->total_penjualan_tunai;
	
			$print->penjualan_tunai_kasir = $penjualan_bersih - $total_retur;
		
			$print->total_pendapatan = $saldo_awal + $print->penjualan_tunai_kasir + $piutang;
	
	
			$total_biaya_lampung = isset($print->rincian_biaya->total) ? $print->rincian_biaya->total : NULL;
			$total_biaya_jakarta = isset($print->rincian_biaya_jakarta->total) ? $print->rincian_biaya_jakarta->total : NULL;
			$print->hppretur = $total_retur - $laba_retur_penjualan;
			$print->persediaanakhir = $print->hpp + $print->hppretur;
			$print->bruto = $print->laba_penjualan - $laba_retur_penjualan;
			$print->totalbiaya = $total_biaya_lampung + $total_biaya_jakarta;
	
			$print->labaoperasional = $print->bruto - $print->totalbiaya;
			
			return $print;
	}




}