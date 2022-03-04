<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class GT_Model extends CI_Model {

	protected $akun_induk;

	public function __construct(){
		parent::__construct();
		$this->akun_induk = (object)[
			'kas' 						=> 1,
			'bank' 						=> 2,
			'piutang'					=> 3,
			'persediaan_barang' 		=> 4,
			'hutang' 					=> 5,
			'modal'						=> 6,
			'laba_rugi' 				=> 7,
			'pendapatan'				=> 8,
			'penjualan' 				=> 9,
			'pembelian' 				=> 10,
			'hpp'						=> 11,
			'biaya'						=> 11
		];
	}

}