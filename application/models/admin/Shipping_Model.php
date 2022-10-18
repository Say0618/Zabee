<?php
class Shipping_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}

	public function getShipping($search = "", $request = "", $userid = ""){
		if($search ==""){
			$this->db->select('*')->from(DBPREFIX."_product_shipping")->order_by('shipping_id','ASC ');
			if($userid == 1){
				$this->db->where('user_id',$userid)->where('is_deleted',"0");
			}else{
				$this->db->where('(user_id="'.$userid.'" OR (user_type="1" AND is_active="1"))',NULL,FALSE)->where('is_deleted',"0");
			}
			if($request['start'] != 0){
				$this->db->limit($request['length'],$request['start']); 
			}
			$query = $this->db->get();
		}else{
			$search = trim($search);
			$this->db->select('*')->from(DBPREFIX."_product_shipping")->like('title',$search)->order_by('shipping_id','ASC ');
			if($userid == 1){
				$this->db->where('user_id',$userid)->where('is_deleted',"0");
			}else{
				$this->db->where('(user_id="'.$userid.'" OR user_type="1" AND is_active="1"))',NULL,FALSE)->where('is_deleted',"0");
			}
			if($request['start'] != 0){
				$this->db->limit($request['length'],$request['start']); 
			}
			$query = $this->db->get();
		}

		$data['draw'] = $request['draw'];
		$data['data'] = $query->result();
		if($request['start'] != 0){
			$data['recordsTotal']= $request['recordsTotal'];
			$data['recordsFiltered']= $request['recordsTotal'];
		}else{
			$data['recordsTotal']= $query->num_rows();
			$data['recordsFiltered']= $query->num_rows();
		}
		return $data;
	}
	
	public function getShippingByUser($user_id, $shipping_id = '', $search = '', $offset = 0, $limit = 100)
	{
		$this->db->select('ps.shipping_id, UNIX_TIMESTAMP(ps.created_date) as created_date,UNIX_TIMESTAMP(ps.updated_date) as updated_date, ps.title, ps.user_id, ps.price, ps.shipping_type, ps.base_weight, ps.weight_unit, ps.base_length, ps.base_width, ps.base_depth, ps.dimension_unit, ps.incremental_price, ps.incremental_unit, ps.free_after, ps.duration, ps.description, ps.countries, ps.states, ps.display_status, ps.is_active,ps.is_deleted')
				 ->from(DBPREFIX."_product_shipping AS ps")
				 ->where('(user_id = "'.$user_id.'" OR (is_active = "1" AND user_type = 1))', null, false)
				 ->where('is_deleted', '0')
				 ->order_by('ps.shipping_id', 'desc');
		if($search != '')
			$this->db->like('title', $search);
		if($limit != -1){
			$this->db->limit($limit,  $offset);		
		}
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		} else {
			return array();
		}
	}
	
	public function shipping_add($data, $user_id){
		$shipping_name = $data['title'];
		$this->db->select("title")
				->from('tbl_product_shipping')
				->where('title', $shipping_name)
				->where('is_deleted', '0')
				->where('is_active', '1')
				->where('user_id', $user_id);
		$query = $this->db->get();
		if($query && $query->num_rows() > 0){
			return false;
		} else {
			$insert_data = array(
				'created_date' => date('Y-m-d H:i:s'),
				'user_id' => $data['user_id'],
				'user_type' => $data['user_type'],
				'title' => $data['title'],
				'price' => $data['price'],
				'shipping_type' => $data['basedOn'],
				'base_weight' => $data['base_weight'],
				'weight_unit' => $data['weight_unit'],
				'base_length' => $data['base_length'],
				'base_width' => $data['base_width'],
				'base_depth' => $data['base_depth'],
				'dimension_unit' => $data['dimension_unit'],
				'incremental_price' => $data['inc_price'],
				'incremental_unit' => $data['inc_unit'],
				'free_after' => $data['free_after'],
				'duration' => $data['minimum_days']."-".$data['maximum_days'],
				"description"=>$data['description']
				);
			//echo '<pre>';print_r($insert_data);echo '<pre>';die();
			$this->db->insert(DBPREFIX.'_product_shipping', $insert_data);
			if($this->db->affected_rows() > 0){
				return true;
			} else {
				return false;
			}
		}
	}

	public function updateShippingStauts($shipping_id,$value,$user_id){
		$result= array("status"=>0);
		$date = date('Y-m-d H:i:s');
		$data = array("is_active"=>$value,'updated_date'=>$date);
		$this->db->where('shipping_id',$shipping_id);
		$this->db->where('user_id',$user_id);
		$this->db->update(DBPREFIX.'_product_shipping',$data);
		if($this->db->affected_rows() > 0){
			$result = array("status"=>1);
		}
		return $result;
	}

	function delete_category($category_id, $user_id, $value){
		$insert_data = array(
			'display_status' => $value
		);
		$this->db->where('category_id', $category_id)
				 ->update('tbl_categories', $insert_data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	function getShippingbyId($id){
		$this->db->select('*')
				->from(DBPREFIX."_product_shipping")
				->where('shipping_id',$id);
		$query = $this->db->get();
		$result =  $query->result();
		return $result;
	}

	function save_edit_shipping($data,$id,$user_id){
		$title = $data['title'];
		$query = $this->db->select("title")
						->from('tbl_product_shipping')
						->where('title', $title)
						->where('shipping_id !=',$id)
						->where('is_active !=',1)
						->where('user_id', $user_id)->get();
		$nor = $query->num_rows();
		$result = ($nor>0)?$query->row():array();
		if($nor > 0 && $title == $result->title){
			return 0;
		} else {
			$insert_data = array(
				'title' => $data['title'],
				'price' => $data['price'],
				'shipping_type' => $data['basedOn'],
				'base_weight' => $data['base_weight'],
				'weight_unit' => $data['weight_unit'],
				'base_length' => $data['base_length'],
				'base_width' => $data['base_width'],
				'base_depth' => $data['base_depth'],
				'dimension_unit' => $data['dimension_unit'],
				'incremental_price' => $data['inc_price'],
				'incremental_unit' => $data['inc_unit'],
				'free_after' => $data['free_after'],
				'duration' => $data['minimum_days']."-".$data['maximum_days'],
				"description"=>$data['description']
			);
			$this->db->where('shipping_id', $id)->update('tbl_product_shipping', $insert_data);

			if($this->db->affected_rows() > 0){
				return 1;
			} else {
				return 2;
			}
		}
	}

	function delete_shipping($shipping_id,$user_id){
		$insert_data = array(
			'is_deleted' => '1'
		);
		$this->db->where('user_id', $user_id)
				 ->where('shipping_id', $shipping_id)
				 ->update('tbl_product_shipping', $insert_data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	public function checkShippingCount($shipping_id,$user_id){
		$ship = "";
		$inventory = $this->db->select("GROUP_CONCAT(pin.inventory_id) AS inventory_id")->from(DBPREFIX."_product_inventory AS pin")->where("pin.shipping_ids", $shipping_id)
				 		  ->group_by("pin.shipping_ids")->get()->result();
		if($inventory){
			$ship = $this->db->select("shipping_id, title, price")->from(DBPREFIX."_product_shipping")->where("(user_id='".$user_id."' OR user_type='1') AND is_active='1' AND is_deleted = '0' AND display_status = '1' AND shipping_id !=".$shipping_id)
				 		  	  ->get()->result();
								// echo"<pre>"; print_r($this->db->last_query()); die();
		}
		$data['ship'] = $ship;
		return $data;
	}

	public function transferShipping($current_ship, $ship_id, $user_id){
		$result = $this->db->where(array("shipping_ids"=>$current_ship, "seller_id"=>$user_id))->update(DBPREFIX."_product_inventory",array("shipping_ids"=>$ship_id));
		// echo $this->db->last_query(); die();
		if($result){
			$this->db->where("shipping_id", $current_ship)->update(DBPREFIX."_product_shipping",array("is_active"=>"0"));
			return array("status"=>"0", "message"=>"true");
		}
		return array("status"=>"1", "message"=>"false");
	}
}	
?>