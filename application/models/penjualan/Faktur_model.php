<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faktur_model extends CI_Model {
	private $module = [
		'name' 	=> 'penjualan',
		'url'	=> 'penjualan/faktur',
	];

	public function __construct(){
		parent::__construct();
	}
	
	private function is_unique_field($column_name, $value, $pk=NULL){
		$query = "SELECT COUNT(`id`) AS `total` FROM `penjualan` WHERE `". $column_name ."`='". $this->db->escape_str($value) ."' ";
		if(!empty($pk)){
			$query .= " AND `id`!='" . $pk ."'";
		}
		$result = $this->db->query($query)->row();
		return isset($result->total) && $result->total > 0 ? FALSE : TRUE;
	}
	public function single_combine($pk){
		$penjualan = (object)[];
		$penjualan = $this->db->from('penjualan')->where(['id'=>$pk])->get()->row();
    	$penjualan->rincian_penjualan = $this->db->select('rincian_penjualan.*, produk.*,  produk.nama AS nama, pemasok.kode AS kode_pemasok ,kode_laba.kode AS kode_laba, pemasok.nama as nama_pemasok , CONCAT(pemasok.nama, " - ", produk.nama) AS `test_nama` ')->from('rincian_penjualan')
			->join('produk','produk.id=rincian_penjualan.id_produk','left')
			->join('pemasok','produk.id_pemasok=pemasok.id','left')
			->join('kode_laba','kode_laba.laba=produk.laba','left')
			->where(array('rincian_penjualan.id_penjualan'=>$penjualan->id))
			->group_by('rincian_penjualan.id')->get()->result();
		$penjualan->pelanggan = $this->db->from('pelanggan')->where(['id'=>$penjualan->id_pelanggan])->get()->row();
		$penjualan->cabang    = $this->db->from('cabang')->where(['id'=>$penjualan->id_cabang])->get()->row();
		
		$penjualan->pelunasan = $this->db
			->select(' *, sum(`nominal`) as nominal')
			->from('pelunasan')->where(['pelunasan.id_penjualan'=>$penjualan->id])->get()->row();
		
		$penjualan->rincian_pelunasan 	= $this->db
			->select('
				rincian_pelunasan.*,
				akun.nama AS nama_akun,
				giro.nomor AS nomor_giro,
				rincian_pelunasan.chek as chek 
			')
			->from('rincian_pelunasan')
			->join('akun','akun.id=rincian_pelunasan.id_akun','left')
			->join('giro','giro.id=rincian_pelunasan.id_giro','left')
			->join('pelunasan','pelunasan.id=rincian_pelunasan.id_pelunasan','left')
			->join('penjualan','pelunasan.id_penjualan=penjualan.id','left')
			->where(array('penjualan.id'=>$pk))->get()->result();
		
		$penjualan->cek_nota = $this->db
			->select('
				sum(rincian_pelunasan.chek) as chek
			')
			->from('rincian_pelunasan')
			->where(array('rincian_pelunasan.id_pelunasan'=>$penjualan->pelunasan->id))->get()->row();
		$penjualan->retur = $this->db
		->select(' *, sum(`nominal`) as nominal')
		->from('retur_penjualan')->where(['id_penjualan'=>$pk])->get()->row();
		$penjualan->rincian_retur = $this->db
		->select(' n.nominal as totalretur ,p.`nama` AS nama_produk, r.`qty` AS qty, r.`harga` AS harga, r.`total` AS nominal ')
		->from('retur_penjualan AS n ')
		->join('rincian_retur_penjualan AS r','r.id_retur_penjualan = n.id  ','left')
		->join('penjualan','penjualan.id = n.id_penjualan','left')
		->join('produk AS p','ON p.id = r.id_produk ','left')
		->where(array('penjualan.id'=>$pk))->get()->result();
		return $penjualan;
	}

	public function single1($pk){
		$penjualan = (object)[];
		$penjualan = $this->db->from('penjualan')->where(['id'=>$pk])->get()->row();
		$penjualan->rincian_penjualan = $this->db->select('produk.*, pemasok.*, rincian_penjualan.*,
		`produk`.`nama` AS `nama_produk`,
		`pemasok`.`nama` AS `nama_pemasok`,
		 pemasok.kode AS kode_pemasok, 
		`produk`.`id` AS `id_produk`, 
		SUM(IFNULL(`stok`.`qty`,0)) + rincian_penjualan.qty AS `saldo`')
		     ->from('produk')
			 ->join('pemasok','produk.id_pemasok=pemasok.id','left')
			 ->join('stok','stok.`id_produk` = produk.`id`','left')
			->join('rincian_penjualan','produk.id=rincian_penjualan.id_produk','left')
			->where(array('rincian_penjualan.id_penjualan'=>$penjualan->id, 'stok.status_ro'=> 1))
			->group_by('rincian_penjualan.id')->get()->result();

		$penjualan->pelanggan = $this->db->from('pelanggan')->where(['id'=>$penjualan->id_pelanggan])->get()->row();
		// $penjualan->retur     = $this->db->from('retur_penjualan')->where(['no_nota'=>$penjualan->nomor])->get()->result();
		$penjualan->pelunasan 			= $this->db->from('pelunasan')->where(['pelunasan.id_penjualan'=>$penjualan->id,'gabung_faktur'=>1])->get()->row();
		$penjualan->rincian_pelunasan 	= $this->db
			->select('
				rincian_pelunasan.*,
				akun.nama AS nama_akun,
				giro.nomor AS nomor_giro
			')
			->from('rincian_pelunasan')
			->join('akun','akun.id=rincian_pelunasan.id_akun','left')
			->join('giro','giro.id=rincian_pelunasan.id_giro','left')
			->where(array('rincian_pelunasan.id_pelunasan'=>$penjualan->pelunasan->id))->get()->result();
			$penjualan->dataretur = $this->db->from('retur_penjualan')->where(['retur_penjualan.id_penjualan'=>$penjualan->id, 'retur_penjualan.id_pelanggan' => $penjualan->pelanggan->id])->get()->row();
			$penjualan->retur = $this->db->select('rincian_retur_penjualan.*, rincian_penjualan.*, rincian_retur_penjualan.harga as harga_jual,rincian_retur_penjualan.qty_awal AS qty_retur,  produk.nama AS nama, rincian_retur_penjualan.qty as qty_input, retur_penjualan.nominal as total_retur, rincian_retur_penjualan.id_produk as id_produk ')->from('rincian_retur_penjualan')
			->join('retur_penjualan','retur_penjualan.id = rincian_retur_penjualan.id_retur_penjualan','left')
			->join('penjualan','penjualan.id=retur_penjualan.id_penjualan','left')
			->join('rincian_penjualan','penjualan.id=rincian_penjualan.id_penjualan','left')
			->join('pelanggan','penjualan.id_pelanggan=pelanggan.id','left')
			->join('produk','produk.id=rincian_retur_penjualan.id_produk','left')
			->where(array('retur_penjualan.id_penjualan'=>$penjualan->id, 'penjualan.id_pelanggan' => $penjualan->pelanggan->id))
			->group_by('rincian_retur_penjualan.id')->get()->result();
			// $penjualan->retur = $this->db->select('rincian_retur_penjualan.*, produk.*')->from('rincian_retur_penjualan')
			// ->join('produk','produk.id=rincian_retur_penjualan.id_produk','left')
			// ->join('retur_penjualan','retur_penjualan.id = rincian_retur_penjualan.id_retur_penjualan','left')
			// ->where(array('retur_penjualan.no_nota'=>$penjualan->nomor))->get()->result();
		return $penjualan;
	}


	public function single($pk){
		$penjualan = (object)[];
		$penjualan = $this->db->from('penjualan')->where(['id'=>$pk])->get()->row();
		$penjualan->rincian_penjualan = $this->db->select('rincian_penjualan.*, produk.*, pemasok.kode AS kode_pemasok ,kode_laba.kode AS kode_laba')->from('rincian_penjualan')
			->join('produk','produk.id=rincian_penjualan.id_produk','left')
			->join('pemasok','produk.id_pemasok=pemasok.id','left')
			->join('kode_laba','kode_laba.laba=produk.laba','left')
			->where(array('rincian_penjualan.id_penjualan'=>$penjualan->id))->get()->result();
		$penjualan->pelanggan = $this->db->from('pelanggan')->where(['id'=>$penjualan->id_pelanggan])->get()->row();
		$penjualan->pelunasan 			= $this->db->from('pelunasan')->where(['pelunasan.id_penjualan'=>$penjualan->id,'gabung_faktur'=>1])->get()->row();
		$penjualan->rincian_pelunasan 	= $this->db
			->select('
				rincian_pelunasan.*,
				akun.nama AS nama_akun,
				giro.nomor AS nomor_giro
			')
			->from('rincian_pelunasan')
			->join('akun','akun.id=rincian_pelunasan.id_akun','left')
			->join('giro','giro.id=rincian_pelunasan.id_giro','left')
			->where(array('rincian_pelunasan.id_pelunasan'=>$penjualan->pelunasan->id))->get()->result();
		return $penjualan;
	}


	public function pelanggan_autocomplete($config){
		extract($config);
		$this->db->from('pelanggan');
		if(isset($params['phrase']) && !empty($params['phrase'])){
			$this->db->like('nama',$params['phrase']);	
		}
		return $this->db->get()->result();
	}

	public function get_kode_pelanggan_by_nama($config){
		extract($config);
		$nama = isset($params['nama']) ? $params['nama']: null;
		$nama = isset($params['alamat']) ? $params['alamat']: null;
		$result  = ['status'=>'error','alamat_pelanggan' => '', 'id' => ''];
		if($row = $this->db->select('`alamat`, id ')->from('pelanggan')->where(array('nama'=>$nama))->get()->row()){
			if(isset($row->alamat)){
				$result  = ['status'=>'success','alamat_pelanggan' => $row->alamat, 'id' => $row->id];
			}
		}
		return $result;
	}

	public function get_kode_produk($config = []){
		extract($config);
		$pk = isset($params['kode_produk']) ? (int)$params['kode_produk']:0;

		// $penjualan = (object)[
		// 	'total_tagihan'=>0,
		// 	'total_pelunasan'=>0,
		// 	'sisa_tagihan'=>0
		// ];
		if($result = $this->db
		->select('produk.*, CONCAT(pemasok.nama," / ", produk.nama) as text,  SUM(IFNULL(`stok`.`qty`,0)) AS `saldo`')
		->from('produk')
		->join('pemasok', 'pemasok.id = produk.id_pemasok', 'left')
		->join('stok', 'stok.`id_produk` = produk.`id`', 'left')
		->where(['kode_produk'=>$pk, 'produk.status'=> "1"])->get()){
			return $result->row();
		}
		return $kode_produk;
	} 

	public function datatable($config = array()){
		extract($config);
			$columns 			= array('tgl_nota', 'nama_pelanggan', 'nomor', 'total_tagihan', 'diskon','notaretur','n.chek','n.total_pelunasan - sum(IFNULL(rp.potongan,0)) - n.chek','sum(IFNULL(rp.potongan,0))', 'total_tagihan','total_pelunasan','sisa_tagihan' ,'id');
		$select_total 		= "SELECT COUNT(DISTINCT(`n`.`id`)) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS `n`.*, 
		s.`nama` AS `nama_pelanggan`, 
		n.total_pelunasan - sum(IFNULL(rp.potongan,0)) - n.chek as bayar, 
		sum(IFNULL(rp.potongan,0)) as rppotongan, 
		n.chek as chek, 
		n.sisa_tagihan - n.chek as sisa";
		$from 				= "
		FROM `penjualan` AS `n` 
			LEFT JOIN `pelanggan` AS `s` ON `s`.`id`=`n`.`id_pelanggan`
			left join pelunasan as p on  n.id = p.id_penjualan
			left join rincian_pelunasan as rp on p.id = rp.id_pelunasan 
		";
		$where 				= "WHERE `n`.`id` IS NOT NULL ";
		$group_by 			= "GROUP BY `n`.`id` ";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";

		if( isset($params['filter']['tgl_nota']) && !empty($params['filter']['tgl_nota']) ){
			$value	= $this->db->escape_str(strip_tags($params['filter']['tgl_nota']));
			$where .= " AND `n`.`tgl_nota`='" . $value ."' ";
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
		$from 			= "FROM `penjualan` ";
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

	public function select2_pelanggan($config = array()){
		extract($config);

		$row_per_page 	= isset($row_per_page) ? $row_per_page : 10;
		$select_total 	= "SELECT COUNT(DISTINCT(pelanggan.`id`)) AS `total` ";
		$select_data	= "SELECT * ";
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

	public function select2_produk($config = array()){
		extract($config);
		$row_per_page 	= isset($row_per_page) ? $row_per_page : 50;
		$select_total 	= "SELECT COUNT(DISTINCT(`produk`.`id`)) AS `total` ";
		$select_data	= "SELECT *,`produk`.`nama` AS `nama_produk`, `produk`.`id` AS `id_produk` ,  SUM(IFNULL(`stok`.`qty`,0)) AS `saldo` ";
		$from 			= "FROM `produk` ";
		$join			= "INNER JOIN `pemasok` ON `pemasok`.`id` = `produk`.`id_pemasok` 
						   INNER JOIN stok ON stok.`id_produk` = produk.`id` ";
		$where 			= "WHERE produk.status = '1' ";
		$term = isset($params['term']) ? $this->db->escape_str($params['term']) : (isset($params['q']) ? $this->db->escape_str($params['q']) : null);
		if(isset($term) && !empty($term)){
			$where .= " AND (pemasok.kode LIKE '%". $term ."%' OR produk.`nama` LIKE '%". $term ."%'  OR pemasok.`nama` LIKE '%". $term ."%') ";
		}

		if(isset($params['id_cabang']) && !empty($params['id_cabang'])){
			$where .= " AND (`produk`.`id_cabang` = " . $params['id_cabang'] . ")";
		}
		$group_by 		= "GROUP BY `produk`.`id` ";
		$order_by 		= "ORDER BY `produk`.`id` ASC ";
		$having			= "HAVING SUM(stok.`qty`) > 0 ";
		$result_total	= $this->db->query($select_total . $from . $join . $where . $group_by . $having .  ";");
		$total_data 	= $result_total->row()->total ?? 0;
		$total_page		= ceil((int)$total_data/$row_per_page);
		$page 			= isset($params['page']) ? (int)$params['page'] : 1;
		$offset 		= (($page - 1) * $row_per_page);
		$result_total->free_result();
		$data = $this->db->query($select_data . $from . $join . $where . $group_by . $having . $order_by ." LIMIT ". $row_per_page ." OFFSET ". $offset .";");
		return array( 
			'results' 		=> $data->result_array(),
			'pagination' 	=> array('more' => ($page < $total_page)) 
		);
		$data->free_result();
	}

	public function struk_print($input){
		// $sales_id = $this->input->post('pk') != null ? $this->input->post('pk') : $this->input->get('pk');
		// return $this->Sales_model->struk_print($sales_id);
	}
	public function render_text_print2($data){
		$total = $no = 0;
		$lines = array($data);

		extract($data);
		$text = text_align( $this->session->cabang->nama, 48, 'center');
		array_push($lines,$text);
		$text = text_align($this->session->cabang->telp, 48, 'center');
		array_push($lines,$text);
		$text = text_align($this->session->cabang->wa, 48, 'center');
		array_push($lines,$text);
		$text = text_align($this->session->cabang->alamat, 48, 'center');
		array_push($lines,$text);

		array_push($lines,str_pad('-', 48, '-'));

		$text = text_align('Nomor Nota', 5, 'left');
		$text .= text_align(':', 2, $align = 'left');
		$text .= text_align($penjualan['nomor'], 2, 'right');
		array_push($lines,$text);

		$text = text_align('Kasir', 5, 'left');
		$text .= text_align(':', 2, $align = 'left');
		$text .= text_align($this->session->user->nama, 2, 'right');
		array_push($lines,$text);

		$text = text_align('Tgl', 5, 'left');
		$text .= text_align(':', 2, 'left');
		$text .= text_align($penjualan['tgl_nota'], 2, 'right');
		array_push($lines,$text);

		array_push($lines,str_pad('-', 48, '-'));
		$text = text_align("Nama Produk", 10, 'left') . ' ';
		$text .= text_align("Harga", 10, 'left') . ' ';
		$text .= text_align("Qty", 8, 'left') . ' ';
		$text .= text_align("Total" , 10, 'right');
		array_push($lines,$text);
		array_push($lines,str_pad('-', 48, '-'));

		$total_items = $total_diskon = $total_tagihan = 0;
		if( isset($rincian) && count($rincian) > 0 ){
			foreach($rincian AS $item){
			
				$product_names 			= preg_replace("/\s++/"," ", 'asem');
				$product_name_split 	= explode("<--x-->", wordwrap($product_names,14,"<--x-->"));

				$text = text_align($product_name_split[0], (15 - 1), 'left')  . ' ';
				$text .= text_align(number_format($item['harga']), 10, 'right');
				$text .= text_align($item['qty'], (5 - 1), 'left') . ' ';
				$text .= text_align(number_format($item['total']), 10, 'right');
				array_push($lines,$text);

				array_shift($product_name_split);
				if(count($product_name_split) > 1){
					foreach($product_name_split AS $product_name){
						$text = text_align(' ', (5 - 1), 'left') . ' ';
						$text .= text_align($product_name, (15 - 1), 'left')  . ' ';
						$text .= text_align(' ', 10, $align = 'right');
						array_push($lines,$text);
					}
				}

				$total_items += $item['qty'] * $item['harga'];

			}

		}
		$total_tagihan = $total_items - $penjualan['diskon'];

// 		$total_tax = $total_service = ($total_tagihan * 0.1);
		
		// $total_tax = $total_service = 0;
		// $total_tagihan = $total_tagihan + $total_tax + $total_service;

		array_push($lines,str_pad('-', 30, '-'));
		$text = text_align('Item',(15 - 1), 'left') . ' ';
		$text .= text_align(number_format($total_items), 15, 'right');
		array_push($lines,$text);
		$text = text_align('Diskon',(15 - 1), 'left') . ' ';
		$text .= text_align(number_format($penjualan['diskon']), 15, 'right');
		array_push($lines,$text);
		$text = text_align('Tagihan',(15 - 1), 'left') . ' ';
		$text .= text_align(number_format($total_tagihan), 15, 'right');
		array_push($lines,$text);
		array_push($lines,str_pad('-', 40, '-'));
		for($i = 0; $i < 1; $i++){
			array_push($lines,str_pad(' ', 40, ' '));
		}
		array_push($lines,text_align('Thank You!', 30, 'center'));
		for($i = 0; $i < 3; $i++){
			array_push($lines,str_pad(' ', 40, ' '));
		}

		$str = implode("\n",$lines);
		// return $str . "\x1B@\x1DV1";
		return $str;
	}

	public function render_text_print($data){
		$total = $no = 0;
		$lines = array();
		extract($data);
		$text = text_align( $this->session->cabang->nama, 48, 'center');
		array_push($lines,$text);
		$text = text_align($this->session->cabang->telp, 48, 'center');
		array_push($lines,$text);
		$text = text_align($this->session->cabang->wa, 48, 'center');
		array_push($lines,$text);
		$text = text_align($this->session->cabang->alamat, 48, 'center');
		array_push($lines,$text);

		array_push($lines,str_pad('-', 48, '-'));

		$text = text_align('Nomor Nota', 5, 'left');
		$text .= text_align(':', 2, $align = 'left');
		$text .= text_align($penjualan['nomor'], 2, 'right');
		array_push($lines,$text);

		$text = text_align('Kasir', 5, 'left');
		$text .= text_align(':', 2, $align = 'left');
		$text .= text_align($this->session->user->nama, 2, 'right');
		array_push($lines,$text);

		$text = text_align('Pelanggan', 5, 'left');
		$text .= text_align(':', 2, $align = 'left');
		$text .= text_align($pelanggan['nama'], 2, 'right');
		array_push($lines,$text);

		$text = text_align('Tgl', 5, 'left');
		$text .= text_align(':', 2, 'left');
		$text .= text_align($penjualan['tgl_nota'], 2, 'right');
		array_push($lines,$text);

		array_push($lines,str_pad('-', 48, '-'));
		$text = text_align("Nama Produk", 15, 'left') . ' ';
		$text .= text_align("Harga", 12, 'left') . ' ';
		$text .= text_align("Qty", 8, 'right') . ' ';
		$text .= text_align("Total" , 10, 'right');
		array_push($lines,$text);
		array_push($lines,str_pad('-', 48, '-'));


		$total_items = $total_diskon = $total_tagihan = 0;
		if( isset($rincian) && count($rincian) > 0 ){
			foreach($rincian AS $item){
				$produk = $this->db->from('produk')->where(['id'=>$item['id_produk']])->get()->row();
				$product_names 			= preg_replace("/\s++/"," ", $produk->nama);
				$product_name_split 	= explode("<--x-->", wordwrap($product_names,14,"<--x-->"));

				$text = text_align($product_name_split[0], 14, 'left')  . ' ';
				$text .= text_align(number_format($item['harga']), 10, 'right');
				$text .= text_align($item['qty'], 8, 'right') . ' ';
				$text .= text_align(number_format($item['total']), 14, 'right');
				array_push($lines,$text);

				array_shift($product_name_split);
				if(count($product_name_split) > 1){
					foreach($product_name_split AS $product_name){
						$text = text_align($product_name, 14, 'left')  . ' ';
						$text .= text_align(' ', 10, $align = 'right');
						$text .= text_align(' ', 7, 'left') . ' ';
						$text .= text_align(' ', 14, $align = 'right');
						array_push($lines,$text);
					}
				}

				$total_items += $item['qty'] * $item['harga'];
			}

		}

		$total_pelunasan = 0;
		if( isset($pelunasan) && count($pelunasan) > 0 ){
			foreach($pelunasan AS $item){
				$total_pelunasan += $item['nominal'] + $item['potongan'];
			}

		}
		$total_tagihan = $total_items - $penjualan['diskon'];

		array_push($lines,str_pad('-', 48, '-'));
		$text = text_align('Item',(15 - 1), 'left') . ' ';
		$text .= text_align(number_format($total_items), 33, 'right');
		array_push($lines,$text);
		$text = text_align('Diskon',(15 - 1), 'left') . ' ';
		$text .= text_align(number_format($total_diskon), 33, 'right');
		array_push($lines,$text);

		$text = text_align('Tagihan',(15 - 1), 'left') . ' ';
		$text .= text_align(number_format($total_tagihan), 33, 'right');
		array_push($lines,$text);
		$text = text_align('Pelunasan',(15 - 1), 'left') . ' ';
		$text .= text_align(number_format($total_pelunasan), 33, 'right');
		array_push($lines,$text);
		$text = text_align('Sisa Tagihan',(15 - 1), 'left') . ' ';
		$text .= text_align(number_format($total_tagihan - $total_pelunasan), 33, 'right');
		array_push($lines,$text);
		array_push($lines,str_pad('-', 48, '-'));
		for($i = 0; $i < 1; $i++){
			array_push($lines,str_pad(' ', 48, ' '));
		}
		array_push($lines,text_align('Thank You!', 48, 'center'));
		for($i = 0; $i < 3; $i++){
			array_push($lines,str_pad(' ', 48, ' '));
		}

		$str = implode("\n",$lines);
		// return $str . "\x1B@\x1DV1";
		return $str;
	}


	public function select2_produk_retur($config = array()){
		extract($config);

		$row_per_page 	= isset($row_per_page) ? $row_per_page : 10;
		$select_total 	= "SELECT COUNT(DISTINCT(`produk`.`id`)) AS `total` ";
		$select_data	= "SELECT `produk`.`id` AS `id_produk`, `produk`.`nama` as `nama_produk`, `pemasok`.`kode` as `kode_pemasok`, `produk`.`harga_jual`, rincian_penjualan.qty as qty ";
		$from 			= "FROM `pelanggan` ";
		$join			= "INNER JOIN `penjualan` ON `penjualan`.`id_pelanggan` = `pelanggan`.`id`
						   INNER JOIN `rincian_penjualan` ON `rincian_penjualan`.`id_penjualan` = `penjualan`.`id`
						   INNER JOIN `produk` ON `produk`.`id` = `rincian_penjualan`.`id_produk` 
						   INNER JOIN `pemasok` ON `pemasok`.`id` = `produk`.`id_pemasok` ";
		$where 			= "WHERE `produk`.`id` IS NOT NULL AND `pelanggan`.`id` = '".$params['id_pelanggan']."' ";
		$term = isset($params['term']) ? $this->db->escape_str($params['term']) : (isset($params['q']) ? $this->db->escape_str($params['q']) : null);
		if(isset($term) && !empty($term)){
			$where .= " AND (`produk`.`id` LIKE '%". $term ."%' OR `produk`.`nama` LIKE '%". $term ."%') ";
		}

		if(isset($params['id_cabang']) && !empty($params['id_cabang'])){
			$where .= " AND (`produk`.`id_cabang` = " . $params['id_cabang'] . ")";
		}
		// if(isset($params['id_pelanggan']) && !empty($params['id_pelanggan'])){
		// 	$where .= " AND (`pelanggan`.`id` = " . $params['id_pelanggan'] . ") ";
		// }


		$group_by 		= "GROUP BY `produk`.`id` ";
		$order_by 		= "ORDER BY `produk`.`id` ASC ";
		$result_total	= $this->db->query($select_total . $from . $join . $where . $group_by . ";");
		$total_data 	= $result_total->row()->total;
		$total_page		= ceil((int)$total_data/$row_per_page);
		$page 			= isset($params['page']) ? (int)$params['page'] : 1;
		$offset 		= (($page - 1) * $row_per_page);
		$result_total->free_result();
		$data = $this->db->query($select_data . $from . $join . $where . $group_by . $order_by ." LIMIT ". $row_per_page ." OFFSET ". $offset .";");
		return array( 
			'results' 		=> $data->result_array(),
			'pagination' 	=> array('more' => ($page < $total_page)) 
		);
		$data->free_result();
	}
	/* CRUD */
	private function insert_pelanggan($data = []){
		if(isset($data['nama']) && !empty($data['nama']) && isset($data['alamat']) && !empty($data['alamat']) ){
			$pelanggan = $this->db->get_where('pelanggan',['nama'=>$data['nama'], 'alamat' => $data['alamat']])->row();
			if(isset($pelanggan->id) && !empty($pelanggan->id)){
				$id_pelanggan = $pelanggan->id;
			} else {
				$data['alamat'] = trim(strip_tags($data['alamat']));
				$data['nama'] = trim(strip_tags($data['nama']));
				if( $this->db->insert('pelanggan', $data) ){
					$id_pelanggan = $this->db->insert_id();
				}
			}
			return $id_pelanggan;
		}
	}
	
	private function insert_produk($data = []){
		if( isset($data['nama']) && !empty($data['nama']) ){
			$data['nama'] 		= trim(strip_tags($data['nama']));
			$data['id_pelanggan'] = (int)$data['id_pelanggan'];
			$data['id_cabang'] = (int)$data['id_cabang'];
			$produk = $this->db->get_where('produk',['nama'=>$data['nama'],'id_pelanggan'=>$data['id_pelanggan']])->row();
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
		$penjualan['id_pelanggan'] 	= null;
		if(isset($pelanggan['nama']) && !empty($pelanggan['nama']) ){
			$penjualan['id_pelanggan'] = $this->insert_pelanggan($pelanggan);
			if( isset($rincian) && is_array($rincian) && count($rincian) > 0 ){
				foreach( $rincian AS $index => $item ){
					$produk[$index]['id_pelanggan'] = $penjualan['id_pelanggan'];
					$produk[$index]['harga_beli'] = $item['harga'];
					if( isset($produk[$index]['nama']) && !empty($produk[$index]['nama']) ){
						$rincian[$index]['id_produk'] = $this->insert_produk($produk[$index]);
					}
				}
			}
		}

		if( isset($penjualan['tgl_nota']) && !empty($penjualan['tgl_nota']) && isset($penjualan['nomor']) && !empty($penjualan['nomor']) ){
			if( isset($penjualan['nomor']) && !empty($penjualan['nomor']) ){
				$penjualan['nomor'] 	= trim(strip_tags($penjualan['nomor']));
				$data_is_valid 	= $this->is_unique_field('nomor', $penjualan['nomor']);
				if( $data_is_valid == FALSE ){
					$result['message'] 	= "Nomor nota penjualan sudah ada.";
				}
			}
		}

		if($data_is_valid === TRUE){
			$this->db->trans_begin();
			extract($penjualan);
			/* Record Data penjualan */
			$this->db->query("
				INSERT INTO `penjualan` (`id_pelanggan`,`tgl_nota`,`nomor`,`diskon`,chek, id_cabang, notaretur) 
					VALUES ('".$id_pelanggan."','".$tgl_nota."','".$nomor. "','".$diskon."','".$chek."','".$id_cabang."', '".$notaretur."');
			");
			/* Get ID penjualan */
			$id_penjualan = (int)$this->db->insert_id();
			/* Build Data Reference */
					$ref = [
						'text' 		=> $nomor,
						'link' 		=> $this->module['url'] .'/single/' . $id_penjualan,
						'pk'		=> $id_penjualan,
						'table'		=> 'penjualan'
					];
			$total_rincian = 0;
					/* Record Data Barang */
					foreach( $rincian AS $index => $item ){		
						$item['total'] = $item['harga'] * $item['qty'];
						/* Record Data Barang */
						$this->db->query("
							INSERT INTO `rincian_penjualan` (`id_penjualan`,`id_produk`,`qty`,`harga`,`total`) 
								VALUES ('".$id_penjualan."','".$item['id_produk']."','".$item['qty']."','".$item['harga']."','".$item['total']."');
						");
						/* Record Data Stok Barang */
						$this->db->query("
							INSERT `stok` (`id_produk`, `tgl`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, `transaksi`, `harga`, `qty`)
							VALUES ('".$item['id_produk']."','".$tgl_nota."','". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."','penjualan','penjualan','".$item['harga']."',".($item['qty'] * -1).");
						");
						$total_rincian += $item['total'];
					}
					$this->db->query("UPDATE rincian_penjualan
					LEFT JOIN
					(SELECT ((rincian_penjualan.total) - SUM(produk.harga_beli * rincian_penjualan.qty)) AS laba_akhir, rincian_penjualan.`id` AS id_jual
					FROM penjualan
					LEFT JOIN rincian_penjualan 
					ON rincian_penjualan.`id_penjualan` = penjualan.`id`
					LEFT JOIN produk
					ON rincian_penjualan.`id_produk` = produk.`id` GROUP BY rincian_penjualan.`id` ) AS t2 ON t2.id_jual = rincian_penjualan.`id`
					SET rincian_penjualan.`laba_akhir` = t2.laba_akhir;");
		// if(isset($retur)){

			
		// }
			$nominal = 0;
			if(isset($retur)){

			$this->db->query("
			    INSERT INTO `retur_penjualan` (id_penjualan,`id_pelanggan`,`tgl_nota`,no_nota, id_cabang) 
					VALUES ('".$id_penjualan."','".$id_pelanggan."','".$tgl_nota."','".$nomor. "','".$id_cabang."');
			");

			$id_retur_penjualan = (int)$this->db->insert_id();

			$ref1 = [
				'text' 		=> 'Retur Penjualan # ' . $id_retur_penjualan,
				'link' 		=> $this->module['url'] .'/single/' . $id_retur_penjualan,
				'pk'		=> $id_retur_penjualan,
				'table'		=> 'retur'
			];

				foreach( $retur AS $index => $item ){
					$item['total'] = $item['harga'] * $item['qty'];

				/* Record Data Barang */
				$this->db->query("
					INSERT INTO `rincian_retur_penjualan` (`id_retur_penjualan`,`id_produk`,`qty`,qty_awal,`harga`,`total`) 
					VALUES ('".$id_retur_penjualan."','".$item['id_produk']."','".$item['qty']."','".$item['qty_retur']."','".$item['harga']."','".$item['total']."');
				");

				/* Record Data Stok Barang */
				$this->db->query("
					INSERT `stok` (`id_produk`, `tgl`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, `transaksi`, `harga`, `qty`)
					VALUES ('".$item['id_produk']."','".$tgl_nota."','". $ref1['text'] ."','". $ref1['link'] ."','". $ref1['pk'] ."','".$ref1['table']."','penjualan','".$item['harga']."',".$item['qty'].");
				");
				$nominal += $item['total'];
				}
				$this->db->query("UPDATE `retur_penjualan` SET `nominal`=". $nominal ." WHERE `id`=". $id_retur_penjualan .";");
			
			}



			/* Tambah Jurnal penjualan */
			$this->db->query("
				INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`,id_cabang)
					VALUES ( 8,'".$tgl_nota."', 0, ". $total_rincian ." ,'". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."' , 'penjualan','".$id_cabang."');
			");

			$total_potongan_dan_diskon 	= isset($diskon) ? (int)$diskon : 0;
			$total_sisa_piutang 			= $total_rincian - $total_potongan_dan_diskon;

			$this->db->query("
				INSERT INTO `pelunasan` (`id_penjualan`, `id_pelanggan`,`tgl_bayar`,`nomor`,`gabung_faktur`) 
				VALUES ('".$id_penjualan."','".$id_pelanggan."','".$tgl_nota."','".$nomor. "',1);
			");

			$id_pelunasan 		= (int)$this->db->insert_id();
			$total_pelunasan 	= 0;
			if(isset($pelunasan)){
				foreach( $pelunasan AS $index => $transaction ){
					extract($transaction);
					$total_pelunasan 	+= $total;
					$total_sisa_piutang 	-= $total;
					$total_potongan_dan_diskon += $potongan;
					$id_giro 	= isset($id_giro) && !empty($id_giro) ? $id_giro :'NULL';
					$tgl_giro 	= isset($tgl_giro) && !empty($tgl_giro)  ? "'" . $tgl_giro . "'" :'NULL';
					$this->db->query("
						INSERT INTO `rincian_pelunasan` (`id_pelunasan`,`id_akun`,`id_giro`,`tgl_giro`,`metode`,`nominal`,`potongan`,chek,`total`) 
							VALUES ('".$id_pelunasan."','".$id_akun."',".$id_giro.",".$tgl_giro.",'".$metode."','".$nominal."','".$potongan."','".$chek."','".$total."');
					");
					$this->db->query("
						INSERT INTO `jurnal` (`id_akun`, `tgl`, `kredit`, `debit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, metode, id_cabang)
							VALUES ('".$id_akun."','".$tgl_nota."', '".$nominal."', 0,'". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."','". $ref['table'] ."', '".$metode."','".$id_cabang."');
					");
					unset($id_giro,$tgl_giro);
				}
			}

			/* Tambah Jurnal Pendapatan Potongan penjualan */
			$this->db->query("
				INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, id_cabang)
					VALUES ( 11,'".$tgl_nota."',". $total_potongan_dan_diskon .", 0, '". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."' ,'". $ref['table'] ."','".$id_cabang."');
			");

			if($total_sisa_piutang > 0){
				/* Tambah Jurnal piutang penjualan */
				$this->db->query("
					INSERT INTO `jurnal` (`id_akun`, `tgl`, `debit`, `kredit`, `ref_text`, `ref_link`, `ref_pk`, `ref_table`, id_cabang)
						VALUES ( 3,'".$tgl_nota."',". $total_sisa_piutang .", 0, '". $ref['text'] ."','". $ref['link'] ."','". $ref['pk'] ."' ,'". $ref['table'] ."','".$id_cabang."');
				");
			}

			$this->db->query("UPDATE `pelunasan` SET `nominal`=". $total_pelunasan ." WHERE `id`=". $id_pelunasan .";");

			$this->db->query("
			UPDATE `penjualan` 
			LEFT JOIN (
				SELECT 
					`rincian_penjualan`.`id_penjualan` AS `id`,
					SUM(`rincian_penjualan`.`total`) AS `total_rincian`,
					SUM(`rincian_penjualan`.`laba_akhir`) AS `laba_akhir`
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
				LEFT JOIN (
					SELECT
						retur_penjualan.id as id_retur,
						retur_penjualan.id_penjualan as id,
						sum(retur_penjualan.nominal) as total_retur
					FROM retur_penjualan
					WHERE retur_penjualan.id_penjualan = ". $id_penjualan ."
				) AS retur ON retur.id = penjualan.id 
				LEFT JOIN (
					SELECT 
					 SUM(produk.laba * rincian_retur_penjualan.qty) AS laba, retur_penjualan.id as id  
				  FROM
					rincian_retur_penjualan 
					LEFT JOIN produk 
					  ON produk.`id` = rincian_retur_penjualan.`id_produk` 
					LEFT JOIN retur_penjualan
					  ON retur_penjualan.id = rincian_retur_penjualan.`id_retur_penjualan`
					LEFT JOIN penjualan
					  ON penjualan.`id` = retur_penjualan.`id_penjualan`
				  WHERE penjualan.id =  ". $id_penjualan ." 
				) AS retur_laba ON retur_laba.id = retur.id_retur 
			SET 
			    `penjualan`.`total_rincian`     = `rincian`.`total_rincian`,
				`penjualan`.`laba_akhir`        = `rincian`.`laba_akhir` - IFNULL(retur_laba.laba,0),
			    `penjualan`.`total_tagihan`     = `rincian`.`total_rincian`-`penjualan`.`diskon` + penjualan.chek - penjualan.notaretur,
			    `penjualan`.`total_pelunasan`  	= `payment`.`total_pelunasan`,
			    `penjualan`.`sisa_tagihan`      = `rincian`.`total_rincian`-`penjualan`.`diskon`-`payment`.`total_pelunasan` - IFNULL(retur.total_retur,0) + penjualan.chek
			WHERE `penjualan`.`id`=". $id_penjualan .";
			");

			if ($this->db->trans_status() === FALSE){
				$result['message'] 	= $this->db->error();
				$this->db->trans_rollback();
			} else {
				$this->db->trans_commit();
				$result['status'] 	= TRUE;
				$result['message'] 	= 'Data telah disimpan.';
				$result['pk'] 		= $id_penjualan;
			}
		}
		unset($penjualan);
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

		$id_pelunasan = [];
		if(isset($pk) && !empty($pk)){

			$cicilan = $this->db->select('id')->from('pelunasan')->where('id_penjualan',$pk)->get()->result_array();
			$id_pelunasan = array_column($cicilan, 'id');

			$deleted = $this->delete($params);
			if(isset($deleted['status']) && $deleted['status'] === TRUE){
				$result = $this->insert($params);
				if( isset($result['status']) && $result['status'] === TRUE && isset($result['pk']) && !empty($result['pk']) ){
					$id_penjualan = $result['pk'];
					if(count($id_pelunasan) > 0){
						$this->db->where('gabung_faktur',0)->where_in('id', $id_pelunasan);
						if( $this->db->update('pelunasan', ['id_penjualan' => $id_penjualan]) ){
							$this->db->query("
							UPDATE `penjualan` 
							LEFT JOIN (
								SELECT 
									`rincian_penjualan`.`id_penjualan` AS `id`,
									SUM(`rincian_penjualan`.`total`) AS `total_rincian`,
									SUM(`rincian_penjualan`.`laba_akhir`) AS `laba_akhir`
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
								LEFT JOIN (
									SELECT
										retur_penjualan.id as id_retur,
										retur_penjualan.id_penjualan as id,
										sum(retur_penjualan.nominal) as total_retur
									FROM retur_penjualan
									WHERE retur_penjualan.id_penjualan = ". $id_penjualan ."
								) AS retur ON retur.id = penjualan.id 
								LEFT JOIN (
									SELECT 
									SUM(produk.laba * rincian_retur_penjualan.qty) AS laba, retur_penjualan.id as id  
								 FROM
								   rincian_retur_penjualan 
								   LEFT JOIN produk 
									 ON produk.`id` = rincian_retur_penjualan.`id_produk` 
								   LEFT JOIN retur_penjualan
									 ON retur_penjualan.id = rincian_retur_penjualan.`id_retur_penjualan`
								   LEFT JOIN penjualan
									 ON penjualan.`id` = retur_penjualan.`id_penjualan`
								 WHERE penjualan.id =  ". $id_penjualan ." 
								) AS retur_laba ON retur_laba.id = retur.id_retur 
							SET 
								`penjualan`.`total_rincian`     = `rincian`.`total_rincian`,
								`penjualan`.`laba_akhir`        = `rincian`.`laba_akhir` - IFNULL(retur_laba.laba,0) - penjualan.diskon,
								`penjualan`.`total_tagihan`     = `rincian`.`total_rincian`-`penjualan`.`diskon` + penjualan.chek - penjualan.notaretur,
								`penjualan`.`total_pelunasan`  	= `payment`.`total_pelunasan`,
								`penjualan`.`sisa_tagihan`      = `rincian`.`total_rincian`-`penjualan`.`diskon`-`payment`.`total_pelunasan` - IFNULL(retur.total_retur,0) + penjualan.chek
							WHERE `penjualan`.`id`=". $id_penjualan .";
							");
						}
					}
				}
			}
		}
		return $result;
		unset($result);
	}
	
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
			(isset($produk['laba']) && !empty($produk['laba'])) &&
			(isset($pk) && !empty($pk))
		){
			if( $data_is_valid == TRUE ){
				if( is_array($produk) && count($produk) > 0 ){
					$result['message'] 	= "Harga Jual Telah Diubah.";
					if( $this->db->update('produk', $produk, array('id'=>$pk)) ){
						$kode_laba = $this->db->get_where('kode_laba',['laba'=>$produk['laba']])->row();
						$result['status'] 		= TRUE;
						$result['message'] 		= 'Data telah disimpan.';
						$result['kode_laba'] 	= isset($kode_laba->kode) ? $kode_laba->kode: '';
					}
				}
			}
		}
		unset($produk);
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
			$retur_penjualan = (object)[];
            $retur_penjualan = $this->db->from('retur_penjualan')->where(['id_penjualan'=>$pk])->get()->row();
			$result['message'] 	= "Data couldnt delete.";
			$this->db->trans_begin();
			$this->db->query("DELETE FROM `pelunasan`  WHERE `gabung_faktur`=1 AND `id_penjualan`='". $pk ."';");
			$this->db->query("DELETE FROM `penjualan` 	WHERE `id`='". $pk ."'");
			$this->db->query("DELETE FROM `jurnal`  	WHERE `ref_table`='penjualan' AND `ref_pk`='". $pk ."';");
			$this->db->query("DELETE FROM `stok`  		WHERE `ref_table`='penjualan' AND `ref_pk`='". $pk ."';");
			$this->db->query("DELETE FROM `retur_penjualan` WHERE `id_penjualan`='". $pk ."'");
			if(isset($retur_penjualan->id) && !empty($retur_penjualan->id)){
				$this->db->query("DELETE FROM `stok`  		WHERE `ref_table`='retur' AND `ref_pk`='".  $retur_penjualan->id ."';");
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
