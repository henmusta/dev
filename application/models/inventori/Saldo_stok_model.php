<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Saldo_stok_model extends CI_Model {
	private $module = [
		'name' 	=> 'Saldo Stok',
		'url'	=> 'inventori/saldo-stok',
	];
	public function __construct(){
		parent::__construct();
	}
	public function datatable($config = array()){

		extract($config);
		$columns 			= array('produk.id', 'kode_pemasok', 'nama','harga_jual', 'harga_beli','laba','saldo');
		$select_total 		= "SELECT COUNT(`produk`.`id`) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT 
			SQL_CALC_FOUND_ROWS 
			`produk`.*, 
			 produk.id as id,
		     produk.harga_beli as harga_beli,
			 produk.harga_jual as harga_jual,
			`pemasok`.`kode` AS `kode_pemasok`, 
			`pemasok`.`nama` AS `nama_pemasok`,
			 SUM(IFNULL(`stok`.`qty`,0)) AS `saldo`
		";

		$from 				= "
		FROM `produk` 
			LEFT JOIN `pemasok` ON `pemasok`.`id`=`produk`.`id_pemasok`
			LEFT JOIN `stok` ON `stok`.`id_produk`=`produk`.`id`
		";
		
		$where 				= "WHERE `produk`.`id` IS NOT NULL AND status_ro = 1 ";
		$group_by 			= "GROUP BY `produk`.`id` ";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";

		if ( isset( $params["filter"]["id_cabang"] )  && !empty($params["filter"]["id_cabang"]) ) {
			$where .= "  AND  produk.id_cabang =  ". $params["filter"]["id_cabang"] ." ";
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

	public function pembelian($config = array()){		
		extract($config);
		$select_total 		= "SELECT COUNT(DISTINCT(stok.`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT 
								stok.`transaksi` AS jenis,
								stok.`tgl` AS tgl,
								stok.`ref_text` AS nomor,
								stok.`qty` AS qty,
								produk.nama AS produk,
								pemasok.`nama` AS pemasok,
								produk.`id` AS id ";
		$from 				= " FROM
								stok 
								LEFT JOIN produk 
								ON produk.`id` = stok.`id_produk` 
								LEFT JOIN pemasok 
								ON pemasok.`id` = produk.`id_pemasok`
								left join pembelian
								on pembelian.id = stok.ref_table ";
		$where 				= "WHERE stok.transaksi = 'pembelian' ";
		$group_by 			= "GROUP BY stok.`id` ";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";

		if( isset($params["filter"]["id_produk"]) && !empty($params["filter"]["id_produk"]) ){
			$produk       = $this->db->escape_str(trim(strip_tags($params["filter"]["id_produk"])));
			$where .= "  AND  stok.id_produk =  ". $produk ." ";
		}

		$totalData 		= $this->db->query($select_total . $from . $where .  $group_by . ";")->row_array();
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

	public function penjualan($config = array()){		
		extract($config);
		$select_total 		= "SELECT COUNT(DISTINCT(stok.`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT 
								stok.`transaksi` AS jenis,
								pelanggan.nama as nama_pelanggan,
								penjualan.tgl_nota AS tgl,
								stok.`ref_text` AS nomor,
								(stok.`qty` * -1) AS qty,
								produk.nama AS produk,
								pemasok.`nama` AS pemasok,
								produk.`id` AS id ";
		$from 				= " FROM stok LEFT JOIN produk 
								ON produk.`id` = stok.`id_produk` 
							LEFT JOIN pemasok 
								ON pemasok.`id` = produk.`id_pemasok` 
							LEFT JOIN penjualan 
								ON stok.`ref_pk` = penjualan.`id` 
							LEFT JOIN pelanggan 
								ON pelanggan.`id` = penjualan.`id_pelanggan` ";
		$where 				= "WHERE stok.ref_table = 'penjualan' ";
		$group_by 			= "GROUP BY stok.`id` ";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";

		if( isset($params["filter"]["id_produk"]) && !empty($params["filter"]["id_produk"]) ){
			$produk       = $this->db->escape_str(trim(strip_tags($params["filter"]["id_produk"])));
			$where .= "  AND  stok.id_produk =  ". $produk ." ";
		}

		$totalData 		= $this->db->query($select_total . $from . $where .  $group_by . ";")->row_array();
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

	public function retur($config = array()){		
		extract($config);
		$select_total 		= "SELECT COUNT(DISTINCT(stok.`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT 
								stok.`transaksi` AS jenis,
								stok.`tgl` AS tgl,
								stok.`ref_text` AS nomor,
								stok.`qty` AS qty,
								produk.nama AS produk,
								pemasok.`nama` AS pemasok,
								produk.`id` AS id ";
		$from 				= " FROM
								stok 
								LEFT JOIN produk 
								ON produk.`id` = stok.`id_produk` 
								LEFT JOIN pemasok 
								ON pemasok.`id` = produk.`id_pemasok` ";
		$where 				= "WHERE stok.ref_table = 'retur' ";
		$group_by 			= "GROUP BY stok.`id` ";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";


		if( isset($params["filter"]["id_produk"]) && !empty($params["filter"]["id_produk"]) ){
			$produk       = $this->db->escape_str(trim(strip_tags($params["filter"]["id_produk"])));
			$where .= "  AND  stok.id_produk =  ". $produk ." ";
		}

		$totalData 		= $this->db->query($select_total . $from . $where .  $group_by . ";")->row_array();
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

	public function opname($config = array()){		
		extract($config);
		$select_total 		= "SELECT COUNT(DISTINCT(stok.`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT 
								stok.`transaksi` AS jenis,
								stok.`tgl` AS tgl,
								stok.`ref_text` AS nomor,
								stok.`qty` AS qty,
								produk.nama AS produk,
								pemasok.`nama` AS pemasok,
								produk.`id` AS id ";
		$from 				= " FROM
								stok 
								LEFT JOIN produk 
								ON produk.`id` = stok.`id_produk` 
								LEFT JOIN pemasok 
								ON pemasok.`id` = produk.`id_pemasok` ";
		$where 				= "WHERE stok.ref_table = 'stok_opname' ";
		$group_by 			= "GROUP BY stok.`id` ";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";


		if( isset($params["filter"]["id_produk"]) && !empty($params["filter"]["id_produk"]) ){
			$produk       = $this->db->escape_str(trim(strip_tags($params["filter"]["id_produk"])));
			$where .= "  AND  stok.id_produk =  ". $produk ." ";
		}

		$totalData 		= $this->db->query($select_total . $from . $where .  $group_by . ";")->row_array();
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

	
	function get_excel_beli($id_produk)
    {   
	
    	$query = $this->db
		->select(' stok.`transaksi` AS jenis,
		stok.`tgl` AS tgl,
		stok.`ref_text` AS nomor,
		stok.`qty` AS qty,
		produk.nama AS produk,
		pemasok.`nama` AS pemasok,
		produk.`id` AS id ')
		->from('stok ')
		->join('produk','produk.id = stok.id_produk ')
		->join('pemasok','pemasok.id = produk.id_pemasok','left')
		->join('pembelian','pembelian.id = stok.ref_table','left')
		->where('stok.ref_table','pembelian')
		->where('stok.id_produk',$id_produk)
		->group_by('stok.id')->get();
		return $query;
    }	

	function get_excel_jual($id_produk)
    {   
	
    	$query = $this->db
		->select(' stok.`transaksi` AS jenis,
		stok.`tgl` AS tgl,
		stok.`ref_text` AS nomor,
		stok.`qty` AS qty,
		produk.nama AS produk,
		pelanggan.`nama` AS pelanggan,
		produk.`id` AS id ')
		->from('stok ')
		->join('produk','produk.id = stok.id_produk ')
		->join('penjualan','penjualan.id = stok.ref_pk','left')
		->join('pelanggan', 'pelanggan.id = penjualan.id_pelanggan','left')
		->where('stok.ref_table','penjualan')
		->where('stok.id_produk',$id_produk)
		->group_by('stok.id')->get();
		return $query;
    }	

	function get_excel_retur($id_produk)
    {   
    	$query = $this->db
		->select(' stok.`transaksi` AS jenis,
				   stok.`tgl` AS tgl,
				   stok.`ref_text` AS nomor,
				   stok.`qty` AS qty,
				   produk.nama AS produk,
				   pemasok.`nama` AS pemasok,
				   produk.`id` AS id ')
				   ->from('stok ')
				   ->join('produk','produk.id = stok.id_produk ')
				   ->join('pemasok','pemasok.id = produk.id_pemasok','left')
				   ->where('stok.ref_table','retur')
				   ->where('stok.id_produk',$id_produk)
				   ->group_by('stok.id')->get();
		return $query;
    }	

	function get_excel_opname($id_produk)
    {   
    	$query = $this->db
		->select(' stok.`transaksi` AS jenis,
					stok.`tgl` AS tgl,
					stok.`ref_text` AS nomor,
					stok.`qty` AS qty,
					produk.nama AS produk,
					pemasok.`nama` AS pemasok,
					produk.`id` AS id ')
				   ->from('stok ')
				   ->join('produk','produk.id = stok.id_produk ')
				   ->join('pemasok','pemasok.id = produk.id_pemasok','left')
				   ->where('stok.ref_table','stok_opname')
				   ->where('stok.id_produk',$id_produk)
				   ->group_by('stok.id')->get();
		return $query;
    }	

}