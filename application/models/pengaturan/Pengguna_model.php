<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Pengguna_model extends CI_Model {

	public function __construct(){
		parent::__construct();
	}

	public function is_unique( $fieldname = '', $unique_term = '', $except_pk = null ){
		$response = array(
			'status_code' 	=> 401,
			'status' 		=> 'error',
			'message' 		=> 'Unautorized',
			'results' 		=> null
		);
		$fieldname 		= !empty($fieldname)  ? $this->db->escape_str($this->security->xss_clean(trim($fieldname))) : 'id_pengguna';
		$unique_term 	= $this->db->escape_str($this->security->xss_clean($unique_term));
		$except_pk 		= $this->db->escape_str($this->security->xss_clean($except_pk));
		$sql 			= "SELECT COUNT(`id_pengguna`) AS `total` FROM `pengguna` WHERE `id_pengguna`!=? AND `". $fieldname ."`= ? LIMIT 1;";
		$results 		= $this->db->query($sql, array($except_pk, $unique_term));
		if( $this->db->affected_rows() > 0){
			$is_unique 	= ( $results->row()->total > 0 ? FALSE : TRUE );
			$response = array(
				'status_code' 	=> ($is_unique == FALSE ? 404 : 200),
				'status' 		=> ($is_unique == FALSE ? 'error' : 'success'),
				'message' 		=> ($is_unique == FALSE ? 'Is not unique' : 'Is unique'),
				'results' 		=> $is_unique
			);
		}
		$results->free_result();
		return $response;
		unset($response, $fieldname, $unique_term, $except_pk, $sql, $results, $is_unique);

	}

	public function insert( $input ){

		$response = array(
			'status_code' 	=> 401,
			'status' 		=> 'error',
			'message'		=> 'Please complete the form field requirements.',
			'redirect'		=> null,
			'results' 		=> null
		);

		$pengguna_form_data = isset($input['pengguna']) ? $input['pengguna'] : array();
		if(isset($pengguna_form_data['password']) && !empty($pengguna_form_data['password'])){
			$pengguna_form_data['password'] = password_hash($pengguna_form_data['password'],PASSWORD_DEFAULT);
		}

		$redirect 			= isset($input['redirect']) ? $input['redirect'] : NULL;

		$insert_valid = TRUE;
		if( isset($pengguna_form_data['id_pengguna']) && !empty($pengguna_form_data['id_pengguna']) ){
			$is_unique_pk = $this->is_unique('id_pengguna', $pengguna_form_data['id_pengguna']);
			$insert_valid = isset($insert_valid['results']) ? $insert_valid['results'] : TRUE;
			if($insert_valid == FALSE){
				$result['message'] 	= "ID sudah digunakan.";
			}
		}

		if( $insert_valid == TRUE ){
			if( is_array($pengguna_form_data) && count($pengguna_form_data) > 0 ){
				// if(empty($pengguna_form_data['id_cabang'])){
				// 	$pengguna_form_data['id_cabang'] = NULL;
				// }
				if(empty($pengguna_form_data['id_cabang'])){
					$pengguna_form_data['id_cabang'] = NULL;
				}
				$response['message'] 	= "Data failed to save.";
				$inserted = $this->db->insert('pengguna', $pengguna_form_data);
				if( $inserted ){
					$response = array(
						'status_code' 	=> 200,
						'status' 		=> 'success',
						'message'		=> 'Data has been save.',
						'redirect'		=> $redirect,
						'results' 		=> null
					);
				}
			}
		}
		return $response;
		unset($response, $input, $pengguna_form_data, $is_unique_pk, $insert_valid, $inserted);

	}

	public function update( $input ){

		$response = array(
			'status_code' 	=> 401,
			'status' 		=> 'error',
			'message'		=> 'Please complete the form field requirements.',
			'redirect'		=> null,
			'results' 		=> null
		);

		$pk 					= isset($input['pk']) ? $this->db->escape_str($this->security->xss_clean($input['pk'])) : NULL;
		$pengguna_form_data 	= isset($input['pengguna']) ? $input['pengguna'] : array();
		$redirect 				= isset($input['redirect']) ? $input['redirect'] : NULL;

		$update_valid = TRUE;
		if( isset($pengguna_form_data['id_pengguna']) && !empty($pengguna_form_data['id_pengguna']) ){
			$is_unique_pk = $this->is_unique( 'id_pengguna', $pengguna_form_data['id_pengguna']);
			$update_valid = isset($update_valid['results']) ? $update_valid['results'] : TRUE;
			if($update_valid == FALSE){
				$result['message'] 	= "ID sudah digunakan.";
			}
		}

		if(isset($pengguna_form_data['password']) && !empty($pengguna_form_data['password'])){
			$pengguna_form_data['password'] = password_hash($pengguna_form_data['password'],PASSWORD_DEFAULT);
		} else {
			unset($pengguna_form_data['password']);
		}

		if( $update_valid == TRUE ){
			if( is_array($pengguna_form_data) && count($pengguna_form_data) > 0 ){
				$response['message'] 	= "Data failed to save.";
				$this->db->where('id_pengguna', $pk);

				if(empty($pengguna_form_data['id_cabang'])){
					$pengguna_form_data['id_cabang'] = NULL;
				}
				$updated = $this->db->update('pengguna', $pengguna_form_data);
				if( $updated ){
					$response = array(
						'status_code' 	=> 200,
						'status' 		=> 'success',
						'message'		=> 'Data has been save.',
						'redirect'		=> $redirect,
						'results' 		=> null
					);
				}
			}
		}
		return $response;
		unset($response, $input, $pengguna_form_data, $is_unique_pk, $insert_valid, $inserted);

	}

	public function delete($input){

		$response = array(
			'status_code' 	=> 401,
			'status' 		=> 'error',
			'message'		=> 'Please complete the form field requirements.',
			'redirect'		=> null,
			'results' 		=> null
		);

		$pk 				= isset($input['pk']) ? $this->db->escape_str($this->security->xss_clean($input['pk'])) : NULL;
		if( !empty($pk) ){
			$result['message'] 	= "Data failed to delete.";
			$tables = array('pengguna');
			$this->db->where('id_pengguna', $pk);
			$deleted = $this->db->delete($tables);
			if( $this->db->affected_rows() > 0 ){
				$response = array(
					'status_code' 	=> 200,
					'status' 		=> 'success',
					'message'		=> 'Data has been deleted.',
					'redirect'		=> null,
					'results' 		=> null
				);
			}
			unset($data);
		}
		return $response;
		unset($response);

	}

	public function datatables($request){
		$columns 			= array("`id_pengguna`","`nama`","`hak_akses`","`status`","`id_pengguna`");
		$select_total 		= "SELECT COUNT(`id_pengguna`) AS `total` ";
		$select_filtered	= "SELECT FOUND_ROWS() AS `total` ";
		$select 			= "SELECT SQL_CALC_FOUND_ROWS * ";
		$from 				= "FROM `pengguna` ";
		$where 				= "";
		$group_by 			= "";
		$having 			= "";
		$order_by 			= "";
		$limit 				= "";
		if( isset($request["search"]["value"]) && !empty($request["search"]["value"]) ) {
			$q	= $this->db->escape_str(strip_tags($request["search"]["value"]));
			$fields = array();
			foreach( $columns AS $col ){
				array_push($fields,"(".$col." LIKE '%".$q."%')");
			}
			$having = " HAVING " . implode(" OR ",$fields) . " "; 
			unset($fields,$col,$q);
		}
		if( isset($request['order'][0]['column']) ){
			$field 	= $columns[$request["order"][0]["column"]];
			$dir 	= strtoupper($this->db->escape_str($request["order"][0]["dir"]));
			$order_by = " ORDER BY " . $field . " " . $dir . " "; 
			unset($field,$dir);
		}
		if ( isset( $request["start"] ) && $request["length"] != '-1' ) {
			$limit = "LIMIT " . $request["start"] . "," . $request["length"];
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
			"status_code"		=> 200,
			"draw" 				=> intval( isset($request['draw']) ? $request['draw'] : 1 ),
			"recordsTotal" 		=> intval( isset($totalData['total']) ? $totalData['total'] : 0 ),
			"recordsFiltered" 	=> intval( isset($totalFiltered['total']) ? $totalFiltered['total'] : 0 ),
			"data"				=> $data 
		); unset($results,$request,$totalData,$totalFiltered,$data);
	}
	public function select2($input){

		$row_per_page 	= 10;
		$select_total 	= "SELECT COUNT(`id_pengguna`) AS `total` ";
		$select_data	= "SELECT `id_pengguna` AS `id_pengguna`, `nama` AS `text` ";
		$from 			= "FROM `pengguna` ";
		$where 			= "WHERE `id_pengguna` != '' ";
		$order_by 		= "ORDER BY `nama` ASC ";

		if(isset($input['q']) && !empty($input['q'])){
			$q 		= $this->db->escape_str($this->security->xss_clean($input['q']));
			$where .= " AND (`nama` LIKE '%". $q ."%' OR `id_pengguna` LIKE '%". $q ."%') ";
		}


		$result_total	= $this->db->query($select_total . $from . $where . ";");
		$total_data 	= $result_total->row()->total;
		$result_total->free_result();

		$total_page		= ceil((int)$total_data/$row_per_page);
		$page 			= isset($input['page']) ? (int)$this->db->escape_str($this->security->xss_clean($input['page'])) : 1;
		$offset 		= (($page - 1) * $row_per_page);

		$data 			= $this->db->query($select_data . $from . $where . $order_by . "LIMIT ". $row_per_page ." OFFSET ". $offset .";");

		$response = array(
			'status_code' 	=> 200,
			'status' 		=> 'success',
			'message'		=> '',
			'results' 		=> $data->result_array(),
			'pagination'	=> array( 'more' => ($page < $total_page) ) 
		);
		$data->free_result();

		return $response;

	}

	public function single($pk){
		$data = $this->db->get_where("pengguna",array('id_pengguna'=>$this->db->escape_str($pk)));
		return array(
			'status_code' 	=> 200,
			'status' 		=> 'success',
			'message'		=> '',
			'results' 		=> $data->row_array()
		);
	}

	public function select2_cabang($params = array()){
		//extract($config);
		$row_per_page 	= isset($row_per_page) ? $row_per_page : 10;
		$select_total 	= "SELECT COUNT(DISTINCT(`id`)) AS `total` ";
		$select_data	= "SELECT * ";
		$from 			= "FROM `cabang` ";
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
		$results = array_merge([
			['id'=>0,'kode'=>NULL,'nama'=>'Seluruh Cabang','jenis'=>'Pusat','logo'=>null,'email'=>'','telp'=>'','alamat'=>''],
			['id'=>0,'kode'=>NULL,'nama'=>'kepala ahsanaabg','jenis'=>'Pusat','logo'=>null,'email'=>'','telp'=>'','alamat'=>'','id_cabang'=>'4']
		], $data->result_array());
		return array( 
			'results' 		=> $results,
			'pagination' 	=> array('more' => ($page < $total_page)) 
		);
		$data->free_result();
	}
	public function select2_hak_akses($params = array()){
		$jenis = 'Pusat';
		$jenis = isset($params['jenis']) &&  !empty($params['jenis']) ? $params['jenis'] : 'Pusat';
		$hak_akses = [
			'Pusat' 		=> [['id'=>'Super Admin','text'=>'Super Admin','selected'=>'true']],
			'Toko' 			=> [['id'=>'Admin','text'=>'Admin','selected'=>'true'],['id'=>'Super Admin','text'=>'Super Admin','selected'=>'true'],['id'=>'Kepala Toko','text'=>'Kepala Toko'],['id'=>'Kasir','text'=>'Kasir Lampung'], ['id'=>'Kasir_jakarta','text'=>'Kasir_jakarta']],
			'Pengadaan' 	=> [['id'=>'Admin','text'=>'Admin','selected'=>'true'],['id'=>'Kasir','text'=>'Kasir']]
		];
		return [
			'results' => $hak_akses[$jenis]
		]; 
	}

}
