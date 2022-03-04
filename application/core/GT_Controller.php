<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class GT_Controller extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->auth 	= (object)$this->session->userdata('auth');
		$this->user 	= (object)$this->session->userdata('user');
		$this->cabang 	= (object)$this->session->userdata('cabang');
		$this->is_login();
	}
	protected function is_login($redirect_to = NULL){
        if(	!isset($this->auth) || !isset($this->auth->status) || $this->auth->status !== 'logged_in' ){ 
            redirect(base_url('auth?redirect=' . $redirect_to));
            exit;
        }
	}
	private function data_navigation(){
		if(isset($this->user->hak_akses) == 'Super Admin'){
			$jenis 		= $this->cabang->jenis == 'Pusat';
			$hak_akses  = $this->user->hak_akses == 'Super Admin';
		}else{
			$hak_akses 	= isset($this->user->hak_akses) && !empty($this->user->hak_akses) ? $this->user->hak_akses: 'Super Admin';
		}
		$jenis 		= isset($this->cabang->jenis) && !empty($this->cabang->jenis) ? $this->cabang->jenis : 'Pusat';
		$hak_akses 	= isset($this->user->hak_akses) && !empty($this->user->hak_akses) ? $this->user->hak_akses: 'Super Admin';
		$user_menu = [
			'Pusat' => [
				'Super Admin' => [
					[ 
						'title' 	=> 'Beranda',
						'icon' 		=> 'fa fa-home',
						'href'		=> 'beranda'
					],
					[ 
						'is_heading' 	=> TRUE,
						'title' 		=> 'Menu Utama',
					],
					[ 
						'title' 	=> 'Master Data',
						'icon' 		=> 'fa fa-database',
						'childs'	=> [
							[ 
								'title' 	=> 'Bagan Akun',
								'childs'	=> [
									[ 
										'title' => 'Kas',
										'href'	=> 'master/bagan-akun/kas'
									],
									[ 
										'title' => 'Bank',
										'href'	=> 'master/bagan-akun/bank'
									],
									[ 
										'title' => 'Biaya-biaya',
										'href'	=> 'master/bagan-akun/biaya'
									],
								],
							],
							[ 
								'title' => 'Supplier',
								'href'	=> 'master/pemasok'
							],
							[ 
								'title' => 'Produk',
								'href'	=> 'master/produk'
							],
							[ 
								'title' => 'Pelanggan',
								'href'	=> 'master/pelanggan'
							],
							[ 
								'title' => 'Giro',
								'href'	=> 'master/giro'
							],
							[ 
								'title' => 'Kode Laba',
								'href'	=> 'master/kode-laba'
							]
						]
					],
					[ 
						'title' 	=> 'Pembelian',
						'icon' 		=> 'fa fa-shopping-cart',
						'childs'	=> [
							[ 
								'title' 	=> 'Faktur Pembelian (PO)',
								'href'		=> 'pembelian/faktur'
							],
							[ 
								'title' 	=> 'Pembayaran Pembelian',
								'href'		=> 'pembelian/pembayaran'
							],
							[ 
								'title' 	=> 'Retur Pembelian',
								'href'		=> 'pembelian/retur'
							],
							[ 
								'title' 	=> 'Pencairan Giro',
								'href'		=> 'pembelian/giro'
							]
						]
					],
					[ 
						'title' 	=> 'Penjualan',
						'icon' 		=> 'fa fa-store',
						'childs'	=> [
							[ 
								'title' 	=> 'Faktur Penjualan (SO)',
								'href'		=> 'penjualan/faktur'
							],
							[ 
								'title' 	=> 'Pelunasan Penjualan',
								'href'		=> 'penjualan/pelunasan'
							],
							[ 
								'title' 	=> 'Retur Penjualan',
								'href'		=> 'penjualan/retur'
							],
							[ 
								'title' 	=> 'Pencairan Giro',
								'href'		=> 'penjualan/giro'
							]
						]
					],
					[ 
						'title' 	=> 'Transaksi',
						'icon' 		=> 'fa fa-cash-register',
						'childs'	=> [
							[ 
								'title' 	=> 'Pendapatan',
								'href'		=> 'transaksi/pendapatan'
							],
							[ 
								'title' 	=> 'Pengeluaran',
								'href'		=> 'transaksi/pengeluaran'
							],
							[ 
								'title' 	=> 'Transfer',
								'href'		=> 'transaksi/transfer'
							],
							[ 
								'title' 	=> 'Kasir',
								'href'		=> 'transaksi/kasir'
							],
							[ 
								'title' 	=> 'Jakarta',
								'href'		=> 'transaksi/jakarta'
							],
						]
					],
					[ 
						'title' 	=> 'Inventori',
						'icon' 		=> 'fa fa-box',
						'childs'	=> [
							[ 
								'title' 	=> 'Saldo Stok',
								'href'		=> 'inventori/saldo-stok'
							],
							[ 
								'title' 	=> 'Stok Opname',
								'href'		=> 'inventori/stok-opname'
							]
						]
					],
					[ 
						'title' 	=> 'Laporan',
						'icon' 		=> 'fa fa-chart-line',
						'childs'	=> [
							[ 
								'title' 	=> 'Laporan Pembelian',						
								'childs'	=> [
									[ 
										'title' 	=> 'Laporan Pembelian',
										'href'		=> 'laporan/laporan-pembelian',
									],
									[ 
										'title' 	=> 'Laporan Retur Pembelian',
										'href'		=> 'laporan/laporan-retur-pembelian'
									]
								]
							],
							[ 
								'title' 	=> 'Laporan Penjualan',
								'childs'	=> [
									[ 
										'title' 	=> 'Laporan Penjualan',
										'href'		=> 'laporan/laporan-penjualan',
									],
									[ 
										'title' 	=> 'Laporan Retur Penjualan',
										'href'		=> 'laporan/laporan-retur-penjualan'
									]
								]
							],
							[ 
								'title' 	=> 'Laporan Kas',
								'href'		=> 'laporan/laporan-kas'
							],
							[ 
								'title' 	=> 'Laporan Biaya Lampung',
								'href'		=> 'laporan/laporan-biaya-lampung'
							],
							// [ 
							// 	'title' 	=> 'Laporan Biaya Jakarta',
							// 	'href'		=> 'laporan/laporan-biaya-jakarta'
							// ],
							// [ 
							// 	'title' 	=> 'Laporan Buku Bank',
							// 	'href'		=> 'laporan/laporan-buku-bank'
							// ],
							// [ 
							// 	'title' 	=> 'Laporan Giro',
							// 	'href'		=> 'laporan/laporan-giro'
							// ],
							[ 
								'title' 	=> 'Laporan Harian',
								'href'		=> 'laporan/laporan-harian'
							]
							// [ 
							// 	'title' 	=> 'Laporan Bulanan',
							// 	'href'		=> 'laporan/laporan-bulanan'
							// ],
							// [ 
							// 	'title' 	=> 'Laporan Tahunan',
							// 	'href'		=> 'laporan/laporan-tahunan'
							// ]
						]
					],
					[ 
						'is_heading' 	=> TRUE,
						'title' 		=> 'Settings',
					],
					[ 
						'title' 	=> 'Pengaturan',
						'icon' 		=> 'fa fa-cogs',
						'childs'	=> [
							[ 
								'title' 	=> 'Pengguna',
								'href'		=> 'pengaturan/pengguna'
							]
						]
					]
				] 
			],
			'Pengadaan' => [
				'Admin' => [
					[ 
						'title' 	=> 'Beranda',
						'icon' 		=> 'fa fa-home',
						'href'		=> 'beranda'
					],
					[ 
						'is_heading' 	=> TRUE,
						'title' 		=> 'Menu Utama',
					],
					[ 
						'title' 	=> 'Pembelian',
						'icon' 		=> 'fa fa-shopping-cart',
						'childs'	=> [
							[ 
								'title' 	=> 'Faktur Pembelian (PO)',
								'href'		=> 'pembelian/faktur'
							],
							[ 
								'title' 	=> 'Pembayaran Pembelian',
								'href'		=> 'pembelian/pembayaran'
							],
							[ 
								'title' 	=> 'Retur Pembelian',
								'href'		=> 'pembelian/retur'
							]
						]
					],
					[ 
						'title' 	=> 'Transaksi',
						'icon' 		=> 'fa fa-cash-register',
						'childs'	=> [
							[ 
								'title' 	=> 'Pengeluaran',
								'href'		=> 'transaksi/pengeluaran'
							]
						]
					]
				],
				'Kasir' => [
					[ 
						'title' 	=> 'Penjualan',
						'icon' 		=> 'fa fa-store',
						'childs'	=> [
							[ 
								'title' 	=> 'Faktur Penjualan (SO)',
								'href'		=> 'penjualan/faktur'
							],
							[ 
								'title' 	=> 'Pelunasan Penjualan',
								'href'		=> 'penjualan/pelunasan'
							],
							[ 
								'title' 	=> 'Retur Penjualan',
								'href'		=> 'penjualan/retur'
							]
						]
					],
					[ 
						'title' 	=> 'Transaksi',
						'icon' 		=> 'fa fa-cash-register',
						'childs'	=> [
							[ 
								'title' 	=> 'Pendapatan',
								'href'		=> 'transaksi/pendapatan'
							],
							[ 
								'title' 	=> 'Jakarta',
								'href'		=> 'transaksi/jakarta'
							]
						]
					],
				]
			],
				'Toko' => [
					'Super Admin' => [
						[ 
							'title' 	=> 'Beranda',
							'icon' 		=> 'fa fa-home',
							'href'		=> 'beranda'
						],
						[ 
							'is_heading' 	=> TRUE,
							'title' 		=> 'Menu Utama',
						],
						[ 
							'title' 	=> 'Master Data',
							'icon' 		=> 'fa fa-database',
							'childs'	=> [
								[ 
									'title' 	=> 'Bagan Akun',
									'childs'	=> [
										[ 
											'title' => 'Kas',
											'href'	=> 'master/bagan-akun/kas'
										],
										[ 
											'title' => 'Bank',
											'href'	=> 'master/bagan-akun/bank'
										]
									],
								],
								[ 
									'title' => 'Supplier',
									'href'	=> 'master/pemasok'
								],
										[ 
									'title' => 'Cabang',
									'href'	=> 'master/cabang'
								],
								[ 
									'title' => 'Produk',
									'href'	=> 'master/produk'
								],
								[ 
									'title' => 'Pelanggan',
									'href'	=> 'master/pelanggan'
								],
								[ 
									'title' => 'Giro',
									'href'	=> 'master/giro'
								],
								[ 
									'title' => 'Kode Laba',
									'href'	=> 'master/kode-laba'
								]
							]
						],
						[ 
							'title' 	=> 'Pembelian',
							'icon' 		=> 'fa fa-shopping-cart',
							'childs'	=> [
								[ 
									'title' 	=> 'Faktur Pembelian (PO)',
									'href'		=> 'pembelian/faktur'
								],
								[ 
									'title' 	=> 'Penerimaan Barang (RO)',
									'href'		=> 'pembelian/ro'
								],
								[ 
									'title' 	=> 'Pembayaran Pembelian',
									'href'		=> 'pembelian/pembayaran'
								],
								[ 
									'title' 	=> 'Pembayaran Pembelian Multi Nota',
									'href'		=> 'pembelian/pembayaranmulti'
								],
								[ 
									'title' 	=> 'Retur Pembelian',
									'href'		=> 'pembelian/retur'
								],
								// [ 
								// 	'title' 	=> 'Pencairan Giro',
								// 	'href'		=> 'pembelian/giro'
								// ]
							]
						],
						[ 
							'title' 	=> 'Penjualan',
							'icon' 		=> 'fa fa-store',
							'childs'	=> [
								[ 
									'title' 	=> 'Faktur Penjualan (SO)',
									'href'		=> 'penjualan/faktur'
								],
								[ 
									'title' 	=> 'Pelunasan Penjualan',
									'href'		=> 'penjualan/pelunasan'
								],
								[ 
									'title' 	=> 'Retur Penjualan',
									'href'		=> 'penjualan/retur'
								],
								// [ 
								// 	'title' 	=> 'Pencairan Giro (ON PROGRESS)',
								// 	'href'		=> 'penjualan/giro'
								// ]
							]
						],
						[ 
							'title' 	=> 'Transaksi',
							'icon' 		=> 'fa fa-cash-register',
							'childs'	=> [
								// [ 
								// 	'title' 	=> 'Pendapatan',
								// 	'href'		=> 'transaksi/pendapatan'
								// ],
								// [ 
								// 	'title' 	=> 'Pengeluaran',
								// 	'href'		=> 'transaksi/pengeluaran'
								// ],
								// [ 
								// 	'title' 	=> 'Transfer',
								// 	'href'		=> 'transaksi/transfer'
								// ],
								// [ 
								// 	'title' 	=> 'Pencairan Giro',
								// 	'href'		=> 'transaksi/giro'
								// ],
								[ 
									'title' 	=> 'Kasir',
									'href'		=> 'transaksi/kasir'
								],
								[ 
									'title' 	=> 'Jakarta',
									'href'		=> 'transaksi/jakarta'
								],
							]
						],
						[ 
							'title' 	=> 'Inventori',
							'icon' 		=> 'fa fa-box',
							'childs'	=> [
								[ 
									'title' 	=> 'Saldo Stok',
									'href'		=> 'inventori/saldo-stok'
								],
								[ 
									'title' 	=> 'Stok Opname',
									'href'		=> 'inventori/stok-opname'
								]
							]
						],
						[ 
							'title' 	=> 'Laporan',
							'icon' 		=> 'fa fa-chart-line',
							'childs'	=> [
								// [ 
								// 	'title' 	=> 'Laporan Barang',
								// 	'childs'	=> [
								// 		[ 
								// 			'title' 	=> 'Barang Masuk Keluar',
								// 			'href'		=> 'laporan/laporan-barang-masuk-keluar',
								// 		],
								// 		[ 
								// 			'title' 	=> 'Laporan Stok',
								// 			'href'		=> 'laporan/laporan-stok'
								// 		]
								// 	]
								// ],
								[ 
									'title' 	=> 'Laporan Pembelian',						
									'childs'	=> [
										[ 
											'title' 	=> 'Laporan Pembelian',
											'href'		=> 'laporan/lap-pembelian/laporan-pembelian',
										],
										[ 
											'title' 	=> 'Laporan Retur Pembelian',
											'href'		=> 'laporan/lap-pembelian/laporan-retur'
										],
										[ 
											'title' 	=> 'Laporan Bon',
											'href'		=> 'laporan/lap-pembelian/Lap_bon'
										]
									]
								],
								[ 
									'title' 	=> 'Laporan Penjualan',
									'childs'	=> [
										[ 
											'title' 	=> 'Laporan Penjualan',
											'href'		=> 'laporan/laporan-penjualan',
										],
										[ 
											'title' 	=> 'Laporan Retur Penjualan',
											'href'		=> 'laporan/laporan-retur-penjualan'
										]
									]
								],
								[ 
									'title' 	=> 'Laporan Kas',
									'href'		=> 'laporan/laporan-kas'
								],
								[ 
									'title' 	=> 'Laporan Biaya Lampung',
									'href'		=> 'laporan/laporan-biaya-lampung'
								],
								[ 
									'title' 	=> 'Laporan Biaya Jakarta',
									'href'		=> 'laporan/laporan-biaya-jakarta'
								],
								[ 
									'title' 	=> 'Laporan Buku Bank',
									'href'		=> 'laporan/laporan-buku-bank'
								],
								[ 
									'title' 	=> 'Laporan Giro',
									'href'		=> 'laporan/laporan-giro'
								],
								[ 
									'title' 	=> 'Laporan Hari/Bulan/Tahun',
									'href'		=> 'laporan/laporan-harian'
								]
								// [ 
								// 	'title' 	=> 'Laporan Tahunan',
								// 	'href'		=> 'laporan/laporan-tahunan'
								// ]
							]
						],
						[ 
							'is_heading' 	=> TRUE,
							'title' 		=> 'Settings',
						],
						[ 
							'title' 	=> 'Pengaturan',
							'icon' 		=> 'fa fa-cogs',
							'childs'	=> [
								[ 
									'title' 	=> 'Pengguna',
									'href'		=> 'pengaturan/pengguna'
								],
								[ 
									'title' 	=> 'Aplikasi',
									'href'		=> 'pengaturan/aplikasi'
								]
							]
						]
					] ,
					'Admin' => [
						[ 
							'title' 	=> 'Master Data',
							'icon' 		=> 'fa fa-database',
							'childs'	=> [
								[ 
									'title' 	=> 'Bagan Akun',
									'childs'	=> [
										[ 
											'title' => 'Kas',
											'href'	=> 'master/bagan-akun/kas'
										],
										[ 
											'title' => 'Bank',
											'href'	=> 'master/bagan-akun/bank'
										]
										// [ 
										// 	'title' => 'Biaya-biaya',
										// 	'href'	=> 'master/bagan-akun/biaya'
										// ],
									],
								],
								[ 
									'title' => 'Supplier',
									'href'	=> 'master/pemasok'
								],
								[ 
									'title' => 'Produk',
									'href'	=> 'master/produk'
								],
								[ 
									'title' => 'Pelanggan',
									'href'	=> 'master/pelanggan'
								],
								[ 
									'title' => 'Giro',
									'href'	=> 'master/giro'
								],
								[ 
									'title' => 'Kode Laba',
									'href'	=> 'master/kode-laba'
								]
							]
						],
						[ 
							'title' 	=> 'Pembelian',
							'icon' 		=> 'fa fa-shopping-cart',
							'childs'	=> [
								[ 
									'title' 	=> 'Faktur Pembelian (PO)',
									'href'		=> 'pembelian/faktur'
								],
								[ 
									'title' 	=> 'Pembayaran Pembelian',
									'href'		=> 'pembelian/pembayaran'
								],
								[ 
									'title' 	=> 'Pembayaran Pembelian Multi Nota',
									'href'		=> 'pembelian/pembayaranmulti'
								],
								[ 
									'title' 	=> 'Retur Pembelian',
									'href'		=> 'pembelian/retur'
								],
								// [ 
								// 	'title' 	=> 'Pencairan Giro',
								// 	'href'		=> 'pembelian/giro'
								// ]
							]
						],
						[ 
							'title' 	=> 'Penjualan',
							'icon' 		=> 'fa fa-store',
							'childs'	=> [
								[ 
									'title' 	=> 'Faktur Penjualan (SO)',
									'href'		=> 'penjualan/faktur'
								],
								[ 
									'title' 	=> 'Pelunasan Penjualan',
									'href'		=> 'penjualan/pelunasan'
								],
								[ 
									'title' 	=> 'Retur Penjualan',
									'href'		=> 'penjualan/retur'
								]
							]
						],
						[ 
							'title' 	=> 'Transaksi',
							'icon' 		=> 'fa fa-cash-register',
							'childs'	=> [
								// [ 
								// 	'title' 	=> 'Pendapatan',
								// 	'href'		=> 'transaksi/pendapatan'
								// ],
								// [ 
								// 	'title' 	=> 'Pengeluaran',
								// 	'href'		=> 'transaksi/pengeluaran'
								// ],
								// [ 
								// 	'title' 	=> 'Transfer',
								// 	'href'		=> 'transaksi/transfer'
								// ],
								[ 
									'title' 	=> 'Kasir',
									'href'		=> 'transaksi/kasir'
								],
								[ 
									'title' 	=> 'Jakarta',
									'href'		=> 'transaksi/jakarta'
								],
							]
						],
						[ 
							'title' 	=> 'Inventori',
							'icon' 		=> 'fa fa-box',
							'childs'	=> [
								[ 
									'title' 	=> 'Saldo Stok',
									'href'		=> 'inventori/saldo-stok'
								],
								[ 
									'title' 	=> 'Stok Opname',
									'href'		=> 'inventori/stok-opname'
								]
							]
						],
						[ 
							'title' 	=> 'Laporan',
							'icon' 		=> 'fa fa-chart-line',
							'childs'	=> [
								[ 
									'title' 	=> 'Laporan Pembelian',						
									'childs'	=> [
										[ 
											'title' 	=> 'Laporan Pembelian',
											'href'		=> 'laporan/laporan-pembelian',
										],
										[ 
											'title' 	=> 'Laporan Retur Pembelian',
											'href'		=> 'laporan/laporan-retur-pembelian'
										],
										[ 
											'title' 	=> 'Laporan Bon',
											'href'		=> 'laporan/laporan-pembelian/bon'
										]
									]
								],
								[ 
									'title' 	=> 'Laporan Penjualan',
									'childs'	=> [
										[ 
											'title' 	=> 'Laporan Penjualan',
											'href'		=> 'laporan/laporan-penjualan',
										],
										[ 
											'title' 	=> 'Laporan Retur Penjualan',
											'href'		=> 'laporan/laporan-retur-penjualan'
										]
									]
								],
								[ 
									'title' 	=> 'Laporan Kas',
									'href'		=> 'laporan/laporan-kas'
								],
								[ 
									'title' 	=> 'Laporan Biaya Lampung',
									'href'		=> 'laporan/laporan-biaya-lampung'
								],
								[ 
									'title' 	=> 'Laporan Biaya Jakarta',
									'href'		=> 'laporan/laporan-biaya-jakarta'
								],
								[ 
									'title' 	=> 'Laporan Buku Bank',
									'href'		=> 'laporan/laporan-buku-bank'
								],
								[ 
									'title' 	=> 'Laporan Giro',
									'href'		=> 'laporan/laporan-giro'
								],
								[ 
									'title' 	=> 'Laporan Hari/Bulan/Tahun',
									'href'		=> 'laporan/laporan-harian'
								]
							]
						],
					],
					// 'Kepala Toko' => [
					// 	[ 
					// 		'title' 	=> 'Pembelian',
					// 		'icon' 		=> 'fa fa-shopping-cart',
					// 		'childs'	=> [
					// 			[ 
					// 				'title' 	=> 'Faktur Pembelian (PO)',
					// 				'href'		=> 'pembelian/faktur'
					// 			],
					// 		]
					// 	],
					// ],
					'Kepala Toko' => [
						[ 
							'title' 	=> 'Pembelian',
							'icon' 		=> 'fa fa-shopping-cart',
							'childs'	=> [
								[ 
											'title' 	=> 'Faktur Pembelian (PO)',
											'href'		=> 'pembelian/faktur'
										],
									]
								],
						[ 
							'title' 	=> 'Penjualan',
							'icon' 		=> 'fa fa-store',
							'childs'	=> [
								[ 
									'title' 	=> 'Faktur Penjualan (SO)',
									'href'		=> 'penjualan/faktur'
								],
								[ 
									'title' 	=> 'Pelunasan Penjualan',
									'href'		=> 'penjualan/pelunasan'
								],
								[ 
									'title' 	=> 'Retur Penjualan',
									'href'		=> 'penjualan/retur'
								]
							]
						],
						[ 
							'title' 	=> 'Transaksi',
							'icon' 		=> 'fa fa-cash-register',
							'childs'	=> [
								// [ 
								// 	'title' 	=> 'Transfer',
								// 	'href'		=> 'transaksi/transfer'
								// ],
								[ 
									'title' 	=> 'Kasir',
									'href'		=> 'transaksi/kasir'
								],
								// [ 
								// 	'title' 	=> 'Jakarta',
								// 	'href'		=> 'transaksi/jakarta'
								// ],
							]
						],
						[ 
							'title' 	=> 'Inventori',
							'icon' 		=> 'fa fa-box',
							'childs'	=> [
								[ 
									'title' 	=> 'Saldo Stok',
									'href'		=> 'inventori/saldo-stok'
								]
							]
						],
					],
					'Kasir' => [
						[ 
							'title' 	=> 'Pembelian',
							'icon' 		=> 'fa fa-shopping-cart',
							'childs'	=> [
								[ 
											'title' 	=> 'Faktur Pembelian (PO)',
											'href'		=> 'pembelian/faktur'
										],
									]
								],
						[ 
							'title' 	=> 'Penjualan',
							'icon' 		=> 'fa fa-store',
							'childs'	=> [
								[ 
									'title' 	=> 'Faktur Penjualan (SO)',
									'href'		=> 'penjualan/faktur'
								],
								[ 
									'title' 	=> 'Pelunasan Penjualan',
									'href'		=> 'penjualan/pelunasan'
								],
								[ 
									'title' 	=> 'Retur Penjualan',
									'href'		=> 'penjualan/retur'
								]
							]
						],
						[ 
							'title' 	=> 'Transaksi',
							'icon' 		=> 'fa fa-cash-register',
							'childs'	=> [
								// [ 
								// 	'title' 	=> 'Transfer',
								// 	'href'		=> 'transaksi/transfer'
								// ],
								[ 
									'title' 	=> 'Kasir',
									'href'		=> 'transaksi/kasir'
								],
								// [ 
								// 	'title' 	=> 'Jakarta',
								// 	'href'		=> 'transaksi/jakarta'
								// ],
							]
						],
						[ 
							'title' 	=> 'Inventori',
							'icon' 		=> 'fa fa-box',
							'childs'	=> [
								[ 
									'title' 	=> 'Saldo Stok',
									'href'		=> 'inventori/saldo-stok'
								]
							]
						],
					],
					'Kasir_jakarta' => [
						[ 
							'title' 	=> 'Pembelian',
							'icon' 		=> 'fa fa-shopping-cart',
							'childs'	=> [
								[ 
									'title' 	=> 'Faktur Pembelian (PO)',
									'href'		=> 'pembelian/faktur'
								],
								[ 
									'title' 	=> 'Pembayaran Pembelian',
									'href'		=> 'pembelian/pembayaran'
								],
								[ 
									'title' 	=> 'Pembayaran Pembelian Multi Nota',
									'href'		=> 'pembelian/pembayaranmulti'
								],
								[ 
									'title' 	=> 'Retur Pembelian',
									'href'		=> 'pembelian/retur'
								],
								// [ 
								// 	'title' 	=> 'Pencairan Giro',
								// 	'href'		=> 'pembelian/giro'
								// ]
							]
						],
						[ 
							'title' 	=> 'Transaksi',
							'icon' 		=> 'fa fa-cash-register',
							'childs'	=> [
								// [ 
								// 	'title' 	=> 'Transfer',
								// 	'href'		=> 'transaksi/transfer'
								// ],
								[ 
									'title' 	=> 'Kasir',
									'href'		=> 'transaksi/kasir'
								],
								// [ 
								// 	'title' 	=> 'Jakarta',
								// 	'href'		=> 'transaksi/jakarta'
								// ],
							]
						],
					]
				]
		];

		return $user_menu[$jenis][$hak_akses];
	}
	public function html_head( $data = array() ){

		$default = array(
			'title'		=> 'AHSANA APPS',
			'base_url' 	=> base_url(),
			'favicons' 	=> array(
				(object)array('rel'=>'shortcut icon', 'type'=> 'image/png', 'sizes' => '32x32', 'href' => 'assets/media/favicons/favicon.png' ),
				(object)array('rel'=>'icon', 'type'=> 'image/png', 'sizes' => '192x192', 'href' => 'assets/media/favicons/favicon-192x192.png' ),
				(object)array('rel'=>'apple-touch-icon', 'type'=> 'image/png', 'sizes' => '180x180', 'href' => 'assets/media/favicons/apple-touch-icon-180x180.png' )
			),
			'heading' 		=> FALSE,
			'stylesheets'	=> array()
		);
		$this->load->view('parts/head', 
			array(
				'user'				=> $this->user,
				'logo'				=> '',
				'brand' 			=> 'AHSANA APPS',
				'main_navigation'	=> $this->data_navigation(),
				'head' 				=> (object)array_merge($default, $data)
			)
		);
		unset($data, $default);
	}
	public function html_foot( $data = array() ){
		$default = array();
		$foot = (object)array_merge($default, $data);
		unset($data,$default);
		$this->load->view('parts/foot', $foot);
		unset($foot);
	}

}