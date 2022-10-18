<?php
class Policies_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}

	public function policies_add($data, $user_id){
			$dateFrom = date_create($data['fromDate']);
			$dateFrom = date_format($dateFrom,"Y/m/d");
			$dateTo = date_create($data['toDate']);
			$dateTo = date_format($dateTo,"Y/m/d");
			$data['fromDate'] = str_replace("/","-", $dateFrom);
			$data['toDate'] = str_replace("/","-", $dateTo);
			$insert_data = array(
				'created' => date('Y-m-d H:i:s'),
				'updated' => date('Y-m-d H:i:s'),
				'userid' => $user_id,
				'title' => $data['discount_title'],
				'valid_from' => $data['fromDate'],
				'valid_to' => $data['toDate'],
				'type' => $data['FixedorPercent'],
				'value' => $data['valueOfPercentOrFixed'],
				'display_status' => '1',
				'active' => '1'
			);
			$this->db->insert('tbl_policies', $insert_data);
			if($this->db->affected_rows() > 0){
				return true;
			} else {
				return false;
			}
	}

	public function policies_update($data, $user_id, $policy_id){
		$title = $data['title'];
		$dateFrom = date_create($data['fromDate']);
		$dateFrom = date_format($dateFrom,"Y/m/d");
		$dateTo = date_create($data['toDate']);
		$dateTo = date_format($dateTo,"Y/m/d");
		$data['fromDate'] = str_replace("/","-", $dateFrom);
		$data['toDate'] = str_replace("/","-", $dateTo);
		$insert_data = array(
			'title' => $title,
			'updated' => date('Y-m-d H:i:s'),
			'userid' => $user_id,
			'valid_from' => $data['fromDate'],
			'valid_to' => $data['toDate'],
			'type' => $data['FixedorPercent'],
			'value' => $data['valueOfPercentOrFixed'],
		);

		$this->db->where('userid', $user_id)
				 ->where('id', $policy_id)
				 ->update('tbl_policies', $insert_data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	public function getUserPoliciesByUserId($user_id, $policy_id = '')
	{
		$where = "(userid='$user_id' OR user_type='1') AND active='1'";

		$this->db->select("*")->from('tbl_policies');
		if($policy_id != ''){
			$this->db->where('id', $policy_id);
		}
		$this->db->where($where);
		$query = $this->db->get();
		// echo $this->db->last_query(); die();
		if($query && $query->num_rows()>0){
			return $query->result();
		} else {
			return FALSE;			
		}
	}

	public function getUserPolicies($user_id, $search, $offset, $length, $policy_id = '')
	{
		$where = "(userid='$user_id' OR user_type='1') AND active='1'";

		$this->datatables->select('id, title, valid_from, valid_to, type, value, display_status, user_type')
			->from('tbl_policies as d')
			->where('active', '1')
			->where($where);
		$this->datatables->like('title', $search);	
		$this->db->order_by('id', 'desc');
		if($length != -1){
			$this->db->limit($length,  $offset);		
		  }
		if($policy_id != ''){
			$this->datatables->where('id', $policy_id);
		} else {
			$this->datatables->or_where('id', 0);
		}
	}

	function applyDiscount($policy_id, $user_id){
		$this->db->where("seller_id",$user_id)->set('discount',$policy_id);
		// echo"<pre>"; print_r($this->db->last_query()); die();
		if($this->db->update('tbl_product_inventory')){
			if($this->db->affected_rows() > 0){
				return array("status"=>"success");
			}else{
				return array('status'=>"success","code"=>"Already apply to all");
			} 
		}else {
			return array("status"=>"error");
		}
	}

	function deleteDiscount($policy_id, $user_id, $value){
		$insert_data = array(
			'display_status' => $value
		);
		$this->db->where('id', $policy_id)
				 ->update('tbl_policies', $insert_data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	function deleteDiscountVoucher($voucher_id, $user_id){
		$update_data = array(
			'updated' => date('Y-m-d H:i:s'),
			'active' => '0'
		);
		$this->db->where(array('id'=>$voucher_id, 'user_id'=>$user_id))
				 ->update('tbl_discount_voucher', $update_data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}
	
	function hard_delete_discount($policy_id, $user_id){
		$this->db->where('id', $policy_id)->delete('tbl_policies');
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}
	
	public function getDiscountCoupons($user_id, $voucher_id = '', $search = '', $offset = 0, $length = 100)
	{
		$this->load->library('datatables');	
		$this->datatables->select("dv.id, voucher_title as title, dv.voucher_code as code, UNIX_TIMESTAMP(valid_from) as valid_from, UNIX_TIMESTAMP(valid_to) as valid_to, apply_on, IF(dv.apply_on = 'product', p.product_name,IF(dv.apply_on = 'category', c.category_name, IF(dv.apply_on = 'user', CONCAT(firstname,' ', middlename, ' ',lastname), IF(dv.apply_on = 'seller', store_name, '-')))) AS apply_on_title, if(discount_type = '1', CONCAT(discount,'%'), discount) as discount, CONCAT(dv.used_limit,'/' ,COUNT(t.`voucher_code`)) as voucher_usage")
			->from('tbl_discount_voucher as dv')
			->join('tbl_product_variant as pv', 'dv.apply_id = pv.pv_id AND dv.apply_on = "product"', 'left')
			->join('tbl_product as p', 'pv.product_id = p.product_id AND dv.apply_on = "product"', 'left')
			->join('tbl_categories as c', 'dv.apply_id = c.category_id AND dv.apply_on = "category"', 'left')
			->join('tbl_users as u', 'dv.apply_id = u.userid AND dv.apply_on = "user"', 'left')
			->join('tbl_seller_store as ss', 'dv.apply_id = ss.seller_id AND dv.apply_on = "seller"', 'left')
			->join('tbl_transaction as t', 'dv.voucher_code = t.voucher_code', 'left')
			->where('dv.active', '1')
			->where('dv.user_id', $user_id);
		if($search != '')
			$this->datatables->like('voucher_title', $search);
		$this->db->order_by('dv.id', 'desc');
		$this->db->group_by('dv.id');
		if($length != -1){
			$this->db->limit($length,  $offset);		
		}
		if($voucher_id != ''){
			$this->datatables->where('dv.id', $voucher_id);
		}
	}
	
	public function getVoucher($user_id, $voucher_id = '', $voucher_code = '', $search = '', $offset = 0, $length = 100)
	{
		$this->db->select("dv.id, voucher_title as title, dv.voucher_code as code, UNIX_TIMESTAMP(valid_from) as valid_from, UNIX_TIMESTAMP(valid_to) as valid_to, apply_on, apply_id, discount_type, discount, min_price, max_price, used_limit, COUNT(t.voucher_code) AS voucher_used, dv.seller_id,IF(dv.apply_on = 'product', p.product_name,IF(dv.apply_on = 'category', c.category_name, IF(dv.apply_on = 'user', CONCAT(firstname,' ', middlename, ' ',lastname), IF(dv.apply_on = 'seller', store_name, '-')))) AS apply_on_title,")
			->from('tbl_discount_voucher as dv')
			->join('tbl_product_variant as pv', 'dv.apply_id = pv.pv_id AND dv.apply_on = "product"', 'left')
			->join('tbl_product as p', 'pv.product_id = p.product_id AND dv.apply_on = "product"', 'left')
			->join('tbl_categories as c', 'dv.apply_id = c.category_id AND dv.apply_on = "category"', 'left')
			->join('tbl_users as u', 'dv.apply_id = u.userid AND dv.apply_on = "user"', 'left')
			->join('tbl_seller_store as ss', 'dv.apply_id = ss.seller_id AND dv.apply_on = "seller"', 'left')
			->join('tbl_transaction as t', 'dv.voucher_code = t.voucher_code', 'left')
			->where('active', '1');
		$this->db->order_by('dv.id', 'desc');
		$this->db->group_by('dv.id');
		if($user_id != ''){
			$this->db->where('dv.user_id', $user_id);
		}
		if($voucher_code != ''){
			$this->db->where('dv.voucher_code', $voucher_code);
		}
		if($voucher_id != ''){
			$this->db->where('dv.id', $voucher_id);
		}
		if($search != '')
			$this->db->like('voucher_title', $search);
		$this->db->limit($length,  $offset);
		$query = $this->db->get();
		//echo $this->db->last_query();die();
		if($query && $query->num_rows()>0){
			return $query->result();
		} else {
			return array();
		}
	}
	
	public function checkVoucher($code)
	{
		$this->db->select("*")->from('tbl_discount_voucher')->where('voucher_code', $code);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return FALSE;
		} else {
			return TRUE;			
		}
	}
	
	public function voucher_add($data, $user_id, $isAdmin)
	{
		//echo '<pre>';print_r($data);die();
		$dateFrom = date_create($data['fromDate']);
		$dateFrom = date_format($dateFrom,"Y/m/d");
		$dateTo = date_create($data['toDate']);
		$dateTo = date_format($dateTo,"Y/m/d");
		$data['fromDate'] = str_replace("/","-", $dateFrom);
		$data['toDate'] = str_replace("/","-", $dateTo);
		$insert_data = array(
			'created' => date('Y-m-d H:i:s'),
			'updated' => date('Y-m-d H:i:s'),
			'user_id' => $user_id,
			'seller_id' => (!$isAdmin)?$user_id:'',
			'voucher_title' => $data['discount_title'],
			'voucher_code' => $data['voucher_code'],
			'used_limit' => $data['discount_limit'],
			'valid_from' => $data['fromDate'],
			'valid_to' => $data['toDate'],
			'discount_type' => $data['discount_type'],
			'discount' => $data['discount_value'],
			'min_price' => $data['min_price'],
			'max_price' => $data['max_price'],
			'apply_on' => $data['apply_on'],
			'apply_id' => $data['apply_id'],
			'active' => '1'
		);
		$this->db->insert('tbl_discount_voucher', $insert_data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}
	
	public function voucher_update($data, $user_id, $voucher_id)
	{
		$dateFrom = date_create($data['fromDate']);
		$dateFrom = date_format($dateFrom,"Y/m/d");
		$dateTo = date_create($data['toDate']);
		$dateTo = date_format($dateTo,"Y/m/d");
		$data['fromDate'] = str_replace("/","-", $dateFrom);
		$data['toDate'] = str_replace("/","-", $dateTo);
		$update_data = array(
			'updated' => date('Y-m-d H:i:s'),
			'user_id' => $user_id,
			//'seller_id' => (!$isAdmin)?$user_id:'',
			'voucher_title' => $data['discount_title'],
			'used_limit' => $data['discount_limit'],
			'valid_from' => $data['fromDate'],
			'valid_to' => $data['toDate'],
			'discount_type' => $data['discount_type'],
			'discount' => $data['discount_value'],
			'min_price' => $data['min_price'],
			'max_price' => $data['max_price'],
			'apply_on' => $data['apply_on'],
			'apply_id' => $data['apply_id'],
			'active' => '1'
		);
		$this->db->where(array('id'=>$voucher_id, 'user_id'=>$user_id));
		if($this->db->update('tbl_discount_voucher', $update_data)){
			//echo $this->db->last_query();die();
			return true;
		} else {
			return false;
		}
	}
	
	public function buyerUsedVouchers($user_id, $voucher_code = '')
	{
		$this->db->select('t.order_id, t.voucher_code')
				 ->from('tbl_transaction as t')
				 ->where('t.user_id', $user_id);
		if($voucher_code != ''){
			$this->db->where('t.voucher_code', $voucher_code);
		}
		$this->db->order_by('t.order_id', 'desc');
		$this->db->group_by('t.voucher_code');
		$query = $this->db->get();
		//echo $this->db->last_query();die();
		if($query && $query->num_rows()>0){
			return $query->result();
		} else {
			return array();
		}
	}
	
	public function getDiscountItemsData($term, $apply_on, $seller_id, $user_type)
	{
		$result = array();
		switch($apply_on){
			case 'product':
				$get_product = $this->Product_Model->getInventoryBySeller($seller_id, $term,$user_type);
				//echo '<pre>';print_r($get_product);echo '</pre>';die();
				$i = 0; 
				$rows = count($get_product);
				while( $i < $rows  ){
					$result[] = array(
						'label' => stripslashes($get_product[$i]->product_name),
						'id'	=> $get_product[$i]->pv_id,
						'value'	=> stripslashes($get_product[$i]->product_name),
						'condition'	=> stripslashes($get_product[$i]->condition_name),
						'variant'	=> stripslashes($get_product[$i]->variant),
						'type'	=> $apply_on,
					);
					$i++;
				}
			break;
			case 'category':
				$this->load->model("admin/Category_Model");
				$getCategories = $this->Category_Model->searchCategory($term);
				$i = 0; 
				$rows = count($getCategories);
				while( $i < $rows  ){	
					$result[] = array(
						'label' => stripslashes($getCategories[$i]->category_name),
						'id'	=> $getCategories[$i]->id,
						'value'	=> stripslashes($getCategories[$i]->category_name),
						'type'	=> $apply_on,
					);
					$i++;
				}
			break;
			case 'seller':
				$this->load->model("admin/User_Model");
				$getSellers = $this->User_Model->get_sellers($term, array('start'=>0, 'draw'=>''));
				$i = 0; 
				$getSellers = $getSellers['data'];
				$rows = count($getSellers);
				while( $i < $rows  ){
					$result[] = array(
						'label' => stripslashes($getSellers[$i]->store_name),
						'id'	=> $getSellers[$i]->seller_id,
						'value'	=> stripslashes($getSellers[$i]->store_name),
						'type'	=> $apply_on,
					);
					$i++;
				}
			break;
			case 'user':
				$this->load->model("admin/User_Model");
				$getUsers = $this->User_Model->get_buyers($term, array('start'=>0, 'draw'=>'', 'length'=>''));
				$i = 0; 
				$getUsers = $getUsers['data'];
				$rows = count($getUsers);
				while( $i < $rows  ){	
					$result[] = array(
						'label' => stripslashes($getUsers[$i]->name),
						'id'	=> $getUsers[$i]->userid,
						'value'	=> stripslashes($getUsers[$i]->name),
						'type'	=> $apply_on,
					);
					$i++;
				}
			break;
		}
		// echo "<pre>"; print_r($result); die();
		return $result;
	}
}